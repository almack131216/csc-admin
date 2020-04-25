<?php

$page_title = "Remove Category From List";
$page_subtitle = "";
$curr_page = "catalogue";
include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if(notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {
	
	if ( empty($_POST['arr_id']) && !isset($_POST['delete'])  ) {
		echo '<p class="error">Selection not recognised. Please try again</p>';
	}	
	
	//////////////////////////////////////////////////////
	/////////// items selected via admin_catalogue_all.php
	if(isset($_POST['editmarked']) && isset($_POST['arr_id']) ){
		$post_success = 1;
		$arr_test = $_POST['arr_id'];
		$$new_status = $_POST['$new_status'];
		//print_r($arr_test);	
		
		$arr_id = array();
		
		for($tmpcount=0;$tmpcount<count($arr_test);$tmpcount++){
			$arr_test_query = "UPDATE $db_clientTable_catalogue SET status='$new_status' WHERE id='$arr_test[$tmpcount]' LIMIT 1";
			$arr_test_result = $db->mysql_query_log($arr_test_query);
			if($arr_test_result && $tmprow = mysql_fetch_row($arr_test_result)){					
				$arr_id[] = $tmprow[0];
			}else{
				$post_success = 0;
			}			
		}		
		
		if($post_success == 1){
			echo '<p class="good">All items selected were successfully updated... See these items below.</p>';
		}else{
			echo '<p class="error">Not all items selcted were updated(see below). Please try again.<br/>';
			echo 'If you continue to experience difficulties then '.show_contact_admin().'</p>';
		}
		
	}
	
	
	if( !empty($arr_id) ){		
	
		echo '<div class="panel">';
		echo '<table width="100%" align="center" cellpadding="5" cellspacing="0" border="1">';
		echo '<tr class="table_titles"><td width="10%"><strong>Preview</strong></td><td width="60%"><strong>Details</strong></td><td width="20%"><strong>Online Status</strong></td><td width="10%" align="center"><strong>Uploaded</strong></td></tr>';
		
		
				
		for($tmp_itemcount=0;$tmp_itemcount<count($arr_id);$tmp_itemcount++) {
			$arr_id_curr	= $arr_id[$tmp_itemcount];
			$item_query	= "SELECT * FROM $db_clientTable_catalogue WHERE id='$arr_id_curr' LIMIT 1";
			$item_result	= mysql_query($item_query);
			$ret_array		= mysql_fetch_array($item_result);				
			$my_id			= $ret_array['id'];
			
			$my_image		 	= $siteroot.$gp_uploadPath['thumbs'].$ret_array['image_large'];
			$my_image_large 	= $siteroot.$gp_uploadPath['large'].$ret_array['image_large'];
			$my_image_thumb 	= $siteroot.$gp_uploadPath['thumbs'].$ret_array['image_large'];
			$my_filename		= $ret_array['image_large'];										

			
			$rowcolor = $CMSShared->GetRowColor($tmp_itemcount,$colors);					
			
			echo '<tr class="body_general" bgcolor='.$rowcolor.'>';					
			
			// (just image) echo '<td align="center" valign="middle"><img src="' . $siteroot.$gp_uploadPath['thumbs'] . $ret_array['image_large'] .'"></td>';
			echo '<td align="center">';
			echo $CMSImages->GetThumb($my_image_large, $my_image_thumb, $my_filename, "true");
			echo '</td>';
			echo '<td>'.$ret_array['name'].'</td>';
			echo '<td>';
			
			$onlinestatus = get_statusname($ret_array['status']);
			switch($ret_array['status']){
				case 1:	echo '<span class="body_good">'.$onlinestatus.'</span>';break;
				case 2: echo '<span class="body_error">'.$onlinestatus.'</span>';break;
				default: echo $onlinestatus;break;
			}					
			echo '</td>';

			echo '<td align="center">'.$CMSTextFormat->FormatDate($ret_array['upload_date'],"cms").'</td></tr>';

		}
		
		echo '</table>';
		echo '</div>';
		
	}


	echo '<div class="panel">';	
		if(isset($tmp_itemcount)){	
			echo '<a href="admin_catalogue_all.php" id="cancel"><span>&#60;&nbsp;Cancel</span></a>';
		}else{
			echo '<a href="admin_catalogue_all.php" id="return"><span>&#60;&nbsp;Return</span></a>';
		}
	echo '</div>';
	

}
	include("includes/admin_pagefooter.php");
	
?>