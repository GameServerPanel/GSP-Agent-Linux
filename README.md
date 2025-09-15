# Manual Agent Linux Installation

## Prerequisites / Installing Dependent Software Packages

The OGP agent is written in PERL and depends on several software packages. The below guide provides code to install the prerequisites. If any of these software packages are missing, the agent will not work correctly.

## Ubuntu

### All Versions of Ubuntu (including 22.04):

```bash
sudo apt-get install libxml-parser-perl libpath-class-perl perl-modules screen rsync sudo e2fsprogs unzip subversion libarchive-extract-perl pure-ftpd libarchive-zip-perl libc6 libgcc1 git curl
sudo apt-get install libc6-i386 
sudo apt-get install libgcc1:i386
sudo apt-get install lib32gcc1
sudo apt-get install libhttp-daemon-perl
```

### Additional packages for modern Ubuntu systems:

```bash
sudo apt-get install libdbi-perl libdbd-mysql-perl libfrontier-rpc-perl libfile-copy-recursive-perl libcrypt-xxtea-perl libschedule-cron-perl libmime-base64-perl libgetopt-long-descriptive-perl libio-compress-perl libcompress-raw-zlib-perl libfile-find-rule-perl libfile-basename-perl libfcgi-perl libwww-perl
```

## Debian

If sudo is not installed automatically on your installation of Debian, switch to the root user using the below command:

```bash
su -
```

Install sudo now before proceeding:

```bash
apt-get install sudo
```

Add your user to the sudo group if you're not a member of it already:

```bash
usermod -aG sudo "{REPLACE_WITH_YOUR_LINUX_USERNAME}"
```

Restart the machine to apply the sudo group change:

```bash
shutdown -r now
```

Now install the prerequisites:

```bash
sudo apt-get install libxml-parser-perl libpath-class-perl perl-modules screen rsync sudo e2fsprogs unzip subversion pure-ftpd libarchive-zip-perl libc6 libgcc1 git curl
sudo apt-get install libc6-i386 lib32gcc1
sudo apt-get install libhttp-daemon-perl
```

### Debian 8, 9, and 10 Extra Packages:

```bash
sudo apt-get install libarchive-extract-perl
```

## CentOS

### CentOS 6:

```bash
sudo yum -y update
sudo yum -y install epel-release wget subversion git
sudo yum install -y perl-libwww-perl proftpd proftpd-utils perl-ExtUtils-MakeMaker glibc.i686 glibc libgcc_s.so.1 perl-IO-Compress-Bzip2 perl-Archive-Extract perl-Archive-Zip perl-Archive-Tar perl-Path-Class
```

After installation has been completed, you'll need to run the following to get proftpd to work properly:

```bash
sudo sed -i "s/^LoadModule\( \)*mod_auth_file.c/#LoadModule mod_auth_file.c/g" "/etc/proftpd.conf"
sudo service proftpd restart
```

### CentOS 7:

```bash
sudo yum -y update
sudo yum -y install epel-release wget subversion git
sudo yum install -y perl-HTTP-Daemon perl-LWP-Protocol-http10 proftpd proftpd-utils perl-ExtUtils-MakeMaker glibc.i686 glibc libgcc_s.so.1 perl-IO-Compress-Bzip2 perl-Archive-Extract perl-Archive-Zip perl-Archive-Tar perl-Path-Class
```

After installation has been completed, you'll need to run the following to get proftpd to work properly:

```bash
sudo sed -i "s/^LoadModule\( \)*mod_auth_file.c/#LoadModule mod_auth_file.c/g" "/etc/proftpd.conf"
sudo systemctl restart proftpd
```

## Quick Install (Ubuntu/Debian)

For Ubuntu 22.04+ systems, you can use the automated installation script:

```bash
sudo bash install_agent_prereqs.sh
```

This will install all dependencies for OGP Agent on Ubuntu 22.04 or higher.

## Optional Packages

### For FTP management:
- pure-ftpd (Ubuntu/Debian)
- proftpd (CentOS)

### For web panel integration:
- apache2
- php
- php-mysql
