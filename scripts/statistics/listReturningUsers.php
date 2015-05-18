<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/connectDynamodb.php';

// get user data
$ddb=connectDynamoDb();
$iterator=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-users',
    'ProjectionExpression' => 'email,registrationDate,lastloginDate,lpns',
    'ExpressionAttributeValues' =>  array ( ':val2' => array('S' => '-')),
    'FilterExpression' => 'not(lastloginDate = :val2)'

));

$i2=iterator_to_array($iterator);
array_walk($i2,function(&$x) {
	$d1=date_create($x['registrationDate']['S']);
	$d2=date_create($x['lastloginDate']['S']);
	if(!$d1||!$d2) {
		throw new Exception("Failed ".$x['registrationDate']['S']." or ".$x['lastloginDate']['S']);
		//$x['dif']=0;
	} else {
		$x['dif']=(int) date_diff($d1,$d2)->format('%R%a');
	}
});
$i2=array_filter($i2,function($x) { return $x['dif']>0; });

echo sprintf("List of returning users (%s)\n",count($i2));
foreach ($i2 as $item) {
    echo sprintf("%s, %s, %3u, %3u, %s\n", $item['registrationDate']['S'], $item['lastloginDate']['S'], $item['dif'], count(json_decode($item['lpns']['S'],true)), $item['email']['S']);

}

