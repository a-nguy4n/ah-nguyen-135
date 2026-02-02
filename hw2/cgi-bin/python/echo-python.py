#!/usr/bin/env python3

import os
import sys
import json
import urllib.parse
import datetime

print("Cache-Control: no-cache")
print("Content-Type: text/html\n")

env = os.environ

# Checking between JS and no JS form
content_type = env.get("CONTENT_TYPE", "")

# Request 
protocol = env.get("SERVER_PROTOCOL", "Unknown")
method = env.get("REQUEST_METHOD", "Unknown")

# GET 
raw_query = env.get("QUERY_STRING", "")
parsed_query = urllib.parse.parse_qs(raw_query)

# POST, PUT, DELETE 
raw_body = ""
content_length = env.get("CONTENT_LENGTH")

if content_length:
    try:
        raw_body = sys.stdin.read(int(content_length))
    except:
        raw_body = ""

if content_type == "application/json":
    try:
        parsed_body = json.loads(raw_body)
    except:
        parsed_body = {}
else:
    parsed_body = urllib.parse.parse_qs(raw_body)

client_ip = (
    env.get("REMOTE_ADDR")
    or "Unknown"
)

hostname = env.get("HTTP_HOST", "Unknown")
user_agent_header = env.get("HTTP_USER_AGENT", "Unknown")
current_datetime = datetime.datetime.now()


data = {
    "Server Protocol": protocol,
    "HTTP Method": method,

    "Raw Query": raw_query,
    "Parsed Query": parsed_query,

    "Raw Message Body": raw_body,
    "Parsed Message Body": parsed_body,

    "Client IP": client_ip,
    "Hostname": hostname,
    "User-Agent": user_agent_header,
    "Current Date and Time": current_datetime.isoformat()
}


print("""<!DOCTYPE html>
<html>
<head>
  <title>Python Echo Form</title>
</head>
<body>
    <h1 align="center"> Python Echo Form </h1>
    <hr>
    <p> Name: 
""")

if data["HTTP Method"] == "GET":
    val = data["Parsed Query"].get("username", "")

elif data["HTTP Method"] == "POST":
    val = data["Parsed Message Body"].get("username", "")

elif data["HTTP Method"] == "PUT":
    val = data["Parsed Message Body"].get("username", "")

elif data["HTTP Method"] == "DELETE":
    val = data["Parsed Message Body"].get("username", "")

# Handle list vs string
if isinstance(val, list):
    val = val[0]

print(f" {val}</p>")

print(f""" <p> Client IP: {data['Client IP']}</p>""")
print(f""" <p> Hostname: {data['Hostname']}</p>""")
print(f""" <p> User-Agent: {data['User-Agent']}</p>""")
print(f""" <p> Current Date and Time: {data['Current Date and Time']}</p>""")
print("""
</body>
</html>
""")
