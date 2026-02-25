/* Shopmoji: fake store with real interactions
   - theme toggle
   - product filtering/sorting/search
   - fake cart in localStorage
   - forms + validation + UX messages
*/

const STORAGE_KEY = "shopmoji_cart_v1";
const THEME_KEY = "shopmoji_theme_v1";

const PRODUCTS = [
  { id: "p1", name: "🍕 Pizza Mood Sticker", emoji: "🍕", category: "food", price: 4.99, stock: 12, popular: 98, desc: "Because every feeling is better with pizza energy." },
  { id: "p2", name: "😺 Cat Vibes Pack", emoji: "😺", category: "animals", price: 7.49, stock: 8, popular: 95, desc: "A premium bundle of purr-certified reactions." },
  { id: "p3", name: "🧠 Big Brain Bundle", emoji: "🧠", category: "productivity", price: 9.99, stock: 5, popular: 92, desc: "For moments when your neurons choose violence (or brilliance)." },
  { id: "p4", name: "😭 Crying But Trying", emoji: "😭", category: "moods", price: 3.49, stock: 0, popular: 88, desc: "A relatable classic. Out of stock… like your patience." },
  { id: "p5", name: "🚀 Launch Day Hype", emoji: "🚀", category: "space", price: 6.99, stock: 14, popular: 90, desc: "Instant momentum for deadlines and dreams." },
  { id: "p6", name: "✨ Sparkle Upgrade", emoji: "✨", category: "moods", price: 2.99, stock: 23, popular: 99, desc: "Add ✨ sparkles ✨ to literally anything." },
  { id: "p7", name: "🧋 Boba Mood Bundle", emoji: "🧋", category: "food", price: 8.49, stock: 4, popular: 94, desc: "Chewy pearls. Softer feelings." },
  { id: "p8", name: "✅ Task Crusher Set", emoji: "✅", category: "productivity", price: 5.99, stock: 11, popular: 89, desc: "Mark it done. Feel unstoppable." },
  { id: "p9", name: "🐶 Doggo Approval Seal", emoji: "🐶", category: "animals", price: 4.49, stock: 17, popular: 87, desc: "For messages that deserve tail-wag validation." },
];

function $(sel) { return document.querySelector(sel); }
function $all(sel) { return Array.from(document.querySelectorAll(sel)); }

function readCart() {
  try {
    return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
  } catch {
    return [];
  }
}
function writeCart(items) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
  updateCartCount();
}
function cartCount() {
  return readCart().reduce((sum, it) => sum + it.qty, 0);
}
function updateCartCount() {
  const el = $("#cartCount");
  if (el) el.textContent = String(cartCount());
}

function addToCart(productId, qty = 1) {
  const cart = readCart();
  const found = cart.find(i => i.id === productId);
  if (found) found.qty += qty;
  else cart.push({ id: productId, qty });
  writeCart(cart);
}

function removeFromCart(productId) {
  const cart = readCart().filter(i => i.id !== productId);
  writeCart(cart);
}

function formatMoney(n) {
  return `$${n.toFixed(2)}`;
}

function applyThemeFromStorage() {
  const t = localStorage.getItem(THEME_KEY);
  if (t === "light") document.documentElement.classList.add("light");
  else document.documentElement.classList.remove("light");
}

function setupThemeToggle() {
  const btn = $("#themeToggle");
  if (!btn) return;

  btn.addEventListener("click", () => {
    const isLight = document.documentElement.classList.toggle("light");
    localStorage.setItem(THEME_KEY, isLight ? "light" : "dark");
    btn.textContent = isLight ? "☀️" : "🌙";
  });

  // initial icon
  const isLight = document.documentElement.classList.contains("light");
  btn.textContent = isLight ? "☀️" : "🌙";
}

function setupSpinDeal() {
  const btn = $("#spinDeal");
  const out = $("#dealText");
  if (!btn || !out) return;

  const deals = [
    "🎉 Deal unlocked: 10% off imaginary shipping!",
    "🌀 You spun: +1 vibe point (redeemable never).",
    "✨ Surprise: Sparkle Upgrade added to your aura.",
    "🔥 Hot deal: buy 0 get 0 free (incredible).",
    "😺 Cat says: you deserve a treat. Add a cat pack.",
  ];

  btn.addEventListener("click", () => {
    out.textContent = deals[Math.floor(Math.random() * deals.length)];
  });
}

function setupNewsletterForm() {
  const form = $("#newsletterForm");
  const msg = $("#newsletterMsg");
  if (!form || !msg) return;

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const email = form.elements.email.value.trim();
    const consent = form.elements.consent.checked;

    if (!email || !email.includes("@")) {
      msg.textContent = "Please enter a valid email 😅";
      return;
    }
    if (!consent) {
      msg.textContent = "Please check the consent box ✅";
      return;
    }
    msg.textContent = `Subscribed! Fake deals are on the way to ${email} ✨`;
    form.reset();
  });
}

