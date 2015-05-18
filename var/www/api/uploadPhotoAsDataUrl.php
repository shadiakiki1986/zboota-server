<?php
// The below CORS solution was forged as a combination of:
// https://github.com/angular/angular.js/issues/4198
// http://stackoverflow.com/a/28760672
// http://stackoverflow.com/a/29075010
//
// Compare this to getEmail.php in yolo-bear-server

header("Access-Control-Allow-Origin: *");

if(strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
	header("Access-Control-Allow-Methods: *");
	header('Access-Control-Allow-Headers: *');
	header('Access-Control-Request-Method: *');

} else if(strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
	require_once '/etc/zboota-server-config.php';
	require_once ROOT.'/lib/uploadPhotoCore.php';


		// Normally, the following would have been:
		//    if(!isset($_POST['image_file'])){
		//	    die('Image file is Missing!'); // output error when above checks fail.
		//    }
		// $xx=$_POST['image_file'];
		// but due to some angular-php post shit
		// http://stackoverflow.com/a/15485690
		// it has to be as such
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata,true);
		    if(!isset($request['image_file'])){
			    die('Image file is Missing!'); // output error when above checks fail.
		    }
		$xx = $request['image_file'];
		//$xx="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAADCAIAAAA7ljmRAAAAGElEQVQIW2P4DwcMDAxAfBvMAhEQMYgcACEHG8ELxtbPAAAAAElFTkSuQmCC";

	$tfn=tempnam(sys_get_temp_dir(), 'FOO');

	$zz=false;
	$yy=strtok($xx,":;,");
	while ($yy !== false) {$zz=$yy; $yy=strtok(":;,");}
	if(!$zz) { die("Invalid data url passed"); } // zz is supposed to be the base64 content of the string, i.e. what is after "data:image/png;base64," in the commented out example above

	file_put_contents($tfn, base64_decode($zz));
	echo uploadPhotoCore($tfn);

}
