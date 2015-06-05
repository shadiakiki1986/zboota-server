<?php

require_once dirname(__FILE__).'/../../config.php';
require_once ROOT.'/lib/Statistics.php';
require_once ROOT.'/lib/mailSend.php';

$chosenAllowed=["all","summary","nocars","locked","mechCars","mechUsers","orphans","photos","retCars","retUsers"];

function showUsage() {
	echo "Usage: php showStatistics.php (console|email) (".implode("|",$GLOBALS['chosenAllowed']).")\n";
	exit;
}

if(isset($argc)&&$argc>1) {
	if($argc>1) $format=$argv[1]; else showUsage();
	if($argc>2) $chosen=$argv[2]; else showUsage();
} else {
	showUsage();
}

if(!in_array($format,["console","email"])) showUsage();
if(!in_array($chosen,$chosenAllowed)) showUsage();

if($format=="email") ob_start();

$ddb=new Statistics();

if(in_array($chosen,["all","summary"])) {
	$ddb->printSummary();
}

if(in_array($chosen,["all","nocars"])) {
	echo "-----------------\n";
	$ddb->printNoCars();
}
if(in_array($chosen,["all","locked"])) {
	echo "-----------------\n";
	$ddb->printLocked();
}
if(in_array($chosen,["all","mechCars"])) {
	echo "-----------------\n";
	$ddb->printMechaniqueCars();
}
if(in_array($chosen,["all","mechUsers"])) {
	echo "-----------------\n";
	$ddb->printMechaniqueUsers();
}
if(in_array($chosen,["all","orphans"])) {
	echo "-----------------\n";
	$ddb->printOrphans();
}
if(in_array($chosen,["all","photos"])) {
	echo "-----------------\n";
	$ddb->printPhotos();
}
if(in_array($chosen,["all","retCars"])) {
	echo "-----------------\n";
	$ddb->printReturningCars();
}
if(in_array($chosen,["all","retUsers"])) {
	echo "-----------------\n";
	$ddb->printReturningUsers();
}

if($format=="email") {
	$myStr = ob_get_contents();
	ob_end_clean();
	$myStr=str_replace("\n","<br>\n",$myStr);

	mailSend("shadiakiki1986@gmail.com", "Zboota statistics ".date("Y-m-d"), $myStr);
}
