<?php

# To test with a different ROOT, uncomment the below
# define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/Statistics.php';

class StatisticsTest extends PHPUnit_Framework_TestCase
{

    public function testAvgdif()
    {
	$ddb=new Statistics();
        $tgrc=$ddb->getReturningCars();

        $this->assertTrue(count($tgrc)>0);
	$this->asserTrue($ddb->avgdif($tgrc)!=0);

    }

}
