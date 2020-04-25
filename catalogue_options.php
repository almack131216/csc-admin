<?php

$page_title = "Catalogue Options";
$page_subtitle = "Select one of the options below to customise &amp; maintain your catalogue";
$curr_page = "catalogue";
include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");

/////////// check to see if option is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
} else {
	
	
	for($tmpcount=0;$tmpcount<count($arr_page_catalogue);$tmpcount++){
		$tmpLink='<div class="panel">';
		$tmpLink.='<h2><a href="'.$arr_page_catalogue[$tmpcount]['href'].'">'.$arr_page_catalogue[$tmpcount]['title'].'&nbsp;:</a></h2>';
		$tmpLink.='<p class="nopad">'.$arr_page_catalogue[$tmpcount]['title_x'].'</p></div>';
		echo $tmpLink;	
	}

}

include("includes/admin_pagefooter.php");
?>
