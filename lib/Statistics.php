<?php
require_once dirname(__FILE__).'/../config.php';
use Aws\S3\S3Client;
require_once ROOT.'/lib/connectDynamodb.php';
require_once ROOT.'/lib/getZbootaUsers.php';

class Statistics {

protected $client;

function __construct() {
	$this->client=connectDynamoDb();
}

function getNoCars() {
	return $this->client->getIterator('Scan',array(
	    'TableName' => 'zboota-users',
	    'ProjectionExpression' => 'email,registrationDate,lastloginDate,lpns',
	    'ExpressionAttributeValues' =>  array ( ':val1' => array('S' => '{}'), ':val2' => array('S' => '-')),
	    'FilterExpression' => 'lpns = :val1 and not(lastloginDate = :val2)'
	));
}

function printNoCars() {
	$iterator=$this->getNoCars();
	echo "List of empty accounts\n";
	foreach ($iterator as $item) {
	    echo $item['registrationDate']['S'].", ".$item['lastloginDate']['S'].": ".$item['email']['S'].", ".$item['lpns']['S']."\n";

	}
}

function getLocked() {
	return $this->client->getIterator('Scan',array(
	    'TableName' => 'zboota-users',
	    'ProjectionExpression' => 'email,passFail,registrationDate,lastloginDate,pass',
	    'ExpressionAttributeValues' =>  array ( ':val1' => array('N' => MAX_PASS_FAIL)),
	    'FilterExpression' => 'passFail >= :val1'
	));
}

function printLocked() {
	$iterator=$this->getLocked();
	echo "List of locked accounts\n";
	foreach ($iterator as $item) {
	    echo $item['registrationDate']['S'].", ".$item['lastloginDate']['S'].": ".$item['email']['S'].", ".$item['pass']['S']."\n";
	}
}

function getMechaniqueCars() {
	return $this->client->getIterator('Scan',array(
	    'TableName' => 'zboota-cars',
	    'ProjectionExpression' => 'id,l,emails',
	    'FilterExpression' => 'attribute_exists(hp)'

	));
}

function printMechaniqueCars() {
	$iterator=$this->getMechaniqueCars();
	echo sprintf("List of cars with mechanique info (%s)\n",iterator_count($iterator));
	foreach ($iterator as $item) {
	    echo sprintf("%s, %s, %s\n", $item['id']['S'], array_key_exists('l',$item)?$item['l']['S']:"", array_key_exists('emails',$item)?$item['emails']['S']:"");

	}
}

function getMechaniqueUsers() {
	return $this->client->getIterator('Scan',array(
	    'TableName' => 'zboota-users',
	    'ProjectionExpression' => 'email,registrationDate,lastloginDate,lpns',
	    'ExpressionAttributeValues' =>  array ( ':val1' => array('S' => '"hp"'), ':val2' => array('S' => '"y"'), ':val3' => array('S' => '"t"') ),
	    'FilterExpression' => 'contains(lpns, :val1) and contains(lpns, :val2) and contains(lpns, :val3)'

	));
}

function printMechaniqueUsers() {
	$iterator=$this->getMechaniqueUsers();
	echo sprintf("List of users with mechanique info (%s)\n",iterator_count($iterator));
	foreach ($iterator as $item) {
	    echo sprintf("%s, %s, %3u, %s\n", $item['registrationDate']['S'], $item['lastloginDate']['S'], count(json_decode($item['lpns']['S'],true)), $item['email']['S']);

	}
}

function getOrphans() {
	$ddb=$this->client;
	$lpns=getZbootaUsers($ddb);
	$zc=$ddb->getIterator('Scan',array(
	    'TableName' => 'zboota-cars'
	));

	$anyOrphans=array();
	foreach($zc as $d1) {
		if(!array_key_exists($d1['id']['S'],$lpns)) {
		    array_push($anyOrphans, $d1['id']['S']);
		}
	}
	return $anyOrphans;
}

function printOrphans() {
	$orphans=$this->getOrphans();
	if(count($orphans)==0) {
		echo "No orphans\n";
	} else {
		echo "Orphan cars:\n";
		foreach($orphans as $oo) {
			echo $oo."\n";
		}
	}
}

function getPhotos() {
	return $this->client->getIterator('Scan',array(
	    'TableName' => 'zboota-users',
	    'ProjectionExpression' => 'email,registrationDate,lastloginDate,lpns',
	    'ExpressionAttributeValues' =>  array ( ':val1' => array('S' => '"photoUrl"') ),
	    'FilterExpression' => 'contains(lpns, :val1)'
	));
}

function printPhotos() {
	$iterator=$this->getPhotos();
	echo sprintf("List of users with photo info (%s)\n",iterator_count($iterator));
	foreach ($iterator as $item) {
	    echo sprintf("%s, %s, %3u, %s\n", $item['registrationDate']['S'], $item['lastloginDate']['S'], count(json_decode($item['lpns']['S'],true)), $item['email']['S']);

	}
}

function getReturningCars($ndays=1) {
	$iterator = $this->client->getIterator('Scan',array(
	    'TableName' => 'zboota-cars',
	    'ProjectionExpression' => 'id,lastGetTs,addedTs',
	    'FilterExpression' => 'not(lastGetTs = addedTs)'
	));

	$i2=iterator_to_array($iterator);

	array_walk($i2,function(&$x) {
		if(!array_key_exists("addedTs",$x)) $x["addedTs"]=array("S"=>"-");
		if(!array_key_exists("lastGetTs",$x)) $x["lastGetTs"]=array("S"=>"-");

		if(!array_key_exists("addedTs",$x) || !array_key_exists("lastGetTs",$x)) {
			$x['dif']=0;
		} else {
			$d1=date_create($x['addedTs']['S']);
			$d2=date_create($x['lastGetTs']['S']);
			if(!$d1||!$d2) {
				//throw new Exception("Failed ".$x['addedTs']['S']." or ".$x['lastGetTs']['S']);
				$x['dif']=0;
			} else {
				$x['dif']=(int) date_diff($d1,$d2)->format('%R%a');
			}
		}
	});
	$i2=array_filter($i2,function($x) use($ndays) { return $x['dif']>$ndays; });
	return $i2;
}

function printReturningCars() {
	$i2=$this->getReturningCars();
	echo sprintf("List of returning cars (%s)\n",count($i2));
	foreach ($i2 as $item) {
	    echo sprintf("%s, %s, %3u, %s\n", $item['addedTs']['S'], $item['lastGetTs']['S'], $item['dif'], $item['id']['S']);
	}
}

function getReturningUsers($ndays=1) {
	$iterator = $this->client->getIterator('Scan',array(
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
	$i2=array_filter($i2,function($x) use($ndays) { return $x['dif']>$ndays; });
	return $i2;
}

function printReturningUsers() {
	$i2=$this->getReturningUsers();

	echo sprintf("List of returning users (%s)\n",count($i2));
	foreach ($i2 as $item) {
	    echo sprintf("%s, %s, %3u, %3u, %s\n", $item['registrationDate']['S'], $item['lastloginDate']['S'], $item['dif'], count(json_decode($item['lpns']['S'],true)), $item['email']['S']);

	}
}

function avgdif($tgrc) {
  return round(array_sum(array_map( function($x) { return $x["dif"]; }, $tgrc ))/count($tgrc));
}

function printSummary() {
	$tgrc=$this->getReturningCars();
	$tgru=$this->getReturningUsers();
	$tgclgb=$this->getCarsLastGetBetween();

	echo "Summary statistics\n";
	echo "No cars \t ".iterator_count($this->getNoCars())."\n";
	echo "Locked users \t ".iterator_count($this->getLocked())."\n";
	echo "Mech-cars \t ".iterator_count($this->getMechaniqueCars())."\n";
	echo "Mech-users \t ".iterator_count($this->getMechaniqueUsers())."\n";
	echo "Orphans \t ".count($this->getOrphans())."\n";
	echo "Photos users \t ".iterator_count($this->getPhotos())."\n";
	echo sprintf("Return-cars  \t %3u \t (avg: %3u days)\n", count($tgrc), $this->avgdif($tgrc) );
	echo sprintf("Return-users \t %3u \t (avg: %3u days)\n", count($tgru), $this->avgdif($tgru) );
	echo sprintf("Cars last get in [%s,%s] \t %3u \t (of which new: %3u)\n", $tgclgb["summary"]["d1"], $tgclgb["summary"]["d2"], $tgclgb["summary"]["total"], $tgclgb["summary"]["new"] );
}

function getCarsLastGetBetween($d1=null,$d2=null) {
	if(is_null($d1)) $d1=date("Y-m-d H:i:s",strtotime("last week"));
	if(is_null($d2)) $d2=date("Y-m-d H:i:s");

	$iterator = $this->client->getIterator('Scan',array(
	    'TableName' => 'zboota-cars',
	    'ProjectionExpression' => 'id,lastGetTs,addedTs',
	    'ExpressionAttributeValues' =>  array (
		':d1' => array('S' => $d1),
		':d2' => array('S' => $d2)
	    ),
	    'FilterExpression' => 'lastGetTs >= :d1 and lastGetTs <= :d2'
	));

	$i2=iterator_to_array($iterator);

	// append isNew flag
	array_walk($i2,function(&$x) use($d1,$d2) {
		if(!array_key_exists("addedTs",$x)) $x["addedTs"]=array("S"=>"-");
		if(!array_key_exists("lastGetTs",$x)) $x["lastGetTs"]=array("S"=>"-");

		if(!array_key_exists("addedTs",$x) || !array_key_exists("lastGetTs",$x)) {
			$x['isNew']=false;
		} else {
			$d3=$x['addedTs']['S'];
			$x['isNew']=($d3>=$d1 && $d3<=$d2);
		}
	});

	// compute some statistics
	$summary=array(
		"d1"=>$d1,
		"d2"=>$d2,
		"total"=>count($i2),
		"new"=>count(array_filter($i2,function($x) { return $x["isNew"]; }))
	);

	// return altogether
	return array("result"=>$i2,"summary"=>$summary);
}

function printCarsLastGetBetween() {
	$i2=$this->getCarsLastGetBetween();
	echo sprintf("List of cars lastGetTs in [%s,%s] (%s of which %s new)\n",$d1,$d2,$i2["summary"]["total"],$i2["summary"]["new"]);
	foreach ($i2["result"] as $item) {
	    echo sprintf("%s, %s, %3u, %s\n", $item['addedTs']['S'], $item['lastGetTs']['S'], $item['isNew'], $item['id']['S']);
	}
}

}
