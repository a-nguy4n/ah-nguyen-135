#!/bin/bash

gcc hello-html-c.c -o hello-html-c.cgi
gcc hello-json-c.c -o hello-json-c.cgi

chmod 755 *.cgi

echo "Build complete."
