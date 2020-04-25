<?php
if($_REQUEST['param']) $GenericProps = $_REQUEST['param'];
$feedback = '';
if(isset($GenericProps['deleteid'])){
	$deleteid = $GenericProps['deleteid'];
	$prefArrayName = $GenericProps['prefArrayName'];
	$itemTitle = $GenericProps['itemTitle'];
	//$tablename = $GenericProps['tablename'];		

	//$field_id = $GenericProps['field_id'];
	
	$curr_page = $GenericProps['curr_page'];
	$curr_page_sub = $GenericProps['curr_page_sub'];
	
	$page_title		= $GenericProps['page_title'];
	$page_subtitle	= $GenericProps['page_subtitle'];
	
	$PrevPage = $GenericProps['PrevPage'];
	$PrevPage2 = $GenericProps['PrevPage2'];
	
}elseif($_POST['my_item_id']){
	$deleteid = $_POST['my_item_id'];
	$tablename = $_REQUEST['tablename'];
	$prefArrayName = $_REQUEST['prefArrayName'];		

	//echo '<br>(FB)table:'.$tablename.'<br>';
	$field_id = $_REQUEST['field_id'];
	$itemTitle = $_REQUEST['itemTitle'];
	$curr_page = $_REQUEST['curr_page'];
	$curr_page_sub = $_REQUEST['curr_page_sub'];
	
	$page_title		= $_REQUEST['page_title'];
	$page_subtitle	= $_REQUEST['page_subtitle'];
	
	$PrevPage = $_REQUEST['PrevPage'];
	$PrevPage2 = $_REQUEST['PrevPage2'];
}
if(empty($PrevPage2)) $PrevPage2 = $PrevPage;

include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle($page_title);
$BuildPage .= $PageBuild->AddPageTip($page_subtitle);
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if(notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {					
	
	if($prefArrayName) $prefArray = ${$prefArrayName};
	$tablename = $prefArray['tablename'];
	$field_id = $prefArray['id']['field'];
	if(empty($tablename)) exit();
	
	$FULLtablename = $db_client.".".$tablename;
	
	
	
	if (isset($_POST['my_item_id']) ) {		
		// get name of item
		$getname_query 	= "SELECT * FROM $FULLtablename WHERE $field_id='$deleteid' LIMIT 1";
		$getname_result = mysql_query($getname_query);
		
		if (!empty($itemTitle) && mysql_num_rows($getname_result) >= 1){
			// delete query
			$delete_query = "DELETE FROM $FULLtablename WHERE $field_id='$deleteid' LIMIT 1";
			$delete_result = $db->mysql_query_log($delete_query);		
	
			// print results
			if ($delete_result) {
				$feedback .= '<p class="good">Item <strong>"'.$itemTitle.'"</strong> was successfully deleted</p>';
			}else{
				$feedback .= '<p class="error">Could not delete this '.$CommonCustomWords['item'].'</p>';
			}
			
		}else{
			$feedback .= '<p class="error">Item could not be found or has already been removed</p>';
		}
		echo $feedback;
		
	} else {
		
		$BuildSelectList = '';
		if (!empty($deleteid)) {
			
			// get name of item
			$getname_query 	= "SELECT * FROM $FULLtablename WHERE $field_id='$deleteid' LIMIT 1";
			$getname_result = mysql_query($getname_query);
			
			if($getname_result && mysql_num_rows($getname_result)>=1){
				
				$BuildSelectList .= '<div class="panel_warning">';
					$BuildSelectList .= '<p>Selected: <strong>'.$itemTitle.'</strong><br/>You are about to delete this '.$CommonCustomWords['item'].'?<br/><strong>Once deleted, this cannot be undone!</strong></p>';
					$BuildSelectList .= '<div class="inner_right">';
						$BuildSelectList .= '<form name="UploadForm" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
						$BuildSelectList .= '<input type="hidden" name="my_item_id" value="'.$deleteid.'">';
						//$BuildSelectList .= '<input type="hidden" name="tablename" value="'.$tablename.'">';
						$BuildSelectList .= '<input type="hidden" name="prefArrayName" value="'.$prefArrayName.'">';
						//$BuildSelectList .= '<input type="hidden" name="field_id" value="'.$field_id.'">';
						$BuildSelectList .= '<input type="hidden" name="itemTitle" value="'.$itemTitle.'">';
						$BuildSelectList .= '<input type="hidden" name="curr_page" value="'.$curr_page.'">';
						$BuildSelectList .= '<input type="hidden" name="curr_page_sub" value="'.$curr_page_sub.'">';
						$BuildSelectList .= '<input type="hidden" name="page_title" value="'.$page_title.'">';
						$BuildSelectList .= '<input type="hidden" name="page_subtitle" value="'.$page_subtitle.'">';
						
						$BuildSelectList .= '<input type="hidden" name="PrevPage2" value="'.$PrevPage2.'">';
						$BuildSelectList .= '<input type="submit" id="delete" name="delete">';
						$BuildSelectList .= '</form>';
					$BuildSelectList .= '</div>';
				$BuildSelectList .= '</div>';
				
			} else {
				$BuildSelectList .= '<p class="error">Item could not be found or has already been removed</p>';
			}
		}else{
			$BuildSelectList .= '<p class="error">This page was not accessed properly. Please try again.</p>';
		}
		echo $BuildSelectList;
	
	}
	
	$BuildBackBut = '';
	if (isset($GenericProps['deleteid']) ) {
		
		$BuildBackBut .= '<div class="panel">';	
		$BuildBackBut .= '<a href="javascript:history.go(-1);" id="cancel"><span>&#60;&nbsp;Cancel</span></a>';
		$BuildBackBut .= '</div>';	
	} else {
		$BuildBackBut .= '<div class="panel">';
		$BuildBackBut .= '<a href="'.$PrevPage2.'" id="return"><span>&#60;&nbsp;Return</span></a>';
		$BuildBackBut .= '</div>';
	}
	echo $BuildBackBut;
	
}
	include("includes/admin_pagefooter.php");
	
?>