<?php

if(isset($_GET['editid'])){
	$editid = true;
	$page_title		= "Edit item";
	$page_subtitle	= "Edit using the form below";	
}else{
	$page_title		= "Add item";
	$page_subtitle	= "Add to the catalogue using the form below";
}
$curr_page = "catalogue";
$curr_page_sub	= "items";
include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
} else {

$thickbox = false;
/////////// check to see if option is set
if(isset($_GET['editid'])){
	$editid = $_GET['editid'];
}
if(isset($_GET['id_xtra'])){
	$my_id_xtra = $_GET['id_xtra'];
}
if(isset($_GET['image_dir'])){
	$my_image_dir = $_GET['image_dir'];
	initImgDir($my_image_dir,'');
}


if(!empty($siteroot)){

	////////////////////////
	/// START TO PRINT PAGE
	echo '<form action="admin_catalogue_upload.php" method="post" target="_parent">';
	if($editid){
		echo '<input type="hidden" name="editid" value="'.$editid.'">';
	}
	if($my_id_xtra){
		echo '<input type="hidden" name="id_xtra" value="'.$my_id_xtra.'">';
	}
	if($my_image_dir){
		echo '<input type="hidden" name="image_dir" value="'.$my_image_dir.'">';
	}
	$dir = $siteroot."uploads/";
	//$dir_folder = array($dir
	//echo $dir;

	$files = scandir($dir,0); //"0" lists in ascending order, "1" lists descending		
	//print_r($files1);
	
	for($tmpcount=0;$tmpcount<count($files);$tmpcount++){
		$my_filename = $files[$tmpcount];
		if(!empty($my_filename) && $my_filename!="." && $my_filename!=".." && $my_filename!="thumbs" ){
			echo '<div class="panel_Smallbox">';
				if($CMSShared->IsImage($my_filename)){						
					echo '<img src="'.$dir."thumbs/".$my_filename.'" alt="'.$my_filename.'" title="'.$my_filename.'">';
				}else{
					$fileType = $CMSShared->GetFileType($my_filename);
					if (!(@fclose(@fopen($dir.$my_filename, "r")))) {
						echo $CMSImages->am_getfileicon($fileType,false,$my_filename);
					}else{
						echo $CMSImages->am_getfileicon($fileType,true,$my_filename);
					}
				}
				echo '<p>&nbsp;<input type="submit" id="fileSelectSelect" name="UploadFile" value="'.$my_filename.'" /></p>';
			echo '</div>';
		}	
	}
	
	echo '</form>';
	
}

include("includes/admin_pagefooter.php");

?>