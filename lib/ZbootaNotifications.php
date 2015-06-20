<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/getZbootaUsers.php';
require_once ROOT.'/lib/connectDynamodb.php';

class ZbootaNotifications {

	var $ddb,$current,$past,$pastFlat;

	function __construct($client=null) {
	// client: returned from connectDynamoDb()
		if(!is_null($client)) $this->ddb=$client; else $this->ddb=connectDynamoDb();
		$this->refresh();
	}

	function refresh() {
		$this->past=$this->getPast();
		$this->pastFlat=array_map(
			function($x) { return $x["email"]["S"]; },
			iterator_to_array($this->past)
		);
		$this->current=$this->getCurrent();
	}

	function getPast() {
		return $this->ddb->getIterator('Scan',array(
		    'TableName' => 'zboota-notifications'
		));
	}

	function getCurrent() {
		$ddb=$this->ddb;

		// get car data from cache
		// Note that this uses the "implied" emails set from "syncAll.php"
		$sc1=$ddb->getIterator('Scan',array(
		    'TableName' => 'zboota-cars'
		));
		// flatten format from AWS
		$sc=array();
		foreach($sc1 as $k=>$v) {
			$sc[$k]=array();
			foreach($v as $k2=>$v2) $sc[$k][$k2]=$v2['S'];
		}

		// append implied emails
		$lpns=getZbootaUsers($ddb);
		foreach($sc as $k=>$v) {
			if(array_key_exists($v['id'],$lpns)) {
				$sc[$k]['emails']=$lpns[$v['id']]['emails'];
			}
		}

		// check for violations and prepare to email users
		$notices=array();
		foreach($sc as $d1) {
			if(array_key_exists('emails',$d1) && (
				($d1['isf']!="None"&&$d1['isf']!="Not available") ||
				($d1['pml']!='None'&&$d1['pml']!='Not available')
			) ) {
				$emails=json_decode($d1['emails']);
				foreach($emails as $d2) {
					if(!isset($notices[$d2])) $notices[$d2]=array();
					array_push($notices[$d2],$d1['id']);
				}
			}
		}

		return $notices;
	}

	function getCurrentMinusPast() {
		$notices=$this->current;

		// drop entries in $notices that are also in zboota-notifications with the same set of car ids to notify
		// i.e. already notified about tickets
		// also, update entries in $notices whose car ids to notify about have changed
		$zn=$this->past;
		foreach($zn as $n1) {
			if(array_key_exists($n1['email']['S'],$notices)) {
				sort($notices[$n1['email']['S']]);
				if($n1['carIds']['S']==json_encode($notices[$n1['email']['S']],true)) {
					unset($notices[$n1['email']['S']]);
				}
			}
		}

		return $notices;
	} // end function get

	function getPastMinusCurrent() {
		$notices=$this->current;

		// i.e. if a user closes all outstanding tickets
		$zn=$this->past;
		$pmc=array();
		foreach($zn as $n1) {
			if(!array_key_exists($n1['email']['S'],$notices)) {
			    array_push($pmc,$n1['email']['S']);
			}
		}

		return $pmc;
	}

	function deleteNoMoreNotices($pmc) {
	// $pmc: $this->getPastMinusCurrent();
		if(!is_array($pmc)) throw new Exception("Please pass array");

		// drop entries
		foreach($pmc as $n1) {
		    $this->ddb->deleteItem(array(
			'TableName' => 'zboota-notifications',
			'Key' => array(
			    'email'   => array('S' => $n1)
			)
		    ));
		}
	}

	function sendEmail($notices,$simulate=false) {
		if(count($notices)==0) {
			echo "No email notifications to send\n";
		} else if(count($notices)>5) {
			// check if we have an obscene number of emails to send, then maybe something went wrong
			if(!$simulate) mailSend("shadiakiki1986@gmail.com",
				"Zboota notification alert",
				"There seems to be an obscene amount of email notifications to send (".count($notices)." emails).<br>\n".
				"Not sending out of suspiscion that there may be something wrong on the server.<br>\n"
				."--Zboota server"
			);
			echo date("Y-m-d H:i")." : Obscene number of emails to be sent (".count($notices)." emails). Skipped intentionally.\n";
			return false;
		} else {
			// send emails of remaining notices
			foreach($notices as $email=>$carIds) {
				sort($carIds);
				if(!$simulate) mailSend($email,
					"Zboota notification",
					"Violations for: ".join(', ',$carIds)."<br>\n"
					."Please check <a href='http://genesis.akikieng.com/zboota-server/client'>your app</a> for more details.<br>\n"
					."--Zboota server"
				);
				echo date("Y-m-d H:i")." : Email {$email} about ".join(', ',$carIds)."\n";
			}
			return true;
		}
	}

	function markAsSent($notices) {
		// send emails of remaining notices
		foreach($notices as $email=>$carIds) {
			sort($carIds);
			// update/insert entry with new notification
			$this->ddb->putItem(array(
			    'TableName' => 'zboota-notifications',
			    'Item' => array(
				'email'   => array('S' => $email),
				'carIds'   => array('S' => json_encode($carIds,true))
			     )
			));

		}
	}


}
