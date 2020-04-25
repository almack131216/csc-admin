<?php

$curr_page = "members";
$curr_page_sub = "members_list";
include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle("Members &#124; List All");
$BuildPage .= $PageBuild->AddPageTip("View member details");
$BuildPage .= $PageBuild->AddTag('mootools.css');
$BuildPage .= $PageBuild->AddTag('paginator.css');
$BuildPage .= $PageBuild->AddTag(array('dir'=>'addingajax/','file'=>'addingajax.js'));
$BuildPage .= $PageBuild->AddTag('JumpForms.js');
include("includes/admin_pageheader.php");


// (ORDER BY) DEFAULT PROPERTIES FOR QUERY
$cust_orderby		= "lname";
$cust_region		= 0;
$cust_maxperpage	= 0;


// (ORDER BY) IF SET...
if($_REQUEST['orderby']) 		$cust_orderby		= $_REQUEST['orderby'];
if($_REQUEST['maxperpage'])		$cust_maxperpage	= $_REQUEST['maxperpage'];
/*MemberRole if($_REQUEST['member_role'])	$cust_role			= $_REQUEST['member_role'];*/
if($_REQUEST['region'])			$cust_region		= $_REQUEST['region'];
if($_REQUEST['jobID'])			$cust_jobID			= $_REQUEST['jobID'];
if($_REQUEST['subcategory'])		$cust_subcategory		= $_REQUEST['subcategory'];
if($_REQUEST['keyword'])		$cust_keyword		= $_REQUEST['keyword'];


