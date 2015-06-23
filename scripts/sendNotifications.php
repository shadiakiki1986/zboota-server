<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/ZbootaNotifications.php';

$ddb=connectDynamoDb();
$zn=new ZbootaNotifications($ddb);
$zn->deleteNoMoreNotices($zn->getPastMinusCurrent());
$notices=$zn->getCurrentMinusPast();
if($zn->sendEmail($notices,true)) $zn->markAsSent($notices); // 2015-06-23: marking as simulated just to watch how the pml=Not available eases out. This should be reverted to false (default) later

