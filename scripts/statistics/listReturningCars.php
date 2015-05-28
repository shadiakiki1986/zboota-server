<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../../config.php';
require_once ROOT.'/lib/connectDynamodb.php';

// get user data
$ddb=connectDynamoDb();
$iterator=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-cars',
    'ProjectionExpression' => 'id,lastGetTs,addedTs',
    'FilterExpression' => 'not(lastGetTs = addedTs)'

));

$i2=iterator_to_array($iterator);
echo sprintf("List of returning cars (%s)\n",count($i2));
foreach ($i2 as $item) {
	if(!array_key_exists("addedTs",$item)) $item["addedTs"]=array("S"=>"-");
	if(!array_key_exists("lastGetTs",$item)) $item["lastGetTs"]=array("S"=>"-");
    echo sprintf("%s, %s, %s\n", $item['addedTs']['S'], $item['lastGetTs']['S'], $item['id']['S']);
}

