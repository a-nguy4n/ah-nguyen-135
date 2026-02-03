#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <sys/stat.h>
#include <unistd.h>

// #define SESSION_DIR "/tmp/cse135_sessions"
#define SESSION_DIR "/var/www/ah-nguyen.site/public_html/hw2/demo-data"
#define SID_COOKIE_NAME "SID"
#define SID_LEN 32

// my helper funcs

// check that the session directory exists on the server
// store per-user session data in /tmp, keyed by SID cookie.
static void ensure_session_dir(){
  struct stat st;
  if (stat(SESSION_DIR, &st) == -1) {
    mkdir(SESSION_DIR, 0700);
  }
}

// generate random 32-character hex session id
// SID is stored in a cookie in the browser
static void random_sid(char sid[SID_LEN + 1]){
  const char *hex = "0123456789abcdef";
  for (int i = 0; i < SID_LEN; i++) sid[i] = hex[rand() % 16];
  sid[SID_LEN] = '\0';
}

// Read action from query string: ?action=save | info | clear
static int get_query_param_action(char *out, size_t outsz) {
  const char *qs = getenv("QUERY_STRING");
  if (!qs) return 0;

  const char *p = strstr(qs, "action=");
  if (!p) return 0;
  p += 7;

  size_t i = 0;
  while (*p && *p != '&' && i + 1 < outsz) out[i++] = *p++;
  out[i] = '\0';
  return 1;
}

// reading the sid bucket from query string: ?sid=1 or ?sid=2
// does NOT create a second browser session.
// It just decides which "bucket" file to view.
static int get_query_param_sid() {
  const char *qs = getenv("QUERY_STRING");
  if (!qs) return 1;

  const char *p = strstr(qs, "sid=");
  if (!p) return 1;

  p += 4;
  int v = atoi(p);
  if (v != 1 && v != 2) v = 1;
  return v;
}

// get the SID cookie from HTTP_COOKIE header.
static int extract_cookie_sid(char sid[SID_LEN + 1]) {
  const char *cookie = getenv("HTTP_COOKIE");
  if (!cookie) return 0;

  const char *p = strstr(cookie, SID_COOKIE_NAME "=");
  if (!p) return 0;
  p += strlen(SID_COOKIE_NAME) + 1;

  size_t i = 0;
  while (*p && *p != ';' && !isspace((unsigned char)*p) && i < SID_LEN) {
    sid[i++] = *p++;
  }
  sid[i] = '\0';

  return (i == SID_LEN);
}

// Build a server-side file path for a given SID and bucket (1 or 2).
// Bucket 1: /tmp/cse135_sessions/sess_<SID>_1.txt
// Bucket 2: /tmp/cse135_sessions/sess_<SID>_2.txt
static void session_file_path(const char *sid, int bucket, char *out, size_t outsz) {
  snprintf(out, outsz, "%s/sess_%s_%d.txt", SESSION_DIR, sid, bucket);
}

// minimal URL-decoding for application/x-www-form-urlencoded: + and %xx
static void url_decode(char *dst, const char *src, size_t dstsz) {
  size_t di = 0;
  for (size_t si = 0; src[si] && di + 1 < dstsz; si++) {
    if (src[si] == '+') {
      dst[di++] = ' ';
    } else if (src[si] == '%' &&
               isxdigit((unsigned char)src[si+1]) &&
               isxdigit((unsigned char)src[si+2])) {
      char hex[3] = { src[si+1], src[si+2], '\0' };
      dst[di++] = (char) strtol(hex, NULL, 16);
      si += 2;
    } else {
      dst[di++] = src[si];
    }
  }
  dst[di] = '\0';
}

// Read POST body and extract message=...
static int read_post_message(char *out, size_t outsz) {
  const char *method = getenv("REQUEST_METHOD");
  if (!method || strcmp(method, "POST") != 0) return 0;

  const char *cl = getenv("CONTENT_LENGTH");
  if (!cl) return 0;

  int len = atoi(cl);
  if (len <= 0 || len > 100000) return 0;

  char *body = (char*)malloc((size_t)len + 1);
  if (!body) return 0;

  fread(body, 1, (size_t)len, stdin);
  body[len] = '\0';

  const char *p = strstr(body, "message=");
  if (!p) { free(body); return 0; }
  p += 8;

  char encoded[2048];
  size_t i = 0;
  while (*p && *p != '&' && i + 1 < sizeof(encoded)) encoded[i++] = *p++;
  encoded[i] = '\0';

  url_decode(out, encoded, outsz);

  free(body);
  return 1;
}

