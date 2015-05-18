<?php

# To test with a different ROOT, uncomment the below
# define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/connectDynamodb.php';
require_once ROOT.'/lib/getZbootaUsers.php';
require_once ROOT.'/lib/sendNotificationsCore.php';

class miscListsTest extends PHPUnit_Framework_TestCase {

    public function testGetZbootaUsers() {
	// list all users
	$ddb=connectDynamoDb();
	$this->assertTrue(count(getZbootaUsers($ddb))>100);
    }

    public function testSendNotifications() {
	// not sure what to test here ... so here's nothing
	$ddb=connectDynamoDb();
	sendNotificationsCore($ddb);
    }

}
