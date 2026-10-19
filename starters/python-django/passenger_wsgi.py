"""
Phusion Passenger WSGI Entrypoint for Spaceship CloudLinux (`server58.shared.spaceship.host`)
Python Binary: /opt/alt/python312/bin/python3

If upgrading this starter to a full Django project, replace the import below with:
    import os
    from django.core.wsgi import get_wsgi_application
    os.environ.setdefault("DJANGO_SETTINGS_MODULE", "myproject.settings")
    application = get_wsgi_application()
"""
import os
import sys

sys.path.insert(0, os.path.dirname(__file__))

from app import application  # noqa: E402, F401
