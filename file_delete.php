<?php

$page_title = "Delete This File";
$page_subtitle = "";
$curr_page = "catalogue";
$curr_page_sub	= "catalogue_fileManage";
include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddThickbox();
include("includes/admin_pageheader.php");

/////////// check to see if option is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
} else {
			
	$dir = $siteroot."uploads/";
	//$dir_folder = array($dir
	//echo $dir;
	
	if(!empty($_POST['DeleteThisFile']) || !empty($_GET['file'])){
		if (!empty($_POST['DeleteThisFile']) && empty($_GET['file']) ) {
			$tmpFilename = $_POST['DeleteThisFile'];
			$FileToDelete = $dir.$tmpFilename;
			// if image exists(avoids errors) then delete it
			if($CMSShared->FileExists($FileToDelete)) $CMSDebug->FileUnlink($FileToDelete);
						
			echo '<div class="panel_good">';
				echo '<p>File successfully deleted.</p>';
				echo '<div class="inner_right">';
					echo '<a href="file_manage.php" id="return"><span>&#60;&nbsp;Return</span></a>';
				echo '</div';
			echo '</div>';
			
					
		} elseif(!empty($_GET['file'])) { // ELSE if 1
			$my_filename = $_GET['file'];
			//////////// GET THUMB	
			echo '<div class="panel_warning">';				
				echo '<p>You are about to delete this file...<br/>';
				echo '<strong>Once deleted, this cannot be undone!</strong>';
				echo '<div class="inner_right">';
					$filesrc=$dir.$my_filename;			
					if($CMSShared->IsImage($my_filename)){
						//echo '<img src="thumb.php?file='.$filesrc.'&size=66&quality=60&nocache=0" alt="'.$my_filename.'">';
						echo '<img src="timthumb.php?src='.$filesrc.'&w=66&q=60" alt="'.$my_filename.'">';
						echo $CMSAddOns->Thickbox("filePreview",$filesrc,$my_filename,"");
					}elseif(!$CMSShared->IsImage($my_filename) && !empty($my_filename) ){
						$fileType = $CMSShared->GetFileType($my_filename);
						if (!$CMSShared->FileExists($filesrc)) {
							echo $CMSImages->GetFileIcon($fileType,false,$my_filename);
						}else{
							echo $CMSImages->GetFileIcon($fileType,true,$my_filename);
						}
						echo '<a href="'.$filesrc.'" title="Preview item:&nbsp;'.$my_filename.'"><img src="includes/icons/icon_item_preview.gif" alt="Preview file:&nbsp;'.$my_filename.'"></a>';
					}
				echo '</div>';
			echo '</div>';				
			
			echo '<div class="panel_oneline">';
				echo '<div class="inner_right">';
					echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
					echo '<input type="hidden" name="DeleteThisFile" value="'.$my_filename.'">';
					echo '<input type="submit" name="Delete File" value="Delete File" title="Delete File" id="delete">';
					echo '</form>';
					echo '<a href="javascript:history.go(-1)" id="cancel"><span>&#60;&nbsp;Cancel</span></a>';
				echo '</div>';
			echo '</div>';
		} // END if 1

	} else {
		echo '<p class="error">Operator error. Item has not been recognised and so cannot be deleted</p>';
	}

}
include("includes/admin_pagefooter.php");
?>






