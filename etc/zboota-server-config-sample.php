<?php

# Copy this file to /etc/zboota-server-config.php
# and edit it with the proper parameter values

# Root directory of installation of zboota-server
define("ROOT", "/home/shadi/Development/zboota-server");

# AWS connection information
// no longer needed with composer // define("AWS_PHAR","/usr/share/php5/aws.phar");
define('AWS_KEY','abcdefghi');
define('AWS_SECRET','abcdefghi');
define('AWS_REGION','abcdefghi');

# Mailgun key and domain
define('MAILGUN_KEY','abcdefghi');
define('MAILGUN_DOMAIN','abcdefghi');
define('MAILGUN_FROM','abcdefghi');
define('MAILGUN_PUBLIC_KEY','abcdefghi');

# Zboota-server URL
define('ZBOOTA_SERVER_URL','http://abcdefghijkl');

# Maximum number of wrong passwords before lock
define('MAX_PASS_FAIL',3);

# backup location
define('BKP_ZBOOTA','/home/shadi/.zboota-server');

define("MY_CURL_TIMEOUT",10);
define('APP_HEADER_MESSAGE','/home/shadi/.zboota-server/headerMessage.txt'); // make sure that www-data has write access

# S3
define('S3_BUCKET',"zboota-server");
define('S3_FOLDER',"photos");
