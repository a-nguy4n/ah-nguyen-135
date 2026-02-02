#!/usr/bin/python3
import os
import datetime

print("Content-Type: text/html\n\n")

print("<!DOCTYPE html>")
print("<html>")
print("<head>")
print("<title>Hello From Allison & Haley (Python Version)!</title>")
print("</head>")

print("<body>")
print("<h1 align='center'>Hello From Allison & Haley (Python Version)!</h1><hr/>")
print("<p> Welcome, Bienvenido, Willkommen </p>")
print("<p>This page was generated with the Python programming language</p>")

current_time = datetime.datetime.now()
print(f"<p>This program was generated at: {current_time}</p>")

address = os.environ.get("REMOTE_ADDR")
print(f"<p>Your current IP Address is: {address}</p>")

print("</body>")
print("</html>")