<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/StatisticsFromBkpFile.php';
require_once ROOT.'/lib/connectDynamodb.php';

	$st=new StatisticsFromBkpFile();
	/*$fn="/home/shadi/Development/bkp-zboota-cars-20150615060001.json";
        $tgrc=array(
		$st->getCarsLastGetBetween($fn,"2015-06-14 06:00:00","2015-06-15 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-06-13 06:00:00","2015-06-14 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-06-12 06:00:00","2015-06-13 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-06-11 06:00:00","2015-06-12 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-06-10 06:00:00","2015-06-11 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-06-09 06:00:00","2015-06-10 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-06-08 06:00:00","2015-06-09 06:00:00")["summary"]
	);
	$fn="/home/shadi/Development/bkp-zboota-cars-20150608060001.json";
        $tgrc=array(
		$st->getCarsLastGetBetween($fn,"2015-06-07 06:00:00","2015-06-08 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-06-06 06:00:00","2015-06-07 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-06-05 06:00:00","2015-06-06 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-06-04 06:00:00","2015-06-05 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-06-03 06:00:00","2015-06-04 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-06-02 06:00:00","2015-06-03 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-06-01 06:00:00","2015-06-02 06:00:00")["summary"]
	);
	$fn="/home/shadi/Development/bkp-zboota-cars-20150601060001.json";
        $tgrc=array(
		$st->getCarsLastGetBetween($fn,"2015-05-31 06:00:00","2015-06-01 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-30 06:00:00","2015-05-31 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-29 06:00:00","2015-05-30 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-28 06:00:00","2015-05-29 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-27 06:00:00","2015-05-28 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-26 06:00:00","2015-05-27 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-25 06:00:00","2015-05-26 06:00:00")["summary"]
	);
	$fn="/home/shadi/Development/bkp-zboota-cars-20150525060001.json";
        $tgrc=array(
		$st->getCarsLastGetBetween($fn,"2015-05-24 06:00:00","2015-05-25 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-23 06:00:00","2015-05-24 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-22 06:00:00","2015-05-23 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-21 06:00:00","2015-05-22 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-20 06:00:00","2015-05-21 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-19 06:00:00","2015-05-20 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-18 06:00:00","2015-05-19 06:00:00")["summary"]
	);*/
	$fn="/home/shadi/Development/bkp-zboota-cars-20150518060001.json";
        $tgrc=array(
		$st->getCarsLastGetBetween($fn,"2015-05-17 06:00:00","2015-05-18 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-16 06:00:00","2015-05-17 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-15 06:00:00","2015-05-16 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-14 06:00:00","2015-05-15 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-13 06:00:00","2015-05-14 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-12 06:00:00","2015-05-13 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-11 06:00:00","2015-05-12 06:00:00")["summary"]
	);
/*	Couldn't use because missing addedTs field

	$fn="/home/shadi/Development/bkp-zboota-cars-20150511060001.json";
        $tgrc=array(
		$st->getCarsLastGetBetween($fn,"2015-05-10 06:00:00","2015-05-11 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-09 06:00:00","2015-05-10 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-08 06:00:00","2015-05-09 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-07 06:00:00","2015-05-08 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-06 06:00:00","2015-05-07 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-05 06:00:00","2015-05-06 06:00:00")["summary"],
        	$st->getCarsLastGetBetween($fn,"2015-05-04 06:00:00","2015-05-05 06:00:00")["summary"]
	);*/

	$ddb=connectDynamoDb();
	for($i=0;$i<7;$i++) {
//		$ddb->putItem(array(
		var_dump(array(
		    'TableName' => 'zboota-statistics',
		    'Item' => array(
			'statName' => array('S' => "carsLastGetInPast24Hrs"),
			'statDate'  => array('S' => $tgrc[$i]["d2"]),
			'd1'  => array('S' => $tgrc[$i]["d1"]),
			'd2'  => array('S' => $tgrc[$i]["d2"]),
			'nTotal' => array('N' => $tgrc[$i]["total"]),
			'nNew' => array('N' => $tgrc[$i]["new"])
		    )
		));
	}

