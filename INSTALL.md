# Requirements
* linux server with public IP and URL to be set in mobile app
 * check variable "ZBOOTA_SERVER_URL" in js/common.js and in config-sample.php

# Prerequisite steps
```
apt-get update
apt-get install git
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit
```

# Installation
* git clone https://shadiakiki1986@github.org/shadiakiki1986/zboota-server
* cd zboota-server
* composer install
* Run install.sh ?

# Uninstall
* crontab -e # remove dropStale and syncAll

```
rm config.php  # from insatllation folder
sudo rm /etc/apache2/conf-enabled/zboota-server.conf 
sudo rm /etc/apache2/conf-available/zboota-server.conf 
rm -rf ~/zboota-server
rm -rf ~/.zboota-server
```
