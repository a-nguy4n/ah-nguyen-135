#!/usr/bin/env python3

import os
import sys
import string
import random
import urllib.parse

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DATA_DIR = os.path.abspath(os.path.join(BASE_DIR, "..", "..", "demo-data"))

env = os.environ

# Check request method
method = env.get("REQUEST_METHOD", "")

if method == "POST":
    # Grabbing data from post request
    raw_body = ""
    content_length = env.get("CONTENT_LENGTH")
    if content_length:
        raw_body = sys.stdin.read(int(content_length))
    parsed_body = urllib.parse.parse_qs(raw_body)

    # Generating session id
    length = 10
    session_id = ''.join(random.choices(string.ascii_letters + string.digits, k=length))

    # Saving data to file
    val = parsed_body.get("message", "")
    file_path = os.path.join(DATA_DIR, f"session_{session_id}.txt")
    data_to_write = f"message: {val[0] if val else ''}\nsession ID: {session_id}"

    with open(file_path, "w") as f:
        f.write(data_to_write)
    
    # Read back the file to display
    with open(file_path, "r") as f:
        message = f.read()

    print("Cache-Control: no-cache")
    print("Set-Cookie: session_id={}; Path=/hw2/cgi-bin/python/".format(session_id))
    print("Content-Type: text/html\n")

else:  # GET request - read existing session
    cookie_header = env.get("HTTP_COOKIE", "")
    session_id = ""
    for cookie in cookie_header.split(";"):
        cookie = cookie.strip()
        if cookie.startswith("session_id="):
            session_id = cookie[len("session_id="):]
            break
    file_path = os.path.join(DATA_DIR, f"session_{session_id}.txt")

    with open(file_path, "r") as f:
        message = f.read()

    print("Cache-Control: no-cache")
    print("Set-Cookie: session_id={}; Path=/hw2/cgi-bin/python/".format(session_id))
    print("Content-Type: text/html\n")

print("""<!DOCTYPE html>
<html>
<head>
  <title>Python Sessions Page 1</title>
</head>
<body>
    <h1 align="center"> Python Sessions Page 1 </h1>
    <hr>
""")
print(f"<pre>{message}</pre>")

print(f"<a href=\"/hw2/cgi-bin/python/state-demo-python-2.py\">Python Session Page 2</a>")
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
import sys
import string
import random
import urllib.parse
import http.cookies
import shelve

# Session storage path (server-writeable directory)
SESSION_DB = "/tmp/python_sessions.db"

# Generate a random session ID
def generate_session_id(length=10):
    return ''.join(random.choices(string.ascii_letters + string.digits, k=length))

# Read environment
env = os.environ
method = env.get("REQUEST_METHOD", "")

# Parse cookies
cookie = http.cookies.SimpleCookie(env.get("HTTP_COOKIE", ""))
session_id = cookie.get("session_id").value if "session_id" in cookie else generate_session_id()

# Open session store
with shelve.open(SESSION_DB, writeback=True) as sessions:
    # Ensure session exists
    if session_id not in sessions:
        sessions[session_id] = {}

    session = sessions[session_id]

    if method == "POST":
        # Read POST data
        content_length = env.get("CONTENT_LENGTH")
        raw_body = sys.stdin.read(int(content_length)) if content_length else ""
        parsed_body = urllib.parse.parse_qs(raw_body)
        message_val = parsed_body.get("message", [""])[0]

        # Save message in session
        session["message"] = message_val
        sessions[session_id] = session 

    # GET request or POST fallback
    message = session.get("message", "")

# Output headers
print("Cache-Control: no-cache")
print(f"Set-Cookie: session_id={session_id}; Path=/hw2/cgi-bin/python/")
print("Content-Type: text/html\n")

# Output HTML
print("""<!DOCTYPE html>
<html>
<head>
  <title>Python Sessions Page 1</title>
</head>
<body>
    <h1 align="center">Python Sessions Page 1</h1>
    <hr>
""")
print(f"<pre>{message}</pre>")

print(f'<a href="/hw2/cgi-bin/python/state-demo-python-2.py">Python Session Page 2</a>')
print("<br>")
print(f'<a href="/hw2/stateDemoForms/state-form-python.html">Python CGI Form</a>')
print("<br>")
print('<form action="python-destroy-session.py" method="GET">')
print('<button type="submit">Destroy Session</button>')
print('</form>')

print("""
</body>
</html>
""")
'''