<?php

$page_title = "Help: Icons Explained";
$page_subtitle = "Click on the icons below for helpful descriptions";
$curr_page = "help";
include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");
include("includes/classes/CMSHelp.php");


	$icons = $CMSHelp->GetIconArray();
	$buttons = $CMSHelp->PrintButtonPanel($curr_page);	
	
	echo $buttons;


include("includes/admin_pagefooter.php");
?>