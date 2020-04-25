<?php

$page_title = "Catalogue Successfully Updated";
$page_subtitle = "";
$curr_page = "superadmin";
$curr_page_sub	= "";

include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
} else {	

	if(!empty($_GET['uid'])){
		$uid = $_GET['uid'];
		echo '<p class="good">All data successfully uploaded</p>';
		
		$uid_query		= "SELECT * FROM $db_shared.users WHERE cid='$uid' LIMIT 1";
		$uid_result		= mysql_query($uid_query);
		$uid_array		= mysql_fetch_array($uid_result);
		
		_panelBox("user_Preview",$uid_array['website']);		
		_panelBox("user_Edit",$uid);		
		_panelBox("user_List","");		
		_panelBox("user_Add","");
		
	}else{
		echo '<p class="error">This page was not accessed properly. Please try again.</p>';
	}
	
}
	
include("includes/admin_pagefooter.php");

?>


