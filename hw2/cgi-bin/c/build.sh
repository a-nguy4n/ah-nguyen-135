#!/bin/bash

gcc hello-html-c.c -o hello-html-c.cgi
gcc hello-json-c.c -o hello-json-c.cgi
gcc environment-c.c -o environment-c.cgi
gcc echo-c.c -o echo-c.cgi

gcc -O2 state_demo-c.c -o state_demo-c.cgi

chmod 755 *.cgi

echo "Build complete."
