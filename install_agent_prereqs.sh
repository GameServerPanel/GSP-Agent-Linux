#!/bin/bash
# OGP Agent Prerequisites Installer for Ubuntu 22.04+
# This script installs all required packages for running OGP Agent
# Usage: sudo bash install_agent_prereqs.sh

set -e

if [ "$(id -u)" -ne 0 ]; then
  echo "This script must be run as root (use sudo)"
  exit 1
fi

# Update package lists
apt-get update

# Install required packages
apt-get install -y \
  perl \
  libdbi-perl \
  libdbd-mysql-perl \
  libarchive-zip-perl \
  libfrontier-rpc-perl \
  libpath-class-perl \
  libfile-copy-recursive-perl \
  libcrypt-xxtea-perl \
  libschedule-cron-perl \
  libmime-base64-perl \
  libgetopt-long-descriptive-perl \
  libio-compress-perl \
  libcompress-raw-zlib-perl \
  libarchive-extract-perl \
  libfile-find-rule-perl \
  libfile-basename-perl \
  libfcgi-perl \
  libwww-perl \
  screen \
  sed \
  rsync

# Optional: For FTP management
apt-get install -y proftpd-basic

# Optional: For web panel integration
apt-get install -y apache2 php php-mysql

# Done

echo "All required packages for OGP Agent have been installed."
