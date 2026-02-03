#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <sys/stat.h>
#include <unistd.h>

#define SESSION_DIR "/tmp/cse135_sessions"
#define SID_COOKIE_NAME "SID"
#define SID_LEN 32

// ---------- helpers ----------
static void ensure_session_dir() {
  struct stat st;
  if (stat(SESSION_DIR, &st) == -1) {
    mkdir(SESSION_DIR, 0700);
  }
}

static void random_sid(char sid[SID_LEN + 1]) {
  const char *hex = "0123456789abcdef";
  for (int i = 0; i < SID_LEN; i++) sid[i] = hex[rand() % 16];
  sid[SID_LEN] = '\0';
}

static int get_query_param_action(char *out, size_t outsz) {
  const char *qs = getenv("QUERY_STRING");
  if (!qs) return 0;
  // find "action="
  const char *p = strstr(qs, "action=");
  if (!p) return 0;
  p += 7;
  size_t i = 0;
  while (*p && *p != '&' && i + 1 < outsz) out[i++] = *p++;
  out[i] = '\0';
  return 1;
}

static int extract_cookie_sid(char sid[SID_LEN + 1]) {
  const char *cookie = getenv("HTTP_COOKIE");
  if (!cookie) return 0;

  // naive cookie parse: look for "SID="
  const char *p = strstr(cookie, SID_COOKIE_NAME "=");
  if (!p) return 0;
  p += strlen(SID_COOKIE_NAME) + 1;

  // copy until ; or end
  size_t i = 0;
  while (*p && *p != ';' && !isspace((unsigned char)*p) && i < SID_LEN) {
    sid[i++] = *p++;
  }
  sid[i] = '\0';

  // basic validation length
  return (i == SID_LEN);
}

static void session_file_path(const char *sid, char *out, size_t outsz) {
  snprintf(out, outsz, "%s/sess_%s.txt", SESSION_DIR, sid);
}

// minimal URL-decoding for application/x-www-form-urlencoded: + and %xx
static void url_decode(char *dst, const char *src, size_t dstsz) {
  size_t di = 0;
  for (size_t si = 0; src[si] && di + 1 < dstsz; si++) {
    if (src[si] == '+') {
      dst[di++] = ' ';
    } else if (src[si] == '%' && isxdigit((unsigned char)src[si+1]) && isxdigit((unsigned char)src[si+2])) {
      char hex[3] = { src[si+1], src[si+2], '\0' };
      dst[di++] = (char) strtol(hex, NULL, 16);
      si += 2;
    } else {
      dst[di++] = src[si];
    }
  }
  dst[di] = '\0';
}

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

  // find "message="
  const char *p = strstr(body, "message=");
  if (!p) { free(body); return 0; }
  p += 8;

  // copy until & or end
  char encoded[2048];
  size_t i = 0;
  while (*p && *p != '&' && i + 1 < sizeof(encoded)) encoded[i++] = *p++;
  encoded[i] = '\0';

  url_decode(out, encoded, outsz);

  free(body);
  return 1;
}

static void redirect_with_cookie(const char *location, const char *sid, int set_cookie) {
  printf("Status: 302 Found\r\n");
  if (set_cookie) {
    // cookie keeps the session id in the browser
    printf("Set-Cookie: %s=%s; Path=/; SameSite=Lax\r\n", SID_COOKIE_NAME, sid);
  }
  printf("Location: %s\r\n", location);
  printf("Content-Type: text/html\r\n\r\n");
  printf("<html><body>Redirecting...</body></html>");
}

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

  char action[32] = "info";
  get_query_param_action(action, sizeof(action));

  // session id from cookie or new one
  char sid[SID_LEN + 1];
  int has_sid = extract_cookie_sid(sid);
  int set_cookie = 0;
  if (!has_sid) {
    random_sid(sid);
    set_cookie = 1;
  }

  char path[512];
  session_file_path(sid, path, sizeof(path));

  // SAVE
  if (strcmp(action, "save") == 0) {
    char msg[1024] = {0};
    read_post_message(msg, sizeof(msg));

    if (msg[0]) {
      FILE *fp = fopen(path, "a");
      if (fp) {
        fprintf(fp, "%s\n", msg);
        fclose(fp);
      }
    }

    redirect_with_cookie("/hw2/cgi-bin/c/state-demo-c.cgi?action=info", sid, set_cookie);
    return 0;
  }

  // CLEAR
  if (strcmp(action, "clear") == 0) {
    unlink(path);
    redirect_with_cookie("/hw2/cgi-bin/c/state-demo-c.cgi?action=info", sid, set_cookie);
    return 0;
  }

  // INFO
  print_header_with_cookie(sid, set_cookie);

  printf("<!DOCTYPE html><html><head><meta charset='UTF-8'><title>State Demo (C) </title></head><body>");
  printf("<h1>Saved Messages (C CGI + Server-side Session)</h1>");
  printf("<p><b>Session ID:</b> %s</p>", sid);

  FILE *fp = fopen(path, "r");
  if (!fp) {
    printf("<p>No messages yet.</p>");
  } else {
    printf("<ol>");
    char line[1200];
    int any = 0;
    while (fgets(line, sizeof(line), fp)) {
      // very simple HTML escaping: replace < and > (enough for a demo)
      for (char *p = line; *p; p++) {
        if (*p == '<') *p = '(';
        if (*p == '>') *p = ')';
      }
      // strip newline
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

  printf("<hr>");
  printf("<a href='/hw2/cgi-bin/c/state-demo-c.cgi?action=clear'>Clear Messages</a><br>");
  printf("<a href='/hw2/stateDemoForms/state-form-c.html'>Back to Form</a>");
  printf("</body></html>");

  return 0;
}
