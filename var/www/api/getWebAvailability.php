<?php

header("Access-Control-Allow-Origin: *");

/*
 Returns zboota-availability contents

 USAGE
	CLI	php getWebAvailability.php

	AJAX
		 $.ajax({
		    url:"http://shadi.ly/zboota-server/api/getWebAvailability.php",
		    success: function (data) {
			console.log(data);
		    },
		    error: function (jqXHR, ts, et) {
			console.log("error", ts, et);
		    }
		 });
*/

require_once dirname(__FILE__).'/../../../config.php';
require_once ROOT.'/lib/WebAvailability.php';

$wa=new WebAvailability();
echo json_encode($wa->res);
