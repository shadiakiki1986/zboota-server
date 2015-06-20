<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/connectDynamodb.php';
require_once ROOT.'/lib/mailSend.php';
require_once ROOT.'/lib/ZbootaNotifications.php';

// get car data from cache
// Note that this uses the "implied" emails set from "syncAll.php"
$ddb=connectDynamoDb();
$zn=new ZbootaNotifications($ddb);
$zn->deleteNoMoreNotices($zn->getPastMinusCurrent());
$notices=$zn->getCurrentMinusPast();
if($zn->sendEmail($notices)) $zn->markAsSent($notices);

