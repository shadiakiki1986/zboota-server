<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/mailValidate.php';
require_once ROOT.'/lib/mailSend.php';

echo("shadiakiki1986 \t ".(mailValidate("shadiakiki1986")?"valid email":"invalid email")."\n");
echo("shadiakiki1986@gmail.com \t ".(mailValidate("shadiakiki1986@gmail.com")?"valid email":"invalid email")."\n");
echo("emailing shadiakiki1986@gmail.com \t ".(mailSend("shadiakiki1986@gmail.com","test","body <b>bold</b> html")?"sent":"not sent")."\n");
