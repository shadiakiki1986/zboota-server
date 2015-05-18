# Requirements
* linux server with public IP and URL to be set in mobile app
 * check variable "ZBOOTA_SERVER_URL" in js/common.js and in etc/zboota-server-config-sample.php

# Steps
* apt-get update
* apt-get install mercurial
* hg clone https://shadiakiki1986@bitbucket.org/shadiakiki1986/zboota-server $INSTALL_DIR # will require password
* Run install.sh

# Uninstall
* crontab -e # remove dropStale and syncAll
```
sudo rm /etc/zboota-server-config.php 
sudo rm /etc/apache2/conf-enabled/zboota-server.conf 
sudo rm /etc/apache2/conf-available/zboota-server.conf 
rm -rf ~/zboota-server
rm -rf ~/.zboota-server
```
