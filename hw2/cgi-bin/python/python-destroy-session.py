#!/usr/bin/env python3

import os

DATA_DIR = "/var/www/ah-nguyen.site/public_html/hw2/tmp"

env = os.environ

# Retrieving session id from cookie
cookie_header = env.get("HTTP_COOKIE", "")
session_id = ""
for cookie in cookie_header.split(";"):
    cookie = cookie.strip()
    if cookie.startswith("session_id="):
        session_id = cookie[len("session_id="):]
        break

# Delete the file
file_path = os.path.join(DATA_DIR, f"session_{session_id}.txt")
# os.remove(file_path)

# Instead of os.remove(file_path), do this:
placeholder_message = f"message: You do not have a message set\nsession ID: {session_id}"
with open(file_path, "w") as f:
    f.write(placeholder_message)


print("Content-Type: text/html")
print("Cache-Control: no-cache")
print("Set-Cookie: session_id=; Path=/hw2/cgi-bin/python/; Expires=Thu, 01 Jan 1970 00:00:00 GMT\n")

print("""<!DOCTYPE html>
<html>
<head>
  <title>Python Sessions Page 1</title>
</head>
<body>
    <h1 align="center"> Python Sessions Page 1 </h1>
    <hr>
    <p> Session Destroyed. </p> 
""")
print('<a href="/hw2/cgi-bin/python/state-demo-python-1.py">Python Session Page 1</a>')
print("<br>")
print(f"<a href=\"/hw2/cgi-bin/python/state-demo-python-2.py\">Python Session Page 2</a>")
print("<br>")
print(f"<a href=\"/hw2/stateDemoForms/state-form-python.html\"> Python CGI Form </a>")
print("""
</body>
</html>
""")

