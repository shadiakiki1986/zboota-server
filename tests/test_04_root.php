<?php
define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once '/etc/zboota-server-config.php';
define("AWS_PHAR","/usr/share/php5/aws.phar");
require_once AWS_PHAR;
echo ROOT."\n";
require_once ROOT.'/lib/connectDynamodb.php';
echo ROOT."\n";
