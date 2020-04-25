<?php
//gp_newsletterTable
$curr_page = "members";
$curr_page_sub = "newsletter_list";

include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle("Newsletter Subscriptions &#124; List All");
$BuildPage .= $PageBuild->AddPageTip("View newsletter subscription details");
$BuildPage .= $PageBuild->AddTag('mootools.css');
$BuildPage .= $PageBuild->AddTag('paginator.css');
$BuildPage .= $PageBuild->AddTag(array('dir'=>'addingajax/','file'=>'addingajax.js'));
$BuildPage .= $PageBuild->AddTag('JumpForms.js');
include("includes/admin_pageheader.php");


// (ORDER BY) DEFAULT PROPERTIES FOR QUERY
$cust_orderby		= "date desc";
$cust_maxperpage	= 0;


// (ORDER BY) IF SET...
if($_REQUEST['orderby']) 		$cust_orderby		= $_REQUEST['orderby'];
if($_REQUEST['maxperpage'])		$cust_maxperpage	= $_REQUEST['maxperpage'];
if($_REQUEST['keyword'])		$cust_keyword		= $_REQUEST['keyword'];
if(isset($_REQUEST['unsubscribe']))	$cust_unsubscribe	= $_REQUEST['unsubscribe'];

/////////// check to see if session is set
if( notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {
	$cust_orderby = $cust_orderby;
	$cust_orderby_arr = $gp_newsletter_arr_orderby;
	$tablename = $db_clientTable_newsletter;
	
	$field_id = $gp_newsletterTable['id']['field'];
	//print_r($gp_newsletterTable);

	$FULLtablename = $tablename;
	$prefArrayName = "gp_newsletterTable";
	if($_GET['success']) echo '<p class="good">Newsletter table successfully updated</p>';

	///////////////////
	/// START PAGE HTML
	/// INITIALISE variables
	$max_results = $cust_maxperpage; /// Number of results per page	
	$itemArray = array();
	$paginatorHref = $_SERVER['PHP_SELF'].'?orderby='.$cust_orderby.'&maxperpage='.$max_results;
	 
	$WHERE = " WHERE t.id>0";
	$PreQuery = "SELECT t.* FROM $FULLtablename AS t";
	
	if(($cust_unsubscribe==1 || $cust_unsubscribe==0) && $cust_unsubscribe!=''){
		$WHERE .= " AND t.unsubscribe=$cust_unsubscribe";
		$paginatorHref .= '&unsubscribe='.$cust_unsubscribe;
	}
	if($cust_keyword){
		$WHERE .= " AND t.email LIKE '%".$cust_keyword."%'";
		$paginatorHref .= '&keyword='.$cust_keyword;		
	}
	
	$PreOrder = " ORDER BY t.$cust_orderby"; /// Your sql statement	
	$WHERE .= $PreOrder;
	$PreTotalsQuery = "SELECT COUNT(*) as Num FROM $FULLtablename AS t $WHERE ";
	
	include("includes/paginator.php");
	
	if(sizeof($itemArray)>0){
		
		include("includes/paginator_order.php");
		echo $BuildOrderOptions;
		
		$BuildTable = '';
		$BuildTable .= '<div class="panel">';
		$BuildTable .= '<form action="admin_newsletter_update.php" method="POST" name="view_all">';
			
		$BuildTable .= '<ul class="sortable-list-titles">';
			$BuildTable .= '<li>';
			$BuildTable .= '<span class="TickBoxes"><input type="checkbox" name="tickbox_all" title="Select All" class="tickbox" onClick="arr_id_fill('.sizeof($itemArray).');"/></span>';
			$BuildTable .= '<span class="BigName">Email</span>';
			$BuildTable .= '<span class="Date">Registered</span>';
			$BuildTable .= '<span class="memberRole">Newsletter</span>';		
			$BuildTable .= '<span class="Actions">Actions</span>';
			$BuildTable .= '</li>';
		$BuildTable .= '</ul>';
		

		$BuildTable .= '<ul class="sortable-list">';
		
		for($tmpcount=0;$tmpcount<sizeof($itemArray);$tmpcount++){			
			$getlinks_id		= $itemArray[$tmpcount][$gp_newsletterTable['id']['field']];			
			$getlinks_regdate	= $itemArray[$tmpcount][$gp_newsletterTable['registered']['field']];
			$getlinks_email		= $itemArray[$tmpcount][$gp_newsletterTable['email']['field']];
			$getlinks_unsubscribe		= $itemArray[$tmpcount][$gp_newsletterTable['unsubscribe']['field']];
			$unsubscribeID		= "unsubscribe_".$getlinks_id;
			
			// coloured rows
			$rowcolor = $CMSShared->GetRowColor($tmpcount,$colors);
			$BuildTable .= '<li id="Item_'.$getlinks_id.'" style="background:'.$rowcolor.'">';
			$BuildTable .= '<span class="TickBoxes"><input name="arr_id[]" type="checkbox" value="'.$getlinks_id.'" class="tickbox" onClick="arr_id_checkselected()"/></span>';
					
			$BuildTable .= '<span class="BigName">'.$getlinks_email.'</span>';
			$BuildTable .= '<span class="Date">'.$getlinks_regdate.'</span>';//Location
			
			$BuildTable .= '<span class="memberRole">';			
			if($getlinks_unsubscribe==0){
				$BuildTable .= '<a href="javascript:changeUnsubscribeStatus(\''.$tablename.'\',\''.$getlinks_id.'\',\'unsubscribe\',\'unsubscribe\')" id="'.$unsubscribeID.'" name="'.$getlinks_id.'" title="Click to Disable" class="subscribed">subscribed</a>';
			}else{
				$BuildTable .= '<a href="javascript:changeUnsubscribeStatus(\''.$tablename.'\',\''.$getlinks_id.'\',\'unsubscribe\',\'subscribe\')" id="'.$unsubscribeID.'" name="'.$getlinks_id.'" title="Click to Enable" class="unsubscribed">unsubscribed</a>';
			}
			$BuildTable .= '</span>';
			
			$SharedParams = '&param[itemTitle]='.$getlinks_email.'&param[curr_page]=members&param[curr_page_sub]=newsletter_list&param[prefArrayName]=gp_newsletterTable&param[PrevPage]=admin_newsletter_list.php';
			$BuildTable .= '<span class="Actions">';
				$BuildTable .= '<ul>';			
				$BuildTable .= '<li><a href="mailto:'.$getlinks_email.'?subject=JoeBloggs Newsletter Team" title="Contact This Person" class="Contact"><span>Contact This Person</span></a></li>';
				$BuildTable .= '<li><a href="admin_genericPreview.php?param[uid]='.$getlinks_id.$SharedParams.'&param[page_title]=Newsletter &#124; Preview Details&param[page_subtitle]=Preview details" class="Preview" title="Preview Details"><span>Preview</span></a></li>';
				$BuildTable .= '<li><a href="admin_genericUpdate.php?param[editid]='.$getlinks_id.$SharedParams.'&param[page_title]=Newsletter &#124; Edit Details&param[page_subtitle]=Edit details using the form below" class="Edit" title="Edit Details"><span>Edit</span></a></li>';
				$BuildTable .= '<li><a href="admin_genericDelete.php?param[deleteid]='.$getlinks_id.$SharedParams.'&param[page_title]=Delete Email Address&param[page_subtitle]=Delete Email Address" title="Delete Email Address" class="Delete"><span>Delete Email Address</span></a></li>';
			$BuildTable .= '</ul>';
			$BuildTable .= '</span>';
			$BuildTable .= '</li>';	
		}
		
		$BuildTable .= '</ul>';
		
		// show Batch Process Buttons (for processing multiple items)
		$BuildBatchButs = '';
		$BuildBatchButs .= '<ul class="sortable-list">';
		
		$BuildBatchButs .= '<input type="hidden" name="thisList" value="newsletter">';
		$BuildBatchButs .= '<li id="extrabuts" class="hidden">';
			$BuildBatchButs .= '<input type="submit" name="deletemarked" title="Delete Marked" id="deletemarked" value="deletemarked">';
			$BuildBatchButs .= '<span class="ChangeStatus"><p><strong>(OR)</strong>&nbsp;Mark these people as Subscribed or UNsubscribed?:&nbsp;&nbsp;</p>';

			$BuildBatchButs .= '<select name="new_status" id="status" onChange="checkeditbut();">';
			$BuildBatchButs .= '<option value="" selected>Please Select</option>';	
			$BuildBatchButs .= '<option value="1">UNsubscribe</option>';
			$BuildBatchButs .= '<option value="0">REsubscribe</option>';			 
			$BuildBatchButs .= '</select>';
		
			$BuildBatchButs .= '<input type="submit" name="editmarked" title="Edit Marked" id="update" class="hidden" value="editmarked">';
			$BuildBatchButs .= '</span>';	
		$BuildBatchButs .= '</li>';
		$BuildBatchButs .= '</form>';
		$BuildBatchButs .= '</ul>';	
		
		
		
		echo $paginator;
		echo $BuildTable;		
		echo $BuildBatchButs;		
		echo '</div>';
		echo $paginator;
		
		//include("includes/sortable-list.php");
		
		
	} else { // if table is empty
		echo '<p class="prompt">There are no newsletter subscriptions listed in your database matching this criteria</p>';
	}

}
include("includes/admin_pagefooter.php");
	
?>