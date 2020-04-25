<?php

	Class CMSImages {
		
		////////////////////////////////////////////////////////////
		////////////////////////// 	 GET THUMBNAIL IMAGE   /////////
		
		function GetThumb($my_image_large, $my_image_thumb, $filename, $ret_true_false) {
			global $CMSImages;
			global $siteroot,$gp_uploadPath;
			global $preview_image_large, $preview_image_thumb, $preview_image;	
			global $my_image_withpath, $my_image_large, $my_image_thumb;
			global $missingimage,$missingthumb,$missinglarge;
			//global $ret_true_false;
			global $image_name, $my_image_thumb;
			global $width_new, $height_new, $width_orig, $height_orig;
			global $my_id,$moreinfopage;
			global $CMSDebug, $CMSShared, $CMSTextFormat;
			//$image_name = $CMSTextFormat->stripCrap2_out( $filename );
					
			$ThisThumb = '';
			
			if($CMSShared->IsImage($filename)){
				$my_image_thumb = $CMSImages->CheckImageExists("thumb",$my_image_thumb,$filename);
				$my_image_large = $CMSImages->CheckImageExists("large",$my_image_large,$filename);
			
				/////////////////////////////////////////////////////////////////////// (END OF) CHECK IMAGES EXIST
				if($my_image_thumb){
					$tmp = @getimagesize($my_image_thumb);	
					// get original (ACTUAL) dimensions
					$height_new = $tmp[1];
					$width_new  = $tmp[0];
					
					if ($ret_true_false == "false") {
						$ThisThumb .= '<img src="'.$my_image_thumb.'" width="'.$width_new.'" height="'.$height_new.'"  class="thumb">';		
					} else {
						$ThisThumb .= '<a href="'.$moreinfopage.'?uid='.$my_id.'" target="_blank" title="Preview">';
						$ThisThumb .= '<img src="'.$my_image_thumb.'" width="'.$width_new.'" height="'.$height_new.'"></a>';
						//image_to_open_fullsize($my_image_large, $image_name, $my_image_thumb, $width_orig, $height_orig);
					}
				
				}
			}else{
				$fileType = $CMSShared->GetFileType($filename);
				if (!$CMSShared->FileExists($my_image_large) || empty($filename)) { //IF FILE CANNOT BE FOUND
					$ThisThumb .= $CMSImages->GetFileIcon($fileType,false,$filename); //SHOW MISSING ICON
				}else{
					if ($ret_true_false == "false") {
						$ThisThumb .= $CMSImages->GetFileIcon($fileType,true,$filename);
					}else{
						$ThisThumb .= '<a href="'.$my_image_large.'" title="Download File: '.$filename.'" target="_blank">';
						$ThisThumb .= $CMSImages->GetFileIcon($fileType,true,$filename);
						$ThisThumb .= '</a>';
					}
					
				}
			}
			
			return $ThisThumb;
			/*
			$tmp = @getimagesize($my_image_large);	
			// get original (ACTUAL) dimensions
			$height_orig = $tmp[1];
			$width_orig  = $tmp[0];
			*/
			//echo 'ret_true_false ('.$ret_true_false.')';	
		}
		// END //
		
		///////////////////////////////////////////////////
		// Get File Icon ("doc",true,"Word Document Title")
		function GetFileIcon($ret_type,$ret_found,$ret_title){
			global $adminroot;
			if($ret_found){
				return '<img src="'.$adminroot.'includes/icons/fileTypes_'.$ret_type.'.png" alt="'.$ret_title.'">';
			}else{
				return '<img src="'.$adminroot.'includes/icons/fileTypes_missing'.$ret_suffix.'.gif" alt="'.$ret_title.'">';
			}
		}
		// END //
		
		///////////////////////////////////////////////////////////////////////////////////
		// To use with 'ImageTrail_tooltip' when hovering over FILE and not thumbnail image
		function GetFileIconHover($ret_type){
			global $adminroot;
			return $adminroot.'includes/icons/fileTypes_'.$ret_type.'.gif';
		}
		// END //
		
		////////////////////////////////////////////////////////////
		////////////////////////// 	 CHECK IMAGES EXIST   //////////
		//...if not, show '_missingimage.jpg'		
		// This OPENS and CLOSES the image file to see if it exists
		// This was implemented as !file_exists does not work (This function will not work on remote files as the file to be examined must be accessible via the servers filesystem.)
		function CheckImageExists($ret_imagesize,$ret_imagepath,$ret_filename){
			global $my_image_thumb, $missingthumb;
			global $my_image_large, $missinglarge;
			global $adminroot,$siteroot,$gp_uploadPath;
			global $CMSShared;
			
			switch($ret_imagesize){
				case "thumb":
					if($CMSShared->IsImage($ret_filename)){
						if (!(@fclose(@fopen($ret_imagepath, "r"))) || $ret_imagepath == $siteroot.$gp_uploadPath['thumbs']) {				
							return $missingthumb;
					    }else{
						    return $my_image_thumb;
						}				
					}break;
					
				case "large":		
					if($CMSShared->IsImage($ret_filename)){
						if (!(@fclose(@fopen($ret_imagepath, "r")))) {
						    $my_image_thumb = $missingthumb;
						    return $missinglarge;
						 }else{
							return $my_image_large;
						}				   
					}else{
						$fileType = $CMSShared->GetFileType($ret_filename);
						if (!(@fclose(@fopen($ret_imagepath, "r")))) {
							return $adminroot.'includes/icons/fileTypes_missing.gif';
						}else{
							return $adminroot.'includes/icons/fileTypes_'.$fileType.'.png';
						}
					    
					}break;		
					
			}
		
		}
		/// END ///
		
		
			
	}
	$CMSImages = new CMSImages();

?>