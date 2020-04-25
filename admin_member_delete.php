<?php


function get_FullName($tmp_fn, $tmp_sn){
	if(empty($tmp_fn) && empty($tmp_sn)){
		return "(No Name Given)";
	}else{
		$tmpFullName = $tmp_fn.'&nbsp;'.$tmp_sn;
		return $tmpFullName;
	}	
}

$page_title = "Delete Member From Database";
$page_subtitle = "";
$curr_page = "members";
$curr_page_sub = "members_list";
include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle("Members &#124; Delete member");
$BuildPage .= $PageBuild->AddPageTip("Are you sure you want to delete this member?");
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if(notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {					
		
	if (isset($_POST['my_member_id'])) {
		$feedback = '';
		$my_member_id = $_POST['my_member_id'];				
		
		// get name of member
		$getname_query 	= "SELECT * FROM $db_clientTable_members WHERE Id=$my_member_id LIMIT 1";
		$getname_result = mysql_query($getname_query);
		$getname_row 	= mysql_fetch_array($getname_result);
		$my_fn	= $getname_row['fname'];
		$my_sn	= $getname_row['lname'];
		$my_FullName = get_FullName($my_fn,$my_sn);	
		
		if (mysql_num_rows($getname_result)==1){
	
			// print results
			if ($CMSDelete->DeleteMember($my_member_id)) {
				$feedback .= '<p class="good">Member <strong>"'.$my_FullName.'"</strong>';
				if($accessResult) $feedback .= ', inclusing all page-permissions';
				$feedback .= ' was successfully deleted</p>';
			}else{
				$feedback .= '<p class="error">Could not delete member</p>';
			}
			
		}else{
			$feedback .= '<p class="error">Member could not be found or has already been removed</p>';
		}
		
		echo $feedback;
		
		
	} else {
		
		if (isset($_GET['currentmember']) ) {
			$my_member_id = $_GET['currentmember'];						
			
			// get name of member
			$getname_query 	= "SELECT * FROM $db_clientTable_members WHERE id=$my_member_id";
			$getname_result = mysql_query($getname_query);			
			
			if($getname_result && mysql_num_rows($getname_result)>=1){
				$getname_row 	= mysql_fetch_array($getname_result);
				$my_fn	= $getname_row['fname'];
				$my_sn	= $getname_row['lname'];			
				$my_FullName = get_FullName($my_fn,$my_sn);				
				
				echo '<div class="panel_warning">';
					echo '<p>Selected Member: <strong>'.$my_FullName.'</strong><br/>You are about to delete this member?<br/><strong>Once deleted, this cannot be undone!</strong></p>';
					echo '<div class="inner_right">';
						echo '<form name="UploadForm" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
						echo '<input type="hidden" name="my_member_id" value="'.$my_member_id.'">';
						echo '<input type="submit" id="delete" name="delete" value="Delete Member">';
						echo '</form>';
					echo '</div>';
				echo '</div>';
				
			} else {
				echo '<p class="error">Member could not be found or has already been removed</p>';
			}
		}else{
			echo '<p class="error">This page was not accessed properly. Please try again.</p>';
		}
	
	}
	
	if (isset($_GET['currentmember']) ) {
		echo '<div class="panel">';	
		echo '<a href="admin_member_list.php" id="cancel"><span>&#60;&nbsp;Cancel</span></a>';
		echo '</div>';	
	} else {
		echo '<div class="panel">';
		echo '<a href="admin_member_list.php" id="return"><span>&#60;&nbsp;Return</span></a>';
		echo '</div>';
	}
	
}
	include("includes/admin_pagefooter.php");
	
?>