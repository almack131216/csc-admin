<?php

	Class CMSMakeImages {

		///////////////////////////
		/// Generate File Name (SEO)
		function GenerateFileName($filename) {
			$temp = trim($filename);
			
			// Lower case
			$temp = strtolower($temp);
			
			// Replace spaces with a '_'
			$badchars = array(" ","/");
			$temp = str_replace($badchars, "-", $temp);
			
			// Loop through string
			$result = '';
			for ($i=0; $i<strlen($temp); $i++) {
				if (preg_match('([0-9]|[a-z]|-)', $temp[$i])) $result = $result . $temp[$i];
			}
			$result = str_replace(array("----","---","--"),"-",$result);
			
			// Return filename
			return $result;
		}
		/// END ///
		
		function RenameFile($getAttributes){			
			rename($getAttributes['oldName'],$getAttributes['newName']);
		}
		
		////////////////////////////////////////////////////////////
		/////// ADMIN   ////////// 	MAKE THUMBNAIL SIZES   /////////
		function MakeImage($my_image_withpath, $filename, $size) {
			global $gp_highres_width, $gp_highres_height, $gp_highres_quality;
			global $gp_large_width, $gp_large_height, $gp_large_quality;
			global $gp_primary_width, $gp_primary_height, $gp_primary_quality;
			global $gp_thumb_width, $gp_thumb_height, $gp_thumb_quality;
			global $CMSMakeImages;
			
			if($size == "thumb"){
				$CMSMakeImages->ImageScale($my_image_withpath, $filename, $gp_thumb_width, "pixels", "width", $gp_thumb_quality, $size);
			}else if ($size == "primary") {
				$CMSMakeImages->ImageScale($my_image_withpath, $filename, $gp_primary_width, "pixels", "width", $gp_primary_quality, $size);
			}else if ($size == "large") {
				$CMSMakeImages->ImageScale($my_image_withpath, $filename, $gp_large_width, "pixels", "width", $gp_large_quality, $size);
			}else if ($size == "highres") {
				$CMSMakeImages->ImageScale($my_image_withpath, $filename, $gp_highres_width, "pixels", "width", $gp_highres_quality, $size);		
			}			
		}
		
		//////////// FUNCTION: scale images in table
		function ImageScale($my_image_withpath, $filename, $value, $factor, $scaleby, $quality, $size) {
			/*global $my_image_withpath, $filename;*/
			global $width_new, $height_new, $width_orig, $height_orig, $my_image_thumb, $my_image_primary;
			global $siteroot,$gp_uploadPath;
			global $CMSMakeImages,$CMSShared;
			
			$tmp = @getimagesize($my_image_withpath);
			
			// get original (ACTUAL) dimensions
			$height_orig = $tmp[1];
			$width_orig  = $tmp[0];
			
			//echo "-------------".$width_orig;
			switch($factor) {
				//////// PIXELS (BY WIDTH)
				case "pixels":
					if ($scaleby == "width") {
						$widthscale = $value / $width_orig;			
						$width_new  = $value;
						$height_new = $height_orig * $widthscale;
					} else {
						$heightscale = $value / $height_orig;			
						$height_new  = $value;
						$width_new = $width_orig * $heightscale;	
					}								
					break;
				
				//////// PERCENTAGE
				case "percent":
					if ($scaleby == "width") {
						$value = $value / 100;
						$width_new  = $width_orig * $value;
						$height_new = $height_orig * $value;
					} else {
						$value = $value / 100;
						$height_new  = $height_orig * $value;
						$width_new = $width_orig * $value;
					}			
					break;		
			}	
			
			
			$type = $CMSShared->GetFileType($filename);
			
			
			$loc_thumbs = $siteroot.$gp_uploadPath['thumbs'];
			$loc_primary = $siteroot.$gp_uploadPath['primary'];
			$loc_highres = $siteroot.$gp_uploadPath['highres'];
			$loc_large = $siteroot.$gp_uploadPath['large'];	
			
			if ($quality != 100){		
				
				if ($type != "gif" ) {
					
					if ( $type=="jpeg" || $type=="jpg" ) {
						$CMSMakeImages->ThumbMe("jpg", $filename, $width_new, $height_new, $quality, $size);					
					} elseif($type="png" || $type="PNG") {
						$CMSMakeImages->ThumbMe("png", $filename, $width_new, $height_new, $quality, $size);
					}
					if($size == "thumb"){
						$my_image_thumb = $loc_thumbs.$filename;
					}elseif($size == "primary"){
						$my_image_primary = $loc_primary.$filename;
					}
					
				} else {
					$CMSMakeImages->ThumbMe("gif", $filename, $width_new, $height_new, $quality, $size);
					if($size == "thumb"){
						$my_image_thumb = $loc_thumbs.$filename;
					}elseif($size == "primary"){
						$my_image_primary = $loc_primary.$filename;
					}			
				}		
					
			} else {
				$CMSMakeImages->ThumbMe("gif", $filename, $width_new, $height_new, $quality, $size);
				if($size == "thumb"){
					$my_image_thumb = $loc_thumbs.$filename;
				}elseif($size == "primary"){
					$my_image_primary = $loc_primary.$filename;
				}	
			}
			
		}
		
		
		////////////////////////////////////////////////////////////
		////////////////////////// 	 MAKE THUMBNAIL IMAGE  /////////
		function ThumbMe($format, $image_name, $width_new, $height_new, $quality, $size){
			global $siteroot,$gp_uploadPath;
			global $CMSMakeImages;
			
			$loc_highresimages = $siteroot.$gp_uploadPath['highres'];
			$loc_largeimages = $siteroot.$gp_uploadPath['large'];
			$loc_primary = $siteroot.$gp_uploadPath['primary'];
			$loc_thumbs = $siteroot.$gp_uploadPath['thumbs'];
			
			if($size == "thumb"){
				$loc_dynamic = $loc_thumbs;
			}elseif($size == "primary"){
				$loc_dynamic = $loc_primary;
			}elseif($size == "highres"){
				$loc_dynamic = $loc_highresimages;
			}else{
				$loc_dynamic = $loc_largeimages;
			}
			
			if($destimg=imageCreate($width_new, $height_new) ) {
				//
			} else {
				echo '<p class="error">Problem creating image. Please contact support</p>';
			}
			//$destimg=imageCreate($width_new, $height_new) or die("Problem creating image");
				
			///////////// MAKE JPEG THUMBNAIL
			switch($format) {
				case "jpg":
					$srcimg=ImageCreateFromJPEG($loc_largeimages.$image_name) or die("problem opening source image $loc_largeimages $image_name");	
					ImageCopyResized($destimg,$srcimg,0,0,0,0,$width_new,$height_new,ImageSX($srcimg),ImageSY($srcimg) ) or die("Problem in resizing");	
					//ImageJPEG($destimg,$loc_dynamic.$image_name, $quality) or die("Problem in saving");
					$CMSMakeImages->MakeImage_MergeCenter($loc_largeimages.$image_name, $loc_dynamic.$image_name, $width_new, $height_new);
					break;			
					
					///////////// MAKE PNG THUMBNAIL
				case "png":
					$srcimg=ImageCreateFromPNG($loc_largeimages.$image_name) or die("problem opening source image $loc_largeimages $image_name ");	
					ImageCopyResized($destimg,$srcimg,0,0,0,0,$width_new,$height_new,ImageSX($srcimg),ImageSY($srcimg) ) or die("Problem in resizing");	
					ImagePNG($destimg,$loc_dynamic.$image_name, $quality) or die("Problem in saving");
					break;
					
					///////////// MAKE PNG THUMBNAIL
				case "gif":
					$srcimg=ImageCreateFromGIF($loc_largeimages.$image_name) or die("problem opening source image $loc_largeimages $image_name ");	
					ImageCopyResized($destimg,$srcimg,0,0,0,0,$width_new,$height_new,ImageSX($srcimg),ImageSY($srcimg) ) or die("Problem in resizing");	
					ImageGIF($destimg,$loc_dynamic.$image_name) or die("Problem in saving");
					break;
					
								
				default:
					break;
					
			}
			//echo "<HR><HR><strong>IMAGE COMPRESSION....(".$format." @:".$quality."%)</strong> can be found in thumbs directory<HR>";
			
		}
		//\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
		
		function MakeImage_MergeCenter($src, $dst, $dstx, $dsty){
		
			$makeRegardless = true;
			//$src = original image location
			//$dst = destination image location
			//$dstx = user defined width of image
			//$dsty = user defined height of image
			
			echo '<br>1. original location:'.$src;
			echo '<br>2. destination:'.$dst;
			echo '<br>3. width:'.$dstx;
			echo '<br>4. height:'.$dsty;
			
			$allowedExtensions = 'jpg jpeg gif png';
			
			$name = explode(".", $src);
			$currentExtensions = $name[count($name)-1];
			$extensions = explode(" ", $allowedExtensions);
		
			for($i=0; count($extensions)>$i; $i=$i+1){
				if($extensions[$i]==$currentExtensions){
					$extensionOK=1;
					$fileExtension=$extensions[$i];
					break;
				}
			}
		
			if($extensionOK){
				$size = getImageSize($src);
				$width = $size[0];
				$height = $size[1];
		
				if(($width >= $dstx AND $height >= $dsty) || $makeRegardless==true){
					$proportion_X = $width / $dstx;
					$proportion_Y = $height / $dsty;
		
					if($proportion_X > $proportion_Y ){
						$proportion = $proportion_Y;
					}else{
						$proportion = $proportion_X ;
					}
					$target['width'] = $dstx * $proportion;
					$target['height'] = $dsty * $proportion;
					
					$original['diagonal_center'] = round(sqrt(($width*$width)+($height*$height))/2);
					$target['diagonal_center'] = round(sqrt(($target['width']*$target['width'])+($target['height']*$target['height']))/2);
		
					$crop = round($original['diagonal_center'] - $target['diagonal_center']);
					if($proportion_X < $proportion_Y ){
						$target['x'] = 0;
						$target['y'] = round((($height/2)*$crop)/$target['diagonal_center']);
					}else{
						$target['x'] =  round((($width/2)*$crop)/$target['diagonal_center']);
						$target['y'] = 0;
					}
		
					if($fileExtension == "jpg" OR $fileExtension=='jpeg'){
						$from = ImageCreateFromJpeg($src);
					}elseif ($fileExtension == "gif"){
						$from = ImageCreateFromGIF($src);
					}elseif ($fileExtension == 'png'){
						$from = imageCreateFromPNG($src);
					}
		
					$new = ImageCreateTrueColor ($dstx,$dsty);
					
					imagecopyresampled ($new,  $from,  0, 0, $target['x'],
					$target['y'], $dstx, $dsty, $target['width'], $target['height']);
		
					if($fileExtension == "jpg" OR $fileExtension == 'jpeg'){
						imagejpeg($new, $dst, 80);
					}elseif ($fileExtension == "gif"){
						imagegif($new, $dst);
					}elseif ($fileExtension == 'png'){
						imagepng($new, $dst);
					}
				}
			}
		}


	}
	$CMSMakeImages = new CMSMakeImages();

?>