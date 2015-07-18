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

## Note on phpunit
I could''ve used the following in composer.json to require phpunit
```
    "require-dev": {
        "phpunit/phpunit": "*"
    }
```
but instead, I''m just requiring its global installation

# Installation
* git clone https://shadiakiki1986@github.org/shadiakiki1986/zboota-server
* cd zboota-server
* composer install
* cp config-sample.php to config.php and modify values as needed
* Append to cron:
 * note: May need to mkdir /home/ubuntu/.zboota-server for below logs

    0 6 * * 1 php /home/ubuntu/zboota-server/scripts/backupTables.php >> /home/ubuntu/.zboota-server/backup.log 2>&1
    2 6 * * 1 php /home/ubuntu/zboota-server/scripts/backupPhotos.php >> /home/ubuntu/.zboota-server/backup.log 2>&1
    4 6 * * 1 php /home/ubuntu/zboota-server/scripts/statistics/showStatistics.php email all 2>&1
    5 6 * * * php /home/ubuntu/zboota-server/scripts/syncAll.php >> /home/ubuntu/.zboota-server/syncAll.log 2>&1
    3 6 * * * php /home/ubuntu/zboota-server/scripts/statistics/uploadStatistics.php carsLastGetInPast24Hrs 2>&1
    3 * * * * php /home/ubuntu/zboota-server/scripts/statistics/uploadStatistics.php carsLastGetInPast1Hr 2>&1

# Uninstall
* crontab -e # remove lines appended in installation

```
rm config.php  # from insatllation folder
sudo rm /etc/apache2/conf-enabled/zboota-server.conf 
sudo rm /etc/apache2/conf-available/zboota-server.conf 
rm -rf ~/zboota-server
rm -rf ~/.zboota-server
```
