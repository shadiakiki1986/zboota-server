<?php

require_once dirname(__FILE__).'/../config.php';
require_once ROOT.'/lib/ZbootaS3Client.php';

# From http://www.sanwebe.com/2012/05/ajax-image-upload-and-resize-with-jquery-and-php
# and http://stackoverflow.com/a/5976031

function uploadPhotoCore($image_temp) {
# $image_temp: Name of file of image, e.g. $_FILES['image_file']['tmp_name']

############ Configuration ##############
$thumb_square_size      = 200; //Thumbnails will be cropped to 200x200 pixels
$max_image_size         = 500; //Maximum image size (height and width)
$thumb_prefix           = "thumb_"; //Normal thumb Prefix
$jpeg_quality           = 90; //jpeg quality
##########################################

    $image_size_info    = getimagesize($image_temp); //gets image size info from valid image file
   
    if($image_size_info){
        $image_width        = $image_size_info[0]; //image width
        $image_height       = $image_size_info[1]; //image height
        $image_type         = $image_size_info['mime']; //image type
    }else{
        die("Make sure image file is valid!");
    }

    //switch statement below checks allowed image type
    //as well as creates new image from given file
    switch($image_type){
        case 'image/png':
            $image_res =  imagecreatefrompng($image_temp);
		$image_extension = "png";
		break;
        case 'image/gif':
            $image_res =  imagecreatefromgif($image_temp);
		$image_extension = "gif";
		break;
        case 'image/jpeg': case 'image/pjpeg':
            $image_res = imagecreatefromjpeg($image_temp);
		$image_extension = "jpeg";
		break;
        default:
		die("Invalid image type");
            $image_res = false;
    }

    if($image_res){
      
        //folder path to save resized images and thumbnails
        $image_save_folder  = tempnam(sys_get_temp_dir(), 'f_'); // good 
      
        //call normal_resize_image() function to proportionally resize image
        if(normal_resize_image($image_res, $image_save_folder, $image_type, $max_image_size, $image_width, $image_height, $jpeg_quality))
        {
/*            //call crop_image_square() function to create square thumbnails
            if(!crop_image_square($image_res, $thumb_save_folder, $image_type, $thumb_square_size, $image_width, $image_height, $jpeg_quality))
            {
                die('Error Creating thumbnail');
            }
  */         
		// Upload to S3 bucket
		$s3=new ZbootaS3Client();
		$s3->connect();
		$f3=$s3->put($image_save_folder,true,true,true);

	        imagedestroy($image_res); //freeup memory

		return($f3);

        } else { die("Error resizing image"); }
       
}

} // end uploadPhotoCore

#####  This function will proportionally resize image #####
function normal_resize_image($source, $destination, $image_type, $max_size, $image_width, $image_height, $quality){
   
    if($image_width <= 0 || $image_height <= 0){return false;} //return false if nothing to resize
   
    //do not resize if image is smaller than max size
    if($image_width <= $max_size && $image_height <= $max_size){
        if(save_image($source, $destination, $image_type, $quality)){
            return true;
        }
    }
   
    //Construct a proportional size of new image
    $image_scale    = min($max_size/$image_width, $max_size/$image_height);
    $new_width      = ceil($image_scale * $image_width);
    $new_height     = ceil($image_scale * $image_height);
   
    $new_canvas     = imagecreatetruecolor( $new_width, $new_height ); //Create a new true color image
   
    //Copy and resize part of an image with resampling
    if(imagecopyresampled($new_canvas, $source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height)){
        save_image($new_canvas, $destination, $image_type, $quality); //save resized image
    }

    return true;
}

##### This function corps image to create exact square, no matter what its original size! ######
function crop_image_square($source, $destination, $image_type, $square_size, $image_width, $image_height, $quality){
    if($image_width <= 0 || $image_height <= 0){return false;} //return false if nothing to resize
   
    if( $image_width > $image_height )
    {
        $y_offset = 0;
        $x_offset = ($image_width - $image_height) / 2;
        $s_size     = $image_width - ($x_offset * 2);
    }else{
        $x_offset = 0;
        $y_offset = ($image_height - $image_width) / 2;
        $s_size = $image_height - ($y_offset * 2);
    }
    $new_canvas = imagecreatetruecolor( $square_size, $square_size); //Create a new true color image
   
    //Copy and resize part of an image with resampling
    if(imagecopyresampled($new_canvas, $source, 0, 0, $x_offset, $y_offset, $square_size, $square_size, $s_size, $s_size)){
        save_image($new_canvas, $destination, $image_type, $quality);
    }

    return true;
}

##### Saves image resource to file #####
function save_image($source, $destination, $image_type, $quality){
    switch(strtolower($image_type)){//determine mime type
        case 'image/png':
            imagepng($source, $destination); return true; //save png file
            break;
        case 'image/gif':
            imagegif($source, $destination); return true; //save gif file
            break;          
        case 'image/jpeg': case 'image/pjpeg':
            imagejpeg($source, $destination, $quality); return true; //save jpeg file
            break;
        default: return false;
    }
}
