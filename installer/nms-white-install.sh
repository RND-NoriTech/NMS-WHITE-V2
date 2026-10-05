#!/usr/bin/env bash
set -Eeuo pipefail

NMS_VERSION="0.1.0"
NMS_REPO="https://github.com/RND-NoriTech/NMS-WHITE-V2.git"
INSTALL_DIR="/opt/librenms"

echo "======================================"
echo "       NMS-WHITE Installer"
echo "       Version ${NMS_VERSION}"
echo "======================================"

if [ "$(id -u)" -ne 0 ]; then
    echo "ERROR: Run installer as root."
    exit 1
fi

if [ -r /etc/os-release ]; then
    . /etc/os-release
else
    echo "ERROR: Unable to detect operating system."
    exit 1
fi

echo "Detected OS: ${PRETTY_NAME}"

case "${ID}:${VERSION_ID}" in
    ubuntu:24.04|ubuntu:26.04|debian:13)
        ;;
    *)
        echo "ERROR: Unsupported operating system: ${ID} ${VERSION_ID}"
        exit 1
        ;;
esac

echo "[1/5] Updating packages..."
apt-get update

echo "[2/5] Installing base dependencies..."
apt-get install -y \
    curl \
    git \
    nginx \
    mariadb-server \
    redis-server \
    fping \
    snmp \
    rrdtool

echo "[3/5] Downloading NMS-WHITE..."

if [ -d "${INSTALL_DIR}/.git" ]; then
    echo "Existing installation detected."
else
    git clone --branch prod "${NMS_REPO}" "${INSTALL_DIR}"
fi

echo "[4/5] Preparing NMS-WHITE..."
chown -R librenms:librenms "${INSTALL_DIR}" 2>/dev/null || true

echo "[5/5] Installation bootstrap complete."

echo
echo "NMS-WHITE installation bootstrap completed."
echo "Further configuration will continue in subsequent installer stages."
