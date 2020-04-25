<?php

if(!empty($_REQUEST['thisList'])){
	$thisList = $_REQUEST['thisList'];
	switch($thisList){
				
		case "catalogue_cats":
			$curr_page		= "catalogue";
			$curr_page_sub	= "categories_list";	
			$fieldname = "category";
			break;
			
		case "catalogue_subcats":
			$curr_page		= "catalogue";
			$curr_page_sub	= "subcategories_list";	
			$fieldname = "subcategory";
			break;
	}
}

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
	
	if($thisList){
		$Feedback = '';
		$FeedbackAppend = '';
		
		if($thisList=="catalogue_cats"){
			$tablename = $db_clientTable_catalogue_cats;
			$tablename_items = $db_clientTable_catalogue;
			$item_editPage = "admin_catalogue_upload.php";
		}elseif($thisList=="catalogue_subcats"){
			$tablename = $db_clientTable_catalogue_subcats;
			$tablename_items = $db_clientTable_catalogue;
			$item_editPage = "admin_catalogue_upload.php";
		}
		
		$FB = '';
		$FB .= '<br/><br/>(FB)thisList? : '.$_POST['thisList'];
		$FB .= '<br/>(FB)editmarked? : '.$_POST['editmarked'];
		$FB .= '<br/>(FB)deletemarked? : '.$_POST['deletemarked'];
		$FB .= '<br/>(FB)ParentCategoryID? : '.$_POST['ParentCategoryID'];
		$FB .= '<br/>(FB)categoryID? : '.$_POST['categoryID'];
		$FB .= '<br/>(FB)subcategoryID? : '.$_POST['subcategoryID'];
		$FB .= '<br/>(FB)delete? : '.$_POST['delete'];
		$FB .= '<br/>(FB)delete_batch? : '.$_POST['delete_batch'];
		//$FB .= '<br/>(FB)arr_id? : '.print_r($_POST['arr_id']);
		//echo $FB;
		
		
		if (!empty($_REQUEST['categoryID'])) $categoryID = $_REQUEST['categoryID'];
		if (!empty($_REQUEST['subcategoryID'])) $categoryID = $_REQUEST['subcategoryID'];
		if (!empty($_REQUEST['ParentCategoryID'])) $ParentCategoryID = $_REQUEST['ParentCategoryID'];
		if($categoryID) $categoryName = get_category($categoryID,"name",$thisList);// get name of category
		
			
		
		if( !empty($categoryID) ) {			
			// count items for this category
			$arr_items = array();
			$item_query	= "SELECT * FROM $tablename_items WHERE $fieldname=$categoryID";
			$item_result	= mysql_query($item_query);
			if($item_result && mysql_num_rows($item_result)>=1){
				$tmpcount = 0;
				while(($item_result) && $tmpcount<mysql_num_rows($item_result)){		
					$tmprow = mysql_fetch_array($item_result);
					$arr_items[] = $tmprow;
					$tmpcount++;
				}
			}
		}elseif(empty($categoryID) && $_POST['arr_id']){
			$arr_items = array();
			$arr_test = $_POST['arr_id'];				
			$arr_id = array();			
			for($tmpcount=0;$tmpcount < count($arr_test);$tmpcount++){
				$tmp_id = $arr_test[$tmpcount];
				$query = "SELECT * FROM $tablename_items WHERE id=$tmp_id LIMIT 1";
				//echo '<br/>QUERY '.$tmpcount.'='.$query;
				$result = mysql_query($query);
				if($result && $tmprow = mysql_fetch_array($result)){					
					//$tmprow = mysql_fetch_array($result);
					$arr_items[] = $tmprow;
				}					
			}
		}
		
		if(isset($_POST['delete']) || isset($_POST['delete_batch'])) {		
			
			if(sizeof($arr_items)>=1){					
				//echo 'COUNT:'.count($arr_items);		
				for($tmp_deletecount=0;$tmp_deletecount<count($arr_items);$tmp_deletecount++){ // FOR LOOP 1
			
					$my_id = $arr_items[$tmp_deletecount]['id'];					
					$attributes = array('itemID'=>$my_id);
					$CMSDelete->DeleteItem($attributes);				
	
				} // END FOR 1	
			}
			
			
			if(!empty($categoryID) && !empty($categoryName)){
				$tmpQueries = '';
				
				if($thisList=="catalogue_cats"){
					$DeleteItemsQuery = "DELETE FROM $db_clientTable_catalogue_subcats WHERE category=$categoryID";
					$DeleteItemsResult = $db->mysql_query_log($DeleteItemsQuery);
					$tmpQueries.= '<br>sub-categories:'.$DeleteItemsQuery;
					if($DeleteItemsResult){
						$FeedbackAppend .= '<br>'.mysql_affected_rows().' sub-categories belonging to this category were successfully removed.';
					}
				}							
				
				if($tmp_deletecount) $FeedbackAppend .= '<br>'.$tmp_deletecount.' items belonging to this '.$fieldname.' were successfully removed.';
					
				// delete query
				$delete_query = "DELETE FROM $tablename WHERE id=$categoryID";
				$tmpQueries.= '<br>MAIN:'.$delete_query;
				echo $tmpQueries;
				$delete_result = $db->mysql_query_log($delete_query);
				//echo '<br/>(FB)delete_query: '.$delete_query ;
			
				// print results
				if($delete_result && !empty($categoryName)){
					$Feedback .= ucfirst($fieldname).' <strong>"'.$categoryName.'"</strong> was successfully deleted'.$FeedbackAppend;
					$JumpToPage = "?success=true&message=".$Feedback;
					
					//if($thisList=="catalogue_subcats" && $categoryID) $JumpToPage .= '&category='.$categoryID;
					if($ParentCategoryID){
						$JumpToPage .= '&thisList=catalogue_subcats&ParentCategoryID='.$ParentCategoryID;
					}else{
						$JumpToPage .= "&thisList=catalogue_cats";
					}					
					
				}else{
					$Feedback .= '<p class="error"><strong>'.$categoryName.'</strong> could not be deleted</p>';
				}
				
			}else{
				$Feedback .= '<p class="error">Selection could not be found or has already been removed</p>';
			}
		}
		
		
		if ( empty($_POST['arr_id']) && ( !isset($_POST['delete']) && (empty($categoryID) && (!isset($_REQUEST['categoryID'])) || (empty($categoryID) && !isset($_POST['arr_id']))) ) ) {
			$Feedback .= '<p class="error">Selection not recognised. Please try again</p>';
		}
	
		if($JumpToPage || $tmp_deletecount>=1){
			if(!$JumpToPage) $JumpToPage = "?thisList=catalogue_cats&success=true";//FOR default batch deletes(not deleting a category / subcategory)

			$JumpToPageRoot = "Location: ".$adminroot."admin_category_list.php";
			$JumpToThisPage = $JumpToPageRoot.$JumpToPage;
			header($JumpToThisPage);
		}else{
			echo $Feedback;
		}
		
		/////////////////////////////////////////////
		/////////// items selected via admin_catalogue_all.php
		if( !empty($_POST['deletemarked']) && isset($_POST['arr_id']) ){
			//echo '<br/>(FB): 1';
			$DeleteItemsArray = '';
			$DeleteItemsArray .= '<form action="admin_category_delete.php" method="post">';
			$DeleteItemsArray .= '<input type="hidden" name="thisList" value="'.$thisList.'">';
			if($ParentCategoryID) $DeleteItemsArray .= '<input type="hidden" name="ParentCategoryID" value="'.$ParentCategoryID.'">';
			$arr_test = $_POST['arr_id'];
			//print_r($arr_test);	
			
			$arr_id = array();			
			for($tmpcount=0;$tmpcount<count($arr_test);$tmpcount++){
				$arr_test_query = "SELECT * FROM $tablename_items WHERE id='$arr_test[$tmpcount]' LIMIT 1";
				$arr_test_result = mysql_query($arr_test_query);
				if($arr_test_result && $tmprow = mysql_fetch_row($arr_test_result)){					
					$arr_id[] = $tmprow[0];
					$DeleteItemsArray .= '<input name="arr_id[]" type="hidden" value="'.$arr_id[$tmpcount].'" class="tickbox" />';
				}
			}
			
			
			$DeleteItemsArray .= '<div class="panel_warning">';
				$DeleteItemsArray .= '<p>Delete ALL items listed below?<br/><strong>This cannot be undone!</strong></p>';
				$DeleteItemsArray .= '<div class="inner_right">';		
					$DeleteItemsArray .= '<input type="submit" name="delete_batch" value="submit" title="Delete" id="delete">';
				$DeleteItemsArray .= '</div>';
			$DeleteItemsArray .= '</div>';
			$DeleteItemsArray .= '</form>';
			echo $DeleteItemsArray;
		}
		
		//////////////////////////////////////////////////////
		/////////// items selected via admin_catalogue_all.php
		if(isset($_POST['editmarked']) && !empty($_POST['editmarked']) && !empty($_POST['arr_id']) && (!empty($_POST['new_status']) || $_POST['new_status'] == 0) ){
			$post_success = 1;
			$arr_test = $_POST['arr_id'];
			$new_status = $_POST['new_status'];
			
			$arr_id = array();
			
			for($tmpcount = 0;$tmpcount < count($arr_test);$tmpcount++){
				$arr_update_query = "UPDATE $tablename_items SET status='$new_status' WHERE id='$arr_test[$tmpcount]' LIMIT 1";
				$arr_update_result = $db->mysql_query_log($arr_update_query);
				if($arr_update_result){
					$arr_test_query = "SELECT * FROM $tablename_items WHERE id='$arr_test[$tmpcount]' LIMIT 1";
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
		
		if(!empty($arr_id) ){
			
			$BuildTitleRow = '';
			$BuildItemRow = '';
			
			$BuildTitleRow .= '<div class="panel">';
			$BuildTitleRow .= '<ul class="sortable-list-titles">';
				$BuildTitleRow .= '<li>';				
				$BuildTitleRow .= '<span class="Date">&nbsp;Date</span>';
				$BuildTitleRow .= '<span class="Name">Name</span>';
				$BuildTitleRow .= '<span class="Category">';				
				if($categoryID==0 || !gp_enabled('subcategory')){
					$BuildTitleRow .= 'Category';
				}else{
					$BuildTitleRow .= 'Sub-Category';
				}
				$BuildTitleRow .= '</span>';
				$BuildTitleRow .= '<span class="Status">Status</span>';
				$BuildTitleRow .= '<span class="Actions">Preview</span>';			
				$BuildTitleRow .= '</li>';
			$BuildTitleRow .= '</ul>';
			$BuildTitleRow .= '<ul class="sortable-list">';
			echo $BuildTitleRow;			
					
			for($tmp_itemcount = 0;$tmp_itemcount < count($arr_id);$tmp_itemcount++) {
				$arr_id_curr	= $arr_id[$tmp_itemcount];
				$item_query	= "SELECT * FROM $tablename_items WHERE id='$arr_id_curr' LIMIT 1";
				$item_result	= mysql_query($item_query);
				$ret_array		= mysql_fetch_array($item_result);				
				$my_id			= $ret_array['id'];				
				
				$rowcolor = $CMSShared->GetRowColor($tmp_itemcount,$colors);		
				$BuildItemRow .= '<li class="sortable-list" style="background:'.$rowcolor.'">';
				
				// DATE
				$BuildItemRow .= '<span class="Date">&nbsp;';
				$BuildItemRow .= $CMSTextFormat->FormatDate($ret_array['upload_date'],"cms");
				$BuildItemRow .= '</span>';
				
				// NAME
				$BuildItemRow .= '<span class="Name">'.$ret_array['name'];								
				if (!empty($my_price)) {
					$my_price = number_format($my_price,2);
					$BuildItemRow .= '<br/><strong>Price: </strong>&pound;'.$my_price.'&nbsp;';
					if ( isset($my_price_details) &&  $my_price_details != "" && !empty($my_price_details) ) {
						$BuildItemRow .= '<span class="prices_details">'.$my_price_details.'</span><br/>';
					}
				}				
				$BuildItemRow .= '</span>';
				
				// CATEGORY
				$BuildItemRow .= '<span class="Category">';
					$BuildItemRow .= get_category($ret_array['category'],"name",$thisList);
				$BuildItemRow .= '</span>';
				
				// STATUS
				$BuildItemRow .= '<span class="Status">';
				$onlinestatus = get_statusname($ret_array['status']);
				switch($ret_array['status']){
					case 1:	$BuildItemRow .= '<span class="body_good">'.$onlinestatus.'</span>';break;
					case 2: $BuildItemRow .= '<span class="body_error">'.$onlinestatus.'</span>';break;
					default: $BuildItemRow .= $onlinestatus;break;
				}					
				$BuildItemRow .= '</span>';
				
				// PREVIEW
				$BuildItemRow .= '<span class="Actions">';
				$BuildItemRow .= '<ul>';
				if($thisList=="catalogue_cats"){
					$my_price		= $ret_array['price'];
					$my_price_details = $ret_array['price_details'];
					
					$my_image_primary 	= $siteroot.$gp_uploadPath['primary'].$ret_array['image_large'];
					$filename	 	= $ret_array['image_large'];
					
					if($CMSShared->IsImage($filename)){
						$myRolloverPreview = $my_image_primary;	
					}else{
						$fileType = $CMSShared->GetFileType($filename);
						$myRolloverPreview = $CMSImages->GetFileIconHover($fileType);
					}
					$ImageDimensions = @getimagesize($myRolloverPreview);
					$BuildItemRow .= '<li><a href="'.$moreinfopage.'?uid='.$my_id.'" title="Preview item:&nbsp;'.$ret_array['name'].'" class="Preview" onmouseover="showtrail('.$ImageDimensions[0].','.$ImageDimensions[1].',\''.$myRolloverPreview.'\');" onmouseout="hidetrail();" target="_blank"><span>Preview</span></a></li>';
						
				}
						
				$BuildItemRow .= '<li><a href="'.$item_editPage.'?editid='.$my_id.'" class="Edit" title="Edit Item"><span>Edit Item</span></a></li>';
				$BuildItemRow .= '</ul>';
				$BuildItemRow .= '</span>';
				
				$BuildItemRow .= '</li>';
				
			}			
				
			$BuildItemRow .= '</ul>';
			echo $BuildItemRow;
			echo '</div>';
		}
	
		$ReturnPanel = '';
		$ReturnPanel .= '<div class="panel">';	
			if (isset($categoryID) ) {
				$ReturnPanel .= '<a href="admin_category_list.php?thisList='.$thisList.'" id="return"><span>&#60;&nbsp;Return</span></a>';
			} else {
				$ReturnPanel .= '<a href="javascript:history.back()" id="return"><span>&#60;&nbsp;Return</span></a>';
			}
		$ReturnPanel .= '</div>';
		echo $ReturnPanel;
		
	}else{
		
	}
	

}
	include("includes/admin_pagefooter.php");
	
?>