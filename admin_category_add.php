<?php

if(!empty($_REQUEST['thisList'])){
	$thisList = $_REQUEST['thisList'];
	switch($thisList){
				
		case "catalogue_cats":
			$curr_page		= "catalogue";
			$curr_page_sub	= "categories_list";
			$ReferToAs = "category";		
			break;
			
		case "catalogue_subcats":
			$curr_page		= "catalogue";
			$curr_page_sub	= "categories_list";
			$ReferToAs = "sub-category";
			$IsSubCategory = true;
			break;
	}
}

if($_REQUEST['editid']){
	$editid = $_REQUEST['editid'];
	$page_title = "Edit ".$ReferToAs;
	$page_subtitle	= "Edit this $ReferToAs";
}else{
	$page_title = "Add ".$ReferToAs;
	$page_subtitle	= "Add a $ReferToAs";
}
if(isset($_REQUEST['extranet'])) $categoryExtranet = $_REQUEST['extranet']; //for sub-category only
if($_REQUEST['category']) $categoryID = $_REQUEST['category']; //for sub-category only
if(isset($_REQUEST['orderby'])) $cust_orderby = $_REQUEST['orderby']; //for sub-category only
if(isset($_REQUEST['template'])) $cust_template = $_REQUEST['template']; //for sub-category only
if(isset($_REQUEST['status'])) $cust_status = $_REQUEST['status']; //for sub-category only
if(isset($_REQUEST['page'])) $cust_page = $_REQUEST['page']; //for sub-category only
if($_REQUEST['PrevPageCategory']) $PrevPageCategory = $_REQUEST['PrevPageCategory'];
if($_REQUEST['PrevPage']) $PrevPage = $_REQUEST['PrevPage'];

