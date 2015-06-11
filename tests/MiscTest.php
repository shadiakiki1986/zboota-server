<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/syncCorePml.php';
require_once ROOT.'/lib/syncCoreIsf.php';
require_once ROOT.'/lib/syncCoreDawlatiMechanique.php';
require_once ROOT.'/lib/mapArea.php';
require_once ROOT.'/lib/mailValidate.php';
require_once ROOT.'/lib/mailSend.php';
require_once ROOT.'/lib/syncCore.php';
require_once ROOT.'/lib/getCore.php';

class MiscTest extends PHPUnit_Framework_TestCase
{

    public function test1()
    {
	$mapIsf=mapAreaIsf();
	$this->assertTrue(syncCoreIsf($mapIsf["G"],"456265")=="08/12/2012");

	$mapPml=mapAreaPml();
	$xx=syncCorePml($mapPml["G"],"456265");
	if($xx=="Not available") {
		$this->markTestIncomplete('It seems that the PML server is overloaded or unavailable');
	} else {
		$this->assertTrue($xx=="None");
	}

	$this->assertTrue(syncCorePml($mapPml["M"],"239296")=="Not available");

	$xx=syncCoreDawlatiMechanique("B","123123","Private cars","1 - 10","2015");
	if($xx=="Not available") {
		$this->markTestIncomplete('It seems that the Dawlati server is overloaded or unavailable');
	} else {
		$this->assertTrue($xx=="325,000 LL, due in April, mandatory inspection: not required");
	}

    }

    public function test2()
    {
	$this->assertTrue(!mailValidate("shadiakiki1986"));
	$this->assertTrue( mailValidate("shadiakiki1986@gmail.com"));
	$this->assertTrue(mailSend("shadiakiki1986@gmail.com","test","body <b>bold</b> html")=="sent");
    }

    public function test3()
    {
	$pass1=uniqid();
	$pass1=substr($pass1,-5,5);
	$pass2=uniqid();
	$pass2=substr($pass2,-5,5);
	$this->assertTrue(strlen($pass1)==5);
	$this->assertTrue(strlen($pass2)==5);
	$this->assertTrue($pass1!=$pass2);
    }

    public function test6()
    {
	$x1=syncCore(array(
		array('a'=>"G",'n'=>"456265"),
		array('a'=>"B",'n'=>"123123",'t'=>"Private cars",'hp'=>"1 - 10",'y'=>"2015"),
		array('a'=>"M",'n'=>"239296",'t'=>"Private transport vehicles",'hp'=>"1 - 10",'y'=>"2010")
	));
	$this->assertTrue(count($x1)==3);
	$this->assertArrayHasKey(0,$x1);
	$this->assertArrayHasKey(1,$x1);
	$this->assertArrayHasKey(2,$x1);
	$this->assertArrayHasKey("isf",$x1[0]);
	$this->assertArrayHasKey("isf",$x1[1]);
	$this->assertArrayHasKey("isf",$x1[2]);
	$this->assertArrayHasKey("pml",$x1[0]);
	$this->assertArrayHasKey("pml",$x1[1]);
	$this->assertArrayHasKey("pml",$x1[2]);
	$this->assertArrayNotHasKey("dm",$x1[0]);
	$this->assertArrayHasKey("dm",$x1[1]);
	$this->assertArrayHasKey("dm",$x1[2]);

	$x2=syncCore(array(array('a'=>"M",'n'=>"239296",'t'=>"Private cars",'hp'=>"1 - 10",'y'=>"2010")));
	if($x2[0]["dm"]=="Not available") {
		$this->markTestIncomplete('It seems that the Dawlati server is overloaded or unavailable');
	} else {
		$this->assertTrue($x2[0]["dm"]=="There are no results matching the specifications you've entered...");
	}

	# expected to fail
	try {  
		syncCore(array(array('a'=>"B",'n'=>"123123",'t'=>"dummy",'hp'=>"1 - 10",'y'=>"2015")));
	} catch (Exception $e) {
		$this->assertTrue($e->getMessage()=="Invalid car type 'dummy'. Please use one of: Private cars, Motorcycles, Mass public transport trucks, Taxis, Public buses & minibuses, Private transport vehicles, Other private vehicles: Ambulances, etc...");
	}
	try {  
		syncCore(array(array('a'=>"B",'n'=>"123123",'t'=>"Private cars",'hp'=>"dummy",'y'=>"2015")));
	} catch (Exception $e) {
		$this->assertTrue($e->getMessage()=="Invalid horse power 'dummy'. Please use one of: 1 - 10, 11-20, 21-30, 31-40, 41-50, 51 and above");
	}
	try {  
		syncCore(array(array('a'=>"B",'n'=>"123123",'t'=>"Private cars",'hp'=>"1 - 10",'y'=>"dummy")));
	} catch (Exception $e) {
		$this->assertTrue($e->getMessage()=="Invalid model year 'dummy'. Please use one of: 2015, 2014, 2013, 2012, 2011, 2010, 2009, 2008, 2007, 2006, 2005, 2004, 2003, 2002, 2001 and before");
	}
    }

    public function test8()
    {
	$ii=array(array('a'=>"B",'n'=>"138288",'t'=>"Private cars",'hp'=>"1 - 10",'y'=>"2015"));
	$x=getCore($ii,true,0.001); // pass very short timeout (1 millisecond)
	$x=$x["B/138288"];
	$this->assertTrue($x["pml"]=="Not available" && $x["isf"]=="Not available" && $x["dm"]=="Not available");

	// Test that getCore will still update with the configured MY_CURL_TIMEOUT
	// including the case where dynamodb stored Not available after above test with short timeout
	$x=getCore($ii,false); // use default timeout
	$x=$x["B/138288"];
	if($x["pml"]!="Not available" && $x["isf"]!="Not available" && $x["dm"]!="Not available") {
		$this->assertTrue(true); // do nothing
	} else {
		if($x["pml"]!="Not available" || $x["isf"]!="Not available" || $x["dm"]!="Not available") {
			$this->markTestIncomplete('It seems that one of the ISF, PML, Dawlati servers is overloaded or unavailable');
		}
	}
    }

}


