#!/bin/bash
# Generates php config file from environment variables
# Output to be put into config.php in the zboota-server root folder
#
# export ZBOOTA_SERVER_AWS_KEY=123
# export ZBOOTA_SERVER_AWS_ACCESS=123

if [ -z "$ZBOOTA_SERVER_AWS_KEY" ]; then echo "Please set AWS_KEY env var"; exit -1; fi
if [ -z "$ZBOOTA_SERVER_AWS_ACCESS" ]; then echo "Please set ACCESS env vars 1st"; exit -1; fi
if [ -z "$ZBOOTA_SERVER_AWS_REGION" ]; then echo "Please set Region env vars 1st"; exit -1; fi
if [ -z "$MAILGUN_KEY" ]; then echo "Please set env vars. Check scripts/env2conf.sh"; exit -1; fi
if [ -z "$MAILGUN_DOMAIN" ]; then echo "Please set env vars. Check scripts/env2conf.sh"; exit -1; fi
if [ -z "$MAILGUN_FROM" ]; then echo "Please set env vars. Check scripts/env2conf.sh"; exit -1; fi
if [ -z "$MAILGUN_PUBLIC_KEY" ]; then echo "Please set env vars. Check scripts/env2conf.sh"; exit -1; fi

echo "<?php"
echo "define('ROOT', dirname(__FILE__));"
echo "require_once ROOT.'/vendor/autoload.php';"
echo "define('AWS_KEY','$ZBOOTA_SERVER_AWS_KEY');"
echo "define('AWS_SECRET','$ZBOOTA_SERVER_AWS_ACCESS');"
echo "define('AWS_REGION','$ZBOOTA_SERVER_AWS_REGION');"
echo "define('S3_BUCKET','zboota-server');"
echo "define('S3_FOLDER','photos');"
echo "define('MAX_PASS_FAIL',3);"
echo "define('MY_CURL_TIMEOUT',10);"
echo "define('MAILGUN_KEY','$MAILGUN_KEY');"
echo "define('MAILGUN_DOMAIN','$MAILGUN_DOMAIN');"
echo "define('MAILGUN_FROM','$MAILGUN_FROM');"
echo "define('MAILGUN_PUBLIC_KEY','$MAILGUN_PUBLIC_KEY');"

