<?php

$page_title = "User List";
$page_subtitle = "Add / Set User Options";
$curr_page = "superadmin";

include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if( notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {	

	
	$tablename = "users";
		

	//////////////////
	/// CHANGE STATUS
	if ( (isset($_GET['user_show'])) && (!empty($_GET['user_show'])) || (isset($_GET['user_hide'])) && (!empty($_GET['user_hide'])) ) {			
			
		if (isset($_GET['user_show'])) {
			$user_show		= $_GET['user_show'];				
			$move_query		= "UPDATE $tablename SET status='1' WHERE cid='$user_show'";
			$move_result	= $db->mysql_query_log($move_query);	
				
		} else if (isset($_GET['user_hide'])) {
			$user_hide	= $_GET['user_hide'];				
			$move_query		= "UPDATE $tablename SET status='0' WHERE cid='$user_hide'";
			$move_result	= $db->mysql_query_log($move_query);			

		}
	
	}		

	///////////////////
	/// START PAGE HTML	
	$getusers_query		= "SELECT * FROM $tablename ORDER BY registration_DATE";
	$getusers_result	= mysql_query($getusers_query);
	if($getusers_result){
		$getusers_numrows 	= mysql_num_rows($getusers_result);	
		$tmp_numrows		= $getusers_numrows;
	}
	
	// check table contents
	if ((!empty($getusers_numrows)) && ($getusers_numrows >= 1) ) {
		tmp_addlink();
		echo '<div class="panel">';
		echo '<strong>Delete / Edit Users:</strong> Set User options in the drop-down lists provided<br/><br/>';
		echo '<table width="100%" border="1" cellpadding="0" cellspacing="0">';
		echo '<tr class="table_titles">';
		echo '<td align="center" width="7%"><strong>Cid</strong></td>';
		echo '<td width="13%"><strong>Registered</strong></td>';
		echo '<td width="20%"><strong>Name</strong></td>';
		echo '<td width="10%"><strong>Status</strong></td>';
		echo '<td width = "30%"><strong>Status Notes</strong></td>';
		echo '<td width = "20%"><strong>Actions</strong></td></tr>';
		
		for($tmpcount = 1;$tmpcount <= $getusers_numrows;$tmpcount++) {
			
			$getusers_row		= mysql_fetch_array($getusers_result);
			// coloured rows
			$rowcolor = $CMSShared->GetRowColor($tmpcount,$colors);
			
			echo '<tr class="body_general" bgcolor='.$rowcolor.'>';
			echo '<td align="center">'.$getusers_row['cid'].'</td>';
			echo '<td>'.$CMSTextFormat->FormatDate($getusers_row['registration_DATE'],"cms").'</td>';
			echo '<td>'.$getusers_row['FirstName'].'&nbsp;'.$getusers_row['Surname'].'</td>';
			echo '<td>'.show_status($getusers_row['status'],"user").'</td>';
			echo '<td>'.$CMSTextFormat->stripCrap2_out_body($getusers_row['status_details']).'</td>';
			echo '<td>';
					
			if($getusers_row['status']==1){
				echo '<a href="'.$_SERVER['PHP_SELF'].'?user_hide='.$getusers_row['cid'].'"><img src="includes/icons/icon_hide.gif" title="Hide user" alt="Hide user"></a>';
			}else{
				echo '<a href="'.$_SERVER['PHP_SELF'].'?user_show='.$getusers_row['cid'].'"><img src="includes/icons/icon_show.gif" title="Show user" alt="Show user"></a>';
			}			
			echo '&nbsp;<a href="'.$getusers_row['website'].'" target="_blank"><img src="includes/icons/icon_item_preview.gif" title="Preview user" alt="Preview user"></a>';
			echo '&nbsp;<a href="super_user_add.php?editid='.$getusers_row['cid'].'"><img src="includes/icons/icon_item_edit.gif" title="Rename Category" alt="Rename Category"></a>';
			//echo '&nbsp;<a href="super_user_delete.php?currentuser='.$getusers_row['cid'].'"><img src="includes/icons/icon_item_delete.gif" title="Delete user" alt="Delete user"></a>';
			echo '</td></tr>';

		}
		echo '</table>';
		echo '</div>';
		
	} else { // if table is empty
		echo '<p class="prompt">There are currently no users listed in your database</p>';
		tmp_addlink();
		
	}
	

}
include("includes/admin_pagefooter.php");
	
?>