include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle($page_title);
$BuildPage .= $PageBuild->AddPageTip($page_subtitle);
$BuildPage .= $PageBuild->AddThickbox();
$BuildPage .= $PageBuild->AddTag('forms.js');
$BuildPage .= $PageBuild->AddTag('templateSelect.css');
if(!$editid) $BuildPage .= $PageBuild->AddTag('forms_clickclear.js');
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
} else {	

	if(!gp_enabled('add_subcategory') && !$_REQUEST['editid']) exit();
	if($thisList){
		if($thisList=="catalogue_cats"){
			$fieldname = "category";
			$tablename = $db_clientTable_catalogue_cats;
		}elseif($IsSubCategory){
			$fieldname = "subcategory";
			$tablename = $db_clientTable_catalogue_subcats;								
		}		
		
		
		if($editid){
			if($_GET['success']) echo '<p class="good">Successfully updated</p>';
						
			$getname_query 	= "SELECT * FROM $tablename WHERE id=$editid LIMIT 1";

			$getname_result = mysql_query($getname_query);
			if($getname_result && mysql_num_rows($getname_result) >= 1){
				$getname_row 	= mysql_fetch_array($getname_result);
				$categoryName	= $getname_row[$fieldname];				
				
				if(!$IsSubCategory){
					$categoryExtranetOld = $getname_row['extranet'];
				}else{
					$categoryIDOLD = $getname_row['category'];
					$cust_orderbyOLD = $getname_row['orderby'];
					$cust_templateOLD = $getname_row['template'];
					$cust_statusOLD = $getname_row['status'];
					$cust_pageOLD = $getname_row['page'];
					if(!$categoryID) $categoryID = $categoryIDOLD;										
				}
			}else{
				$error = true;
				echo '<p class="error">This page has not been accessed properly. Please try again</p>';
			}
			//echo '<br>(FB):'.$thisList.'/'.$categoryID.'/'.$categoryIDOLD;
		}else{
			$cust_statusOLD = 1;
			$add_position_query		= "SELECT * FROM $tablename";
			if($IsSubCategory) $add_position_query.=" WHERE category=$categoryID";
			$add_position_result	= mysql_query($add_position_query);
			$add_position = 0;
			if($add_position_result && mysql_num_rows($add_position_result)>=1){
				$add_position = mysql_num_rows($add_position_result);
			}
			$add_position++;
			//echo '<br/>(FB):'.$add_position;
			$categoryName = $gp_defVal_mixbag['categoryname'];
		}
		

		//////////////////////////////////////
		/////// UPDATE or INSERT INTO DATABASE
		/////// the cancel button needs to know how many steps back to take
		/////// (1 will be added with each update to avoid cancelling back to previous amend)
			
		//////////////////////////// check for name (not required)
		if ( !empty($_POST['categoryName']) && $_POST['categoryName']!=$gp_defVal_mixbag['categoryname'] ) { // (2)				
			$categoryNameNew = $CMSTextFormat->stripCrap2_in($_POST['categoryName']);
			$categoryNameNew = stripslashes($categoryNameNew);
				
			//if($categoryName==$categoryNameNew && (!$IsSubCategory || ($IsSubCategory && $categoryID==$categoryIDOLD)) ){ // (3): IF name is unchanged
			//	echo '<p class="error">'.$ReferToAs.' name has not changed</p>';					
			//}else{ // (3): IF name is unchanged (ELSE)
				
				$duplicate_query	= "SELECT * FROM $tablename WHERE id!=$editid AND $fieldname='$categoryNameNew'";
				//if(!$IsSubCategory) $duplicate_query .= " AND extranet=$categoryExtranet";
				if($IsSubCategory) $duplicate_query .= " AND category=$categoryID";
				$duplicate_query .= " LIMIT 1";
				//echo '<br>(FB): DUPLICATE?:'.$duplicate_query;
				$duplicate_result	= mysql_query($duplicate_query);
							
				if($duplicate_result && mysql_num_rows($duplicate_result) >= 1){ // (4): IF name already exists in database
					echo '<p class="error">This name is already taken. Please try again</p>';
				}else{ // (4): ELSE
					
					$JumpToPage	= "Location: ".$adminroot."admin_category_add.php?success=true&thisList=".$thisList."&editid=";
					
					if($editid){ // (5): IF editing (not adding)
							
						$update_query = "UPDATE $tablename SET $fieldname='$categoryNameNew'";
						if(!$IsSubCategory && (isset($categoryExtranet) && $categoryExtranet!=$categoryExtranetOld)) $update_query .= ", extranet=$categoryExtranet";
						if($IsSubCategory && $categoryID!=$categoryIDOLD){
							$update_query .= ", category=$categoryID";
							$UpdateItemCategory=true;
						}
						if($IsSubCategory && $cust_orderbyOLD!=$cust_orderby) $update_query .= ", orderby='$cust_orderby'";
						if($IsSubCategory && $cust_templateOLD!=$cust_template) $update_query .= ", template='$cust_template'";
						if($IsSubCategory && $cust_statusOLD!=$cust_status) $update_query .= ", status='$cust_status'";
						if($IsSubCategory && $cust_pageOLD!=$cust_page) $update_query .= ", page='$cust_page'";
						
						$update_query .= " WHERE id=$editid";
						$update_result = $db->mysql_query_log($update_query);
						if($update_result && mysql_affected_rows()==1){
							//$uid = $editid;
							if($UpdateItemCategory){
								$itemQuery = "UPDATE $db_clientTable_catalogue SET category=$categoryID WHERE subcategory=$editid AND category=$categoryIDOLD";
								$itemResult = $db->mysql_query_log($itemQuery);
							}
							header($JumpToPage.$editid);
						}else{
							echo '<p class="error">Failed to update database... please try again</p>';
							//echo '<br>'.$update_query;
						}
												
					}else{ // (5): ELSE (IF ADDING)
						
						if(isset($_POST['add_position'])){
							$add_position = $_POST['add_position'];
						}else{
							$add_position = "999";
						}
						
						if($IsSubCategory){								
							$query = "INSERT into $tablename (category,position,position_incat,$fieldname,orderby,status) VALUES ('$categoryID','$add_position','$add_position','$categoryNameNew','$cust_orderby','1')";
						}else{
							$query = "INSERT into $tablename (position,$fieldname,extranet,status) VALUES ('$add_position','$categoryNameNew','$categoryExtranet','1')";
						}
						$result = $db->mysql_query_log($query);
						
						
						if($result){
							$uid = mysql_insert_id();
							header ($JumpToPage.$uid);
						}else{
							echo '<p class="error">Failed to update database... please try again</p>';
							echo $query;
						}				
		
					} // (5): END (IF EDITING OR ADDING)						
					
				} // (4): END
				
			//} // (3)				
			
		} else { // (2): ELSE
			if(isset($_POST['categoryName'])) echo '<p class="error">Failed to update database. Name has not been given... please try again</p>';
		} // (2): END
	
	
	$BuildForm = '<form action="'.$_SERVER['PHP_SELF'].'" enctype="multipart/form-data" method="POST" name="UploadForm">';
	if(!$error){
		
			$BuildForm .= '<div class="panel_oneline">';			
				if($editid){
					$BuildForm .= '<p><span class="steptitle">Rename this '.$ReferToAs.':</span></p>';
				}else{
					$BuildForm .= '<p><span class="steptitle">Submit new '.$ReferToAs.':</span></p>';
				}
				$BuildForm .= '<div class="inner_right">';
					$BuildForm .= '<input type="text" name="categoryName" value="'.$categoryName.'"/>';				
				$BuildForm .= '</div>';			
			$BuildForm .= '</div>';
			
			/////////////////////////////////////
			//IF WE ARE EDITING A SUB-CATEGORY...
			if(!$IsSubCategory){
				/*
				$BuildForm .= '<div class="panel_oneline">';
					$BuildForm .= '<p><span class="steptitle">Is this category an extranet?:</span></p>';
					$BuildForm .= '<div class="inner_right">';
						$BuildForm .= '<input type="radio" name="extranet" class="radio" value="1"';
						if($categoryExtranetOld==1) $BuildForm .= ' checked';
						$BuildForm .= '/>Yes';
						$BuildForm .= '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="extranet" class="radio" value="0"';
						if($categoryExtranetOld==0) $BuildForm .= ' checked';
						$BuildForm .= '/>NO';				
					$BuildForm .= '</div>';			
				$BuildForm .= '</div>';
				*/
			}else{
				$BuildForm .= '<div class="panel_oneline">';			
					if($editid){
						$BuildForm .= '<p><span class="steptitle">This '.$ReferToAs.' belongs to parent category:</span></p>';
					}else{
						$BuildForm .= '<p><span class="steptitle">Assign parent category:</span></p>';
					}
					$BuildForm .= '<div class="inner_right">';
					$ListPropsArr = array('name'=>'category','query'=>"SELECT * FROM $db_clientTable_catalogue_cats ORDER BY $gp_category_orderby $gp_category_orderby_desc",'dbTable_field'=>'category','query_qty'=>"SELECT * FROM $db_clientTable_catalogue WHERE status=1 AND id_xtra=0 AND category=",'selected'=>$categoryID,'adding'=>true);
					$BuildForm .= $CMSSelectOptions->Build($ListPropsArr);
					$BuildForm .= '</div>';			
				$BuildForm .= '</div>';
				
				// ORDER BY
				$BuildForm .= '<div class="panel_oneline">';			
					$BuildForm .= '<p><span class="steptitle">Order '.$ReferToAs.' by:</span></p>';
					$BuildForm .= '<div class="inner_right">';
					$ListPropsArr = array('name'=>'orderby','array'=>$gp_array_subcategory_orderby,'selected'=>$cust_orderbyOLD);
					$BuildForm .= $CMSSelectOptions->Build($ListPropsArr);
					$BuildForm .= '</div>';			
				$BuildForm .= '</div>';
				
				// ORDER BY
				
				$BuildForm .= '<div class="panel_oneline">';			
					$BuildForm .= '<p><span class="steptitle">Show / Hide '.$ReferToAs.' this sub-category:</span></p>';
					$BuildForm .= '<div class="inner_right">';
					$ListPropsArr = array('name'=>'status','query'=>"SELECT * FROM $db_shared.catalogue_status WHERE id<=1 ORDER BY id asc",'dbTable_field'=>'status','selected'=>$cust_statusOLD);
					$BuildForm .= $CMSSelectOptions->Build($ListPropsArr);
					$BuildForm .= '</div>';			
				$BuildForm .= '</div>';
				/*
				// TEMPLATE
				$BuildForm .= '<div class="panel_oneline">';			
					$BuildForm .= '<p><span class="steptitle">Choose template layout for this '.$ReferToAs.': </span></p>';

					$BuildForm .= '<ul class="templateSelect">';
					for($i=0;$i<sizeof($gp_array_subcategory_template);$i++){//sizeof($gp_array_subcategory_template)
						$TemplateImg = "layout/Template_".str_pad($i+1, 2, '0', STR_PAD_LEFT).".jpg";
						
						if($gp_array_subcategory_template[$i]['value'] == $cust_templateOLD){
							$BuildForm .= '<li class="thumb_selected">';
						}else{
							$BuildForm .= '<li>';
						}
						//$BuildForm .= '<img class="thumb" src="thumb.php?file='.$TemplateImg.'&size=140&quality=80&nocache=0" alt="'.$gp_array_subcategory_template[$i]['title'].'">';
						$BuildForm .= '<img class="thumb" src="timthumb.php?src='.$TemplateImg.'&w=140&q=80" alt="'.$gp_array_subcategory_template[$i]['title'].'">';
						$BuildForm .= $CMSAddOns->Thickbox("filePreview",$TemplateImg,$gp_array_subcategory_template[$i]['title'],"");
						$BuildForm .= '<input type="radio" class="radio" name="template" value="'.$gp_array_subcategory_template[$i]['value'].'"';
						if($gp_array_subcategory_template[$i]['value'] == $cust_templateOLD) $BuildForm .= ' checked';
						$BuildForm .= '><br>';
						$BuildForm .= $gp_array_subcategory_template[$i]['title'];
						$BuildForm .= '</li>';
					}
					$BuildForm .= '</ul>';
					//$ListPropsArr = array('name'=>'template','array'=>$gp_array_subcategory_template,'selected'=>$cust_templateOLD);
					//$BuildForm .= $CMSSelectOptions->Build($ListPropsArr);
					//$BuildForm .= '</div>';			
				$BuildForm .= '</div>';
				
				// PAGE (if using custom page)
				$BuildForm .= '<div class="panel_oneline">';			
					$BuildForm .= '<p><span class="steptitle">Custom page?</span><br>Leave blank to use standard page.<br>If new page is required, please contact support.</p>';
					$BuildForm .= '<div class="inner_right">';
					$BuildForm .= '<input type="text" name="page" value="'.$cust_pageOLD.'"/>';
					$BuildForm .= '</div>';			
				$BuildForm .= '</div>';
				*/

			}
		}
		
		
		//FINAL STEP: SUBMIT
		$BuildForm .= '<div class="panel_oneline">';
			//$BuildForm .= '<p><span class="steptitle">Process item:</span>';
			$BuildForm .= '<div class="inner_right">';
				if(!$error){
					$BuildForm .= '<input type="submit" id="submit" name="submit" value="Submit '.$ReferToAs.'">';
				}
				if($editid){
					$BuildForm .= '<input type="hidden" name="editid" value="'.$editid.'"/>';
				}else{
					$BuildForm .= '<input type="hidden" name="add_position" value="'.$add_position.'"/>';
				}

				//$BuildForm .= '<a href="admin_category_list.php?thisList='.$thisList.'&category='.$PrevPageCategory.'" id="return"><span>&#60;&nbsp;Return</span></a>';

				$BuildForm .= '<input type="hidden" name="thisList" value="'.$thisList.'">';
				//$BuildForm .= '</fieldset>';
			$BuildForm .= '</div>';
		$BuildForm .= '</div>';
		
		$BuildForm .= '</form>';
		echo $BuildForm;
		
		if($fieldname=="category" && gp_enabled("add_category")){
			$SubNavInner = '<ul id="SubNavInner">';
			$SubNavInner .= '<li class="add"><a href="admin_category_add.php?thisList='.$thisList.'">add new category</a></li>';		
			$SubNavInner .= '</ul>';
			echo $SubNavInner;
		}
		
		if($fieldname=="subcategory" && gp_enabled("add_subcategory")){
			$SubNavInner = '<ul id="SubNavInner">';
			$SubNavInner .= '<li class="add"><a href="admin_category_add.php?thisList='.$thisList.'&category='.$categoryID.'">add new sub-category</a></li>';		
			$SubNavInner .= '</ul>';
			echo $SubNavInner;
		}
		
	}else{
		
	}

}
	include("includes/admin_pagefooter.php");
	
?>

