<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../../config.php';
require_once ROOT.'/lib/connectDynamodb.php';

if(isset($argc)&&$argc>1) {
	$bw=$argv[1];
} else {
	throw new Exception("Usage: php listBegins.php shadi");
}

// get user data
$ddb=connectDynamoDb();
$iterator=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-users',
    'ProjectionExpression' => 'email,passFail,registrationDate,lastloginDate,pass',
    'ExpressionAttributeValues' =>  array ( ':val1' => array('S' => $bw)),
    'FilterExpression' => 'begins_with(email, :val1)'
));

echo "List of emails beginning with '$bw': \n";
foreach ($iterator as $item) {
    echo $item['registrationDate']['S'].", ".$item['lastloginDate']['S'].": ".$item['email']['S'].", ".$item['pass']['S']."\n";

}

