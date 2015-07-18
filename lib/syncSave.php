<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/connectDynamodb.php';

function syncSave($data,$updateLastget=false) {
# Pass in output of syncCore

  $ddb=connectDynamoDb();

  foreach($data as $k=>$v) {

    // convert to AWS format
    $awsItem=array();
    foreach($v as $k2=>$v2) $awsItem[$k2]=array('S'=>$v2);

    // prepare to augment $data with fields in the dynamodb tables
    $k="{$v['a']}/{$v['n']}";
    $ud=$ddb->getItem(array(
        'TableName' => 'zboota-cars',
        'Key' => array( 'id' => array('S' => $k))
    ));

    // manage lastGetTs field
    if($updateLastget) {
      // update to present time
      $awsItem['lastGetTs']=array('S'=>date("Y-m-d H:i:s"));
    } else {
      // keep the lastGetTs field as is
                  if(count($ud)==1 && array_key_exists("lastGetTs",$ud['Item'])) {
        $awsItem['lastGetTs']=$ud['Item']['lastGetTs'];
      } else {
        // car doesn't exist (which doesn't make sens if syncAll is the only caller of syncSave with updateLastget=false),
        // or it doesn't yet have a lasteGetTs yet (cars added prior to the implementation of this feature), 
        // so update to present time
        $awsItem['lastGetTs']=array('S'=>date("Y-m-d H:i:s"));
      }
    }

    // do the same with addedTs, the date at which a car was first added
    if(count($ud)==1 && array_key_exists("addedTs",$ud['Item'])) {
      // car already exists, keep the added date
      $awsItem['addedTs']=$ud['Item']['addedTs'];
    } else {
      // car doesn't exist, or it doesn't yet have a addedTs yet (cars added prior to the implementation of this feature), update to present time
      $awsItem['addedTs']=array('S'=>date("Y-m-d H:i:s"));
    }

    // store in zboota-cars table
    $ddb->putItem(array(
        'TableName' => 'zboota-cars',
        'Item' => $awsItem
    ));

  }

  return $data;
}
