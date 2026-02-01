#include <stdio.h>
#include <stdlib.h>
#include <time.h>

int main(void){

    time_t now;
    time(&now);

    char *ip = getenv("REMOTE_ADDR");
    if (ip == NULL) {
        ip = "Unknown";
    }

    printf("Cache-Control: no-cache\n");
    printf("Content-Type: text/html\n\n");

    printf("<!DOCTYPE html>\n");
    printf("<html>\n");

    printf("<head>\n");
    printf("  <meta charset='UTF-8'>\n");
    printf("  <title>Hello From Allison & Haley! (C Version) </title>\n");
    printf("</head>\n");

    printf("<body>\n");

    printf("  <h1 style='text-align:center;'>Hello Allison & Haley (C Version)</h1>\n");
    printf("  <hr/>\n");

    printf("  <p>Thanks for stopping by!</p>\n");
    printf("  <p>This page was generated with the C programming language</p>\n");

    printf("  <p>This program was generated at: %s</p>\n", ctime(&now));
    printf("  <p>Your current IP Address is: %s</p>\n", ip);

    printf("</body>\n");
    printf("</html>\n");

    return 0;
}
