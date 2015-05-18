<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/connectDynamodb.php';
require_once ROOT.'/lib/getZbootaUsers.php';

# drop all orphan entries in zboota-cars
$ddb=connectDynamoDb();
$lpns=getZbootaUsers($ddb);
$zc=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-cars'
));

echo "Orphan cars\n";
$anyOrphans=false;
foreach($zc as $d1) {
	if(!array_key_exists($d1['id']['S'],$lpns)) {
	    echo $d1['id']['S']."\n";
		$anyOrphans=$anyOrphans||true;
	}
}

if(!$anyOrphans) {
	echo "None\n";
}
