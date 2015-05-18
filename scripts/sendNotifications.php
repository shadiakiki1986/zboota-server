<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/connectDynamodb.php';
require_once ROOT.'/lib/mailSend.php';
require_once ROOT.'/lib/sendNotificationsCore.php';

// get car data from cache
// Note that this uses the "implied" emails set from "syncAll.php"
$ddb=connectDynamoDb();
$notices=sendNotificationsCore($ddb);

// send emails of remaining notices
foreach($notices as $email=>$carIds) {
	sort($carIds);
	echo date("Y-m-d H:i")." : Email {$email} about ".join(', ',$carIds)."\n";
	mailSend($email,
		"Zboota notification",
		"Violations for: ".join(', ',$carIds)."<br>\n"
		."Please check <a href='http://genesis.akikieng.com/zboota-server/client'>your app</a> for more details.<br>\n"
		."--Zboota server"		
	);

	// update/insert entry with new notification
	$ddb->putItem(array(
	    'TableName' => 'zboota-notifications',
	    'Item' => array(
		'email'   => array('S' => $email),
		'carIds'   => array('S' => json_encode($carIds,true))
	     )
	));

}
