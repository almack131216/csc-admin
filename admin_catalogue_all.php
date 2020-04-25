<?php
$suid_PageAccess = true;
$curr_page = "catalogue";
$curr_page_sub	= "items";

//$alert_CannotMoveWithoutCategory = "You must select a category to use this feature";


// (ORDER BY) DEFAULT PROPERTIES FOR QUERY
$cust_status		= 1;
$cust_category		= 0;
$cust_orderby		= "upload_date desc";//$gp_orderby
$cust_maxperpage	= 0;

// (ORDER BY) IF SET...
$SaveToExcelProps = array();
if(isset($_REQUEST['status']))	$cust_status		= $_REQUEST['status'];$SaveToExcelProps['status']=$cust_status;
if($_REQUEST['category'])		$cust_category		= $_REQUEST['category'];$SaveToExcelProps['category']=$cust_category;
if($_REQUEST['subcategory'])	$cust_subcategory	= $_REQUEST['subcategory'];$SaveToExcelProps['subcategory']=$cust_subcategory;
if($_REQUEST['orderby']) 		$cust_orderby		= $_REQUEST['orderby'];$SaveToExcelProps['orderby']=$cust_orderby;
if($_REQUEST['maxperpage'])		$cust_maxperpage	= $_REQUEST['maxperpage'];$SaveToExcelProps['maxperpage']=$cust_maxperpage;
if($_REQUEST['keyword'])		$cust_keyword		= $_REQUEST['keyword'];$SaveToExcelProps['keyword']=$cust_keyword;

include("includes/classes/PageBuild.php");
require_once("prefs/catalogue_prefs.php");
$BuildPage .= $PageBuild->AddPageTitle("Pages &#124; items");
$tmpCatalogueData = $PageBuild->GetCatalogueData($cust_category,$cust_subcategory,"");
$BuildTip = "Showing <strong>".$tmpCatalogueData['count']."</strong> items";
if($cust_category && !$cust_subcategory) $BuildTip .= ' within '.$tmpCatalogueData['categoryName'];
if($cust_category && $cust_subcategory) $BuildTip .= ' within <a href="admin_catalogue_all.php?status='.$cust_status.'&category='.$cust_category.'"><em>'.$tmpCatalogueData['categoryLink'].'</em></a> &gt; '.$tmpCatalogueData['subcategoryName'];
if(!$cust_category) $BuildTip .= " in your database. Use the drop-downs to fine-tune the range of items listed below";
if($suid) $BuildTip = "Please select one of your entries to edit details";

if($tmpCatalogueData['count']==0){
	if($cust_category) $NoItems = '<p class="prompt">There are currently no '.$CommonCustomWords['item'].'s to view in '.$tmpCatalogueData['categoryLink'];
	if($cust_category && $cust_subcategory) $NoItems .= ' &gt; '.$tmpCatalogueData['subcategoryName'];
	
	$CatQuery = "SELECT * FROM $db_clientTable_catalogue_cats";
	$CatResult = mysql_query($CatQuery);
	if($CatResult && mysql_num_rows($CatResult)>=1){
		$tmpTitle = 'add '.$CommonCustomWords['item'];
		
		if($tmpCatalogueData['categoryNameRaw']){			
			if($cust_subcategory){
				$tmpTitle .= ' to \''.$tmpCatalogueData['subcategoryNameRaw'].'\'';
			}else{
				$tmpTitle .= ' to \''.$tmpCatalogueData['categoryNameRaw'].'\'';
			}			
		}else{
			$NoItems = '<p class="prompt">You have <strong>'.mysql_num_rows($CatResult).'</strong> categories in your table but no '.$CommonCustomWords['item'].'s added yet.</p>';
		}	
		
		
	}else{			
		$NoItems = '<p class="prompt">You do not have any categories listed. Please add one.</p>';
		//if(gp_enabled("add_category")) $SubNavInner = '<li class="add"><a href="admin_category_add.php?thisList=catalogue_cats">add category</a></li>';
	}
}

