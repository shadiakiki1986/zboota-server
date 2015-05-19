<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../../config.php';
require_once ROOT.'/lib/connectDynamodb.php';

// get user data
$ddb=connectDynamoDb();
$iterator=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-cars',
    'ProjectionExpression' => 'id,l,emails',
    'FilterExpression' => 'attribute_exists(hp)'

));

echo sprintf("List of cars with mechanique info (%s)\n",iterator_count($iterator));
foreach ($iterator as $item) {
    echo sprintf("%s, %s, %s\n", $item['id']['S'], array_key_exists('l',$item)?$item['l']['S']:"", array_key_exists('emails',$item)?$item['emails']['S']:"");

}

