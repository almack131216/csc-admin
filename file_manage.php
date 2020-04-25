<?php

$thickbox = false; // NEED THIS
$selectfile = "manage";
$curr_page = "ThinUpload";
$curr_page_sub	= "catalogue_fileManage";
$page_title		= "File Manager";
$page_subtitle	= "Roll-over files to show their full name";

include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddThickbox();
include("includes/admin_pageheader.php");

/////////// check to see if option is set
if(isset($_GET['editid']))	$editid = $_GET['editid'];
if(isset($_GET['id_xtra']))	$my_id_xtra = $_GET['id_xtra'];

/////////// check to see if session is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
}else if($_SESSION['suid'] && $suid_PageAccess && $_SESSION['suid']!=$itemID){
	echo suid_pageAccessMessage();
}else{
	if(!empty($siteroot)){
		$dir = $siteroot."uploads/";
		$uploadPage = 'jquery_ui_widget.html';
		if($_SESSION['quickname']=="mcw") $uploadPage = 'jquery_ui_widget_mcw.html';


		$SubNavInner = '<ul id="SubNavInner">';
		//$SubNavInner .= '<li class="add"><a href="ThinUpload_applet.php" title="Use the drag-and-drop image resize and compression tool for uploading your photos">add files to file manager</a></li>';	
		
		$SubNavInner .= '<li class="add"><a href="plupload/examples/jquery/'.$uploadPage.'" title="Use the drag-and-drop image resize and compression tool for uploading your photos">add files to file manager</a></li>';		
		$SubNavInner .= '</ul>';
		echo $SubNavInner;
		
		///////////////////////
		/// START TO PRINT PAGE
		require("file_selectfile_files.php");	
	}
}

?>