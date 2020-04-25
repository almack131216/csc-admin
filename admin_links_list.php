<?php

$curr_page = "links";
$curr_page_sub	= "links_list";

include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle("Friendly URLs &#124; List");
$BuildPage .= $PageBuild->AddPageTip("Add / Edit Friendly URLs");
$BuildPage .= $PageBuild->AddTag('mootools.css');
$BuildPage .= $PageBuild->AddTag(array('dir'=>'addingajax/','file'=>'addingajax.js'));
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if( notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {
	if(!gp_enabled('links')) exit();
	$tablename = $db_clientTable_links;
	
	$SortableList = true;
	$SetStatus = false;
	$sortableDetail = "position";//SortableList
	$fieldname = "directory";//SortableList

	///////////////////
	/// START PAGE HTML
	
	//////////////////////////////////////
	//PRINT successful add / edit message
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

	$getlinks_query="SELECT * FROM $tablename";
	//SortableList
	if($SortableList){
		$getlinks_query.=" ORDER BY position asc";
	}else{
		$getlinks_query.=" ORDER BY date desc";
	}

	//echo '<br>(FB):'.$getlinks_query;
	$getlinks_result	= mysql_query($getlinks_query);
	
	
	// check table contents
	if ($getlinks_result && mysql_num_rows($getlinks_result) >= 1) {
		$getlinks_numrows 	= mysql_num_rows($getlinks_result);	
		$tmp_numrows		= $getlinks_numrows;
		
		$buildTable = '';
		$buildTable .= '<div class="panel">';
			//$buildTable .= '<p><span class="steptitle">Delete / Edit category:</span> Select an action from drop-down lists</p>';
			
			$buildTable .= '<ul class="sortable-list-titles">';
				$buildTable .= '<li>';
				$buildTable .= '<span class="OneDigit">#Pos</span>';
				$buildTable .= '<span class="Date">Date</span>';
				$buildTable .= '<span class="Date">Directory</span>';
				$buildTable .= '<span class="BigName">URL</span>';
				$buildTable .= '<span class="Actions">Actions</span>';
				$buildTable .= '</li>';
			$buildTable .= '</ul>';
			
			//SortableList
			if($SortableList){
				$buildTable .= '<script type="text/javascript" src="'.$adminroot.'includes/js/scriptaculous/lib/prototype.js"></script>'."\r\n";
				$buildTable .= '<script type="text/javascript" src="'.$adminroot.'includes/js/scriptaculous/src/scriptaculous.js"></script>'."\r\n";
				$buildTable .= '<ul id="SortableList" class="sortable-list">';
			}else{
				$buildTable .= '<ul class="sortable-list">';
			}
			
		
			for($i=0;$i<$getlinks_numrows;$i++) {
				
				$getlinks_row		= mysql_fetch_array($getlinks_result);			
				$getlinks_id		= $getlinks_row['id'];
				$statusID			= "statusID_".$getlinks_id;
				$getlinks_pos		= $getlinks_row['position'];
				$getlinks_date		= $getlinks_row['date'];
				
				$getlinks_name		= $getlinks_row['directory'];
				$getlinks_address	= $getlinks_row['url'];
				$getlinks_status	= $getlinks_row['status'];			
				
				if($SortableList){
					$tmp_rowPosition = $getlinks_pos;
				}else{
					$tmp_rowPosition = $i;
				}
				
				// coloured rows
				$rowcolor = $CMSShared->GetRowColor($i,$colors);
				$buildTable .= '<li id="Item_'.$getlinks_id.'" style="background:'.$rowcolor.'">';
				$buildTable .= '<span class="OneDigit">'.$tmp_rowPosition.'</span>';
				$buildTable .= '<span class="Date">'.$getlinks_date.'</span>';
				$buildTable .= '<span class="Date">'.$getlinks_name.'</span>';
				$buildTable .= '<span class="BigName">'.$getlinks_address.'</a></span>';
				
				$buildTable .= '<span class="Actions">';
					$buildTable .= '<ul>';
					if($SortableList) $buildTable .= '<li><a href="#" class="Move" title="Move link by clicking and dragging into place"><span>Move Link</span></a></li>';
					if($SetStatus){
						if($getlinks_status==1){
							$buildTable .= '<li><a href="javascript:changeStatus(\''.$tablename.'\',\''.$getlinks_id.'\',\'hide\')" id="'.$statusID.'" name="'.$getlinks_id.'" class="Showing" title="Click to Hide Link"><span>Show / Hide Link</span></a></li>';
						}else{
							$buildTable .= '<li><a href="javascript:changeStatus(\''.$tablename.'\',\''.$getlinks_id.'\',\'show\')" id="'.$statusID.'" name="'.$getlinks_id.'" class="Hidden" title="Click to Show Link"><span>Show / Hide Link</span></a></li>';
						}
					}
					$SharedParams = '&param[itemTitle]='.$getlinks_name.'&param[curr_page]=links&param[curr_page_sub]=links_list&param[prefArrayName]=gp_linksTable&param[PrevPage]=admin_links_list.php';
					$buildTable .= '<li><a href="'.$getlinks_address.'" target="_blank" class="Preview" title="Preview Link"><span>Preview</span></a></li>';
					$buildTable .= '<li><a href="admin_genericUpdate.php?param[editid]='.$getlinks_row['id'].$SharedParams.'&param[page_title]=Friendly URLs &#124; Edit&param[page_subtitle]=Edit details using the form below" class="Edit" title="Edit Friendly URL"><span>Edit Friendly URL</span></a></li>';
					$buildTable .= '<li><a href="admin_genericDelete.php?param[deleteid]='.$getlinks_row['id'].$SharedParams.'&param[page_title]=Friendly URLs &#124; Delete Friendly URL&param[page_subtitle]=Delete This Friendly URL" title="Delete Friendly URL" class="Delete"><span>Delete Friendly URL</span></a></li>';
				$buildTable .= '</ul>';
				$buildTable .= '</span>';
				$buildTable .= '</li>';	
			}
			
			$buildTable .= '</ul>';
		$buildTable .= '</div>';
		echo $buildTable;
		
		if($SortableList) include("includes/sortable-list.php");//SortableList
				
		$SubNavInner = '<ul id="SubNavInner">';
			$SubNavInner .= '<li class="add"><a href="admin_genericUpdate.php?param[adding]=true'.$SharedParams.'&param[page_title]=Friendly URLs &#124; Add a new Friendly URL&param[page_subtitle]=Add a new Friendly URL using the form below" title="add new Friendly URL">add new Friendly URL</a></li>';
		$SubNavInner .= '</ul>';
		echo $SubNavInner;
		
	} else { // if table is empty
		echo '<p class="prompt">There are currently no Links listed in your database</p>';
	}
	

}
include("includes/admin_pagefooter.php");
	
?>