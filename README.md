# OGP Agent Linux Installation Requirements

## Supported OS
- Ubuntu 22.04 or higher (required)

## Prerequisites
Before installing or running the OGP Agent, you must install the following packages:

- perl
- libdbi-perl
- libdbd-mysql-perl
- libarchive-zip-perl
- libfrontier-rpc-perl
- libpath-class-perl
- libfile-copy-recursive-perl
- libcrypt-xxtea-perl
- libschedule-cron-perl
- libmime-base64-perl
- libgetopt-long-descriptive-perl
- libio-compress-perl
- libcompress-raw-zlib-perl
- libarchive-extract-perl
- libfile-find-rule-perl
- libfile-basename-perl
- libfcgi-perl
- libwww-perl
- screen
- sed
- rsync

Optional (for FTP management):
- proftpd-basic

Optional (for web panel integration):
- apache2
- php
- php-mysql

## Quick Install
Run the following script as root to install all required packages:

```bash
sudo bash install_agent_prereqs.sh
```

This will install all dependencies for OGP Agent on Ubuntu 22.04 or higher.
