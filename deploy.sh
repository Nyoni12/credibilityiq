#!/bin/bash
# CredibilityIQ — VPS deployment script
# Run once on a fresh Ubuntu 22.04 VPS as root
set -e

DOMAIN="credibilityiq.piquesquid.net"
EMAIL="godfrey12nyoni@gmail.com"
REPO="https://github.com/Nyoni12/credibilityiq.git"
APP_DIR="/opt/credibilityiq"

echo "==> Installing Docker..."
apt-get update -y
apt-get install -y ca-certificates curl gnupg
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
chmod a+r /etc/apt/keyrings/docker.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
  https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" \
  > /etc/apt/sources.list.d/docker.list
apt-get update -y
apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
systemctl enable docker
systemctl start docker

echo "==> Cloning repo..."
git clone "$REPO" "$APP_DIR"
cd "$APP_DIR"

echo ""
echo "==> EDIT .env.prod now — fill in SECRET_KEY, DB_PASSWORD, SUPERADMIN_PASSWORD"
echo "    Then press ENTER to continue."
cp .env.prod.example .env.prod 2>/dev/null || true
nano .env.prod
read -r -p "Press ENTER when .env.prod is ready..."

echo "==> Starting services (HTTP only for SSL challenge)..."
# Temporarily serve HTTP only so certbot can verify domain
docker compose -f docker-compose.prod.yml up -d nginx certbot

echo "==> Obtaining SSL certificate..."
docker compose -f docker-compose.prod.yml run --rm certbot \
  certonly --webroot --webroot-path=/var/www/certbot \
  --email "$EMAIL" --agree-tos --no-eff-email \
  -d "$DOMAIN"

echo "==> Starting all services..."
docker compose -f docker-compose.prod.yml up -d --build

echo ""
echo "✓ Deployed at https://$DOMAIN"
echo "  Admin panel: https://$DOMAIN/django-admin/"
