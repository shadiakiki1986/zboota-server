<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/ZbootaNotifications.php';

class ZbootaNotificationsTest extends PHPUnit_Framework_TestCase
{
    public function testCounts() {
	$zn=new ZbootaNotifications();
	// uncomment these 2 lines to manually mark accounts with notifications as sent
	//$zn->markAsSent($zn->getCurrentMinusPast());
	//$zn->refresh();
        $gcmp=$zn->getCurrentMinusPast();
	// uncomment these 2 lines to manually purge accounts with no more notifications
	//$zn->deleteNoMoreNotices($zn->getPastMinusCurrent());
	//$zn->refresh();
        $gpmc=$zn->getPastMinusCurrent();
        $gc=$zn->current;
        $gp=$zn->past;
	// uncomment this to see simulation of emails to send
	//$zn->sendEmail($gcmp,true);
	//var_dump($gc,$gp,$gcmp,$gpmc);
	//var_dump(count($gc),count($gp),count($gcmp),count($gpmc));

	$this->assertTrue(count($gcmp)<=count($gc));
	$this->assertTrue(count($gpmc)<=count($gp));
        $this->assertTrue(count($gpmc)>=0 && count($gpmc)<=5); // normally, not  more than 5 new cars pay off violations on a particular day (if the scripts are run daily)
        $this->assertTrue(count($gcmp)>=0 && count($gcmp)<=5); // normally, not more than 5 new cars violate the law on a particular day (if the scripts are run daily)
    }

    public function testDelete() {
	$zn=new ZbootaNotifications();
	$this->assertArrayNotHasKey("test@email.com",$zn->current);

	if(in_array("test@email.com",$zn->pastFlat)) {
		$zn->deleteNoMoreNotices(array("test@email.com"));
		$zn->refresh();
	}

	$this->assertTrue(!in_array("test@email.com",$zn->pastFlat));

	$zn->markAsSent(array("test@email.com"=>array("car 1","car 2","car 3")));
	$zn->refresh();
	$this->assertTrue( in_array("test@email.com",$zn->pastFlat));
	$zn->deleteNoMoreNotices(array("test@email.com"));
	$zn->refresh();
	$this->assertTrue(!in_array("test@email.com",$zn->pastFlat));
    }

}
