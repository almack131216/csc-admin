<?php
$PrevPage = "javascript:history.go(-1);";
if($_REQUEST['param']) $GenericProps = $_REQUEST['param'];

if(isset($GenericProps['uid'])){
	$uid = $GenericProps['uid'];
	$itemTitle = $GenericProps['itemTitle'];
	$tablename = $GenericProps['tablename'];		

	$field_id = $GenericProps['field_id'];
	$curr_page = $GenericProps['curr_page'];
	$curr_page_sub = $GenericProps['curr_page_sub'];
	
	$page_title		= $GenericProps['page_title'];
	$page_subtitle	= $GenericProps['page_subtitle'];
	$prefArrayName = $GenericProps['prefArrayName'];
}

include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if(notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {					

	
	$boolArray = array("");
	
	if (isset($GenericProps['uid']) || !empty($_GET['uid'])) {
		///////////////////
		/// START PAGE HTML	
		$getApp_query	= "SELECT * FROM $db_client.$tablename WHERE $field_id=$uid LIMIT 1";
		$getApp_result	= mysql_query($getApp_query);
		if($getApp_result && mysql_num_rows($getApp_result)==1){
			$SubjectTitle = 'RE: JoeBloggs Careers';
			$getAppForm_row = mysql_fetch_row($getApp_result);

			$ReturnPanel = '<div class="panel">';
				$ReturnPanel .= '<a href="'.$PrevPage.'" id="return"><span>&#60;&nbsp;Return</span></a>';
				$ReturnPanel .= '<div class="inner_right">';
						//$ReturnPanel .= '<li><a href="admin_genericUpdate.php?param[editid]='.$uid.'&param[prefArrayName]='.$prefArrayName.'&param[tablename]='.$tablename.'&param[field_id]='.$gp_branchTable['id']['field'].'&param[curr_page]=applications&param[curr_page_sub]=app_list&param[page_title]=Edit Applicant Data&param[page_subtitle]=Edit Applicant details using the form below" class="Edit" title="Edit Link"><span>Edit</span></a></li>';
						$ReturnPanel .= '<a href="admin_genericDelete.php?param[deleteid]='.$uid.'&param[tablename]='.$tablename.'&param[field_id]='.$gp_branchTable['id']['field'].'&param[itemTitle]='.$itemTitle.'&param[PrevPage]=custom_app_preview.php&param[PrevPage2]=admin_workshops_list.php&param[curr_page]=applications&param[page_title]=Delete applicant&param[page_subtitle]=Delete this applicant" class="Delete" title="Delete" id="delete"><span>Delete Applicant</span></a>';
				$ReturnPanel .= '</div>';
			$ReturnPanel .= '</div>';
			
			
			$BuildTable = '';
			$BuildTable .= $ReturnPanel;
			
			
			$BuildTable .= '<div class="panel">';
			$BuildTable .= '<table width="100%" border="1" cellpadding="0" cellspacing="0">';
				$BuildTable .= '<tr class="table_titles">';
				$BuildTable .= '<td width="40%">&nbsp;<strong>Field</strong></td>';
				$BuildTable .= '<td width="60%"><strong>Value</strong></td>';		
				
				$tmpcountShown = 0;
				for($tmpcount=0;$tmpcount<mysql_num_fields($getApp_result);$tmpcount++){
										
					if(!empty($getAppForm_row[$tmpcount])){
						$rowcolor = $CMSShared->GetRowColor($tmpcountShown,$colors);
						$BuildTable .= '<tr class="body_general" bgcolor='.$rowcolor.'>';							
						$meta = mysql_fetch_field($getApp_result, $tmpcount);
						$fieldValue = $getAppForm_row[$tmpcount];
						
						if($meta->name=="job_position" || $meta->name=="job_location") $SubjectTitle .= ", ".$fieldValue;
						
						if($CMSTextFormat->StringContains($meta->name,"email") && $CMSForms->ValidEmail($fieldValue)) $fieldValue = '<a href="mailto:'.$fieldValue.'?subject='.$SubjectTitle.'" title="Contact this person">'.$fieldValue.'</a>';
						
						if ( $fieldValue==1 && in_array($meta->name, $boolArray) ){
							$fieldValue = "Yes";
						}
							
						
						$BuildTable .= '<td>&nbsp;'.$meta->name.'</td><td>'.$fieldValue.'</td>';
						$BuildTable .= '</tr>';
						$tmpcountShown++;
					}						
				}
			
			$BuildTable .= '</table>';
			$BuildTable .= '</div>';
			echo $BuildTable;
			
		}else{
			echo '<p class="error">Application could not be found or has already been removed</p>';
		}/// END ///
	}else{
		echo '<p class="error">This page was not accessed properly. Please try again.</p>';
	}
	
	echo $ReturnPanel;
}
include("includes/admin_pagefooter.php");
	
?>