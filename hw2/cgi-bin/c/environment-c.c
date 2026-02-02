#include <stdio.h>
#include <stdlib.h>

extern char **environ;

int main(void){

    printf("Content-Type: text/html\n\n");

    printf("<!DOCTYPE html>\n");
    printf("<html>\n");
    printf("<head>\n");
    printf("<title>Environment Variables (C)</title>\n");
    printf("</head>\n");
    printf("<body>\n");

    printf("<h1 align=\"center\">Environment Variables (C)</h1>\n");
    printf("<hr>\n");

    for (char **env = environ; *env != NULL; env++) {
        printf("<b>%s</b><br />\n", *env);
    }

    printf("</body>\n");
    printf("</html>\n");

    return 0;
}
