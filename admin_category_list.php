<?php



if($_REQUEST['category'])			$cust_category		= $_REQUEST['category'];
if($_REQUEST['ParentCategoryID'] && $_GET['thisList']=="catalogue_subcats")	$cust_category		= $_REQUEST['ParentCategoryID'];
if($_REQUEST['subcategory'])		$cust_subcategory	= $_REQUEST['subcategory'];


function init_thisList(){
	global $thisList,$curr_page,$curr_page_sub,$page_title,$BuildTip;
	global $fieldname,$thisPage,$previewOnlinePage,$moreinfopage,$siteroot;
	
	if(!empty($_GET['thisList'])){
		$thisList = $_GET['thisList'];
		switch($thisList){
					
			case "catalogue_cats":
				$curr_page		= "catalogue";
				$curr_page_sub	= "categories_list";
				$page_title = "Pages &#124; Categories";
				$BuildTip = "Manage Categories";				
				$thisPage = $_SERVER['PHP_SELF']."?thisList=catalogue_cats";
				$previewOnlinePage = $moreinfopage;
				break;
				
			case "catalogue_subcats":
				$curr_page		= "catalogue";
				$curr_page_sub	= "subcategories_list";
				$page_title = "Pages &#124; Sub-Categories";
				$BuildTip = "Manage Sub-Categories";				
				$thisPage = $_SERVER['PHP_SELF']."?thisList=catalogue_subcats";
				$previewOnlinePage = $moreinfopage;
				break;
		}
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
$BuildPage .= $PageBuild->AddTag('ImageTrail_tooltip.js');
$BuildPage .= $PageBuild->AddTag('ImageTrail_ajax.js');
include("includes/admin_pageheader.php");
include("includes/classes/CMSHelp.php");
//init_thisList();

/////////// check to see if session is set
if( notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {	
	
	require_once('includes/loadCategories.php');
	if($thisList){
		
		if($_GET['success']){
			$SuccessBanner = '';
			$SuccessBanner .= '<p class="good">';
			if(!empty($_GET['message'])){
				$SuccessBanner .= stripslashes($_GET['message']);
			}else{
				$SuccessBanner .= 'Successfully updated';
			}
			$SuccessBanner .= '</p>';
			echo $SuccessBanner;
		}
		
		if($thisList=="catalogue_cats"){
			$fieldname = "category";
			$tablename = $db_clientTable_catalogue_cats;
		}elseif($thisList=="catalogue_subcats"){
			$fieldname = "subcategory";
			$tablename = $db_clientTable_catalogue_subcats;
		}
			
		//(DEBUG): reset_category_positions();
		//putCatNamesIntoArray();
		//SortableList
		$attributes = array('tablename'=>$tablename,'fieldname'=>$fieldname);
		$Categories = getCategories($attributes);//see 'includes/loadCategories.php'		
		
		if($fieldname=="subcategory"){
			
			if(!empty($cust_category)){
				$getcats_query	= "SELECT * FROM $tablename WHERE id!=0";
				$getcats_query .= " AND category=$cust_category";
				$sortableDetail = "position_incat";
			}
			if($gp_subcategory_orderby=="position_incat" && !$cust_category){//if listing ALL sub-categories, order by position, position_incat asc
				$getcats_query .= " ORDER BY position asc, $gp_subcategory_orderby $gp_subcategory_orderby_desc";
			}else{
				$getcats_query .= " ORDER BY $gp_subcategory_orderby $gp_subcategory_orderby_desc";//preferences
			}
			
		}else{
			$getcats_query		= "SELECT * FROM $tablename";
			if($gp_category_orderby && $gp_category_orderby_desc) $getcats_query .= " ORDER BY $gp_category_orderby $gp_category_orderby_desc";	
		}
		//echo '<br>(FB):'.$getcats_query;
		$getcats_result		= mysql_query($getcats_query);			
		
		$BuildSelect = '';
		if($fieldname=="subcategory"){
			$BuildSelect .= '<div class="panel_oneline">';			
			$BuildSelect .= '<form name="form1" action="'.$_SERVER['PHP_SELF'].'" method="post">';
			$BuildSelect .= '<p><span class="steptitle">Select Category:&nbsp;</span>';
			$ListPropsArr = array('name'=>'category','query'=>"SELECT * FROM $db_client.catalogue_cats ORDER BY $gp_category_orderby $gp_category_orderby_desc",'dbTable_field'=>'category','query_qty'=>"SELECT * FROM $db_clientTable_catalogue_subcats WHERE category=",'selected'=>$cust_category,'optionValue'=>$_SERVER['PHP_SELF'].'?thisList='.$thisList.'&category=','jump'=>true);
			$BuildSelect .= $CMSSelectOptions->Build($ListPropsArr);
			$BuildSelect .= '</form></p>';					
			$BuildSelect .= '<div class="inner_right">';		
			$BuildSelect .= '<p>&nbsp;</p>';
			$BuildSelect .= '</div>';
			$BuildSelect .= '</div>';
		}
		echo $BuildSelect;
		
		// check table contents
		if ($getcats_result && mysql_num_rows($getcats_result) >= 1 ) {
			$getcats_numrows 	= mysql_num_rows($getcats_result);
			$tmp_numrows		= $getcats_numrows;
		
			$my_price_total = 0;
			
			$DataBuild = '';
			$DataBuild .= '<div class="panel">';
			//$DataBuild .= '<p><span class="steptitle">Delete / Edit category:</span> Select an action from drop-down lists</p>';
			
			$DataBuild .= '<ul class="sortable-list-titles">';
				$DataBuild .= '<li>';
				$DataBuild .= '<span class="OneDigit">#Pos</span>';
				
				if(gp_enabled("price")) $DataBuild .= '<span class="Price">&pound;Stock</span>';
				//$DataBuild .= '<span class="Status">Status</span>';
				if($fieldname=="category"){
					$DataBuild .= '<span class="Category">Categories</span>';
					$DataBuild .= '<span class="Name">Sub-Categories</span>';
				}else{
					//$DataBuild .= '<span class="Category">Categories</span>';
					$DataBuild .= '<span class="BigName">Sub-Categories</span>';
				}
				$DataBuild .= '<span class="Category">Items in Category</span>';
				$DataBuild .= '<span class="Actions">Actions</span>';
				$DataBuild .= '</li>';
			$DataBuild .= '</ul>';

			if(($fieldname=="category" && gp_enabled('move_category') && $gp_category_orderby=="position") || ($fieldname=="subcategory" && gp_enabled('move_subcategory') && $gp_subcategory_orderby=="position_incat")){
				$OrderByPositionForced = true;
				$DataBuild .= '<script type="text/javascript" src="'.$adminroot.'includes/js/scriptaculous/lib/prototype.js"></script>'."\r\n";
				$DataBuild .= '<script type="text/javascript" src="'.$adminroot.'includes/js/scriptaculous/src/scriptaculous.js"></script>'."\r\n";
				$DataBuild .= '<ul id="SortableList" class="sortable-list">';//SortableList
			}else{
				$DataBuild .= '<ul class="sortable-list">';
			}
			
			for($tmpcount=1;$tmpcount<=$getcats_numrows;$tmpcount++){
								
				$getcats_row		= mysql_fetch_array($getcats_result);
				if($fieldname=="subcategory"){
					$categoryID			= $getcats_row['category'];
					$subcategoryID		= $getcats_row['id'];
					$statusID			= "statusID_".$subcategoryID;
					$protectedID		= "protectedID_".$subcategoryID;
					$getcats_pos		= $getcats_row['position'];
					if($cust_category) $getcats_pos	= $getcats_row['position_incat'];

					$getcats_name		= $getcats_row['subcategory'];
					$getcats_status		= $getcats_row['status'];
					$getcats_protected	= $getcats_row['protected'];
					$TableID = $subcategoryID;
				}else{
					$categoryID			= $getcats_row['id'];
					$statusID			= "statusID_".$categoryID;
					$getcats_pos		= $getcats_row['position'];
					$getcats_name		= $getcats_row['category'];
					$getcats_status		= $getcats_row['status'];
					$getcats_extranet	= $getcats_row['extranet'];
					$TableID = $categoryID;
				}			
				
				if($OrderByPositionForced){
					$tmp_rowPosition = $getcats_pos;
				}else{
					$tmp_rowPosition = $tmpcount;
				}
					
				// coloured rows
				$rowcolor = $CMSShared->GetRowColor($tmpcount,$colors);
				$DataBuild .= '<li id="Category_'.$TableID.'" style="background:'.$rowcolor.'">';
				$DataBuild .= '<span class="OneDigit">'.$tmp_rowPosition.'</span>';
				
				if(gp_enabled("price")){
					$catTotal = get_category($TableID,"stock_value",$thisList);
					$my_price_total += $catTotal;
					$DataBuild .= '<span class="Price">'.$CMSTextFormat->Price_StripDecimal($catTotal).'</span>';
				}

				
				if($fieldname=="category"){
					$DataBuild .= '<span class="Category"><a href="admin_category_add.php?thisList='.$thisList.'&editid='.$TableID.'&PrevPageCategory='.$cust_category.'" class="Edit" title="Edit Category">'.$getcats_name.'</a></span>';
					$DataBuild .= '<span class="Name">';
					/*$SubCatQuery = "SELECT sc.id,sc.subcategory,sc.category,sc.position_incat,c.category,c.subcategory";
					$SubCatQuery .= " FROM $db_clientTable_catalogue_subcats AS sc, $db_clientTable_catalogue AS c";
					$SubCatQuery .= " WHERE sc.id=c.subcategory AND c.category=$categoryID GROUP BY sc.id ORDER BY sc.$gp_subcategory_orderby $gp_subcategory_orderby_desc";
					$SubCatResult = mysql_query($SubCatQuery);*/
					
					$SubCatQuery = "SELECT csc.id,csc.subcategory,csc.category,csc.position_incat,c.id AS itemID,c.subcategory AS subcategoryName";
					$SubCatQuery .= " FROM $db_clientTable_catalogue_subcats AS csc, $db_clientTable_catalogue AS c";
					$SubCatQuery .= " WHERE (csc.category=$categoryID || c.category=$categoryID AND c.subcategory=csc.id) GROUP BY csc.id ORDER BY csc.$gp_subcategory_orderby $gp_subcategory_orderby_desc";
					$SubCatResult = mysql_query($SubCatQuery);
					
					if($SubCatResult && mysql_num_rows($SubCatResult)>=1){			
						$DataBuild .= '<a href="javascript:OpenCloseSubcategory(\'subcategoryDiv'.$categoryID.'\');" id="subcategoryDiv'.$categoryID.'_Link" title="Show sub-categories for \''.$getcats_name.'\'" class="subcategoryDivLink">'.mysql_num_rows($SubCatResult).' sub-cats.</a>';
						$DataBuild .= ' &#124; <a href="'.$_SERVER['PHP_SELF'].'?thisList=catalogue_subcats&category='.$categoryID.'" title="Manage sub-categories">organise</a>';
						
						$DataBuild .= '<span id="subcategoryDiv'.$categoryID.'" class="hidden">';
						$DataBuild .= '<ul class="subcategoryList">';
						for($sc=0;$sc<mysql_num_rows($SubCatResult);$sc++){
							$scRow = mysql_fetch_row($SubCatResult);
							
							$SubCatCountQuery = "SELECT id FROM $db_clientTable_catalogue WHERE status=1 AND category=$categoryID AND subcategory=${scRow[0]}";
							$SubCatCountResult = mysql_query($SubCatCountQuery);
							
							$DataBuild .= '<li>';
							if($SubCatCountResult && mysql_num_rows($SubCatCountResult)>=1){
								$DataBuild .= '<a href="admin_catalogue_all.php?status=1&category='.$categoryID.'&subcategory='.$scRow[0].'" class="NoStyle">'.mysql_num_rows($SubCatCountResult).' '.$CommonCustomWords['item'].'s</a>';
							}else{
								$DataBuild .= '0 '.$CommonCustomWords['item'].'s';
							}
							
							$DataBuild .= ' in <a href="admin_category_add.php?thisList=catalogue_subcats&editid='.$scRow[0].'" title="Edit Sub-Category" class="NoStyle">'.$CMSTextFormat->ReduceString($scRow[1],30).'</a>';
							$DataBuild .= '&nbsp;&#124;&nbsp;<a href="admin_catalogue_upload.php?category='.$categoryID.'&subcategory='.$scRow[0].'" title="add '.$CommonCustomWords['item'].' to this sub-category" class="NoStyle AddItem">add '.$CommonCustomWords['item'].'</a>';
							$DataBuild .= '</li>';
							
						}
						//$DataBuild .= '<li></li>';
						$DataBuild .= '</ul>';
						$DataBuild .= '</span>';
					}else{
						$DataBuild .= '0 sub-categories';
					}
					
					if(gp_enabled("add_subcategory")) $DataBuild .= ' &#124; <a href="admin_category_add.php?thisList=catalogue_subcats&category='.$categoryID.'" title="add sub-category to this category" class="NoStyle AddItem">add sub-cat.</a>';
					
				}else{
					$DataBuild .= '<span class="BigName">';
					$DataBuild .= '<a href="admin_category_add.php?thisList='.$thisList.'&editid='.$TableID.'&PrevPageCategory='.$cust_category.'">'.$getcats_name.'</a>';
					
					$DataBuild .= '&nbsp;&#124;&nbsp;<a href="admin_catalogue_upload.php?category='.$categoryID.'&subcategory='.$TableID.'" title="add '.$CommonCustomWords['item'].' to this sub-category" class="NoStyle AddItem">add '.$CommonCustomWords['item'].'</a>';
					$DataBuild .= '</span>';
					//$DataBuild .= '<span class="BigName">';
				}
				$DataBuild .= '</span>';
				
				$DataBuild .= '<span class="Category"><a href="admin_catalogue_all.php?category='.$categoryID.'&subcategory='.$subcategoryID;
				if($thisList=="catalogue_cats") $DataBuild .= '&amp;category='.$categoryID;
				$DataBuild .= '">'.get_category($TableID,"stock_count",$thisList).' '.$CommonCustomWords['item'].'s</a>';
				$DataBuild .= ' &#124; <a href="admin_catalogue_upload.php?category='.$categoryID.'" title="Add item to this category" class="NoStyle AddItem">add item</a>';
				$DataBuild .= '</span>';
				
				$DataBuild .= '<span class="Actions">';
					$DataBuild .= '<ul>';		
					if($OrderByPositionForced) $DataBuild .= '<li><a href="#" class="Move" title="Move category by clicking and dragging into place"><span>Move '.$fieldname.'</span></a></li>';					
					
					if(($fieldname=="category" && gp_enabled("hide_category")) || ($fieldname=="subcategory" && gp_enabled("hide_subcategory"))){
						if($getcats_status==1){
							$DataBuild .= '<li><a href="javascript:changeStatus(\''.$tablename.'\',\''.$TableID.'\',\'hide\')" id="'.$statusID.'" name="'.$TableID.'" class="Showing" title="Click to Hide"><span>Show / Hide</span></a></li>';
						}else{
							$DataBuild .= '<li><a href="javascript:changeStatus(\''.$tablename.'\',\''.$TableID.'\',\'show\')" id="'.$statusID.'" name="'.$TableID.'" class="Hidden" title="Click to Show"><span>Show / Hide</span></a></li>';
						}						
					}
					/*
					if($fieldname=="subcategory" && gp_enabled("protect_subcategory")){
						if($getcats_protected==1){
							$DataBuild .= '<li><a href="javascript:changeStatus(\''.$tablename.'\',\''.$TableID.'\',\'unprotect\')" id="'.$protectedID.'" name="'.$TableID.'" class="Protected" title="Click to Unprotect"><span>Show / Hide Category</span></a></li>';
						}else{
							$DataBuild .= '<li><a href="javascript:changeStatus(\''.$tablename.'\',\''.$TableID.'\',\'protect\')" id="'.$protectedID.'" name="'.$TableID.'" class="unProtected" title="Click to Protect"><span>Show / Hide Category</span></a></li>';
						}
					}
					
					//Show category icon on rollover?
					
					$myRolloverPreview = "layout/CategoryIcon_".str_pad($categoryID, 2, '0', STR_PAD_LEFT).".gif";
					if($cust_category){
						$myRolloverPreviewSC = "layout/CategoryIcon_".str_pad($categoryID, 2, '0', STR_PAD_LEFT)."_".str_pad($subcategoryID, 2, '0', STR_PAD_LEFT).".gif";
						if($CMSShared->FileExists($myRolloverPreviewSC)) $myRolloverPreview = $myRolloverPreviewSC;
					}

					$DataBuild .= '<li><a href="'.$previewOnlinePage.'?status=1&category='.$categoryID.'&subcategory='.$subcategoryID.'"';					
					if($CMSShared->FileExists($myRolloverPreview)){
						$ImageDimensions = @getimagesize($myRolloverPreview);
						$DataBuild .= ' onmouseover="showtrail('.$ImageDimensions[0].','.$ImageDimensions[1].',\''.$myRolloverPreview.'\');" onmouseout="hidetrail();"';
					}
					$DataBuild .= ' target="_blank" class="Preview"><span>Preview</span></a></li>';
					*/
					$DataBuild .= '<li><a href="admin_category_add.php?thisList='.$thisList.'&editid='.$TableID.'&PrevPageCategory='.$cust_category.'" class="Edit" title="Edit Category"><span>Edit</span></a></li>';
					
					if( ($thisList=="catalogue_cats" && gp_enabled("delete_category")) || ($thisList=="catalogue_subcats" && gp_enabled("delete_subcategory")) ){
						$DataBuild .= '<li><a href="admin_catalogue_all.php?category='.$categoryID;
						if($thisList=="catalogue_cats"){
							$DataBuild .= '&deleteCategory='.$categoryID;
						}elseif($thisList=="catalogue_subcats"){
							$DataBuild .= '&subcategory='.$subcategoryID.'&deleteSubCategory='.$subcategoryID.'&ParentCategoryID='.$categoryID;
						}
						$DataBuild .= '" class="Delete" title="Delete Category"><span>Delete</span></a></li>';
					}
					if($fieldname=="category" && $getcats_extranet) $DataBuild .= ' Extranet';
					$DataBuild .= '</ul>';
					$DataBuild .= '</span>';
				$DataBuild .= '</li>';						
			}
			$DataBuild .= '</ul>';			
			
			
			// show Item Totals
			if(gp_enabled("price")){
				$DataBuild .= '<ul class="sortable-list-titles">';
					$DataBuild .= '<li>';
					$DataBuild .= '<span class="OneDigit">&nbsp;</span>';
					$DataBuild .= '<span class="ItemCount">Total:</span>';
					if(gp_enabled("price"))	$DataBuild .= '<span class="Price">'.$CMSTextFormat->Price_StripDecimal($my_price_total).'</span>';
					$DataBuild .= '<span class="Status">&nbsp;</span>';
					$DataBuild .= '<span class="BigName">&nbsp;</span>';
					$DataBuild .= '<span class="Actions">&nbsp;</span>';
					$DataBuild .= '</li>';
				$DataBuild .= '</ul>';
			}
			$DataBuild .= '</div>';
			echo $DataBuild;
			
			include("includes/sortable-list.php");        
		
		} else { // if table is empty
			//echo '<p class="prompt">Your '.$fieldname.' table is empty. Please add one.</p>';
		}
		
		if(($fieldname=="category" || $fieldname=="subcategory" && !$categoryID) && gp_enabled("add_category")){
			$SubNavInner = '<ul id="SubNavInner">';
			$SubNavInner .= '<li class="add"><a href="admin_category_add.php?thisList='.$thisList.'">add new category</a></li>';		
			$SubNavInner .= '</ul>';
			echo $SubNavInner;
		}
		
		if($fieldname=="subcategory" && gp_enabled("add_subcategory") && $categoryID){
			$SubNavInner = '<ul id="SubNavInner">';
			$SubNavInner .= '<li class="add"><a href="admin_category_add.php?thisList='.$thisList.'&category='.$categoryID.'">add new sub-category</a></li>';
			if($categoryID) $SubNavInner .= '<li class="edit"><a href="admin_category_add.php?thisList=catalogue_cats&editid='.$categoryID.'">edit category</a></li>';
			$SubNavInner .= '</ul>';
			echo $SubNavInner;
		}
		
		//if($fieldname!="subcategory"){
			$icons = $CMSHelp->GetIconArray();
			$buttons = $CMSHelp->PrintButtonPanel($thisList);
			echo $buttons;
		//}
	}
	/// END /// if($thislist)	

}
include("includes/admin_pagefooter.php");
	
?>