// Redirect to another page, optionally setting the SID cookie for new sessions.
static void redirect_with_cookie(const char *location, const char *sid, int set_cookie) {
  printf("Status: 302 Found\r\n");
  if (set_cookie) {
    printf("Set-Cookie: %s=%s; Path=/; SameSite=Lax\r\n", SID_COOKIE_NAME, sid);
  }
  printf("Location: %s\r\n", location);
  printf("Content-Type: text/html\r\n\r\n");
  printf("<html><body>Redirecting...</body></html>");
}

// Print headers for normal HTML responses, optionally setting SID cookie.
static void print_header_with_cookie(const char *sid, int set_cookie) {
  if (set_cookie) {
    printf("Set-Cookie: %s=%s; Path=/; SameSite=Lax\r\n", SID_COOKIE_NAME, sid);
  }
  printf("Content-Type: text/html\r\n\r\n");
}

// ---------- main ----------
int main(void) {
  srand((unsigned int)getpid());
  ensure_session_dir();

  // action defaults to info
  char action[32] = "info";
  get_query_param_action(action, sizeof(action));

  // which bucket are we VIEWING (sid=1 or sid=2)
  int bucket = get_query_param_sid();

  // session id from cookie or new one
  char sid[SID_LEN + 1];
  int has_sid = extract_cookie_sid(sid);
  int set_cookie = 0;

  if (!has_sid) {
    random_sid(sid);
    set_cookie = 1;
  }

  // keeping two separate "buckets" for messages per user session:
  // bucket 1 file and bucket 2 file.
  char path1[512], path2[512], view_path[512];
  session_file_path(sid, 1, path1, sizeof(path1));
  session_file_path(sid, 2, path2, sizeof(path2));
  session_file_path(sid, bucket, view_path, sizeof(view_path));

  // -------- SAVE: save the message to BOTH bucket files --------
  if (strcmp(action, "save") == 0) {
    char msg[1024] = {0};
    read_post_message(msg, sizeof(msg));

    if (msg[0]) {
      // append to session 1
      FILE *fp1 = fopen(path1, "a");
      if (fp1) { fprintf(fp1, "%s\n", msg); fclose(fp1); }

      // append to session 2
      FILE *fp2 = fopen(path2, "a");
      if (fp2) { fprintf(fp2, "%s\n", msg); fclose(fp2); }
    }

    // Redirect back to the info page, keeping sid=bucket for view
    char loc[256];
    snprintf(loc, sizeof(loc),
             "/hw2/cgi-bin/c/state-demo-c.cgi?action=info&sid=%d",
             bucket);

    redirect_with_cookie(loc, sid, set_cookie);
    return 0;
  }

  // -------- CLEAR: clear BOTH bucket files --------
  if (strcmp(action, "clear") == 0) {
    unlink(path1);
    unlink(path2);

    char loc[256];
    snprintf(loc, sizeof(loc),
             "/hw2/cgi-bin/c/state-demo-c.cgi?action=info&sid=%d",
             bucket);

    redirect_with_cookie(loc, sid, set_cookie);
    return 0;
  }

  print_header_with_cookie(sid, set_cookie);

  printf("<!DOCTYPE html><html><head><meta charset='UTF-8'><title>State Demo (C)</title></head><body>");
  printf("<h1>Saved Messages (C)</h1>");

  printf("<h2>Viewing Session %d</h2>", bucket);
  printf("<p><b>Session ID (cookie):</b> %s</p>", sid);

  printf("<p>");
  printf("<a href='/hw2/cgi-bin/c/state-demo-c.cgi?action=info&sid=1'>View Session 1</a> | ");
  printf("<a href='/hw2/cgi-bin/c/state-demo-c.cgi?action=info&sid=2'>View Session 2</a>");
  printf("</p>");

  FILE *fp = fopen(view_path, "r");
  if (!fp) {
    printf("<p>No messages yet.</p>");
  } else {
    printf("<ol>");
    char line[1200];
    int any = 0;

    while (fgets(line, sizeof(line), fp)) {
      for (char *p = line; *p; p++) {
        if (*p == '<') *p = '(';
        if (*p == '>') *p = ')';
      }

      line[strcspn(line, "\r\n")] = 0;
      if (line[0]) {
        any = 1;
        printf("<li>%s</li>", line);
      }
    }
    printf("</ol>");
    fclose(fp);
    if (!any) printf("<p>No messages yet.</p>");
  }

  // clears out both 
  printf("<hr>");
  printf("<a href='/hw2/cgi-bin/c/state-demo-c.cgi?action=clear&sid=%d'>Clear Messages (clears in Session 1 & 2)</a><br>", bucket);
  printf("<a href='/hw2/stateDemoForms/state-form-c.html'>Back to Form</a>");

  printf("</body></html>");
  return 0;
}