/////////// check to see if session is set
if( notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {
	$cust_orderby = $cust_orderby;
	$cust_orderby_arr = $gp_members_arr_orderby;
	$tablename = $db_clientTable_members;
	
	$field_id = $gp_membersTable['id']['field'];
	//print_r($gp_membersTable);

	$FULLtablename = $tablename;
	$prefArrayName = "gp_membersTable";
	if($_GET['success']) echo '<p class="good">Members table successfully updated</p>';

	///////////////////
	/// START PAGE HTML
	/// INITIALISE variables
	$max_results = $cust_maxperpage; /// Number of results per page	
	$itemArray = array();
	$paginatorHref = $_SERVER['PHP_SELF'].'?orderby='.$cust_orderby.'&maxperpage='.$max_results;
	 
	$WHERE = " WHERE tm.id>0";
	$PreQuery = "SELECT tm.* FROM $FULLtablename AS tm";
	
	/*MemberRole
	if($cust_role){
		
		switch ($cust_role) {
			case "pm":
				$WHERE .= " AND tm.rolePM = 1";		
				break;
			case "sb":
				$WHERE .= " AND tm.roleSb = 1";	
				break;
			case "cd":
				$WHERE .= " AND tm.roleCD = 1";	
				break;
			case "c":
				$WHERE .= " AND tm.roleC = 1";	
				break;
		}
		$paginatorHref .= "&member_role=".$cust_role;
	}
	*/
	$paginatorHref .= "&subcategory=".$cust_subcategory;
	
	if($cust_jobID){
		$WHERE .= " AND tm.id=$cust_jobID AND scw.date>'2009-03-01'";		
		$paginatorHref .= "&cust_jobID=".$cust_jobID;
	}
	
	if($cust_keyword){
		$WHERE .= " AND (tm.fname LIKE '%".$cust_keyword."%' OR tm.lname LIKE '%".$cust_keyword."%' OR tm.email LIKE '%".$cust_keyword."%')";		
		$paginatorHref .= "&cust_jobID=".$cust_jobID;
	}
	
	
	$PreOrder = " ORDER BY tm.$cust_orderby"; /// Your sql statement	
	$WHERE .= $PreOrder;
	$PreTotalsQuery = "SELECT COUNT(*) as Num FROM $FULLtablename AS tm $WHERE ";
	
	include("includes/paginator.php");
	
	if(sizeof($itemArray)>0){
		
		include("includes/paginator_order.php");
		echo $BuildOrderOptions;
		
		$BuildTable = '';
		$BuildTable .= '<div class="panel">';
		$BuildTable .= '<form action="admin_member_update.php" method="POST" name="view_all">';
			
		$BuildTable .= '<ul class="sortable-list-titles">';
			$BuildTable .= '<li>';
			$BuildTable .= '<span class="TickBoxes"><input type="checkbox" name="tickbox_all" title="Select All" class="tickbox" onClick="arr_id_fill('.sizeof($itemArray).');"/></span>';
			//$BuildTable .= '<span class="Date">&nbsp;Title</span>';
			$BuildTable .= '<span class="memberName">Member\'s Name</span>';
			//$BuildTable .= '<span class="memberName">Email</span>';
			$BuildTable .= '<span class="Date">Registered</span>';
			$BuildTable .= '<span class="Category">Region</span>';
			
			/*MemberRole
			$BuildTable .= '<span class="memberRole">PM</span>';
			$BuildTable .= '<span class="memberRole">SB</span>';
			$BuildTable .= '<span class="memberRole">CD</span>';
			$BuildTable .= '<span class="memberRole">C</span>';
			*/
			
			//$BuildTable .= '<span class="Category">For Bookings</span>';
			$BuildTable .= '<span class="Actions">Actions</span>';
			$BuildTable .= '</li>';
		$BuildTable .= '</ul>';
		

		$BuildTable .= '<ul class="sortable-list">';
		
		for($tmpcount=0;$tmpcount<sizeof($itemArray);$tmpcount++){			
			$getlinks_id		= $itemArray[$tmpcount][$gp_membersTable['id']['field']];			
			$getlinks_regdate	= $itemArray[$tmpcount][$gp_membersTable['registered']['field']];
			$getlinks_title		= $itemArray[$tmpcount][$gp_membersTable['title']['field']];
			$getlinks_name		= $itemArray[$tmpcount][$gp_membersTable['fname']['field']].' '.$itemArray[$tmpcount][$gp_membersTable['lname']['field']];
			$getlinks_email		= $itemArray[$tmpcount][$gp_membersTable['email']['field']];
			$getlinks_region	= $itemArray[$tmpcount][$gp_membersTable['region']['field']];
			/*MemberRole
			$getlinks_rolePM	= $itemArray[$tmpcount][$gp_membersTable['rolePm']['field']];
			$getlinks_roleSB	= $itemArray[$tmpcount][$gp_membersTable['roleSb']['field']];
			$getlinks_roleCD	= $itemArray[$tmpcount][$gp_membersTable['roleCD']['field']];
			$getlinks_roleC		= $itemArray[$tmpcount][$gp_membersTable['roleC']['field']];
			$rolePmID		= "rolePm_".$getlinks_id;
			$roleSbID		= "roleSb_".$getlinks_id;
			$roleCDID		= "roleCD_".$getlinks_id;
			$roleCID		= "roleC_".$getlinks_id;
			*/
			
			// coloured rows
			$rowcolor = $CMSShared->GetRowColor($tmpcount,$colors);
			$BuildTable .= '<li id="Item_'.$getlinks_id.'" style="background:'.$rowcolor.'">';
			$BuildTable .= '<span class="TickBoxes"><input name="arr_id[]" type="checkbox" value="'.$getlinks_id.'" class="tickbox" onClick="arr_id_checkselected()"/></span>';
			
			//$BuildTable .= '<span class="Date">'.$getlinks_title.'</span>';

			$BuildTable .= '<span class="memberName">';
			
			$tablename_access = $db_client.".tbl_members_access";
			
			$PageQuery = "SELECT m.Id,ma.id AS accessID,ma.memberID,ma.sectionID,ma.status,csc.id,csc.subcategory";
			$PageQuery .= " FROM $db_client.tbl_members AS m, $db_client.tbl_members_access AS ma, $db_client.catalogue_subcats AS csc";
			$PageQuery .= " WHERE ma.sectionID=csc.id AND m.Id=ma.memberID AND m.Id=$getlinks_id";
			$PageQuery .= " GROUP BY csc.id";
			$PageQuery .= " ORDER BY csc.subcategory ASC";
			$PageResult = mysql_query($PageQuery);
			
			if($PageResult && mysql_num_rows($PageResult)>=1){
				$pending = false;
				$BuildTable .= '<a href="javascript:OpenCloseMemberPages(\'memberDiv'.$getlinks_id.'\');" id="memberDiv'.$getlinks_id.'_Link" title="Show extranet access for \''.$getlinks_name.'\'" class="subcategoryDivLink">'.$getlinks_name.'</a>';
				
			
				$HiddenPageList = '<span id="memberDiv'.$getlinks_id.'" class="hidden">';
				$HiddenPageList .= '<ul class="subcategoryList">';
			
				for($i=0;$i<mysql_num_rows($PageResult);$i++){
					$PageRow = mysql_fetch_array($PageResult);
					$fieldname="status";
					$accessIDName = "accessID_".$PageRow['accessID'];
					switch($PageRow['status']){
						case 2:
							$pending = true;
							$tmpArray = array('status'=>'pending','nextstatus'=>'enable','class'=>'light_amber','title'=>'PENDING - Click To Enable');break;
						case 1: $tmpArray = array('status'=>'enable','nextstatus'=>'disable','class'=>'light_green','title'=>'ENABLED - Click To Disable');break;
						case 0: $tmpArray = array('status'=>'disable','nextstatus'=>'pending','class'=>'light_red','title'=>'DISABLED - Click To Mark As Pending');break;
					}

					$HiddenPageList .= '<li>';
					$HiddenPageList .= '<a href="javascript:changeAccessStatus(\''.$tablename_access.'\',\''.$PageRow['accessID'].'\',\''.$fieldname.'\',\''.$tmpArray['nextstatus'].'\')" id="'.$accessIDName.'" name="'.$accessIDName.'" title="'.$tmpArray['title'].'" class="'.$tmpArray['class'].'"><span>'.$tmpArray['status'].'</span></a>';
					$HiddenPageList .= $PageRow['subcategory'];					
					$HiddenPageList .= '</li>';
				}
				
				$HiddenPageList .= '</ul>';
				$HiddenPageList .= '</span>';
				
				if($pending) $BuildTable .= '<img src="./includes/icons/light_amber.gif" />';
				$BuildTable .= $HiddenPageList;
			}else{
				$BuildTable .= $getlinks_name;
			}
			$BuildTable .= '</span>';		
			
			//$BuildTable .= '<span class="memberName">'.$getlinks_email.'</span>';
			$BuildTable .= '<span class="Date">'.$getlinks_regdate.'</span>';//Location
			//$BuildTable .= '<span class="Category">'.$getlinks_detail.'</span>';
			
			$BuildTable .= '<span class="Category">'.$getlinks_region.'</span>';//region
			/*MemberRole
			$BuildTable .= '<span class="memberRole">';
			
			if($getlinks_rolePM==1){
				$BuildTable .= '<a href="javascript:changeRoleStatus(\''.$tablename.'\',\''.$getlinks_id.'\',\'rolePm\',\'disable\')" id="'.$rolePmID.'" name="'.$getlinks_id.'" title="Click to Disable" class="light_green"><span>enabled</span></a>';
			}else{
				$BuildTable .= '<a href="javascript:changeRoleStatus(\''.$tablename.'\',\''.$getlinks_id.'\',\'rolePm\',\'enable\')" id="'.$rolePmID.'" name="'.$getlinks_id.'" title="Click to Enable" class="light_red"><span>disabled</span></a>';
			}
			
			$BuildTable .= '</span>';
			$BuildTable .= '<span class="memberRole">';
			
			if($getlinks_roleSB==1){
				$BuildTable .= '<a href="javascript:changeRoleStatus(\''.$tablename.'\',\''.$getlinks_id.'\',\'roleSb\',\'disable\')" id="'.$roleSbID.'" name="'.$getlinks_id.'" title="Click to Disable" class="light_green"><span>enabled</span></a>';
			}else{
				$BuildTable .= '<a href="javascript:changeRoleStatus(\''.$tablename.'\',\''.$getlinks_id.'\',\'roleSb\',\'enable\')" id="'.$roleSbID.'" name="'.$getlinks_id.'" title="Click to Enable" class="light_red"><span>disabled</span></a>';
			}
			
			$BuildTable .= '</span>';
			$BuildTable .= '<span class="memberRole">';
			if($getlinks_roleCD==1){
				$BuildTable .= '<a href="javascript:changeRoleStatus(\''.$tablename.'\',\''.$getlinks_id.'\',\'roleCD\',\'disable\')" id="'.$roleCDID.'" name="'.$getlinks_id.'" title="Click to Disable" class="light_green"><span>enabled</span></a>';
			}else{
				$BuildTable .= '<a href="javascript:changeRoleStatus(\''.$tablename.'\',\''.$getlinks_id.'\',\'roleCD\',\'enable\')" id="'.$roleCDID.'" name="'.$getlinks_id.'" title="Click to Enable" class="light_red"><span>disabled</span></a>';
			}
			$BuildTable .= '</span>';
			$BuildTable .= '<span class="memberRole">';
			
			if($getlinks_roleC==1){
				$BuildTable .= '<a href="javascript:changeRoleStatus(\''.$tablename.'\',\''.$getlinks_id.'\',\'roleC\',\'disable\')" id="'.$roleCID.'" name="'.$getlinks_id.'" title="Click to Disable" class="light_green"><span>enabled</span></a>';
			}else{
				$BuildTable .= '<a href="javascript:changeRoleStatus(\''.$tablename.'\',\''.$getlinks_id.'\',\'roleC\',\'enable\')" id="'.$roleCID.'" name="'.$getlinks_id.'" title="Click to Enable" class="light_red"><span>disabled</span></a>';
			}
			$BuildTable .= '</span>';
			*/
			
			$SharedParams = '&param[itemTitle]='.$getlinks_name.'&param[curr_page]=members&param[curr_page_sub]=members_list&param[prefArrayName]=gp_membersTable';
			$BuildTable .= '<span class="Actions">';
				$BuildTable .= '<ul>';
				$BuildTable .= '<li><a href="mailto:'.$getlinks_email.'?subject=JoeBloggs Extranet" title="Contact This Person: '.$getlinks_email.'" class="Contact"><span>Contact This Person</span></a></li>';
				$BuildTable .= '<li><a href="admin_genericPreview.php?param[uid]='.$getlinks_id.$SharedParams.'&param[page_title]=Members &#124; Preview Details&param[page_subtitle]=Preview details" class="Preview" title="Preview Details"><span>Preview</span></a></li>';
				$BuildTable .= '<li><a href="admin_genericUpdate.php?param[editid]='.$getlinks_id.$SharedParams.'&param[page_title]=Members &#124; Edit Details&param[page_subtitle]=Edit details using the form below" class="Edit" title="Edit Details"><span>Edit</span></a></li>';
				$BuildTable .= '<li><a href="admin_member_delete.php?currentmember='.$getlinks_id.'" class="Delete" title="Delete Details"><span>Delete</span></a></li>';
			$BuildTable .= '</ul>';
			$BuildTable .= '</span>';
			$BuildTable .= '</li>';	
		}
		
		$BuildTable .= '</ul>';
		
		// show Batch Process Buttons (for processing multiple items)
		$BuildBatchButs = '';
		$BuildBatchButs .= '<ul class="sortable-list">';
		
		$BuildBatchButs .= '<input type="hidden" name="thisList" value="members">';
		$BuildBatchButs .= '<li id="extrabuts" class="hidden">';
			$BuildBatchButs .= '<input type="submit" name="deletemarked" title="Delete Marked" id="deletemarked" value="deletemarked">';
			//$BuildBatchButs .= '<span class="ChangeStatus"><p><strong>(OR)</strong>&nbsp;Change status of selected items:&nbsp;&nbsp;</p>';
			//$BuildBatchButs .= list_status_select();
			//$BuildBatchButs .= '<input type="submit" name="editmarked" title="Edit Marked" id="update" class="hidden" value="editmarked">';
			//$BuildBatchButs .= '</span>';			
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
		echo '<p class="prompt">There are no members listed in your database matching your criteria</p>';
	}

}
include("includes/admin_pagefooter.php");
	
?>