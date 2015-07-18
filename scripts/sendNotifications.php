<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/ZbootaNotifications.php';

define("SIMULATE",false);

$ddb=connectDynamoDb();
$zn=new ZbootaNotifications($ddb);
$zn->deleteNoMoreNotices($zn->getPastMinusCurrent());
$notices=$zn->getCurrentMinusPast();
if($zn->sendEmail($notices,SIMULATE,10)) {
  if(!SIMULATE) {
    $zn->markAsSent($notices);
  }
}

