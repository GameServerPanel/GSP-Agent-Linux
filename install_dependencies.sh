#!/bin/bash
# OGP Agent Dependency Installation Script for Ubuntu 22.04+
# This script installs all required Perl modules for the OGP Agent

echo "Installing OGP Agent dependencies..."

# Update package list
apt-get update

# Install Perl and basic dependencies
apt-get install -y perl

# Install required Perl modules
apt-get install -y \
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
    libfcgi-perl \
    libwww-perl \
    libxml-parser-perl

# Install system utilities
apt-get install -y screen sed rsync wget curl

# Optional packages for FTP and web integration
apt-get install -y proftpd-basic apache2 php php-mysql

echo "Dependency installation completed!"
echo ""
echo "Next steps:"
echo "1. Configure Cfg/Config.pm with your settings"
echo "2. Set the agent key to match your web panel"
echo "3. Optionally configure MySQL for resource statistics"
echo "4. Run the agent with: perl ogp_agent.pl"
echo ""
echo "See CONFIGURATION.md for detailed setup instructions."