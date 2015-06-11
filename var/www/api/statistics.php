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

$stats=array('users'=>count(iterator_to_array(
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
	))
);
echo json_encode($stats);