function setupVibeForm() {
  const form = $("#vibeForm");
  const msg = $("#vibeMsg");
  if (!form || !msg) return;

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const vibe = form.elements.vibe.value;
    const recs = {
      cozy: "Recommendation: 🧋 Boba Mood Bundle",
      chaotic: "Recommendation: ✨ Sparkle Upgrade",
      productive: "Recommendation: ✅ Task Crusher Set",
      sleepy: "Recommendation: 😭 Crying But Trying (relatable)",
    };
    msg.textContent = recs[vibe] || "Pick a vibe to get a recommendation 🙂";
  });
}

function renderShop() {
  const grid = $("#productGrid");
  if (!grid) return;

  const searchInput = $("#searchInput");
  const categorySelect = $("#categorySelect");
  const onlyInStock = $("#onlyInStock");
  const sortSelect = $("#sortSelect");

  function getFiltered() {
    const q = (searchInput?.value || "").trim().toLowerCase();
    const cat = categorySelect?.value || "all";
    const inStockOnly = !!onlyInStock?.checked;
    const sort = sortSelect?.value || "popular";

    let list = PRODUCTS.slice();

    if (cat !== "all") list = list.filter(p => p.category === cat);
    if (inStockOnly) list = list.filter(p => p.stock > 0);
    if (q) list = list.filter(p =>
      p.name.toLowerCase().includes(q) ||
      p.emoji.includes(q) ||
      p.category.toLowerCase().includes(q)
    );

    if (sort === "priceLow") list.sort((a,b) => a.price - b.price);
    if (sort === "priceHigh") list.sort((a,b) => b.price - a.price);
    if (sort === "name") list.sort((a,b) => a.name.localeCompare(b.name));
    if (sort === "popular") list.sort((a,b) => b.popular - a.popular);

    return list;
  }

  function draw() {
    const list = getFiltered();
    grid.innerHTML = "";

    if (!list.length) {
      grid.innerHTML = `<div class="card"><h2>No results 🥲</h2><p class="muted">Try a different search or category.</p></div>`;
      return;
    }

    for (const p of list) {
      const inStock = p.stock > 0;
      const badge = inStock
        ? `<span class="badge in">In stock</span>`
        : `<span class="badge out">Out</span>`;

      const el = document.createElement("article");
      el.className = "card product-card";
      el.setAttribute("data-analytics", "shop_product_card");
      el.innerHTML = `
        <div class="top">
          <strong>${p.name}</strong>
          ${badge}
        </div>
        <div class="row between">
          <span class="muted">${p.category}</span>
          <span class="price">${formatMoney(p.price)}</span>
        </div>
        <div class="row between">
          <span class="emoji" style="width:64px;height:64px;font-size:32px;border-radius:16px">${p.emoji}</span>
          <div class="row gap">
            <a class="btn small" href="product.html?id=${encodeURIComponent(p.id)}" data-analytics="shop_view_product">View</a>
            <button class="btn small ${inStock ? "primary" : ""}" ${inStock ? "" : "disabled"}
              data-analytics="shop_add_to_cart">${inStock ? "Add 🛒" : "Sold out"}</button>
          </div>
        </div>
      `;

      // Card click opens product
      el.addEventListener("click", (e) => {
        const target = e.target;
        // If clicking the buttons/links, let them handle
        if (target.closest("a") || target.closest("button")) return;
        window.location.href = `product.html?id=${encodeURIComponent(p.id)}`;
      });

      // Add to cart button
      const btn = el.querySelector("button");
      if (btn && inStock) {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          addToCart(p.id, 1);
          btn.textContent = "Added ✅";
          setTimeout(() => (btn.textContent = "Add 🛒"), 900);
        });
      }

      grid.appendChild(el);
    }
  }

  [searchInput, categorySelect, onlyInStock, sortSelect].forEach(el => {
    if (!el) return;
    el.addEventListener("input", draw);
    el.addEventListener("change", draw);
  });

  draw();
}

