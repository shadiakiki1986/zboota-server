<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/mapArea.php';

function errorInLpns($lpns,$onlyArea=false) {
	$mapIsf=mapAreaIsf();
	$mapPml=mapAreaPml();
	foreach($lpns as $v) {
		// get user data
		if(!$onlyArea && !is_numeric($v['n'])) {
			return "Your car plate number '".$v['n']."' is invalid. Please correct it.";
		}
		if(strlen($v['a'])!=1 || !in_array($v['a'],array_keys($mapIsf)) || !in_array($v['a'],array_keys($mapPml))) {
			return "Your car plate letter '".$v['a']."' is invalid. Please correct it.";
		}
	}

	return false;
}
