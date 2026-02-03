#!/usr/bin/env python3

import os
import sys
import string
import random
import urllib.parse


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
    file_path = f"../../../session_{session_id}.txt"
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
    file_path = f"../../../session_{session_id}.txt"
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
    <p> <b> Message: </b> 
""")

print(f" {message} </p>")
print(f"<p> Session ID: {session_id} </p>")

print(f"<a href=\"/hw2/cgi-bin/python/state-demo-python-2.py\">Python Session Page 2</a>")
print(f"<a href=\"/hw2/stateDemoForms/state-form-python.html\"> Python CGI Form </a>")

print('<form action="python-destroy-session.py" method="GET">')
print('<button type="submit">Destroy Session</button>')
print('</form>')

print("""
</body>
</html>
""")

