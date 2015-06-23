<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/WebAvailability.php';

class WebAvailabilityTest extends PHPUnit_Framework_TestCase
{

    public function testPing() {
	$wa=new WebAvailability();
        $this->assertTrue( $wa->ping("http://www.google.com"));
        $this->assertTrue(!$wa->ping("http://randomserverthatdoesntexist.com"));
        $this->assertTrue(!$wa->ping("http://www.google.com",1/1000)); // very small timeout should fail
    }

    public function testRes() {
	$wa=new WebAvailability();
        $this->assertArrayHasKey("isf", $wa->res);
        $this->assertArrayHasKey("pml", $wa->res);
        $this->assertArrayHasKey("dawlati", $wa->res);
        $this->assertTrue(is_bool($wa->res["isf"]));
        $this->assertTrue(is_bool($wa->res["dawlati"]));
        $this->assertTrue(is_bool($wa->res["pml"]));
    }

}
