<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/mapArea.php';
require_once ROOT.'/lib/syncCoreIsf.php';
require_once ROOT.'/lib/syncCorePml.php';
require_once ROOT.'/lib/syncCoreDawlatiMechanique.php';
require_once ROOT.'/lib/errorInLpns.php';

function syncCore($lpns,$timeout=MY_CURL_TIMEOUT) {

	$mapIsf=mapAreaIsf();
	$mapPml=mapAreaPml();

	$eil=errorInLpns($lpns,true);
	if(!!$eil) throw new Exception($eil);

	# core retrieval
	$data=array();
	foreach($lpns as $k=>$v) {
		//echo "{$k}\n";
		$v['isf']=syncCoreIsf($mapIsf[$v['a']],$v['n'],$timeout);
		$v['pml']=syncCorePml($mapPml[$v['a']],$v['n'],$timeout);
		if(array_key_exists('t',$v) && array_key_exists('hp',$v) && array_key_exists('y',$v)) {
			$v['dm']=syncCoreDawlatiMechanique($v['a'],$v['n'],$v['t'],$v['hp'],$v['y'],$timeout);
		} else {
			if(array_key_exists('dm',$v)) unset($v['dm']);
		}

		// add fields
		$v['id']=$k;
		$v['dataTs']=date("Y-m-d H:i:s");
		$data[$k]=$v;
	}

	return $data;
}
