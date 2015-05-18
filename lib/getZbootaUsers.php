<?php

require_once '/etc/zboota-server-config.php';

function getZbootaUsers($ddb) {
# pass in dynamodb connection: result of connectDynamoDb()

	// get user data
	$ud=$ddb->getIterator('Scan',array(
	    'TableName' => 'zboota-users'
	));


	// list locked emails
	$lockedEmails=array_map(function($x) { return $x['email']['S']; },
		array_filter(iterator_to_array($ud),function($x) {
			return isset($x['passFail']) && $x['passFail']['N']>MAX_PASS_FAIL;
		})
	);
	//var_dump($lockedEmails);

	// gather all users' cars
	$lpns=array();
	foreach($ud as $d1) {
		// only focus on non-locked accounts
		if(!in_array($d1['email']['S'],$lockedEmails)) {
			$d2=json_decode($d1['lpns']['S'],true);
			foreach($d2 as $d3) {
				$id="{$d3['a']}/{$d3['n']}";
				if(!isset($lpns[$id])) $lpns[$id]=$d3;

				// imply all emails attached to this car
				if(!isset($lpns[$id]['emails'])) $lpns[$id]['emails']="[]";
				$emails=json_decode($lpns[$id]['emails']);
				array_push($emails,$d1['email']['S']);
				$lpns[$id]['emails']=json_encode($emails);
			}
		}
	}

	return $lpns;
}
