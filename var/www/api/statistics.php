<?php

header("Access-Control-Allow-Origin: *");

/*
 Returns number of registered users

 USAGE
	CLI	php statistics.php

	AJAX
		 $.ajax({
		    url:"http://shadi.ly/zboota-server/api/statistics.php",
		    success: function (data) {
			console.log(data);
		    },
		    error: function (jqXHR, ts, et) {
			console.log("error", ts, et);
		    }
		 });
*/

require_once dirname(__FILE__).'/../../../config.php';
require_once ROOT.'/lib/connectDynamodb.php';

$ddb=connectDynamoDb();

$clgil24=$ddb->getIterator('Scan',array(
    'TableName' => 'zboota-statistics',
    'ProjectionExpression' => 'statDate,nTotal,nNew',
    'ExpressionAttributeValues' =>  array ( ':val1' => array('S' => 'carsLastGetInPast24Hrs')),
    'FilterExpression' => 'statName = :val1'
));
$clgil24=iterator_to_array($clgil24);
// flatten into structure suitable for jqplot
$clgil24=array(
	"nTotal"=>array_map(
		function($x) {
			return array($x["statDate"]["S"],(int)$x["nTotal"]["N"]);
		},
		$clgil24
	),
	"nNew"=>array_map(
		function($x) {
			return array($x["statDate"]["S"],(int)$x["nNew"]["N"]);
		},
		$clgil24
	)
);

$stats=array(
	'users'=>count(iterator_to_array(
		$ddb->getIterator('Scan',array(
		    'TableName' => 'zboota-users'
		))
	)),
	'cars'=>count(iterator_to_array(
		$ddb->getIterator('Scan',array(
		    'TableName' => 'zboota-cars'
		))
	)),
	'notifications'=>count(iterator_to_array(
		$ddb->getIterator('Scan',array(
		    'TableName' => 'zboota-notifications'
		))
	)),
	'carsLastGetInPast24Hrs'=>$clgil24
);
echo json_encode($stats);
