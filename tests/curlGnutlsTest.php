<?php

# To test with a different ROOT, uncomment the below
# define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/getCore.php';

class curlGnutlsTest extends PHPUnit_Framework_TestCase
{

    public function testVersion()
    {
	# checking that I installed my own curl and gnutls for the dynamodb tests to work
	# (https://github.com/shadiakiki1986/just-want-to-pass-dynamodb-travisci)
	$cv=curl_version();
	$this->assertTrue($cv['version']=='7.42.1');
	$this->assertTrue($cv['ssl_version']=="GnuTLS/3.1.28");
    }

}
