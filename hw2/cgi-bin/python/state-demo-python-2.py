#!/usr/bin/env python3

import os

env = os.environ

# Retrieving session id from cookie
cookie_header = env.get("HTTP_COOKIE", "")
session_id = ""
for cookie in cookie_header.split(";"):
    cookie = cookie.strip()
    if cookie.startswith("session_id="):
        session_id = cookie[len("session_id="):]
        break
file_path = "../../demo-data/session_" + session_id + ".txt"
message = ""
with open(file_path, "r") as f:
    message = f.read()

print("Cache-Control: no-cache")
print("Content-Type: text/html\n")

print("""<!DOCTYPE html>
<html>
<head>
  <title>Python Sessions Page 2</title>
</head>
<body>
    <h1 align="center"> Python Sessions Page 2 </h1>
    <hr>
""")

print(f"<pre>{message}</pre>") 

print('<p><a href="/hw2/cgi-bin/python/state-demo-python-1.py">Python Session Page 1</a></p>')
print(f"<a href=\"/hw2/stateDemoForms/state-form-python.html\"> Python CGI Form </a>")

print('<form action="python-destroy-session.py" method="GET">')
print('<button type="submit">Destroy Session</button>')
print('</form>')

print("""
</body>
</html>
""")