$BuildPage .= $PageBuild->AddPageTip($BuildTip);
$BuildPage .= $PageBuild->AddTag(array('dir'=>'addingajax/','file'=>'addingajax.js'));
$BuildPage .= $PageBuild->AddTag('mootools.css');
$BuildPage .= $PageBuild->AddTag('paginator.css');
$BuildPage .= $PageBuild->AddTag(array('dir'=>'includes/css/','file'=>'myclassPrint.css','media'=>'print'));
$BuildPage .= $PageBuild->AddTag('JumpForms.js');
// third-party Image Rollover (ajax)
$BuildPage .= $PageBuild->AddTag('ImageTrail_tooltip.js');
$BuildPage .= $PageBuild->AddTag('ImageTrail_ajax.js');
$BuildPage .= $PageBuild->AddTag("ss.js");
include("includes/classes/CMSHelp.php");
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
}else if($_SESSION['suid'] && $suid_PageAccess && !is_array($_SESSION['suid'])){
	echo suid_pageAccessMessage();
}else{
	require_once('includes/loadCategories.php');
	$cust_orderby_arr = $gp_arr_orderby;
	$tablename = $db_clientTable_catalogue;
	
	$FULLtablename = $tablename;
	
	
	// Order items by...
	if(($cust_category && !$cust_subcategory && get_category($cust_category,"orderby","catalogue_cats")=="position_incat asc")){
		$OrderByPositionForced = true;
		$PreOrder = " ORDER BY c.position_incat asc";
		$SaveToExcelProps['orderby'] = "position_incat asc";
		$sortableDetail = "position_incat";
		$fieldname = "category";
	}elseif(($cust_category && $cust_subcategory && get_category($cust_subcategory,"orderby","catalogue_subcats")=="position_insubcat asc")){
		$OrderByPositionForced = true;
		$PreOrder = " ORDER BY c.position_insubcat asc";
		$SaveToExcelProps['orderby'] = "position_insubcat asc";	
		$sortableDetail = "position_insubcat";
		$fieldname = "name";
	}else{
		if($cust_orderby=="category asc" || $cust_orderby=="category desc"){
			$PreOrder = " ORDER BY cc.$cust_orderby";//category (A-Z / Z-A)		
		}else{
			$PreOrder = " ORDER BY c.$cust_orderby";
		}
	}
	if($OrderByPositionForced){
		$attributes = array('tablename'=>$tablename,'fieldname'=>$fieldname);
		$Categories = getCategories($attributes);//see 'includes/loadCategories.php'
	}
	//echo '(FB): TABLE = '.$tablename;
	
	///////////////////
	/// START PAGE HTML
	/// INITIALISE variables
	$max_results = $cust_maxperpage; /// Number of results per page	
	$itemArray = array();
	
	if($cust_status==2) $queryDate = $queryDateExpired;
	
	$paginatorHref = $_SERVER['PHP_SELF'].'?status='.$cust_status.'&orderby='.$cust_orderby.'&maxperpage='.$max_results.'&keyword='.$cust_keyword;
	$WHERE = "WHERE c.category=cc.id AND c.subcategory=csc.id AND $queryDate AND c.id_xtra=0";
	
	if($suid && is_array($suid)){
		$WHERE .= ' AND (';
		for($i=0;$i<sizeof($suid);$i++){
			if($i>0) $WHERE .= ' OR ';
			$WHERE .= 'c.id='.$suid[$i];
		}
		$WHERE .= ')';
	}
	
	if(!empty($cust_category)){//IF CATEGORY IS SET
		$paginatorHref .= '&amp;category='.$cust_category;
		$WHERE .= " AND c.category=$cust_category";
	}
	if(!empty($cust_subcategory)){//IF SUBCATEGORY IS SET
		$paginatorHref .= '&amp;subcategory='.$cust_subcategory;
		$WHERE .= " AND c.subcategory=$cust_subcategory";
	}
	if($cust_keyword) $WHERE .= " AND (c.name LIKE '%$cust_keyword%' OR c.description LIKE '%$cust_keyword%')";
	//echo $WHERE;
	
	// Get all items within custom selection
	$PreQuery = "SELECT c.*,cc.id,cc.category AS categoryName,csc.subcategory AS subcategoryName,cc.orderby FROM $FULLtablename AS c, $db_clientTable_catalogue_cats AS cc, $db_clientTable_catalogue_subcats AS csc";		
	$WHERE .= $PreOrder;
	$PreTotalsQuery = "SELECT COUNT(*) as Num FROM $FULLtablename AS c, $db_clientTable_catalogue_cats AS cc, $db_clientTable_catalogue_subcats AS csc $WHERE ";
	
	include("includes/paginator.php");
	
	if( (gp_enabled("delete_category") && $_GET['deleteCategory']) || (gp_enabled("delete_subcategory") && $_GET['deleteSubCategory']) ){
		if($_GET['deleteSubCategory']){
			$thisList = "catalogue_subcats";
			$thisTitle = $tmpCatalogueData['subcategoryName'];
			$thisType = "sub-category";
			$hiddenField = '<input type="hidden" name="subcategoryID" value="'.$cust_subcategory.'">';
		}else{
			$thisList = "catalogue_cats";
			$thisTitle = $tmpCatalogueData['categoryName'];
			$thisType = "category";
			$hiddenField = '<input type="hidden" name="categoryID" value="'.$cust_category.'">';
		}
		$DeletePanel = '<div class="panel_warning">';
			$DeletePanel .= '<p class="squashLeft"><strong>You are about to delete '.$thisTitle.'!</strong>';
			if(sizeof($itemArray)>=1){
				$DeletePanel .= '<br/>This '.$thisType.' contains <strong>'.sizeof($itemArray).'</strong> items(see below).';
				$DeletePanel .= '<br/>These will also be deleted if you click the \'Delete\' button.</p>';
			}else{
				$DeletePanel .= '<br/>This '.$thisType.' is currently empty.</p>';
			}
			
			$DeletePanel .= '<div class="inner_right">';
				$DeletePanel .= '<form action="admin_category_delete.php" method="POST">';
					$DeletePanel .= $hiddenField;
					if($_REQUEST['ParentCategoryID']) $DeletePanel .= '<input type="hidden" name="ParentCategoryID" value="'.$_REQUEST['ParentCategoryID'].'">';
					$DeletePanel .= '<input type="hidden" name="thisList" value="'.$thisList.'">';
					$DeletePanel .= '<input type="submit" name="delete" value="Delete" title="Delete Selection" id="delete">';
				$DeletePanel .= '</form>';
			$DeletePanel .= '</div>';
		$DeletePanel .= '</div>';
		echo $DeletePanel;
	}
	
	include("includes/paginator_order.php");
	echo $BuildOrderOptions;
	echo $paginator;
	
	if(sizeof($itemArray)>0){	
	
		///////////// SET VALUES
		$first 		= true;

		/////////////////
		/// LIST RESULTS
		$my_price_total = 0;
		echo '<form action="admin_category_delete.php" method="post" name="view_all">'; //admin_catalogue_all_process.php
		echo '<input type="hidden" name="thisList" value="catalogue_cats">';
		echo '<div class="panel">';	
		
		for($tmpcount=0;$tmpcount<sizeof($itemArray);$tmpcount++){
			$my_id 				= $itemArray[$tmpcount]['id'];
			$my_name			= stripslashes($itemArray[$tmpcount]['name']);
			$my_index			= $itemArray[$tmpcount]['id_index'];
			$my_position		= $itemArray[$tmpcount]['position'];
			$my_detail_1 		= $itemArray[$tmpcount]['detail_1'];
			
			$my_categoryName 	= get_category($itemArray[$tmpcount]['category'],"name","catalogue_cats");
			$my_subcategoryName	= get_category($itemArray[$tmpcount]['subcategory'],"name","catalogue_subcats");			
			$my_status			= $itemArray[$tmpcount]['status'];
			$statusID			= "statusID_".$my_id;
			$my_price			= $itemArray[$tmpcount]['price'];					
			$my_date			= $itemArray[$tmpcount]['upload_date'];			
			$my_date			= $CMSTextFormat->FormatDate($my_date,"cms");
			
			$filename			= $itemArray[$tmpcount]['image_large'];
			$my_image_large		= $siteroot.$gp_uploadPath['large'].$filename;
			$my_image_primary	= $siteroot.$gp_uploadPath['primary'].$filename;
			$my_image_thumb		= $siteroot.$gp_uploadPath['thumbs'].$filename;
			
				
			// PRINT TABLE HEADERS
			if($first) {
				$BuildTitleRow = '';
				$BuildTitleRow .= '<ul class="sortable-list-titles">';
					$BuildTitleRow .= '<li>';
					$BuildTitleRow .= '<span class="TickBoxes"><input type="checkbox" name="tickbox_all" title="Select All" class="tickbox" onClick="arr_id_fill('.$itemtotal.')"/></span>';
					$BuildTitleRow .= '<span class="Date">Date</span>';
					if(gp_enabled("price") && !FieldDisabled(array('category'=>$cust_category,'fieldname'=>'price')) ) $BuildTitleRow .= '<span class="Price">Price</span>';
					//$BuildTitleRow .= '<span class="Status">Status</span>';
					//if(gp_enabled("detail_1")) $BuildTitleRow .= '<span class="Price">'.$gp_arr_details[1]['name'].'</span>';
					$BuildTitleRow .= '<span class="Name">Name</span>';
					$BuildTitleRow .= '<span class="Category">';
					
					if($cust_category==0 || !gp_enabled('subcategory')){
						$BuildTitleRow .= 'Category';
					}else{
						$BuildTitleRow .= 'Sub-Category';
					}

					$BuildTitleRow .= '</span>';
					//$BuildTitleRow .= '<span class="Status">Status</span>';
					$BuildTitleRow .= '<span class="Actions">Actions</span>';
					$BuildTitleRow .= '</li>';
				$BuildTitleRow .= '</ul>';
				
				if($OrderByPositionForced){
					$BuildTitleRow .= '<script type="text/javascript" src="'.$adminroot.'includes/js/scriptaculous/lib/prototype.js"></script>'."\r\n";
					$BuildTitleRow .= '<script type="text/javascript" src="'.$adminroot.'includes/js/scriptaculous/src/scriptaculous.js"></script>'."\r\n";
					$BuildTitleRow .= '<ul id="SortableList" class="sortable-list">';
				}else{
					$BuildTitleRow .= '<ul class="sortable-list">';
				}
				echo $BuildTitleRow;
			}			
			
			
			/////// DETERMINE ROW COLOR
			$rowcolor = $CMSShared->GetRowColor($tmpcount,$colors);
			$onlinestatus = get_statusname($my_status);
				
				
			///////////////////
			//////// LIST items
			if(!empty($itemArray[$tmpcount]['id'])){
				$BuildItemRow = '';
				$BuildItemRow .= '<li id="Category_'.$my_id.'" style="background:'.$rowcolor.'">';
					$BuildItemRow .= '<span class="TickBoxes"><input name="arr_id[]" type="checkbox" value="'.$my_id.'" class="tickbox" onClick="arr_id_checkselected()"/></span>';
					$BuildItemRow .= '<span class="Date">'.$my_date.'</span>';
					if(gp_enabled("price") && !FieldDisabled(array('category'=>$cust_category,'fieldname'=>'price')) ) $BuildItemRow .= '<span class="Price">'.$CMSTextFormat->Price_StripDecimal($my_price).'</span>';
					$BuildItemRow .= '<span class="Name">';
					$BuildItemRow .= '<a href="admin_catalogue_upload.php?editid='.$my_id.'" class="Edit" title="Edit Item">'.$my_name.'</a>';
					
					$BuildItemRow .= '</span>';
					$BuildItemRow .= '<span class="Category">';
					if($cust_category==0 || !gp_enabled('subcategory')){
						$BuildItemRow .= $my_categoryName;
					}else{
						$BuildItemRow .= $my_subcategoryName;
					}
					$BuildItemRow .= '</span>';					

					
					
					//$BuildItemRow .= '<span class="Status">'.show_status($my_status,"item").'</span>';
					$BuildItemRow .= '<span class="Actions">';
						$BuildItemRow .= '<ul>';
						
						if($cust_subcategory && gp_enabled('item_index')){
							$BuildItemRow .= '<li><input class="radio" title="set as index page " type="radio" name="indexItem" value="'.$my_id.'"';
							if($my_index) $BuildItemRow .= ' checked="checked"';
							$BuildItemRow .= ' onclick="javascript:setIndex(\''.$tablename.'\',\''.$my_id.'\',\''.$cust_subcategory.'\')"></li>';
						}
						if($OrderByPositionForced) $BuildItemRow .= '<li><a href="#" class="Move" title="Move item by clicking and dragging into place"><span>Move Item</span></a></li>';
						
						/* DISABLED : 2009-02-08 : Because the status change should be done via the selection tool - is quite confusing otherwise
						if($my_status==1){							
							$BuildItemRow .= '<li><a href="javascript:changeStatus(\''.$_SESSION['db_client'].".".$tablename.'\',\''.$my_id.'\',\'hide\')" id="'.$statusID.'" name="'.$my_id.'" class="Showing" title="Click to Hide Item"><span>Show / Hide Item</span></a></li>';
						}else{
							$BuildItemRow .= '<li><a href="javascript:changeStatus(\''.$_SESSION['db_client']."."$tablename.'\',\''.$my_id.'\',\'show\')" id="'.$statusID.'" name="'.$my_id.'" class="Hidden" title="Click to Show Item"><span>Show / Hide Item</span></a></li>';
						}
						*/
						if($CMSShared->IsImage($filename)){
							$myRolloverPreview = $my_image_thumb;	
						}else{
							$fileType = $CMSShared->GetFileType($filename);
							$myRolloverPreview = $CMSImages->GetFileIconHover($fileType);
						}
						$ImageDimensions = array(0,0);//@getimagesize($myRolloverPreview);
						if(gp_enabled("preview_item")) $BuildItemRow .= '<li><a href="'.$moreinfopage.'?uid='.$my_id.'" title="Preview item:&nbsp;'.$my_name.'" class="Preview" onmouseover="showtrail('.$ImageDimensions[0].','.$ImageDimensions[1].',\''.$myRolloverPreview.'\');" onmouseout="hidetrail();" target="_blank"><span>Preview '.$CommonCustomWords['item'].'</span></a></li>';
						$BuildItemRow .= '<li><a href="admin_catalogue_upload.php?editid='.$my_id.'" class="Edit" title="Edit Item"><span>Edit Item</span></a></li>';
						$BuildItemRow .= '<li><a href="admin_catalogue_item_delete.php?uid='.$my_id.'&prevpage=item_list" class="Delete" title="Delete Item"><span>Delete</span></a></li>';
						$BuildItemRow .= '</ul>';
					$BuildItemRow .= '</span>';				
				$BuildItemRow .= '</li>';
				echo $BuildItemRow;
				
				$my_price_total+=$my_price;
				$first = false; // ensure we don't repeat printing of table headers
			}					
			
		} ////// END OF (FOR) LISTING RESULTS IN TABLE
		echo '</ul>';
		
		// show Item Price Total (only if using prices within CMS prefs)

		if(gp_enabled("price") && !empty($my_price_total)){				
			$totalPrice = '';
			$totalPrice .= '<ul class="sortable-list-titles">';
				$totalPrice .= '<li>';
					$totalPrice .= '<span class="TickBoxes">&nbsp;</span>';
					$totalPrice .= '<span class="Date"><strong>Total:</strong></span>';
					$totalPrice .= '<span class="Price"><strong>'.$CMSTextFormat->Price_StripDecimal($my_price_total).'</strong></span>';
					$totalPrice .= '<span class="Name">&nbsp;</span>';
					$totalPrice .= '<span class="Category">&nbsp;</span>';
					//$totalPrice .= '<span class="Status">&nbsp;</span>';
					$totalPrice .= '<span class="Actions">&nbsp;</span>';				
				$totalPrice .= '</li>';
			$totalPrice .= '</ul>';
			echo $totalPrice;
		}				
		
		
		// show Batch Process Buttons (for processing multiple items)
		$BuildBatchButs = '';
		$BuildBatchButs .= '<ul class="sortable-list">';
		$BuildBatchButs .= '<li id="extrabuts" class="hidden">';
			$BuildBatchButs .= '<input type="submit" name="deletemarked" title="Delete Marked" id="deletemarked" value="deletemarked">';
			$BuildBatchButs .= '<span class="ChangeStatus"><p><strong>(OR)</strong>&nbsp;Change status of selected items:&nbsp;&nbsp;</p>';
			$BuildBatchButs .= list_status_select();
			$BuildBatchButs .= '<input type="submit" name="editmarked" title="Edit Marked" id="update" class="hidden" value="editmarked">';
			$BuildBatchButs .= '</span>';			
		$BuildBatchButs .= '</li>';
		$BuildBatchButs .= '</ul>';		
		echo $BuildBatchButs;
		
		echo '</form>';
		echo '</div>';	
		$fieldname = "name";
		$sortableDetail = "position_insubcat";	
		if($OrderByPositionForced) include("includes/sortable-list.php");
		
		echo $paginator;
		
		
		//include("includes/catalogue_pagenav.php");
		if(gp_enabled("SaveToExcel")){
			$SaveToExcel = "SaveToExcel.php?filename=ItemsList&db_table=".$db_clientTable_catalogue;
			echo $CMSCommon->panel_Save($SaveToExcel,$SaveToExcelProps,"to view and save this list (Microsoft Excel Worksheet)");		
		}
		
	}else{
		echo $NoItems;
		echo $SubNavInner;
	}
	/// END LIST RESULTS ///
	
	//if(!$SubNavInner){
		$SubNavInner = '';
		$SubNavInner .= '<ul id="SubNavInner">';
		
		//$tmpAddLink = 'admin_catalogue_upload.php';
		//$tmpTitle = 'add '.$CommonCustomWords['item'];
		$tmpAddLink = '';
		$tmpTitle = '';
		$tmpTitleAppend = '';		
		
		$tmpDeleteTitle = 'delete';
		$tmpDeleteLink = $_SERVER['PHP_SELF'].'?thisList=catalogue_cats&category='.$cust_category;

		
		if($cust_category && !$cust_subcategory && gp_enabled("add_subcategory")){
			$tmpTitle = 'add sub-category';
			$tmpAddLink = 'admin_category_add.php?thisList=catalogue_subcats&category='.$cust_category;
			$tmpTitleAppend = ' to \''.$tmpCatalogueData['categoryNameRaw'].'\'';
			$tmpDeleteTitleAppend = ' \''.$tmpCatalogueData['categoryNameRaw'].'\'';
		}
		if($cust_category && $cust_subcategory){
			$tmpAddLink = 'admin_catalogue_upload.php?category='.$cust_category.'&subcategory='.$cust_subcategory;
			$tmpTitleAppend = 'add '.$CommonCustomWords['item'].' to \''.$tmpCatalogueData['subcategoryNameRaw'].'\'';
			$tmpDeleteLink .= '&subcategory='.$cust_subcategory.'&deleteSubCategory='.$cust_subcategory;
			$tmpDeleteTitleAppend = ' \''.$tmpCatalogueData['subcategoryNameRaw'].'\'';
		}else{
			$tmpDeleteLink .= '&deleteCategory='.$cust_category;
		}
		if($tmpAddLink) $SubNavInner .= '<li class="add"><a href="'.$tmpAddLink.'" title="'.$tmpTitle.$tmpTitleAppend.'">'.$tmpTitle.$tmpTitleAppend.'</a></li>';
		if( !$DeletePanel && (gp_enabled("delete_category") && $cust_category) || !$DeletePanel && (gp_enabled("delete_subcategory") && $cust_subcategory) ){
			$SubNavInner .= '<li class="delete"><a href="'.$tmpDeleteLink.'" title="'.$tmpDeleteTitle.$tmpDeleteTitleAppend.'">'.$tmpDeleteTitle.$tmpDeleteTitleAppend.'</a></li>';
		}
		$SubNavInner .= '</ul>';
		echo $SubNavInner;
	//}
	
	$icons = $CMSHelp->GetIconArray();
	$buttons = $CMSHelp->PrintButtonPanel($thisList);
	echo $buttons;
			
}

include("includes/admin_pagefooter.php");
?>