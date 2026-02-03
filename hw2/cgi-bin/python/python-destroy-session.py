#!/usr/bin/env python3

import os

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DATA_DIR = os.path.abspath(os.path.join(BASE_DIR, "..", "..", "demo-data"))

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
os.remove(file_path)

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
print('<p><a href="/hw2/cgi-bin/python/state-demo-python-1.py">Python Session Page 1</a></p>')
print(f"<a href=\"/hw2/cgi-bin/python/state-demo-python-2.py\">Python Session Page 2</a>")
print("<br>")
print(f"<a href=\"/hw2/stateDemoForms/state-form-python.html\"> Python CGI Form </a>")
print("""
</body>
</html>
""")


'''
#!/usr/bin/env python3

import os
import http.cookies
import shelve

# Path to server-side session store
SESSION_DB = "/tmp/python_sessions.db"

# Read cookies
env = os.environ
cookie = http.cookies.SimpleCookie(env.get("HTTP_COOKIE", ""))
session_id = cookie.get("session_id").value if "session_id" in cookie else None

# Delete session from shelve
if session_id:
    with shelve.open(SESSION_DB) as sessions:
        if session_id in sessions:
            del sessions[session_id]

# Output headers
print("Content-Type: text/html")
print("Cache-Control: no-cache")
print("Set-Cookie: session_id=; Path=/hw2/cgi-bin/python/; Expires=Thu, 01 Jan 1970 00:00:00 GMT\n")

# Output HTML
print("""<!DOCTYPE html>
<html>
<head>
  <title>Python Sessions Destroyed</title>
</head>
<body>
    <h1 align="center">Python Sessions</h1>
    <hr>
    <p>Session Destroyed.</p>
""")

print('<a href="/hw2/cgi-bin/python/state-demo-python-1.py">Python Session Page 1</a>')
print('<a href="/hw2/cgi-bin/python/state-demo-python-2.py">Python Session Page 2</a>')
print('<a href="/hw2/stateDemoForms/state-form-python.html">Python CGI Form</a>')

print("""
</body>
</html>
""")
'''