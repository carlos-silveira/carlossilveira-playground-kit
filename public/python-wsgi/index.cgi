#!/opt/alt/python312/bin/python3
"""
CloudLinux Python 3.12 CGI-to-WSGI Bridge for https://carlossilveira.dev/lab/python-wsgi/
Executes `application` from `passenger_wsgi.py` via `/opt/alt/python312/bin/python3`.
"""
import os
import sys
from wsgiref.handlers import CGIHandler

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from passenger_wsgi import application  # noqa: E402

if __name__ == "__main__":
    CGIHandler().run(application)
