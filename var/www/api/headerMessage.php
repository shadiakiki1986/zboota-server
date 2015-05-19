<?php
header("Access-Control-Allow-Origin: *");

require_once dirname(__FILE__).'/../../../config.php';
if(!file_exists(APP_HEADER_MESSAGE)) file_put_contents(APP_HEADER_MESSAGE,"");
$msg = file_get_contents(APP_HEADER_MESSAGE);
if(!$msg) $msg="";
echo json_encode(array("message"=>rtrim($msg)));

