<?php

require_once '/etc/zboota-server-config.php';
include AWS_PHAR;
use Aws\DynamoDb\DynamoDbClient;

function connectDynamodb() {
return 	DynamoDbClient::factory(array(
    'key' => AWS_KEY, # check config file
    'secret'  => AWS_SECRET,
    'region'  => AWS_REGION
));
}

