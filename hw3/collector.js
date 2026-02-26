(function() {

  'use strict';

  // Configuration Defaults 
  const config = {
    endpoint: '',
    enableVitals: true,
    enableErrors: true,
    sampleRate: 1.0,
    debug: false,
    respectConsent: true,
    detectBots: true
  };

  // Internal State 
  let initialized = false;
  let blocked = false;           // Set true if consent/bot/sampling blocks collection
  const customData = {};         // Data set via set()
  let userId = null;             // Data set via identify()
  const plugins = [];            // Registered plugins
  const reportedErrors = new Set();
  let errorCount = 0;
  const MAX_ERRORS = 10;

  // Web Vitals State 
  const vitals = { lcp: null, cls: 0, inp: null };

  // Time-on-Page State
  let pageShowTime = Date.now();
  let totalVisibleTime = 0;

  // Utility
  /**
   * Round a number to two decimal places.
   */
  function round(n) {
    return Math.round(n * 100) / 100;
  }

  /**
   * Merge properties from src into dst (shallow).
   */
  function merge(dst, src) {
    for (const key of Object.keys(src)) {
      dst[key] = src[key];
    }
    return dst;
  }

  // Consent

  /**
   * Check whether the user has granted analytics consent.
   * Returns false if Global Privacy Control is set or if the
   * analytics_consent cookie is absent or set to 'false'.
   */
  function hasConsent() {
    // Check Global Privacy Control
    if (navigator.globalPrivacyControl) {
      return false;
    }

    // Check consent cookie
    const cookies = document.cookie.split(';');
    for (const c of cookies) {
      const cookie = c.trim();
      if (cookie.indexOf('analytics_consent=') === 0) {
        return cookie.split('=')[1] === 'true';
      }
    }

    // No consent signal â€” default to false (GDPR opt-in model)
    return false;
  }

  // Bot Detection

  /**
   * Detect common bots and automated browsers.
   * Returns true if the visitor appears to be a bot.
   */
  function isBot() {
    // WebDriver flag (Puppeteer, Selenium, Playwright)
    if (navigator.webdriver) return true;

    // Headless browser indicators in user agent
    const ua = navigator.userAgent;
    if (/HeadlessChrome|PhantomJS|Lighthouse/i.test(ua)) return true;

    // Chrome UA without window.chrome object
    if (/Chrome/.test(ua) && !window.chrome) return true;

    // Automation framework globals
    if (window._phantom || window.__nightmare || window.callPhantom) return true;

    return false;
  }

  // Sampling

  /**
   * Determine whether this session should be sampled.
   * Uses a persistent random value per session so the decision
   * is consistent across page navigations within the same session.
   */
  function isSampled() {
    if (config.sampleRate >= 1.0) return true;
    if (config.sampleRate <= 0) return false;

    const key = '_collector_sample';
    let val = sessionStorage.getItem(key);
    if (val === null) {
      val = Math.random();
      sessionStorage.setItem(key, val);
    } else {
      val = parseFloat(val);
    }
    return val < config.sampleRate;
  }

  // Session Identity

  /**
   * Generate or retrieve a session ID from sessionStorage.
   */
  function getSessionId() {
    let sid = sessionStorage.getItem('_collector_sid');
    if (!sid) {
      sid = Math.random().toString(36).substring(2) + Date.now().toString(36);
      sessionStorage.setItem('_collector_sid', sid);
    }
    return sid;
  }

  // Technographics

  /**
   * Collect network information via the Network Information API.
   */
  function getNetworkInfo() {
    if (!('connection' in navigator)) return {};
    const conn = navigator.connection;
    return {
      effectiveType: conn.effectiveType,
      downlink: conn.downlink,
      rtt: conn.rtt,
      saveData: conn.saveData
    };
  }

  function imageSupported() {
    return new Promise(function(resolve, reject) {
        const image = document.createElement('img');
        image.src = 
        image.onload = function() {
            resolve(true);
        };
        image.onerror = function() {
            resolve(false); 
        };
    });
  }

  /**
   * Collect a complete technographic profile.
   */
  async function getTechnographics() {
    const imgSupported = await imageSupported();
    return {
        userAgent: navigator.userAgent,
        language: navigator.language,
        cookiesEnabled: navigator.cookieEnabled,
        viewportWidth: window.innerWidth,
        viewportHeight: window.innerHeight,
        screenWidth: window.screen.width,
        screenHeight: window.screen.height,
        pixelRatio: window.devicePixelRatio,
        cores: navigator.hardwareConcurrency || 0,
        memory: navigator.deviceMemory || 0,
        network: getNetworkInfo(),
        colorScheme: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light',
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        javascriptEnabled: true,
        imagesEnabled: imgSupported
    };
  }

  // checks to see if CSS external sheets works/loads: 
  // if not --> CSS suggested to be blocked/disabled 
   function isExternalCSSLoaded(){
    const links = [...document.querySelectorAll('link[rel="stylesheet"]')];

    if(links.length === 0) {
      return{ 
        found: false, 
        anyLoaded: false, 
        reason: "no stylesheets found" 
      };
    }   

    const results = links.map(link => {
      const sheet = link.sheet;

      // if no sheet object, definitely not loaded
      if(!sheet){
        return{ 
          href: link.href, 
          loaded: false, 
          reason: "no link.sheet" 
        };
      }
      // attempting to access cssRules (can throw if not actually available / cross-origin)
      try{
        const rulesCount = sheet.cssRules ? sheet.cssRules.length : 0;
        return{ 
          href: link.href, 
          loaded: true, rulesCount 
        };
      } 
      catch (e){
        // Cross-origin stylesheets often throw SecurityError here
        return{ 
          href: link.href, 
          loaded: true, 
          reason: "cross-origin (cannot read rules)" 
        };
      }
    });

    const anyLoaded = results.some(r => r.loaded);
    
    return{
      found: true,
      anyLoaded,
      stylesheets: results
    };
  }

   /**
   * Collecting performance data, utilizing getNavigationTiming() reference code.
   */
  function getPerformanceData() {
    const n = performance.getEntriesByType('navigation')[0];
    return {
        rawTiming: n.toJSON(),
        loadStart: n.fetchStart - n.startTime,
        loadEnd: n.loadEventEnd - n.startTime,
        totalLoadTime: n.loadEventEnd - n.fetchStart
    };
  }

  window.addEventListener('load', function(){
    const staticData = getTechnographics();
    
      // send it to your server
      fetch('/api/collect', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(staticData)
      });

      console.log("Is External CSS Loaded:", isExternalCSSLoaded());
  });
})