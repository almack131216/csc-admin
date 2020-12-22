<?php

if($_REQUEST['category']) $cust_category = $_REQUEST['category'];
if($_REQUEST['ParentCategoryID'] && $_GET['thisList']=="catalogue_subcats")	$cust_category = $_REQUEST['ParentCategoryID'];
if($_REQUEST['subcategory']) $cust_subcategory = $_REQUEST['subcategory'];

function init_thisList(){
	global $thisList,$curr_page,$curr_page_sub,$page_title,$BuildTip;
	global $fieldname,$thisPage,$previewOnlinePage,$moreinfopage,$siteroot;
	
	if(!empty($_GET['thisList'])){
		$thisList = $_GET['thisList'];

		$curr_page		= "catalogue";
		$curr_page_sub	= "categories_list";
		$page_title = "Pages &#124; Categories";
		$BuildTip = "Manage Categories";				
		$thisPage = $_SERVER['PHP_SELF']."?thisList=catalogue_cats";
		$previewOnlinePage = $moreinfopage;
	}
}

init_thisList();
include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle($page_title);
$tmpCatalogueData = $PageBuild->GetCatalogueData($cust_category,$cust_subcategory,"");
if($cust_category && $tmpCatalogueData['categoryName'])	$BuildTip .= ' within '.$tmpCatalogueData['categoryLink'];
$BuildPage .= $PageBuild->AddPageTip($BuildTip);
$BuildPage .= $PageBuild->AddTag('JumpForms.js');
$BuildPage .= $PageBuild->AddTag('mootools.css');
$BuildPage .= $PageBuild->AddTag(array('dir'=>'addingajax/','file'=>'addingajax.js'));
// third-party Image Rollover (ajax)
// $BuildPage .= $PageBuild->AddTag('ImageTrail_tooltip.js');
// $BuildPage .= $PageBuild->AddTag('ImageTrail_ajax.js');
include("includes/admin_pageheader.php");
include("includes/classes/CMSHelp.php");

imgDirsReset();
//init_thisList();

