#!/opt/alt/python312/bin/python3
"""
Zero-Config CGI Bridge for CloudLinux Python 3.12 on LiteSpeed (`server58.shared.spaceship.host`).
Executes the PEP 3333 WSGI `application` from `passenger_wsgi.py` immediately without requiring manual cPanel Passenger registration.
"""
import os
import sys
from wsgiref.handlers import CGIHandler

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from passenger_wsgi import application  # noqa: E402

if __name__ == "__main__":
    CGIHandler().run(application)
