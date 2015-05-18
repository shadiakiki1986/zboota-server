<?php

require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/connectDynamodb.php';
require_once ROOT.'/lib/syncCore.php';
require_once ROOT.'/lib/syncSave.php';

# retrieval from dynamo db table
function getCore($lpns,$force=false) {
	$ddb=connectDynamoDb();
	$data=array();
	foreach($lpns as $v) {
		// get user data
		$k="{$v['a']}/{$v['n']}";

		$ud=$ddb->getItem(array(
		    'TableName' => 'zboota-cars',
		    'Key' => array( 'id' => array('S' => $k))
		));

		if(count($ud['Item'])>0) {
			foreach(array('hp','y','t') as $k2) {
				$force = $force ||
					(!array_key_exists($k2,$ud['Item']) && array_key_exists($k2,$v)) ||
					// no need to force refresh in the case below since the 'dm' field will be dropped before returning the data to the user
					//(array_key_exists($k2,$ud['Item']) && !array_key_exists($k2,$v)) ||
					(array_key_exists($k2,$ud['Item']) && array_key_exists($k2,$v) && $ud['Item'][$k2]['S']!=$v[$k2]);
			}
			$force=$force||($ud['Item']['pml']['S']=="Not available");
			$force=$force||($ud['Item']['isf']['S']=="Not available");
			$force=$force||(date("Y-m-d")>date("Y-m-d",strtotime($ud['Item']['dataTs']['S'])));
			if(array_key_exists('dm',$ud['Item'])) $force=$force||($ud['Item']['dm']['S']=="Not available");
		}

//var_dump($v,$ud['Item'],$force);
		if(count($ud['Item'])==0 || $force) {
			// if not found, retrieve
			$ud=syncSave(syncCore(array($k=>$v)),true);

			// repeat retrieval
			$ud=$ddb->getItem(array(
			    'TableName' => 'zboota-cars',
			    'Key' => array( 'id' => array('S' => $k))
			));
			if(count($ud['Item'])==0) { throw new Exception("Something is wrong with syncCore"); }
		} else {
			// if found, only update the last get timestamp
			$awsItem=$ud['Item'];
                        $awsItem['lastGetTs']=array('S'=>date("Y-m-d H:i:s"));
			$ddb->putItem(array(
			    'TableName' => 'zboota-cars',
			    'Item' => $awsItem
			));
		}

		// convert $ud to regular php array
		$phpArray=array();
		foreach($ud['Item'] as $k2=>$v2) $phpArray[$k2]=$v2['S'];

		// keep only fields that can be shown to user
		$phpArray = array_intersect_key($phpArray, array_flip(array('a','n','isf','pml','dm','dataTs')));
		if(array_key_exists('dm',$phpArray) && (!array_key_exists("hp",$v) || !array_key_exists("t",$v) || !array_key_exists("y",$v))) unset($phpArray['dm']);

		// store
		$data[$k]=$phpArray;
	}

	return $data;
}
