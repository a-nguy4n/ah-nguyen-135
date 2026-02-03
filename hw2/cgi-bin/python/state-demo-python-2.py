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
file_path = os.path.join(DATA_DIR, f"session_{session_id}.txt")
message = ""
if os.path.exists(file_path):
    with open(file_path, "r") as f:
        message = f.read()
else:
    message = "You did not leave a message."

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

print('<a href="/hw2/cgi-bin/python/state-demo-python-1.py">Python Session Page 1</a>')
print("<br>")
print(f"<a href=\"/hw2/stateDemoForms/state-form-python.html\"> Python CGI Form </a>")
print("<br>")
print('<form action="python-destroy-session.py" method="GET">')
print('<button type="submit">Destroy Session</button>')
print('</form>')

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

# Default message
message = ""

if session_id:
    with shelve.open(SESSION_DB) as sessions:
        session = sessions.get(session_id, {})
        message = session.get("message", "")

# Output headers
print("Cache-Control: no-cache")
print("Content-Type: text/html\n")

# Output HTML
print("""<!DOCTYPE html>
<html>
<head>
  <title>Python Sessions Page 2</title>
</head>
<body>
    <h1 align="center">Python Sessions Page 2</h1>
    <hr>
""")

print(f"<pre>{message}</pre>")

print('<a href="/hw2/cgi-bin/python/state-demo-python-1.py">Python Session Page 1</a>')
print('<a href="/hw2/stateDemoForms/state-form-python.html">Python CGI Form</a>')
print("<br>")
print('<form action="python-destroy-session.py" method="GET">')
print('<button type="submit">Destroy Session</button>')
print('</form>')

print("""
</body>
</html>
""")
'''