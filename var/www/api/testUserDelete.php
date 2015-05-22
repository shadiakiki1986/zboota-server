<?php

header("Access-Control-Allow-Origin: *");

/*
  The test account is the account that I use to test zboota-app (note the app, not the server)
  It's shadiakiki1986@hotmail.com
  This api hook just drops that entry so that I can re-create it from the app, testing if that works or not
*/

require_once dirname(__FILE__).'/../../../config.php';
require_once ROOT.'/lib/ZbootaTestUser.php';

$ztu=new ZbootaTestUser();
if($ztu->exists()) $ztu->deleteTestUser();
echo "{}";
