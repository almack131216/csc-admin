<?php

if(isset($_GET['editid'])){
	$editid = true;
	$page_title		= "Edit User";
	$page_subtitle	= "Edit this user using the form below";	
}else{
	$page_title		= "Add a New User";
	$page_subtitle	= "Add a new user using the form below";
}

$curr_page = "superadmin";

include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
} else {	
	$panel_username = 'panel_oneline';
	$panel_password = 'panel_oneline';
	$panel_quickname = 'panel_oneline';	
	
	$fb_website = "Enter address / URL";
	$tablename = $db_shared."users";
				
	/////// the cancel button needs to know how many steps back to take
	/////// (1 will be added with each update to avoid cancelling back to previous amend)
	if (isset($_POST['updater']) ) {
		$updated = $_POST['updater'];
		$updated++;
	} else{
		/// if we've just entered page(previous page (-1) will be allocated to cancel button
		$updated = 1;
	}
	
	////////// START PAGE HTML
	if (isset($_POST['updater'])) { // handle form
		$post_success = 1;
			
		//////////////////////////// check for name (not required)
		if (isset($_POST['editid'])) $editid = $_POST['editid'];
		
		
		if (!empty($_POST['username'])) {
			$un = $CMSTextFormat->stripCrap2_in($_POST['username']);		
		} else {
			$panel_username = 'panel_error';
			$post_success = 0;
			$un = '';
		}		
		
		//////////////////////////// check for First Name (not required)
		if (!empty($_POST['FirstName'])) {
			$fn = $CMSTextFormat->stripCrap2_in($_POST['FirstName']);		
		} else {
			$fn = '';
		}
		
		//////////////////////////// check for First Name (not required)
		if (!empty($_POST['Surname'])) {
			$sn = $CMSTextFormat->stripCrap2_in($_POST['Surname']);		
		} else {
			$sn = '';
		}
		
		//////////////////////////// check for First Name (not required)
		if (!empty($_POST['email'])) {
			$e = $CMSTextFormat->stripCrap2_in($_POST['email']);		
		} else {
			$e = '';
		}
		
		//////////////////////////// check for First Name (not required)
		if (!empty($_POST['password'])) {
			$pw = $_POST['password'];

			$string = $pw;
			$salt = 's+(_a*';
			$hash = md5($string.$salt);
			$pw = $hash;
			if($editid && strlen($string) < 10){
				$query = "UPDATE $tablename SET password='$pw' WHERE cid='$editid' LIMIT 1";
				$result = $db->mysql_query_log($query);
			}

				
		} else {
			$panel_password = 'panel_error';
			$post_success = 0;
			$pw = '';
		}
		
		//////////////////////////// check for First Name (not required)
		if (!empty($_POST['quickname'])) {
			$qn = $CMSTextFormat->stripCrap2_in($_POST['quickname']);		
		} else {
			$panel_quickname = 'panel_error';
			$post_success = 0;
			$qn = '';
		}
		
		//////////////////////////// check for address
		if (!empty($_POST['website']) && $_POST['website'] != "http://" && strlen($_POST['website']) >= 8 ) {
			$w = $CMSTextFormat->stripCrap2_in($_POST['website']);		
		} else {
			$w	= "http://";
		}
		
		//////////////////////////// check for

		//////////////////////////// check for 
		if (!empty($_POST['status'])) {
			$s = $_POST['status'];					
		} else {
			$s = 1;
		}
		//////////////////////////// check for status
		if (!empty($_POST['status_details'])) {
			//$sd = $CMSTextFormat->stripCrap2_in($_POST['status_details']);	
			$sd = $CMSTextFormat->stripCrap2_in_body($_POST['status_details']);		
		} else {
			$sd = '';
		}
				
		//////////////////////////// add record to database		
		if ( $post_success == 1 ) {
			if ($editid) {
				$query = "UPDATE $tablename SET username='$un',FirstName='$fn',Surname='$sn',email='$e',quickname='$qn',website='$w',status='$s',status_details='$sd' WHERE cid='$editid' LIMIT 1";
				$result = $db->mysql_query_log($query);
				$uid = $editid;				
			} else {
				$query = "INSERT into $tablename (cid,username,FirstName,Surname,email,password,registration_DATE,quickname,website,status,status_details) VALUES ('','$un','$fn','$sn','$e','$pw',NOW(),'$qn','$w','$s','$sd' )";
				$result = $db->mysql_query_log($query);
				if($result){
					$uid = mysql_insert_id();					
					// create entry in contact_details table
					$cd_query = "INSERT INTO contact_details (cid,name,email,tel_h,fax,tel_m) VALUES ('$uid','','$e','','','')";
					$cd_result = $db->mysql_query_log($cd_query);
					
					// create entry in catalogue_prefs table
					$cp_query = "INSERT INTO catalogue_prefs (cid,orderby,orderby_desc,maxperpage,showname,showdetail,showprice,showdescription,category_orderby,category_orderby_desc)";
					$cp_query .= "VALUES ('$uid', 'upload_date', 'desc', '0', '1', '1', '1', '1', 'category', 'desc')";
					$cp_result = $db->mysql_query_log($cp_query);

				}else{
					echo '<p class="error">1. COULD NOT INSERT NEW USER USING THIS QUERY:<br/>'.$query.'</p>';
					$post_success=0;
				}					
			}

		}
		
	} // END IF POSTED DATA	
	
	/// IF POSTED DATA IS SUCCESSFUL....
	/// GO TO COMPLETE SCREEN
	if (isset($post_success) && $post_success != 0 ) {
		
		header ("Location: ".$adminroot."super_user_success.php?uid=".$uid."");
		
		
	} else { /// ELSE IF POSTED DATA IS SUCCESSFUL....
		
		echo '<form enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST" name="UploadForm">';		

		if (isset($_GET['editid'])) {
			$editid = $_GET['editid'];
			echo '<input type="hidden" name="editid" value='.$editid.'>';
			$edit_query = "SELECT * FROM $tablename WHERE cid='$editid' LIMIT 1";
			$edit_result = mysql_query($edit_query);					
			$edit_array = mysql_fetch_array($edit_result);
			
			$un	= $edit_array['username'];
			$fn	= $edit_array['FirstName'];
			$sn	= $edit_array['Surname'];
			$e	= $edit_array['email'];
			$pw	= $edit_array['password'];
			$qn	= $edit_array['quickname'];
			$w	= $edit_array['website'];
			$s	= $edit_array['status'];
			$sd	= $edit_array['status_details'];

		} else {			
			
			$un	= '';
			$fn	= '';
			$sn	= '';
			$e	= '';
			$pw	= '';
			$qn	= '';
			$w	= "http://";
			$s	= 1;
			$sd	= '';
		}

		// stepnum needs to be a variable as some features will be hidden when uploading to existing item(additional images)
		$stepnum = 1;
		
		//STEP 1: GIVE NAME		
		echo '<div class="'.$panel_username.'">';
			echo '<p class="nopad"><span class="steptitle">Step '.$stepnum.':</span> Username and Password</p>';
			echo '<div class="inner_right">';
				echo '<input type="text" id="price_details" name="username" value="'.$un.'"/>';
				echo '<br/><input type="password" id="price_details" name="password" value="'.$pw.'"/>';
			echo '</div>';
		echo '</div>';
		$stepnum++;
		
		//STEP 1: GIVE NAME		
		echo '<div class="panel_oneline">';
			echo '<p class="nopad"><span class="steptitle">Step '.$stepnum.':</span> Christian &amp; Surname</p>';
			echo '<div class="inner_right">';
				echo '<input type="text" id="price_details" name="FirstName" value="'.$fn.'"/>';
				echo '<br/><input type="text" id="price_details" name="Surname" value="'.$sn.'"/>';
			echo '</div>';
		echo '</div>';
		$stepnum++;
		
		//STEP 1: GIVE NAME		
		echo '<div class="panel_oneline">';
			echo '<p class="nopad"><span class="steptitle">Step '.$stepnum.':</span> User\'s Email address</p>';
			echo '<div class="inner_right">';
				echo '<input type="text" name="email" value="'.$e.'"/>';
			echo '</div>';
		echo '</div>';
		$stepnum++;
		
		////////////////////
		/////// GIVE website		
		echo '<div class="panel_oneline">';
		echo '<p class="nopad"><span class="steptitle">Step '.$stepnum.':</span> '.$fb_website.'</p>';
		echo '<div class="inner_right">';
		echo '<input type="text" name="website" value="'.$w.'">' ;
		echo '</div>';
		echo '</div>';
		$stepnum++;	
			
		//STEP 1: GIVE NAME		
		echo '<div class="'.$panel_quickname.'">';
			echo '<p class="nopad"><span class="steptitle">Step '.$stepnum.':</span> \'Quickname\' and Site directory</p>';
			echo '<div class="inner_right">';
				echo '<input type="text" id="price_details" name="quickname" value="'.$qn.'"/>';
			echo '</div>';
		echo '</div>';
		$stepnum++;			
		
		//////////////////////////////////		
		///STEP 1: GIVE NAME		
		echo '<div class="panel_oneline">';
			echo '<p class="nopad"><span class="steptitle">Step '.$stepnum.':</span> Any notes?</p>';
			echo '<div class="inner_right">';
				echo '<textarea id="description" name="status_details" cols="35" rows="3">'.$sd.'</textarea>';
			echo '</div>';
		echo '</div>';
		$stepnum++;
		
				
		//STEP 5: SUBMIT
		echo '<div class="panel_oneline">';
		echo '<p><span class="steptitle">Step '.$stepnum.':</span> Process user</p>';
		echo '<div class="inner_right">';
		echo '<a href="Javascript:document.forms.UploadForm.submit();" id="submit" name="submit"><span>Submit Details</span></a>';
		//echo '<input type="image" src="includes/btns/btn_submit.gif" id="submit" name="submit" value="Submit User" alt="Submit User"/>';
		echo '<input type="hidden" name="updater" value="'.$updated.'">';
		echo '<a href="javascript:history.go(-'.$updated.  ')" id="return"><span>&#60;&nbsp;Return</span></a>';
		echo '</form>';		
		echo '</div>';
		echo '</div>';
	} /// END IF POSTED DATA IS SUCCESSFUL....	
}
	
include("includes/admin_pagefooter.php");

?>