#!/bin/bash
# Check document INSTALL

INSTALL_DIR=.
CONFIG=$INSTALL_DIR/config.php

apt-get install apache2 php5 php5-cli php5-curl apache2-utils

cp $INSTALL_DIR/config-sample.php $CONFIG
sed -i "s/abcdefghijklmnopqrstuvwxyz/`openssl rand -base32 18`/g" $CONFIG
vim $CONFIG # edit parameters

cp $INSTALL_DIR/etc/apache2/conf-available/zboota-server-sample.conf /etc/apache2/conf-available/zboota-server.conf # Modify file if needed
ln -s /etc/apache2/conf-available/zboota-server.conf /etc/apache2/conf-enabled/zboota-server.conf
service apache2 restart

# configure client app served over web
vim $INSTALL_DIR/var/www/client/js/common.js

# AWS php SDK
wget "https://github.com/aws/aws-sdk-php/releases/download/2.7.9/aws.phar" -O /usr/share/php5/aws.phar # not sure why curl downloads only the first 300 bytes and stops

# append to cronfile
mkdir /home/ubuntu/.zboota-server
tcf=`tempfile`
crontab -u $SUDO_USER -l > $tcf
echo '' >> $tcf
echo '# Automatically appended from zboota-server/install.sh' >> $tcf
echo '5 6 * * * php /home/ubuntu/zboota-server/scripts/syncAll.php > /home/ubuntu/.zboota-server/syncAll.log 2>&1' >> $tcf
echo '0 6 * * 1 php /home/ubuntu/zboota-server/scripts/backupTables.php >> /home/ubuntu/.zboota-server/backupTables.log 2>&1' >> $tcf
crontab $tcf -u $SUDO_USER

# Test
wget --spider `grep ZBOOTA_SERVER_URL $CONFIG | sed "s/^.*'\(http:\/\/[a-zA-Z\.]*\)'.*$/\1/g"`
wget --spider `grep ZBOOTA_SERVER_URL $INSTALL_DIR/var/www/client/js/common.js | sed "s/^ZBOOTA_SERVER_URL=\"\(http:\/\/[a-zA-Z\.\/-]*\)\"$/\1/g"`
