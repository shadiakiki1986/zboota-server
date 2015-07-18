<?php

require_once dirname(__FILE__).'/../../config.php';
require_once ROOT.'/lib/Statistics.php';
require_once ROOT.'/lib/connectDynamodb.php';

$dynamodbAllowed=array("carsLastGetInPast1Hr","carsLastGetInPast24Hrs");

function showUsage() {
  echo "Usage: php uploadStatistics.php [".implode("|",$GLOBALS['dynamodbAllowed'])."]\n";
  exit;
}

if(isset($argc)&&$argc>1) {
  if($argc>1) $chosen=$argv[1]; else showUsage();
} else {
  showUsage();
}

if(!in_array($chosen,$dynamodbAllowed)) { showUsage(); }

$st=new Statistics();

$tgclgb=array();
switch($chosen) {
  case "carsLastGetInPast24Hrs":
    $tgclgb=$st->getCarsLastGetBetween(date("Y-m-d H:i:s",time()-1*24*60*60),date("Y-m-d H:i:s"));
    var_dump("Uploading: carsLastGetInPast24Hrs",$tgclgb["summary"]);
    break;
  case "carsLastGetInPast1Hr":
    $tgclgb=$st->getCarsLastGetBetween(date("Y-m-d H:i:s",time()-1*60*60),date("Y-m-d H:i:s"));
    var_dump("Uploading: carsLastGetInPast1Hr",$tgclgb["summary"]);
    break;
  default: showUsage(); exit;
}

$ddb=connectDynamoDb();
$ddb->putItem(array(
  'TableName' => 'zboota-statistics',
  'Item' => array(
    'statName' => array('S' => $chosen),
    'statDate'  => array('S' => $tgclgb["summary"]["d2"]),
    'd1'  => array('S' => $tgclgb["summary"]["d1"]),
    'd2'  => array('S' => $tgclgb["summary"]["d2"]),
    'nTotal' => array('N' => $tgclgb["summary"]["total"]),
    'nNew' => array('N' => $tgclgb["summary"]["new"])
  )
));
