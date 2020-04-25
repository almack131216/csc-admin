<?php

$PrevPage = "javascript:history.go(-1);";
if($_REQUEST['param']) $GenericProps = $_REQUEST['param'];

if(isset($GenericProps['uid'])){
	$uid = $GenericProps['uid'];
	$prefArrayName = $GenericProps['prefArrayName'];
	
	$itemTitle = $GenericProps['itemTitle'];
	$curr_page = $GenericProps['curr_page'];
	$curr_page_sub = $GenericProps['curr_page_sub'];
	
	$page_title		= $GenericProps['page_title'];	
	$page_subtitle	= $GenericProps['page_subtitle'];
}

include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle($page_title);
$BuildPage .= $PageBuild->AddPageTip($page_subtitle);
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if(notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {
	
	$boolArray = array("");
	
	if($uid) {
		if($prefArrayName) $prefArray = ${$prefArrayName};//if this array exists and is populated then show form according to fields specified in this array
	
		$tablename = $prefArray['tablename'];
		$field_id = $prefArray['id']['field'];
		
		if($tablename){
			$FULLtablename = $db_client.".".$tablename;
			$query=mysql_query("SELECT * FROM $FULLtablename") or die (mysql_error());
					
			$field_divs = $dbFields->mysql_field_array( $query );		
			$field_divs_type = $dbFields->mysql_field_type_array( $query );
		
			if($uid){
				$value_query = "SELECT * FROM $FULLtablename WHERE $field_id=$uid LIMIT 1";
				$value_result = mysql_query($value_query);
				if($value_result && mysql_num_rows($value_result)==1){
					$PrintForm = true;
					$value_array = mysql_fetch_row($value_result);
					for ($i = 0; $i < sizeof($field_divs); $i++) {
						${"value_".$i} = $value_array[$i];
					}
				}else{
					$PrintForm = false;
				}
			}

			
			///////////////////////
			/// START PRINTING FORM			
			if($PrintForm){
				$buildForm = '';
		
				$buildForm .= '<div class="panel">';
				$buildForm .= '<table width="100%" border="1" cellpadding="0" cellspacing="0">';
				$buildForm .= '<tr class="table_titles">';
				$buildForm .= '<td width="40%">&nbsp;<strong>Field</strong></td>';
				$buildForm .= '<td width="60%"><strong>Value</strong></td>';
				
				$fields = mysql_num_fields($query);
				$tmpcountShown = 0;		
				for($i=1;$i<$fields;$i++) {//starts on 1 to avoid ID				
					$fieldValue = ${"value_".$i};
					
						
					//$row = mysql_fetch_row($result);
					$fname=mysql_field_name($query, $i);
					$ftype=mysql_field_type($query, $i);
					$flength = mysql_field_len($query, $i);
					$fflags = mysql_field_flags($query, $i);
					

					if($prefArray[$fname]['boolean']){
						if($fieldValue=='1' || $fieldValue==1){
							$fieldValue = "YES";
						}else{
							$fieldValue = "NO";
						}
					}
					
					if($prefArray[$fname]['boolean'] || (!empty($fieldValue) && $dbFields->FormatFieldName($fname) && !$prefArray[$fname]['skip'])){//if using a pref array then limit to fields specified in prefs
						$rowcolor = $CMSShared->GetRowColor($tmpcountShown,$colors);
						
						//put email address into clickable mailto tag
						if($CMSTextFormat->StringContains($fname,"email") && $CMSForms->ValidEmail($fieldValue)) $fieldValue = '<a href="mailto:'.$fieldValue.'?subject=Contact this person" title="Contact this person">'.$fieldValue.'</a>';						
								
						
						$buildForm .= '<tr class="body_general" bgcolor='.$rowcolor.'>';
						$buildForm .= '<td>&nbsp;'.$dbFields->FormatFieldName($fname).'</td><td>'.$fieldValue.'</td>';
						$buildForm .= '</tr>';
						$tmpcountShown++;
					}
				}
				
				$buildForm .= '</table>';
				$buildForm .= '</div>';
				
				echo $buildForm;
				
				if($uid || $addedid){
					if(!$itemTitle) $itemTitle = "Unknown";
					$SharedParams = '&param[itemTitle]='.$itemTitle.'&param[prefArrayName]='.$prefArrayName.'&param[curr_page]='.$curr_page;
					$SubNavInner = '<ul id="SubNavInner">';
					if($prefArrayName=="gp_branchTable") $SubNavInner .= '<li class="map"><a href="admin_branch_list.php?uid='.$uid.'" title="update map position">map position</a></li>';		
					$SubNavInner .= '<li class="edit"><a href="admin_genericUpdate.php?param[editid]='.$uid.$SharedParams.'&param[curr_page_sub]='.$curr_page_sub.'&param[page_title]=Edit details&param[page_subtitle]=Edit details" title="Edit">Edit</a></li>';
					if($prefArrayName=="gp_membersTable") $SubNavInner .= '<li class="delete"><a href="admin_member_delete.php?currentmember='.$uid.'" title="Delete">delete</a></li>';		
					$SubNavInner .= '</ul>';
					echo $SubNavInner;
				}
			}
		}

	}else{
		echo '<p class="error">This page was not accessed properly. Please try again.</p>';
	}
	
	echo $ReturnPanel;
}
include("includes/admin_pagefooter.php");
	
?>