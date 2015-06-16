<?php

class StatisticsFromBkpFile {

function getCarsLastGetBetween($fn, $d1=null, $d2=null) {
// fn: filename
// d1: date start
// d2: date end

	if(!file_exists($fn)) throw new Exception("File passed doesn't exist");
	if(is_null($d1)) $d1=date("Y-m-d H:i:s",strtotime("last week"));
	if(is_null($d2)) $d2=date("Y-m-d H:i:s");

	$data=file_get_contents($fn);
	$data=json_decode($data,true);
	$data=array_filter($data,function($x) use($d1,$d2) { return array_key_exists("lastGetTs",$x); });
	$data=array_filter($data,function($x) use($d1,$d2) { if(!array_key_exists("lastGetTs",$x)) { var_dump($x); exit; } return $x["lastGetTs"]["S"]>=$d1 && $x["lastGetTs"]["S"]<=$d2; });
	$data=array_map(function($x) { return array("id"=>$x["id"],"lastGetTs"=>$x["lastGetTs"],"addedTs"=>$x["addedTs"]); }, $data);
	$i2=$data;

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