function renderProductPage() {
  const nameEl = $("#pName");
  if (!nameEl) return;

  const params = new URLSearchParams(window.location.search);
  const id = params.get("id") || "p6";
  const p = PRODUCTS.find(x => x.id === id) || PRODUCTS[0];

  $("#pEmoji").textContent = p.emoji;
  nameEl.textContent = p.name;
  $("#pDesc").textContent = p.desc;
  $("#pPrice").textContent = formatMoney(p.price);

  const addBtn = $("#addToCartBtn");
  const wishBtn = $("#wishlistBtn");
  const msg = $("#productMsg");

  addBtn.disabled = p.stock <= 0;
  addBtn.textContent = p.stock > 0 ? "Add to cart 🛒" : "Sold out 😭";

  addBtn.addEventListener("click", () => {
    addToCart(p.id, 1);
    msg.textContent = "Added to cart ✅";
  });

  wishBtn.addEventListener("click", () => {
    msg.textContent = "Wishlisted 💖 (not really, but emotionally yes)";
  });

  // Review form + character counter
  const reviewText = $("#reviewText");
  const charCount = $("#charCount");
  if (reviewText && charCount) {
    const update = () => (charCount.textContent = String(reviewText.value.length));
    reviewText.addEventListener("input", update);
    update();
  }

  const reviewForm = $("#reviewForm");
  const reviewMsg = $("#reviewMsg");
  if (reviewForm && reviewMsg) {
    reviewForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const rating = reviewForm.elements.rating.value;
      const text = reviewForm.elements.review.value.trim();
      if (!rating || !text) {
        reviewMsg.textContent = "Please add a rating and a review 🙂";
        return;
      }
      reviewMsg.textContent = "Review submitted! Thanks for your emotional support ⭐";
      reviewForm.reset();
      if (reviewText && charCount) charCount.textContent = "0";
    });
  }
}

function renderCheckout() {
  const cartBox = $("#cartItems");
  const totalEl = $("#cartTotal");
  if (!cartBox || !totalEl) return;

  function draw() {
    const cart = readCart();
    cartBox.innerHTML = "";

    if (!cart.length) {
      cartBox.innerHTML = `<div class="muted">Your cart is empty 🫥</div>`;
      totalEl.textContent = "$0.00";
      return;
    }

    let total = 0;

    for (const item of cart) {
      const p = PRODUCTS.find(x => x.id === item.id);
      if (!p) continue;
      total += p.price * item.qty;

      const row = document.createElement("div");
      row.className = "cart-item";
      row.innerHTML = `
        <div class="left">
          <span class="mini">${p.emoji}</span>
          <div>
            <div><strong>${p.name}</strong></div>
            <div class="muted">Qty: ${item.qty}</div>
          </div>
        </div>
        <div class="row gap">
          <strong>${formatMoney(p.price * item.qty)}</strong>
          <button class="btn small" data-analytics="cart_remove">Remove</button>
        </div>
      `;

      row.querySelector("button").addEventListener("click", () => {
        removeFromCart(item.id);
        draw();
      });

      cartBox.appendChild(row);
    }

    totalEl.textContent = formatMoney(total);
  }

  const clearBtn = $("#clearCartBtn");
  if (clearBtn) {
    clearBtn.addEventListener("click", () => {
      writeCart([]);
      draw();
    });
  }

  const giftToggle = $("#checkoutForm")?.elements?.gift;
  const giftFields = $("#giftFields");
  if (giftToggle && giftFields) {
    const update = () => giftFields.classList.toggle("hidden", !giftToggle.checked);
    giftToggle.addEventListener("change", update);
    update();
  }

  const form = $("#checkoutForm");
  const msg = $("#checkoutMsg");
  if (form && msg) {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      const cart = readCart();
      if (!cart.length) {
        msg.textContent = "Add something to your cart first 🛒";
        return;
      }

      // simple validation
      const name = form.elements.name.value.trim();
      const email = form.elements.email.value.trim();
      const address = form.elements.address.value.trim();
      const city = form.elements.city.value.trim();
      const zip = form.elements.zip.value.trim();

      if (!name || !email.includes("@") || !address || !city || !/^\d{5}$/.test(zip)) {
        msg.textContent = "Please fill out all fields correctly (ZIP must be 5 digits) 😅";
        return;
      }

      msg.textContent = `Order placed! 🎉 (Fake). Confirmation sent to ${email}`;
      writeCart([]);
      draw();
      form.reset();
      if (giftFields) giftFields.classList.add("hidden");
    });
  }

  draw();
}

function setupContactForm() {
  const form = $("#contactForm");
  const msg = $("#contactMsg");
  if (!form || !msg) return;

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const name = form.elements.name.value.trim();
    const email = form.elements.email.value.trim();
    const message = form.elements.message.value.trim();

    if (!name || !email.includes("@") || message.length < 8) {
      msg.textContent = "Please enter a name, a valid email, and a longer message 🙂";
      return;
    }

    msg.textContent = "Message sent! (To the void.) Thanks 💌";
    form.reset();
  });
}

document.addEventListener("DOMContentLoaded", () => {
  applyThemeFromStorage();
  updateCartCount();
  setupThemeToggle();

  setupSpinDeal();
  setupNewsletterForm();
  setupVibeForm();

  renderShop();
  renderProductPage();
  renderCheckout();

  setupContactForm();
});