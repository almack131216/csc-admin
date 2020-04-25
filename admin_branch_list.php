<?php

$curr_page = "workshops";
$curr_page_sub = "branch_list";
$uid = $_REQUEST['uid'];
include("includes/classes/PageBuild.php");
include("includes/classes/GoogleMap.php");
$BuildPage .= $PageBuild->AddPageTitle("Workshops &#124; Locations");
$BuildPage .= $PageBuild->AddPageTip("Please ensure you enter details accurately as these details go onto your website");
$BuildPage .= $PageBuild->AddTag('JumpForms.js');
$BuildPage .= $PageBuild->AddGoogleMagTags();
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
} else {	

	if(empty($gp_branchTable)) exit();//Check prefs file for branch database table info
	$GMap->FieldInit();
	
	//if($field_name == $field_address){
	//	$field_append = $field_town;
	//}else{
		$field_append = $field_address;
	//}
	
	if($_POST['UpdateMap']){
		$map_lat = $_POST['map_lat'];
		$map_lng = $_POST['map_lng'];
		$query = "UPDATE $db_client.$tablename SET $field_map_lat=$map_lat,$field_map_lng=$map_lng";
		if($field_positionset) $query .= ", $field_positionset='1'";
		$query .= " WHERE $field_id=$uid LIMIT 1";
		$result = $db->mysql_query_log($query);
		if($result) $success=true;
		//echo '(FB):'.$query;
	}

	/////////// If added / updated
	if($_REQUEST['success'] || $success) echo '<p class="good">Workshop successfully updated</p>';
	
	
				
	/////////// Select From List
	echo '<div class="panel_oneline">';
		echo '<p><span class="steptitle">Select Workshop From List:&nbsp;&nbsp;</span></p>';
		echo '<div class="inner_right">';
			$tmpQuery = "SELECT $field_id, $field_name, $field_town";
			//if($field_address != $field_name) $tmp .= ", $field_address ";
			$tmpQuery .= " FROM $db_client.$tablename WHERE id_xtra=0 ORDER BY id asc";
			echo $tmpQuery;
			$ListPropsArr = array('name'=>'uid','field_id'=>$field_id,'query'=>$tmpQuery,'dbTable_field'=>$field_name,'dbTable_field_append'=>$field_append,'selected'=>$uid,'optionValue'=>$_SERVER['PHP_SELF'].'?uid=','jump'=>true);
			echo $CMSSelectOptions->Build($ListPropsArr);
			
			//echo $ListPropsArr['query'];
		echo '</div>';
	echo '</div>';
	
	if(!$uid) $uid = $gp_branchTable['id_default'];
		
	$store_query = "SELECT * FROM $db_client.$tablename WHERE $field_id=$uid LIMIT 1";
	$store_result = mysql_query($store_query);
	if($store_result && mysql_num_rows($store_result)==1){
		
		echo '<div class="panel_oneline">';
			echo '<div class="inner_right">';					
				
			$row = mysql_fetch_array($store_result);	
			$itemTitle = $row[$field_name].', '.$row[$field_town];
			
			$BranchName = '';
			$BranchData = '';
			$BranchData .= '<p><span class="steptitle">Workshop Location Details:</span></p>';
			if($field_store_number){
				$BranchData .= '<p><strong>Workshop Number: ';
				if(empty($row[$field_store_number])){
					$BranchData .= 'HEAD OFFICE (0)';
					$BranchName .= '0';
				}else{
					$BranchData .= $row[$field_store_number];
					$BranchName .= $row[$field_store_number];
				}
				$BranchData .= '</strong></p>';
			}
			
			if(!empty($row[$field_name]))		$BranchData .= $row[$field_name].'<br>';//(field_name)
			if(!empty($row[$field_address]) && $row[$field_name]!=$row[$field_address])	$BranchData .= $row[$field_address].'<br>';//(field_address)
			if(!empty($row[$field_town]))		$BranchData .= $row[$field_town].'<br>';//(field_town)
			if(!empty($row[$field_county]))		$BranchData .= $row[$field_county].'<br>';//(field_county)
			if(!empty($row[$field_postcode]))	$BranchData .= $row[$field_postcode];//(field_postcode)
			
			if(!empty($row[$field_date]))	$BranchData .= '<br><br><strong>Date:</strong> '.$row[$field_date].'<br>';//(field_postcode)
			
			if(!empty($row[$field_name]))		$BranchName .= ", ".$row[$field_name];
			if(!empty($row[$field_address]))	$BranchName .= ", ".$row[$field_address];
			if(!empty($row[$field_town]))		$BranchName .= ", ".$row[$field_town];
			
			$BranchData .= '</p><p>';
			if(!empty($row[$field_tel]))		$BranchData .= 'Telephone: '.$row[$field_tel].'()<br>';
			if(!empty($row[$field_fax]))		$BranchData .= 'Fax: '.$row[$field_fax];
			$BranchData .= '</p>';

			if(strtolower($row[$field_showcomments])=="y" && !empty($row[$field_comments]))	$BranchData .= '<p><strong>Show Text:</strong><br>'.$row[$field_comments].'</p>';
			if(!empty($row[$field_openingtimes]))	$BranchData .= '<p><strong>Opening Times:</strong><br>'.$row[$field_openingtimes].'</p>';

			echo $BranchData;
		
			// CUSTOM ACTION
			$Jobs_query = "SELECT c.id AS myid, c.*,bm.*,bmj.* FROM $db_client.catalogue AS c, $db_client.bm_branches_master AS bm, bm_job_descriptions AS bmj WHERE c.status=1 AND c.detail_3=$uid AND bm.id=$uid AND c.detail_2=bmj.job_id ORDER BY bmj.job_title ASC";
			$Jobs_result = mysql_query($Jobs_query);
			if($Jobs_result && mysql_num_rows($Jobs_result)>=1){
				$JobsListed = '<p>&nbsp;</p>';
				//$JobsListed .= '<p><strong>Vacancies at this '.$CommonCustomWords['branch'].':</strong></p>';
				for($i=0;$i<mysql_num_rows($Jobs_result);$i++){
					$row = mysql_fetch_array($Jobs_result);
					$JobsListed .= '<p>'.$row['job_title'].', '.$row['storenametown'];
					$JobsListed .= '<br><a href="admin_catalogue_upload.php?editid='.$row['myid'].'" title="View / Edit this job">Edit Job</a>';
					
					$Apps_query = "SELECT job_id FROM bm_job_applications WHERE job_id=".$row['myid']." AND application_date_applied_for>'2009-03-01'";
					$Apps_result = mysql_query($Apps_query);
					if($Apps_result && mysql_num_rows($Apps_result)>=1) $JobsListed .= '&nbsp;&#124;&nbsp;<a href="admin_workshops_list.php?jobID='.$row['myid'].'" title="View Applicants">'.mysql_num_rows($Apps_result) .' Applications</a>';
				}
				$JobsListed .= '</p>';
				echo $JobsListed;
			}
			echo '</div>';
			echo '<div id="map" style="loat:left;width:440px;height:350px;background:#ccc;margin-bottom:5px;">';				
			echo '</div>';
			
			echo '<div class="gmapDetails">';
			if(!$row[$field_map_lat] || !$row[$field_map_lng]) echo '<p class="notice">No coordinates saved. Move marker and click \'Update\' to save</p>';
			echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" class="UpdateMap">';				
			echo '<input type="hidden" name="uid" value="'.$uid.'">';				
			echo '<div class="innerBox">Lat: <input type="text" name="map_lat" id="lat" class="VerySmall" value="'.$row[$field_map_lat].'" readonly=true></div>';
			echo '<div class="innerBox">Long: <input type="text" name="map_lng" id="lng" class="VerySmall" value="'.$row[$field_map_lng].'" readonly=true></div>';
			echo '<div class="innerBox"><input type="submit" class="GenericUpdate" name="UpdateMap" value="Save Location"></div>';
			echo '</form>';
			echo '</div>';			
			
			$buildGM = '';
			$buildGM .= '<script type="text/javascript">';
				if($row[$field_map_lat] && $row[$field_map_lng]){
					$buildGM .= 'addLoadEvent(mapLoadByCoords(\''.$row[$field_map_lat].'\',\''.$row[$field_map_lng].'\'));'."\n";
				}else{				
					$buildGM .= 'addLoadEvent(mapLoadByPostcode(\''.$row[$field_postcode].'\',0));'."\n";
				}
				$buildGM .= 'addUnLoadEvent(GUnload);'."\n";			
			$buildGM .= '</script>';
			echo $buildGM;
			
			echo '</div>';
		echo '</div>';
		
		$SubNavInner = '<ul id="SubNavInner">';
		$SharedParams = '&param[itemTitle]='.$itemTitle.'&param[prefArrayName]=gp_branchTable&param[PrevPage]=admin_workshops_list.php&param[curr_page]=workshops';
		//$SubNavInner .= '<li class="add"><a href="'.$GenericUpdateLink['add_branch'].'" title="Add '.$CommonCustomWords['branch'].'">Add '.$CommonCustomWords['branch'].'</a></li>';
		$SubNavInner .= '<li class="edit"><a href="admin_genericUpdate.php?param[editid]='.$uid.$SharedParams.'&param[curr_page_sub]=branch_list&param[page_title]=Edit Workshop Details&param[page_subtitle]=Edit Workshop Details for '.$itemTitle.'" title="Edit Workshop Details">Edit Workshop Details</a></li>';
		//$SubNavInner .= '<li class="attach"><a href="admin_catalogue_upload.php?UploadFileSkipped=true&category=5&detail_3='.$uid.'" title="Attach Vacancy">attach vacancy</a></li>';
		$SubNavInner .= '<li class="delete"><a href="admin_genericDelete.php?param[deleteid]='.$uid.$SharedParams.'&param[curr_page_sub]=branch_delete&param[page_title]=Delete Workshop&param[page_subtitle]=Delete Workshop" title="Delete Workshop">Delete Workshop</a></li>';		
		$SubNavInner .= '</ul>';
		echo $SubNavInner;
	}
		

}
include("includes/admin_pagefooter.php");
?>