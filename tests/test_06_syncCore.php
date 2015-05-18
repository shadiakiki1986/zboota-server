<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/syncCore.php';

var_dump(syncCore(array(
	array('a'=>"G",'n'=>"456265"),
	array('a'=>"B",'n'=>"123123",'t'=>"Private cars",'hp'=>"1 - 10",'y'=>"2015"),
	array('a'=>"M",'n'=>"239296",'t'=>"Private transport vehicles",'hp'=>"1 - 10",'y'=>"2010")
)));

var_dump(syncCore(array(array('a'=>"M",'n'=>"239296",'t'=>"Private cars",'hp'=>"1 - 10",'y'=>"2010"))));

# expected to fail
# syncCore(array(array('a'=>"B",'n'=>"123123",'t'=>"dummy",'hp'=>"1 - 10",'y'=>"2015")));
# syncCore(array(array('a'=>"B",'n'=>"123123",'t'=>"Private cars",'hp'=>"dummy",'y'=>"2015")));
# syncCore(array(array('a'=>"B",'n'=>"123123",'t'=>"Private cars",'hp'=>"1 - 10",'y'=>"dummy")));
