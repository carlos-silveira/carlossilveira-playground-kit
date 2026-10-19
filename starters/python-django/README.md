# Python 3.12 WSGI / Django Starter (`python-django`) — `carlossilveira.dev/lab/__LAB_SLUG__/`

Dual-mode Python 3.12 starter for Spaceship Web Hosting (`server58.shared.spaceship.host`).

## Runtime Details

- **CloudLinux Python 3.12**: `/opt/alt/python312/bin/python3`
- **Instant CGI-WSGI Execution**: `index.cgi` runs `wsgiref.handlers.CGIHandler().run(application)` out of the box with zero cPanel configuration.
- **Persistent Phusion Passenger Mode**: `passenger_wsgi.py` exports `application` for persistent CloudLinux Python App pools or full Django deployments.

## Local Development

```bash
python3 app.py
# Open http://localhost:8000
```
