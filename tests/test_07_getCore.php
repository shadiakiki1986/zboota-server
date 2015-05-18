<?php

//define("ROOT", "/home/ubuntu/Development/zboota-server"); // Development ROOT
require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/getCore.php';

// Active assert and make it quiet
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 1);

$x1=getCore(array(array("a"=>"B","n"=>"138288")));
$x2=getCore(array(array('a'=>"B",'n'=>"138288",'t'=>"Private cars",'hp'=>"1 - 10",'y'=>"2015")));
$x3=getCore(array(array("a"=>"B","n"=>"138288")));

assert(array_key_exists("B/138288",$x1));
assert(array_key_exists("B/138288",$x2));
assert(array_key_exists("B/138288",$x3));

assert(!array_key_exists("dm",$x1["B/138288"]));
assert(array_key_exists("dm",$x2["B/138288"]));
assert(!array_key_exists("dm",$x3["B/138288"]));

//var_dump($x1,$x2,$x3);
echo "Test passed\n";
