from time import localtime
import json
import os

print("Cache-Control: no-cache\n")
print("Content-type: application/json\n\n")

date = localtime()
ip = os.environ.get("REMOTE_ADDR", "Unknown")

python_data = {
    "title": "Yay! Python",
    "heading": "This is our Python JSON",
    "message":"Welcome, welcome, welcome!",
    "time": {
        "hour": date.tm_hour,
        "minute": date.tm_min,
        "second": date.tm_sec
    },

    "ip_address": ip
}

print(json.dumps(python_data))