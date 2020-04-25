<?php


function get_FullName( $tmp_fn, $tmp_sn){
	if(empty($tmp_fn) && empty($tmp_sn)){
		return "(No Name Given)";
	}else{
		$tmpFullName = $tmp_fn.'&nbsp;'.$tmp_sn;
		return $tmpFullName;
	}	
}

$page_title = "Delete User From Database";
$page_subtitle = "";
$curr_page = "superadmin";

include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if(notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {					
	
	$tablename = "users";
	
	if (isset($_POST['my_user_cid'])) {
		$my_user_cid = $_POST['my_user_cid'];				
		
		// get name of user
		$getname_query 	= "SELECT * FROM $tablename WHERE cid = '$my_user_cid' LIMIT 1";
		$getname_result = mysql_query($getname_query);
		$getname_row 	= mysql_fetch_array($getname_result);
		$my_fn	= $getname_row['FirstName'];
		$my_sn	= $getname_row['Surname'];
		$my_FullName = get_FullName($my_fn,$my_sn);	
		
		if (mysql_num_rows($getname_result) >= 1){
			
			$arr_tables = array('users','contact_details','catalogue_prefs');
			
			for($tmpcount=0;$tmpcount<count($arr_tables);$tmpcount++){
				$tmp_tablename = $arr_tables[$tmpcount];
				
				// delete query
				$delete_query = "DELETE FROM $tmp_tablename WHERE cid='$my_user_cid'";
				$delete_result = $db->mysql_query_log($delete_query);		
		
				// print results
				if ($delete_result) {
					echo '<p class="good">User <strong>"'.$my_FullName.'"</strong> was successfully deleted from table <strong>&quot;'.$tmp_tablename.'&quot;</strong></p>';
				}else{
					echo '<p class="error">Could not delete user from table <strong>&quot;'.$tmp_tablename.'&quot;</strong></p>';
				}
			}
			
		}else{
			echo '<p class="error">User could not be found or has already been removed</p>';
		}
		
		
	} else {
		
		if (isset($_GET['currentuser']) ) {
			$my_user_cid = $_GET['currentuser'];						
			
			// get name of user
			$getname_query 	= "SELECT * FROM $tablename WHERE cid = '$my_user_cid'";
			$getname_result = mysql_query($getname_query);			
			
			if($getname_result && mysql_num_rows($getname_result)>=1){
				$getname_row 	= mysql_fetch_array($getname_result);
				$my_fn	= $getname_row['FirstName'];
				$my_sn	= $getname_row['Surname'];			
				$my_FullName = get_FullName($my_fn,$my_sn);				
				
				echo '<div class="panel_warning">';
					echo '<p>Selected User: <strong>'.$my_FullName.'</strong><br/>You are about to delete this user?<br/><strong>Once deleted, this cannot be undone!</strong></p>';
					echo '<div class="inner_right">';
						echo '<form name="UploadForm" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
						echo '<input type="hidden" name="my_user_cid" value="' . $my_user_cid.'">';
						echo '<a href="Javascript:document.forms.UploadForm.submit();" id="delete" name="delete"><span>Delete User</span></a>';
						echo '</form>';
					echo '</div>';
				echo '</div>';
				
			} else {
				echo '<p class="error">User could not be found or has already been removed</p>';
			}
		}else{
			echo '<p class="error">This page was not accessed properly. Please try again.</p>';
		}
	
	}
	
	if (isset($_GET['currentuser']) ) {
		echo '<div class="panel">';	
		echo '<a href="super_user_list.php" id="cancel"><span>&#60;&nbsp;Cancel</span></a>';
		echo '</div>';	
	} else {
		echo '<div class="panel">';
		echo '<a href="super_user_list.php" id="return"><span>&#60;&nbsp;Return</span></a>';
		echo '</div>';
	}
	
}
	include("includes/admin_pagefooter.php");
	
?>