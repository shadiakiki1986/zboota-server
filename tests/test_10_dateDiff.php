<?php

$d1=date_create("2015-01-15 17:52:17");
$d2=date_create("2015-05-15 18:54:52");
if(!$d1||!$d2) {
	throw new Exception("Failed");
} else {
	var_dump((int) date_diff($d1,$d2)->format('%R%a'));
}

