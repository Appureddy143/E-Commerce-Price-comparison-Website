#!/usr/bin/env bash
set -e

# Default to 10000 if PORT not provided (Render default is 10000)
: "${PORT:=10000}"

# Replace Listen directive in ports.conf
if grep -q "^Listen " /etc/apache2/ports.conf; then
  sed -ri "s/^(Listen).*/\1 ${PORT}/" /etc/apache2/ports.conf
else
  echo "Listen ${PORT}" >> /etc/apache2/ports.conf
fi

# Update VirtualHost in default site config (000-default.conf)
SITE_CONF="/etc/apache2/sites-available/000-default.conf"
if [ -f "$SITE_CONF" ]; then
  sed -ri "s/<VirtualHost \*:.*>/<VirtualHost *:${PORT}>/" "$SITE_CONF"
fi

# Ensure Apache binds to 0.0.0.0
# (Apache by default binds to 0.0.0.0 for listening addresses; no further action required)

# Exec the original apache foreground command
exec "$@"
