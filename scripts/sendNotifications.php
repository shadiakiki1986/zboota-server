<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/ZbootaNotifications.php';
require_once ROOT.'/lib/connectDynamodb.php';

if(!defined("NOTIF_SIMULATION")) define("NOTIF_SIMULATION",false);
if(!defined("NOTIF_BREAKER")) define("NOTIF_BREAKER",10);

$ddb=connectDynamoDb();
$zn=new ZbootaNotifications($ddb);
$zn->deleteNoMoreNotices($zn->getPastMinusCurrent());
$notices=$zn->getCurrentMinusPast();
if($zn->sendEmail($notices,NOTIF_SIMULATION,NOTIF_BREAKER)) {
  if(!NOTIF_SIMULATION) {
    $zn->markAsSent($notices);
  }
}

