<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/Statistics.php';

class StatisticsTest extends PHPUnit_Framework_TestCase
{

    public function testAvgdif()
    {
	$ddb=new Statistics();
        $tgrc=$ddb->getReturningCars();

        $this->assertTrue(count($tgrc)>0);
	$this->assertTrue($ddb->avgdif($tgrc)!=0);

    }

}
