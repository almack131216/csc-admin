<?php

$curr_page = "workshops";
$curr_page_sub = "workshops_list";
include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle("Workshops &#124; List All");
$BuildPage .= $PageBuild->AddPageTip("View workshop details");
$BuildPage .= $PageBuild->AddTag('mootools.css');
$BuildPage .= $PageBuild->AddTag('paginator.css');
$BuildPage .= $PageBuild->AddTag('JumpForms.js');
include("includes/admin_pageheader.php");


// (ORDER BY) DEFAULT PROPERTIES FOR QUERY
$cust_orderby		= $gp_branchTable['date']['field'];
$cust_region		= 0;
$cust_maxperpage	= 0;


// (ORDER BY) IF SET...
if($_REQUEST['orderby']) 		$cust_orderby		= $_REQUEST['orderby'];
if($_REQUEST['maxperpage'])		$cust_maxperpage	= $_REQUEST['maxperpage'];
if($_REQUEST['region'])			$cust_region		= $_REQUEST['region'];
if($_REQUEST['jobID'])			$cust_jobID			= $_REQUEST['jobID'];
if($_REQUEST['category'])		$cust_category		= $_REQUEST['category'];
if($_REQUEST['keyword'])		$cust_keyword		= $_REQUEST['keyword'];



/////////// check to see if session is set
if( notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {
	$cust_orderby = $cust_orderby;
	$cust_orderby_arr = $gp_workshops_arr_orderby;
	$tablename = $gp_branchTable['tablename'];
	
	$field_id = $gp_branchTable['id']['field'];

	$FULLtablename = $tablename;
	$prefArrayName = "gp_branchTable";
	

	///////////////////
	/// START PAGE HTML
	/// INITIALISE variables
	$max_results = $cust_maxperpage; /// Number of results per page	
	$itemArray = array();
	$paginatorHref = $_SERVER['PHP_SELF'].'?orderby='.$cust_orderby.'&maxperpage='.$max_results;
	 
	$WHERE = " WHERE scw.id>0";
	$PreQuery = "SELECT scw.* FROM $FULLtablename AS scw";
	if($cust_region){
		$WHERE .= " AND scw.region LIKE \"%$cust_region%\"";		
		$paginatorHref .= "&region=".$cust_region."&category=".$cust_category;
	}
	if($cust_jobID){
		$WHERE .= " AND scw.id=$cust_jobID AND scw.date>'2009-03-01'";		
		$paginatorHref .= "&cust_jobID=".$cust_jobID;
	}
	if($cust_keyword){
		$WHERE .= " AND (scw.courseproduct LIKE '%".$cust_keyword."%' OR scw.organiser LIKE '%".$cust_keyword."%' OR scw.location LIKE '%".$cust_keyword."%')";		
		$paginatorHref .= "&keyword=".$cust_keyword;
	}
	$PreOrder = " ORDER BY scw.$cust_orderby"; /// Your sql statement	
	$WHERE .= $PreOrder;
	$PreTotalsQuery = "SELECT COUNT(*) as Num FROM $FULLtablename AS scw $WHERE ";
	
	include("includes/paginator.php");
	include("includes/paginator_order.php");
	echo $BuildOrderOptions;
	
	if(sizeof($itemArray)>0){	
		
		$BuildTable = '';
		$BuildTable .= '<div class="panel">';
			//$BuildTable .= '<p><span class="steptitle">Delete / Edit category:</span> Select an action from drop-down lists</p>';
			
		$BuildTable .= '<ul class="sortable-list-titles">';
			$BuildTable .= '<li>';
			$BuildTable .= '<span class="Date">&nbsp;Date</span>';
			$BuildTable .= '<span class="Name">Workshop Title</span>';
			$BuildTable .= '<span class="Category">Organiser</span>';
			$BuildTable .= '<span class="Category">Location</span>';
			//$BuildTable .= '<span class="Category">For Bookings</span>';
			$BuildTable .= '<span class="ActionsLite">Actions</span>';
			$BuildTable .= '</li>';
		$BuildTable .= '</ul>';
		

		$BuildTable .= '<ul class="sortable-list">';		
	
		for($tmpcount=0;$tmpcount<sizeof($itemArray);$tmpcount++){			
			$getlinks_id				= $itemArray[$tmpcount][$field_id];			
			$getlinks_date	= $itemArray[$tmpcount][$gp_branchTable['date']['field']];
			$getlinks_title			= $itemArray[$tmpcount][$gp_branchTable['name']['field']];
			$getlinks_organiser			= $itemArray[$tmpcount][$gp_branchTable['organiser']['field']];
			$getlinks_address			= $itemArray[$tmpcount][$gp_branchTable['address']['field']];
			$itemTitle = $getlinks_title.', '.$getlinks_organiser;
			
			// coloured rows
			$rowcolor = $CMSShared->GetRowColor($tmpcount,$colors);
			$BuildTable .= '<li id="Item_'.$getlinks_id.'" style="background:'.$rowcolor.'">';
			$BuildTable .= '<span class="Date">'.$getlinks_date.'</span>';

			$BuildTable .= '<span class="Name">'.$getlinks_title.'</span>';
			$BuildTable .= '<span class="Category">'.$getlinks_organiser.'</span>';
			$BuildTable .= '<span class="Category"><a href="admin_branch_list.php?uid='.$getlinks_id.'" title="View location">'.$getlinks_address.'</a></span>';//Location
			
			$SharedParams = '&param[prefArrayName]='.$prefArrayName.'&param[itemTitle]='.$itemTitle.'&param[curr_page]=workshops&param[curr_page_sub]=app_list';
			$BuildTable .= '<span class="ActionsLite">';
				$BuildTable .= '<ul>';
				$BuildTable .= '<li><a href="admin_genericPreview.php?param[uid]='.$getlinks_id.$SharedParams.'&param[page_title]=Preview workshop data &#124; '.$itemTitle.'&param[page_subtitle]=Preview details" class="Preview" title="Preview"><span>Preview</span></a></li>';
				$BuildTable .= '<li><a href="admin_genericUpdate.php?param[editid]='.$getlinks_id.$SharedParams.'&param[page_title]=Edit workshop data &#124; '.$itemTitle.'&param[page_subtitle]=Edit workshop details using the form below" class="Edit" title="Edit Link"><span>Edit</span></a></li>';
				$BuildTable .= '<li><a href="admin_genericDelete.php?param[deleteid]='.$getlinks_id.$SharedParams.'&param[field_id]='.$field_id.'&param[itemTitle]='.$itemTitle.'&param[PrevPage]=admin_branch_list.php&param[curr_page]=workshops&param[page_title]=Delete workshop&param[page_subtitle]=Delete this workshop" class="Delete" title="Delete"><span>Delete</span></a></li>';
			$BuildTable .= '</ul>';
			$BuildTable .= '</span>';
			$BuildTable .= '</li>';	
		}
		
		$BuildTable .= '</ul>';
		$BuildTable .= '</div>';
		
		echo $paginator;
		echo $BuildTable;		
		echo $paginator;
		
		//include("includes/sortable-list.php");
		
		$SubNavInner = '<ul id="SubNavInner">';
		//$SharedParams = '&param[itemTitle]='.$itemTitle.'&param[prefArrayName]=gp_branchTable&param[PrevPage]=admin_workshops_list.php&param[curr_page]=workshops';
		$SubNavInner .= '<li class="add"><a href="'.$GenericUpdateLink['add_workshop'].'" title="Add workshop to this database">Add workshop</a></li>';
		$SubNavInner .= '</ul>';
		echo $SubNavInner;
		
		
	} else { // if table is empty
		echo '<p class="prompt">There are currently no workshops listed in your database</p>';
	}
	

}
include("includes/admin_pagefooter.php");
	
?>