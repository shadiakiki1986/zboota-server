<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/syncCore.php';
require_once ROOT.'/lib/syncSave.php';
require_once ROOT.'/lib/connectDynamodb.php';
require_once ROOT.'/lib/mailSend.php';
require_once ROOT.'/lib/getZbootaUsers.php';

// get user data
$ddb=connectDynamoDb();
$lpns=getZbootaUsers($ddb);

# section here for testing purposes
# $lpns=array_slice($lpns,1,1);
# var_dump($lpns);
#exit;

# save new results to zboota-cars
$sc=syncSave(syncCore($lpns));
echo date("Y-m-d H:i")." : Sync complete\n";

#
include(ROOT.'/scripts/dropUnconfirmed.php');
#include(ROOT.'/scripts/dropOrphanCars.php');
include(ROOT.'/scripts/sendNotifications.php');
