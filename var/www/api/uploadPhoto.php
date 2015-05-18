<?php

throw new Exception("This is not currently being used in favor of uploadPhotoAsDataUrl");

header("Access-Control-Allow-Origin: *");

require_once '/etc/zboota-server-config.php';
require_once ROOT.'/lib/uploadPhotoCore.php';

// cannot implement same solution for angular post as in uploadPhotoAsDataUrl because this uses _FILES and I don't want to spend time now on figuring out _FILE versus _POST for the solution

    // check $_FILES['ImageFile'] not empty
    if(!isset($_FILES['image_file']) || !is_uploaded_file($_FILES['image_file']['tmp_name'])){
            die('Image file is Missing!'); // output error when above checks fail.
    }
   
//get uploaded file info before we proceed
//$image_size = $fff['size']; //file size
//	$_FILES['image_file']['name'], // file name
echo uploadPhotoCore(
	$_FILES['image_file']['tmp_name'] // file temp
);

