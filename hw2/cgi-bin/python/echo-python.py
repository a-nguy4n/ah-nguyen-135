#!/usr/bin/env python3

import os
import sys
import json
import urllib.parse

print("Cache-Control: no-cache")
print("Content-Type: application/json\n")

env = os.environ

# Request 
protocol = env.get("SERVER_PROTOCOL", "Unknown")
method = env.get("REQUEST_METHOD", "Unknown")

# GET 
raw_query = env.get("QUERY_STRING", "")
parsed_query = urllib.parse.parse_qs(raw_query)

# POST 
raw_body = ""
content_length = env.get("CONTENT_LENGTH")

if content_length:
    try:
        raw_body = sys.stdin.read(int(content_length))
    except:
        raw_body = ""

parsed_body = urllib.parse.parse_qs(raw_body)

client_ip = (
    env.get("HTTP_X_FORWARDED_FOR")
    or env.get("HTTP_X_REAL_IP")
    or env.get("REMOTE_ADDR")
    or "Unknown"
)

data = {
    "Server Protocol": protocol,
    "HTTP Method": method,

    "Raw Query": raw_query,
    "Parsed Query": parsed_query,

    "Raw Message Body": raw_body,
    "Parsed Message Body": parsed_body,

    "Client IP": client_ip
}

print(json.dumps(data, indent=2))