/////////// check to see if session is set
if( notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {
	$categoryForSale = 2;
	$categorySold = 999;
	$categoryPlates = 6;

	$q = "SELECT cc.id AS categoryId,cc.category AS categoryName FROM stemmvog_csc2.catalogue_cats AS cc ORDER BY cc.position ASC";
	$q = "SELECT cc.position,cc.id AS categoryId,cc.category AS categoryName FROM stemmvog_csc2.catalogue_cats AS cc UNION ALL SELECT cc2.position,cc2.id AS categoryId,cc2.category AS categoryName FROM stemmvog_csc2.catalogue_cats AS cc2 WHERE cc2.id=2 ORDER BY position ASC";
	$r = mysql_query($q);

	$catArr = [];

	if($r && mysql_num_rows($r)>1){
		$ccNumRows = mysql_num_rows($r);
		$tmp_numrows = $ccNumRows;	
		$my_price_total = 0;
		
		$DataBuild = '';
		$DataBuild .= '<div class="panel">';
		//$DataBuild .= '<p><span class="steptitle">Delete / Edit category:</span> Select an action from drop-down lists</p>';
		
		$DataBuild .= '<ul class="sortable-list-titles">';
			$DataBuild .= '<li>';			
			if(gp_enabled("price")) $DataBuild .= '<span class="Price">&pound;Stock</span>';
			//$DataBuild .= '<span class="Status">Status</span>';
			$DataBuild .= '<span class="Category">Categories</span>';
			$DataBuild .= '<span class="Name">Sub-Categories</span>';

			$DataBuild .= '<span class="Category">Items in Category</span>';
			$DataBuild .= '<span class="Actions">Actions</span>';
			$DataBuild .= '</li>';
		$DataBuild .= '</ul>';

		$DataBuild .= '<ul class="sortable-list">';
		
		for($i=1;$i<=$ccNumRows;$i++){
			$row = mysql_fetch_array($r);
			$categoryId = $row['categoryId'];
			$categoryName = $row['categoryName'];
			$itemCountInCategory = 0;

			if(!in_array($categoryId,$catArr)){
				$catArr[] = $categoryId;
			}else{
				$categoryId = $categorySold;
				$categoryName = '--- Classic Cars ARCHIVE';
			}

			$isClassified = false;
			if($categoryId==$categoryForSale || $categoryId==$categorySold) $isClassified = true;
				
			// coloured rows
			$rowColor = $CMSShared->GetRowColor($i,$colors);
			$DataBuild .= '<li id="Category_'.$categoryId.'" style="background:'.$rowColor.'">';
			
			if(gp_enabled("price")){
				$catTotal = $categoryId==$categoryForSale || $categoryId==$categoryPlates ? get_category($categoryId,"stock_value",$thisList) : null;
				$my_price_total += $catTotal;
				$DataBuild .= '<span class="Price">';
				if($catTotal) $DataBuild .= $CMSTextFormat->Price_StripDecimal($catTotal);
				$DataBuild .= '</span>';
			}
			
			$DataBuild .= '<span class="Category">';
			// $DataBuild .= '<a href="admin_category_add.php?thisList='.$thisList.'&editid='.$categoryId.'&PrevPageCategory='.$cust_category.'" class="Edit" title="Edit Category">'.$categoryName.'</a>';
			$DataBuild .= $categoryName;
			$DataBuild .= '</span>';
			$DataBuild .= '<span class="Name">';
			
			$q2 = "SELECT COUNT(c.id) AS itemCount, cc.id AS categoryId,cc.category AS categoryName,csc.id AS subcategoryId,csc.subcategory AS subcategoryName FROM stemmvog_csc2.catalogue AS c";
			$q2 .= " join stemmvog_csc2.catalogue_subcats AS csc ON csc.id=c.subcategory";
			$q2 .= " join stemmvog_csc2.catalogue_cats AS cc ON cc.id=c.category";			
			if($categoryId==$categorySold){
				$status = 2;
				$q2 .= " WHERE cc.id=2";
			}else{
				$status = 1;
				$q2 .= " WHERE cc.id=$categoryId";				
			}
			$q2 .= " AND c.status=$status";
			$q2 .= " GROUP BY csc.id ORDER BY cc.position ASC,csc.subcategory ASC";
			$r2 = mysql_query($q2);

			$DataBuild .= '<a href="javascript:OpenCloseSubcategory(\'subcategoryDiv'.$categoryId.'\');" id="subcategoryDiv'.$categoryId.'_Link" title="Show sub-categories for \''.$categoryName.'\'" class="subcategoryDivLink">Show / Hide</a>';
			// $DataBuild .= ' &#124; <a href="'.$_SERVER['PHP_SELF'].'?thisList=catalogue_subcats&category='.$categoryId.'" title="Manage sub-categories">organise</a>';
			
			$DataBuild .= '<span id="subcategoryDiv'.$categoryId.'" class="hidden">';
			$DataBuild .= '<ul class="subcategoryList">';
			for($sc=0;$sc<mysql_num_rows($r2);$sc++){
				$scRow = mysql_fetch_array($r2);
				$itemCount = $scRow['itemCount'];
				$itemCountInCategory += $itemCount;
				$categoryId = $scRow['categoryId'];
				$categoryName = $scRow['categoryName'];
				$subcategoryId = $scRow['subcategoryId'];
				$subcategoryName = $scRow['subcategoryName'];
				
				$DataBuild .= '<li>';					
				$DataBuild .= '<a href="admin_catalogue_all.php?status='.$status.'&category='.$categoryId.'&subcategory='.$subcategoryId.'" class="NoStyle">'.$itemCount.' items</a>';
				// if($isClassified) $DataBuild .= '&nbsp;&#124;&nbsp;<a href="admin_catalogue_all.php?status=2&category='.$categoryId.'&subcategory='.$subcategoryId.'" class="NoStyle">SOLD</a>';
				
				$DataBuild .= ' in ';
				// $DataBuild .= '<a href="admin_category_add.php?thisList=catalogue_subcats&editid='.$subcategoryId.'" title="Edit Sub-Category" class="NoStyle">'.$CMSTextFormat->ReduceString($subcategoryName,30).'</a>';
				$DataBuild .= '<strong>'.$CMSTextFormat->ReduceString($subcategoryName,30).'</strong>';
				$DataBuild .= '&nbsp;&#124;&nbsp;<a href="admin_catalogue_upload.php?category='.$categoryId.'&subcategory='.$subcategoryId.'" title="add item to this sub-category" class="NoStyle AddItem">add item</a>';
				$DataBuild .= '</li>';
				
			}
			//$DataBuild .= '<li></li>';
			$DataBuild .= '</ul>';
			$DataBuild .= '</span>';
			
			if(gp_enabled("add_subcategory")) $DataBuild .= ' &#124; <a href="admin_category_add.php?thisList=catalogue_subcats&category='.$categoryId.'" title="add sub-category to this category" class="NoStyle AddItem">add sub-cat.</a>';

			$DataBuild .= '</span>';
			
			$DataBuild .= '<span class="Category"><a href="admin_catalogue_all.php?status='.$status.'&category='.$categoryId.'">'.$itemCountInCategory.' items</a>';
			$DataBuild .= ' &#124; <a href="admin_catalogue_upload.php?category='.$categoryId.'" title="Add item to this category" class="NoStyle AddItem">add item</a>';
			$DataBuild .= '</span>';
			
			$DataBuild .= '<span class="Actions">';
				$DataBuild .= '<ul>';		

				$DataBuild .= '<li><a href="admin_category_add.php?thisList='.$thisList.'&editid='.$categoryId.'&PrevPageCategory='.$cust_category.'" class="Edit" title="Edit Category"><span>Edit</span></a></li>';				
				// if( ($thisList=="catalogue_cats" && gp_enabled("delete_category")) || ($thisList=="catalogue_subcats" && gp_enabled("delete_subcategory")) ){
				// 	$DataBuild .= '<li><a href="admin_catalogue_all.php?category='.$categoryId;
				// 	if($thisList=="catalogue_cats"){
				// 		$DataBuild .= '&deleteCategory='.$categoryId;
				// 	}elseif($thisList=="catalogue_subcats"){
				// 		$DataBuild .= '&subcategory='.$subcategoryId.'&deleteSubCategory='.$subcategoryId.'&ParentCategoryID='.$categoryId;
				// 	}
				// 	$DataBuild .= '" class="Delete" title="Delete Category"><span>Delete</span></a></li>';
				// }
				$DataBuild .= '</ul>';
				$DataBuild .= '</span>';
			$DataBuild .= '</li>';						
		}
		$DataBuild .= '</ul>';					
		
		// show Item Totals
		if(gp_enabled("price")){
			$DataBuild .= '<ul class="sortable-list-titles">';
				$DataBuild .= '<li>';
				if(gp_enabled("price"))	$DataBuild .= '<span class="Price">'.$CMSTextFormat->Price_StripDecimal($my_price_total).'</span>';
				$DataBuild .= '<span class="Status">&nbsp;</span>';
				$DataBuild .= '<span class="BigName">&nbsp;</span>';
				$DataBuild .= '<span class="Actions">&nbsp;</span>';
				$DataBuild .= '</li>';
			$DataBuild .= '</ul>';
		}
		$DataBuild .= '</div>';
		echo $DataBuild;						
	}
	/// END /// if($thislist)	
}
include("includes/admin_pagefooter.php");	
?>