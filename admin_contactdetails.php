<?php

$curr_page = "contactdetails";
$curr_page_sub = "contactdetails";
include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle("Contact Details");
$BuildPage .= $PageBuild->AddPageTip("Please ensure you enter details accurately as these details go onto your website");
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
} else {

$CMSShared->SetContactDetails();

////////// START PAGE HTML

$tmptitle = '<p><span class="steptitle">Company Name:&nbsp;</span>';
if (isset($_POST['field_name'])) {
	//echo 'IS SET';
	$clientname = $CMSTextFormat->stripCrap2_in($_POST['field_name']);
	$query = "UPDATE $db_shared.contact_details SET name='$clientname' WHERE cid=$cid LIMIT 1";
	$result = $db->mysql_query_log($query );

	if (mysql_affected_rows() == 1) {
		echo '<div class="panel_good">';		
		echo $tmptitle.'Successfully updated.';
	} else {
		echo '<div class="panel_oneline">';
		echo $tmptitle.'Name is unchanged.';
	}

} else {
	echo '<div class="panel_oneline">';
	echo $tmptitle;
}

echo '</p>';
echo '<div class="inner_right">';
echo '<form method="POST" action="admin_contactdetails.php" name="ContactDetails">';
echo '<input type="text" name="field_name" value="'.$clientname.'">';
echo '</div>';
echo '</div>';

////////// START PAGE HTML

$tmptitle = '<p><span class="steptitle">Email Address:&nbsp;</span>';
if (isset($_POST['field_email'])) {
	//echo 'IS SET';
	$email = $CMSTextFormat->stripCrap2_in($_POST['field_email']);
	//$email = stripslashes($email);
	$query = "UPDATE $db_shared.contact_details SET email='$email' WHERE cid=$cid LIMIT 1";
	$result = $db->mysql_query_log($query );

	if (mysql_affected_rows() == 1) {
		echo '<div class="panel_good">';		
		echo $tmptitle.'Successfully updated.';
	} else {
		echo '<div class="panel_oneline">';
		echo $tmptitle.'Email is unchanged.';
	}

} else {
	echo '<div class="panel_oneline">';
	echo $tmptitle;
}

echo '</p>';
echo '<div class="inner_right">';
echo '<input type="text" name="field_email" value="'.$email.'">';
echo '</div>';
echo '</div>';

/////////// TELEPHONE (LAND)

$tmptitle = '<p><span class="steptitle">Telephone Number:&nbsp;</span>';
if (isset($_POST['field_tel'])) {
	//echo 'IS SET';
	$tel_land = $_POST['field_tel'];
	$tel_land = stripslashes($tel_land);
	$query = "UPDATE $db_shared.contact_details SET tel_h='$tel_land' WHERE cid=$cid LIMIT 1";
	$result = $db->mysql_query_log($query );

	if (mysql_affected_rows() == 1) {
		echo '<div class="panel_good">';	
		echo $tmptitle.'Successfully updated.';
	} else {
		echo '<div class="panel_oneline">';
		echo $tmptitle.'Number is unchanged.';
	}

} else {
	echo '<div class="panel_oneline">';
	echo $tmptitle;
}

echo '</p>';
echo '<div class="inner_right">';
echo '<input type="text" name="field_tel" value="'.$tel_land.'">';
echo '</div>';
echo '</div>';

/////////// TELEPHONE (LAND)

$tmptitle = '<p><span class="steptitle">Fax Number:&nbsp;</span>';
if (isset($_POST['field_fax'])) {
	//echo 'IS SET';
	$fax = $_POST['field_fax'];
	$fax = stripslashes($fax);
	$query = "UPDATE $db_shared.contact_details SET fax='$fax' WHERE cid=$cid LIMIT 1";
	$result = $db->mysql_query_log($query );

	if (mysql_affected_rows() == 1) {
		echo '<div class="panel_good">';
		echo $tmptitle.'Successfully updated.';
	} else {
		echo '<div class="panel_oneline">';
		echo $tmptitle.'Number is unchanged.';
	}

} else {
	echo '<div class="panel_oneline">';
	echo $tmptitle;
}

echo '</p>';
echo '<div class="inner_right">';
echo '<input type="text" name="field_fax" value="'.$fax.'">';
echo '</div>';
echo '</div>';

/////////// TELEPHONE (MOBILE)

$tmptitle = '<p><span class="steptitle">Mobile Number:&nbsp;</span>';
if (isset($_POST['field_mob'])) {
	//echo 'IS SET';
	$tel_mob = $_POST['field_mob'];
	$tel_mob  = stripslashes($tel_mob);
	$query = "UPDATE $db_shared.contact_details SET tel_m='$tel_mob' WHERE cid=$cid LIMIT 1";
	$result = $db->mysql_query_log($query );

	if (mysql_affected_rows() == 1) {
		echo '<div class="panel_good">';
		echo $tmptitle.'Successfully updated.';
	} else {
		echo '<div class="panel_oneline">';
		echo $tmptitle.'Number is unchanged.';
	}

} else {
	echo '<div class="panel_oneline">';
	echo $tmptitle;
}

echo '</p>';
echo '<div class="inner_right">';
echo '<input type="text" name="field_mob" value="'.$tel_mob.'">';
echo '</div>';
echo '</div>';

/////////// SUBMIT BUTTON (MOBILE)
echo '<div class="panel_oneline">';
	echo '<p><span class="steptitle">Update Details:</span></p>';
	echo '<div class="inner_right">';
		echo '<a href="Javascript:document.forms.ContactDetails.submit();" id="submit" name="Submit"><span>&#62;&nbsp;Update Contact Details</span></a>';
		echo '</form>';
	echo '</div>';
echo '</div>';
		

}
include("includes/admin_pagefooter.php");
?>


