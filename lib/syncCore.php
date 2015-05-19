<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/mapArea.php';
require_once ROOT.'/lib/syncCoreIsf.php';
require_once ROOT.'/lib/syncCorePml.php';
require_once ROOT.'/lib/syncCoreDawlatiMechanique.php';

function syncCore($lpns) {

	$mapIsf=mapAreaIsf();
	$mapPml=mapAreaPml();

	# sanity check
	foreach($lpns as $v) {
		if(!in_array($v['a'],array_keys($mapIsf)) | !in_array($v['a'],array_keys($mapPml))) {
			throw new Exception("Unsupported area code {$v['a']}");
		}
	}

	# core retrieval
	$data=array();
	foreach($lpns as $k=>$v) {
		//echo "{$k}\n";
		$v['isf']=syncCoreIsf($mapIsf[$v['a']],$v['n']);
		$v['pml']=syncCorePml($mapPml[$v['a']],$v['n']);
		if(array_key_exists('t',$v) && array_key_exists('hp',$v) && array_key_exists('y',$v)) {
			$v['dm']=syncCoreDawlatiMechanique($v['a'],$v['n'],$v['t'],$v['hp'],$v['y']);
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
