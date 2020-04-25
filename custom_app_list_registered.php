<?php

$page_title = "Links";
$page_subtitle = "Add / Set Link Options";
$curr_page = "applications";
$curr_page_sub = "app_list";

// (ORDER BY) DEFAULT PROPERTIES FOR QUERY
$cust_orderby		= "bc_dateregistered";
$cust_asc_or_desc	= "desc";
//$cust_status		= 0;
$cust_maxperpage	= 0;


// (ORDER BY) IF SET...
if($_REQUEST['orderby']) 		$cust_orderby		= $_REQUEST['orderby'];
if($_REQUEST['asc_or_desc'])	$cust_asc_or_desc	= $_REQUEST['asc_or_desc'];
//if($_REQUEST['status'])			$cust_status		= $_REQUEST['status'];
if($_REQUEST['maxperpage'])		$cust_maxperpage	= $_REQUEST['maxperpage'];

include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if( notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {
	$cust_orderby_arr = $gp_custom_arr_orderby;
	$field_id = 'bc_id';
	$tablename = "bm_bc_registrations";
	$FULLtablename = $db_client.".".$tablename;
	

	///////////////////
	/// START PAGE HTML
	/// INITIALISE variables
	$max_results = $cust_maxperpage; /// Number of results per page	
	$itemArray = array();
	$paginatorHref = $_SERVER['PHP_SELF'].'?orderby='.$cust_orderby.'&asc_or_desc='.$cust_asc_or_desc.'&maxperpage='.$max_results;
	$WHERE = "WHERE $field_id>0";
	
	include("includes/paginator.php");
	include("includes/paginator_order.php");
	echo $BuildOrderOptions;
	
	if(sizeof($itemArray)>0){	
		
		$BuildTable = '';
		$BuildTable .= '<div class="panel">';
			//$BuildTable .= '<p><span class="steptitle">Delete / Edit category:</span> Select an action from drop-down lists</p>';
			
		$BuildTable .= '<ul class="sortable-list-titles">';
			$BuildTable .= '<li>';
			$BuildTable .= '<span class="Date">&nbsp;Registered</span>';
			$BuildTable .= '<span class="Category">First Name</span>';
			$BuildTable .= '<span class="Category">Surame</span>';
			$BuildTable .= '<span class="Category">Town</span>';
			$BuildTable .= '<span class="Category">Member</span>';
			$BuildTable .= '<span class="Actions">Actions</span>';
			$BuildTable .= '</li>';
		$BuildTable .= '</ul>';
		

		$BuildTable .= '<ul class="sortable-list">';		
	
		for($tmpcount=0;$tmpcount<sizeof($itemArray);$tmpcount++){			
			$getlinks_id				= $itemArray[$tmpcount]['bc_id'];			
			$getlinks_dateregistered	= $itemArray[$tmpcount]['bc_dateregistered'];
			$getlinks_firstname			= $itemArray[$tmpcount]['bc_firstname'];
			$getlinks_surname			= $itemArray[$tmpcount]['bc_lastname'];
			$getlinks_town				= $itemArray[$tmpcount]['bc_town_city'];
			$getlinks_membernum			= $itemArray[$tmpcount]['bc_bonuscardnum'];
			
			// coloured rows
			$rowcolor = $CMSShared->GetRowColor($tmpcount,$colors);
			$BuildTable .= '<li id="Item_'.$getlinks_id.'" style="background:'.$rowcolor.'">';
			$BuildTable .= '<span class="Date">'.$getlinks_dateregistered.'</span>';

			$BuildTable .= '<span class="Category">'.$getlinks_firstname.'</span>';
			$BuildTable .= '<span class="Category">'.$getlinks_surname.'</span>';
			$BuildTable .= '<span class="Category">'.$getlinks_town.'</span>';
			$BuildTable .= '<span class="Category">'.$getlinks_membernum.'</span>';
			
			$SharedParams = '&param[itemTitle]='.$getlinks_firstname.' '.$getlinks_surname.'&param[tablename]='.$tablename.'&param[field_id]=bc_id&param[curr_page]=applications&param[curr_page_sub]=app_list';
			$BuildTable .= '<span class="Actions">';
				$BuildTable .= '<ul>';
				$BuildTable .= '<li><a href="custom_app_preview.php?param[uid]='.$getlinks_id.$SharedParams.'&param[prefArrayName]=gp_registrationsTable&param[page_title]=Preview Applicant Data&param[page_subtitle]=Preview details" class="Preview" title="Preview"><span>Preview</span></a></li>';
				$BuildTable .= '<li><a href="admin_genericUpdate.php?param[editid]='.$getlinks_id.$SharedParams.'&param[prefArrayName]=gp_registrationsTable&param[page_title]=Edit Applicant Data&param[page_subtitle]=Edit Applicant details using the form below" class="Edit" title="Edit Link"><span>Edit</span></a></li>';
				$BuildTable .= '<li><a href="admin_genericDelete.php?param[deleteid]='.$getlinks_id.$SharedParams.'&param[prefArrayName]=gp_branchTable&param[field_id]=bc_id&param[itemTitle]='.$getlinks_firstname.' '.$getlinks_surname.'&param[PrevPage]=admin_workshops_list.php&param[curr_page]=applications&param[page_title]=Delete applicant&param[page_subtitle]=Delete this applicant" class="Delete" title="Delete"><span>Delete</span></a></li>';
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
		
		
	} else { // if table is empty
		echo '<p class="prompt">There are currently no applicants listed in your database</p>';
	}
	

}
include("includes/admin_pagefooter.php");
	
?>