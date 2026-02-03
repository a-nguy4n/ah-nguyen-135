#!/usr/bin/env python3

import os
import sys
import string
import random
import urllib.parse


env = os.environ

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
file_path = f"/demo-data/session_{session_id}.txt"
data_to_write = f"message: {val[0] if val else ''}\nsession ID: {session_id}"

with open(file_path, "w") as f:
    f.write(data_to_write)

print("Cache-Control: no-cache")
print("Set-Cookie: session_id={}; Path=/hw2/cgi-bin/python/".format(session_id))
print("Content-Type: text/html\n")

data= {
    "Raw Message Body": raw_body,
    "Parsed Message Body": parsed_body,
    "Session ID": session_id
}

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

print(f" {val} </p>")
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

