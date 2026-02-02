#include <stdio.h>
#include <stdlib.h>
#include <time.h>

int main(void){

    time_t now = time(NULL);
    char *date = ctime(&now);

    if (date){
        char *p = date;
        while (*p) {
            if (*p == '\n') {
                *p = '\0';
                break;
            }
            p++;
        }
    }

    char *ip = getenv("REMOTE_ADDR");
    if (!ip) ip = "Unknown";

    printf("Cache-Control: no-cache\n");
    printf("Content-Type: application/json\n\n");

    printf("{\n");
    printf("  \"title\": \"Hello, Allison & Haley! (C Version - JSON)\",\n");
    printf("  \"heading\": \"Hello from another planet! (C Version - JSON)\",\n");
    printf("  \"message\": \"This page was generated with the C programming language\",\n");
    printf("  \"time\": \"%s\",\n", date);
    printf("  \"IP\": \"%s\"\n", ip);
    printf("}\n");

    return 0;
}
