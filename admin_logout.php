<?php

include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle("Log-In");
$BuildPage .= $PageBuild->AddPageTip("Please contact support if you have trouble logging in");
include("includes/admin_pageheader.php");

if (!isset($_SESSION['FirstName'])) {
	//Link To Marks' Prodadmin
	header ("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php?client=".$_GET['client']);
	ob_end_clean();
	exit();
} else {
 	$_SESSION = array(); //Destroy variables
 	session_destroy(); //destroy the session itself
 	setcookie(session_name(), '', time()-300, '/', '', 0); //destroy cookie
}

// print message
	
	echo '<p class="prompt">You have successfully logged out</p>';
	include('includes/admin_pagefooter.php');
?>

