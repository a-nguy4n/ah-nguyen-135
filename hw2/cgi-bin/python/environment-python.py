#!/usr/bin/python3
import os

print("Content-Type: text/html \n\n")

print("""<!DOCTYPE html>
<html>
<head>
  <title>Environment Variables (python)</title>
</head>
<body>
  <h1 align="center">Environment Variables (python)</h1>
  <hr>
""")

for variable in sorted(os.environ.keys()):
    print(f"<b>{variable}:</b> {os.environ[variable]}<br />")

print("""
</body>
</html>
""")
