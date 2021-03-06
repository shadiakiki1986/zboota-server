<?php

# Copy this file to config.php in the root folder of the installation
# and edit it with the proper parameter values

# Root directory of installation of zboota-server
define("ROOT", dirname(__FILE__));
require_once ROOT.'/vendor/autoload.php'; #  if this line throw an error, I probably forgot to run composer install

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

# Maximum number of wrong passwords before lock
define('MAX_PASS_FAIL',3);

# backup location
define('BKP_ZBOOTA','/home/shadi/.zboota-server'); # Important to explicitly spell out "/home/shadi" and not just use a tilde

define("MY_CURL_TIMEOUT",10);
# no longer using this in favor of file in s3 # define('APP_HEADER_MESSAGE','/home/shadi/.zboota-server/headerMessage.txt'); // make sure that www-data has write access

# S3
define('S3_BUCKET',"zboota-server");
define('S3_FOLDER',"photos");

# Check note in
# http://php.net/manual/en/function.date-default-timezone-set.php
date_default_timezone_set('UTC');

# Notification breaker
define('NOTIF_BREAKER',10);

# Notifications: run simulation
define("NOTIF_SIMULATION",false);
