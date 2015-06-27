<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/ZbootaNotifications.php';

$ddb=connectDynamoDb();
$zn=new ZbootaNotifications($ddb);
$zn->deleteNoMoreNotices($zn->getPastMinusCurrent());
$notices=$zn->getCurrentMinusPast();
if($zn->sendEmail($notices,false,10)) $zn->markAsSent($notices);

