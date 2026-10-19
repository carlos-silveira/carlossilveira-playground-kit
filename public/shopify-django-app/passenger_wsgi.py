"""
Carlos Silveira Private Lab — Django / WSGI Entrypoint for `/lab/shopify-django-app/`
Runtime on Spaceship server58: /opt/alt/python312/bin/python3
"""
from __future__ import annotations

import base64
import datetime
import hashlib
import hmac
import json

SHOPIFY_WEBHOOK_SECRET = b"shpss_cs_liquid_glass_shopify_2026_secret"


def verify_shopify_hmac(raw_body: bytes, hmac_header: str) -> bool:
    digest = hmac.new(SHOPIFY_WEBHOOK_SECRET, raw_body, hashlib.sha256).digest()
    computed = base64.b64encode(digest).decode("utf-8")
    return hmac.compare_digest(computed, hmac_header)


def application(environ: dict, start_response):
    payload = {
        "ok": True,
        "app": "shopify-django-app",
        "stack": "Django 5.1 / Python 3.12",
        "shop_domain": "carlos-silveira-dev.myshopify.com",
        "timestamp": datetime.datetime.now(datetime.timezone.utc).isoformat(),
    }
    body = json.dumps(payload, indent=2).encode("utf-8")
    start_response(
        "200 OK",
        [
            ("Content-Type", "application/json; charset=utf-8"),
            ("X-Robots-Tag", "noindex, nofollow"),
            ("Content-Length", str(len(body))),
        ],
    )
    return [body]
