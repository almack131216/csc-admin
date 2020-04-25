<?php

$curr_page		= "members";
$curr_page_sub	= "members_list";	
$fieldname = "sectionID";


include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddTag('mootools.css');
$BuildPage .= $PageBuild->AddPageTitle("Delete Selection");
$BuildPage .= $PageBuild->AddPageTip("Delete Selection");
// third-party Image Rollover (ajax)
$BuildPage .= $PageBuild->AddTag('ImageTrail_tooltip.js');
$BuildPage .= $PageBuild->AddTag('ImageTrail_ajax.js');
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if(notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {	

	$Feedback = '';
	$FeedbackAppend = '';	

	$tablename = $db_clientTable_members;
	$tablename_items = $db_clientTable_members_access;
	$item_editPage = "admin_catalogue_upload.php";
	
	/*
	$FB = '';
	$FB .= '<br/>(FB)editmarked? : '.$_POST['editmarked'];
	$FB .= '<br/>(FB)deletemarked? : '.$_POST['deletemarked'];
	$FB .= '<br/>(FB)delete_batch? : '.$_POST['delete_batch'];
	$FB .= '<br/>(FB)arr_id? : '.print_r($_POST['arr_id']);
	echo $FB;
	*/	
	
	if($_POST['arr_id']){
		$arr_items = array();
		$arr_test = $_POST['arr_id'];				
		$arr_id = array();			
		for($tmpcount=0;$tmpcount < sizeof($arr_test);$tmpcount++){
			//echo '<br>arr_test:'.$arr_test;
			$tmp_id = $arr_test[$tmpcount];
			$query = "SELECT tm.* FROM $tablename AS tm WHERE tm.Id=$tmp_id LIMIT 1";
			//echo '<br/>QUERY '.$tmpcount.'='.$query;
			$result = mysql_query($query);
			if($result && $tmprow = mysql_fetch_array($result)){					
				//$tmprow = mysql_fetch_array($result);
				$arr_items[] = $tmprow;
			}					
		}
	}
	
	if(!empty($_POST['delete_batch'])){		
		if(sizeof($arr_items)>=1){					
			for($tmp_deletecount=0;$tmp_deletecount<count($arr_items);$tmp_deletecount++){ // FOR LOOP 1
				$CMSDelete->DeleteMember($arr_items[$tmp_deletecount]['Id']);
			} // END FOR 1	
		}
	}
	
	/*
	if(!empty($_POST['delete_single'])){		
		if(sizeof($arr_items)>=1){					
			for($tmp_deletecount=0;$tmp_deletecount<count($arr_items);$tmp_deletecount++){ // FOR LOOP 1
				$CMSDelete->DeleteMember($arr_items[$tmp_deletecount]['Id']);
			} // END FOR 1	
		}
	}
	*/
	
	if ( empty($_POST['arr_id']) ) {
		$Feedback .= '<p class="error">Selection not recognised. Please try again</p>';
	}

	if($JumpToPage || $tmp_deletecount>=1){
		if(!$JumpToPage) $JumpToPage = "?success=true";//FOR default batch deletes(not deleting a category / subcategory)

		$JumpToPageRoot = "Location: ".$adminroot."admin_member_list.php";
		$JumpToThisPage = $JumpToPageRoot.$JumpToPage;
		header($JumpToThisPage);
	}else{
		echo $Feedback;
	}
	
	/////////////////////////////////////////////
	/////////// items selected via admin_catalogue_all.php
	if( !empty($_POST['deletemarked']) && !empty($_POST['arr_id']) ){
		//echo '<br/>(FB): 1';
		$DeleteItemsArray = '';
		$DeleteItemsArray .= '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		$arr_test = $_POST['arr_id'];
		//print_r($arr_test);	
		
		$arr_id = array();			
		for($tmpcount=0;$tmpcount<count($arr_test);$tmpcount++){
			$arr_test_query = "SELECT tm.Id FROM $tablename AS tm WHERE tm.Id='$arr_test[$tmpcount]'";
			$arr_test_result = mysql_query($arr_test_query);
			if($arr_test_result && $tmprow = mysql_fetch_row($arr_test_result)){					
				$arr_id[] = $tmprow[0];
				$DeleteItemsArray .= '<input name="arr_id[]" type="hidden" value="'.$arr_id[$tmpcount].'" />';
			}
		}
		
		
		$DeleteItemsArray .= '<div class="panel_warning">';
			$DeleteItemsArray .= '<p>Delete members listed below?<br/><strong>This cannot be undone!</strong></p>';
			$DeleteItemsArray .= '<div class="inner_right">';		
				$DeleteItemsArray .= '<input type="submit" name="delete_batch" value="Delete Batch" title="Delete" id="delete">';
			$DeleteItemsArray .= '</div>';
		$DeleteItemsArray .= '</div>';
		$DeleteItemsArray .= '</form>';
		echo $DeleteItemsArray;
	}
	
	/*DISABLED FOR NOW
	//////////////////////////////////////////////////////
	/////////// items selected via admin_catalogue_all.php
	if((!empty($_POST['editmarked']) && !empty($_POST['arr_id'])) && (!empty($_POST['new_status']) || $_POST['new_status'] == 0) ){
		$post_success = 1;
		$arr_test = $_POST['arr_id'];
		$new_status = $_POST['new_status'];
		
		$arr_id = array();
		
		for($tmpcount = 0;$tmpcount < count($arr_test);$tmpcount++){
			$arr_update_query = "UPDATE $tablename_items SET status='$new_status' WHERE memberID='$arr_test[$tmpcount]'";
			$arr_update_result = $db->mysql_query_log($arr_update_query);
			if($arr_update_result){
				$arr_test_query = "SELECT * FROM $tablename_items WHERE memberID='$arr_test[$tmpcount]'";
				$arr_test_result = mysql_query($arr_test_query);
				if($arr_test_result){					
					$tmprow = mysql_fetch_row($arr_test_result);
					$arr_id[] = $tmprow[0];
				}
			}			
		}
		
		if($post_success == 1){
			$Feedback2 = '<p class="good">All items selected were successfully updated... See these items below.</p>';
		}else{
			$Feedback2 = '<p class="error">Not all items selcted were updated(see below). Please try again.<br/>';
			$Feedback2 .= show_contact_admin().' if you continue to experience difficulties.</p>';
		}
		echo $Feedback2;
	}
	*/
	
	if(!empty($arr_id) ){
		
		$BuildTitleRow = '';
		$BuildItemRow = '';
		
		$BuildTitleRow .= '<div class="panel">';
		$BuildTitleRow .= '<ul class="sortable-list-titles">';
			$BuildTitleRow .= '<li>';				
			$BuildTitleRow .= '<span class="BigName">Member\'s Name</span>';
			$BuildTitleRow .= '<span class="memberName">Email</span>';
			$BuildTitleRow .= '<span class="Date">Registered</span>';			
			$BuildTitleRow .= '</li>';
		$BuildTitleRow .= '</ul>';
		$BuildTitleRow .= '<ul class="sortable-list">';
		echo $BuildTitleRow;			
				
		for($tmp_itemcount = 0;$tmp_itemcount < count($arr_id);$tmp_itemcount++) {
			$arr_id_curr	= $arr_id[$tmp_itemcount];
			$item_query	= "SELECT tm.* FROM $tablename AS tm WHERE tm.Id='$arr_id_curr'";
			$item_result	= mysql_query($item_query);
			$ret_array		= mysql_fetch_array($item_result);				
			$my_id			= $ret_array['id'];				
			//echo '<br>(FB):'.$item_query;
			
			$rowcolor = $CMSShared->GetRowColor($tmp_itemcount,$colors);		
			$BuildItemRow .= '<li class="sortable-list" style="background:'.$rowcolor.'">';
			
			// NAME
			$BuildItemRow .= '<span class="BigName">'.$ret_array['title'].' '.$ret_array['fname'].' '.$ret_array['lname'].'</span>';
			
			// STATUS
			$BuildItemRow .= '<span class="memberName">'.$ret_array['email'].'</span>';
			
			// DATE
			$BuildItemRow .= '<span class="Date">'.$ret_array['registered'].'</span>';			
			$BuildItemRow .= '</li>';
			
		}			
			
		$BuildItemRow .= '</ul>';
		echo $BuildItemRow;
		echo '</div>';
	}

	$ReturnPanel = '';
	$ReturnPanel .= '<div class="panel">';
		$ReturnPanel .= '<a href="javascript:history.back()" id="return"><span>&#60;&nbsp;Return</span></a>';
	$ReturnPanel .= '</div>';
	echo $ReturnPanel;
	

}
	include("includes/admin_pagefooter.php");
	
?>