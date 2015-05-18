#!/bin/bash
# Generates php config file from environment variables
# Output to be put into /etc/zboota-server-config.php
#
# export ZBOOTA_SERVER_AWS_KEY=123
# export ZBOOTA_SERVER_AWS_ACCESS=123

if [ -z "$ZBOOTA_SERVER_AWS_KEY" ]; then echo "Please set AWS_KEY env var"; exit -1; fi
if [ -z "$ZBOOTA_SERVER_AWS_ACCESS" ]; then echo "Please set ACCESS env vars 1st"; exit -1; fi

echo "<?php"
echo "define('ROOT','~/zboota-server');"
echo "define('AWS_PHAR','/usr/share/php5/aws.phar');"
echo "define('AWS_KEY','$ZBOOTA_SERVER_AWS_KEY');"
echo "define('AWS_SECRET','$ZBOOTA_SERVER_AWS_ACCESS');"
echo "define('AWS_REGION','us-west-2');"
echo "define('S3_BUCKET','zboota-server');"
echo "define('S3_FOLDER','photos');"
