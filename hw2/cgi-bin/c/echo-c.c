#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

// POST body
void read_body(char *buffer, int max) {
    char *len = getenv("CONTENT_LENGTH");
    if (!len) {
        buffer[0] = '\0';
        return;
    }

    int length = atoi(len);
    if (length > max - 1) length = max - 1;

    fread(buffer, 1, length, stdin);
    buffer[length] = '\0';
}

// Geting the username=value
void get_username(char *src, char *dest, int max) {
    char *p = strstr(src, "username=");

    if (!p) {
        dest[0] = '\0';
        return;
    }

    p += 9; 

    int i = 0;
    while (*p && *p != '&' && i < max - 1) {
        dest[i++] = *p++;
    }

    dest[i] = '\0';
}

// Getting username from JSON
void get_json_username(char *src, char *dest, int max) {
    char *p = strstr(src, "\"username\":\"");

    if (!p) {
        dest[0] = '\0';
        return;
    }
    p = strchr(p, ':');
    if (!p) {
        dest[0] = '\0';
        return;
    }
    p++;
    while (*p == ' ' || *p == '\t') {
        p++;
    }
    if (*p == '"') p++;
    int i = 0;
    while (*p && *p != '"' && i < max - 1) {
        dest[i++] = *p++;
    }
    dest[i] = '\0';
}
    

int main(void) {

    printf("Cache-Control: no-cache\n");
    printf("Content-Type: text/html\n\n");

    // Environment
    char *method   = getenv("REQUEST_METHOD");
    char *protocol = getenv("SERVER_PROTOCOL");
    char *query    = getenv("QUERY_STRING");
    char *content_type = getenv("CONTENT_TYPE");

    char *ip   = getenv("REMOTE_ADDR");
    char *host = getenv("HTTP_HOST");
    char *ua   = getenv("HTTP_USER_AGENT");

    if (!method)   method = "Unknown";
    if (!protocol)protocol = "Unknown";
    if (!query)    query = "";
    if (!content_type) content_type = "";
    if (!ip)       ip = "Unknown";
    if (!host)     host = "Unknown";
    if (!ua)       ua = "Unknown";

    // Reading body
    char body[4096];
    read_body(body, sizeof(body));

    // Extracting username
    char name[256];

    if (strcmp(method, "GET") == 0) {
        get_username(query, name, sizeof(name));
    }
    else {
        // Check if JSON or form data
        if (strstr(content_type, "application/json")) {
            get_json_username(body, name, sizeof(name));
        } else {
            get_username(body, name, sizeof(name));
        }
    }

    time_t now = time(NULL);
    char *time_str = ctime(&now);
    time_str[strlen(time_str)-1] = '\0';

    printf("<!DOCTYPE html>\n");
    printf("<html>\n<head>\n");
    printf("<title>C Echo Form</title>\n");
    printf("</head>\n<body>\n");

    printf("<h1 align=\"center\">C Echo Form</h1>\n");
    printf("<hr>\n");

    printf("<p>Name: %s</p>\n", name);
    printf("<p>Client IP: %s</p>\n", ip);
    printf("<p>Hostname: %s</p>\n", host);
    printf("<p>User-Agent: %s</p>\n", ua);
    printf("<p>Current Date and Time: %s</p>\n", time_str);

    printf("</body>\n</html>\n");

    return 0;
}
