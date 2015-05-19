<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/syncCorePml.php';
require_once ROOT.'/lib/syncCoreIsf.php';
require_once ROOT.'/lib/syncCoreDawlatiMechanique.php';
require_once ROOT.'/lib/mapArea.php';

$mapIsf=mapAreaIsf();
var_dump(syncCoreIsf($mapIsf["G"],"456265"));

$mapPml=mapAreaPml();
echo syncCorePml($mapPml["G"],"456265")."\n";
echo syncCorePml($mapPml["M"],"239296")."\n";

var_dump(syncCoreDawlatiMechanique("B","123123","Private cars","1 - 10","2015"));
