<?php
// https://github.com/rcambien/riverline-dynamodb/blob/master/tests/bootstrap.php
$vendorDir = __DIR__ . '/../vendor';
// Include the composer autoloader
$loader = require dirname(__DIR__) . '/vendor/autoload.php';
if (!@include(__DIR__ . '/../vendor/autoload.php')) {
	die("You must set up the project dependencies, run the following commands:
	wget http://getcomposer.org/composer.phar
	php composer.phar install
	");
}

