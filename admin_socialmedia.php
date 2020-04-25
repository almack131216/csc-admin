<?php

$GenericUpdate = true;
$PrintForm = true; //this is disabled when data is successfully inserted(during ADDING)
if($_REQUEST['param']) $GenericProps = $_REQUEST['param'];

//if entering this page... (GET properties)
if(isset($GenericProps['editid']) || isset($GenericProps['adding'])){
	
	if(isset($GenericProps['editid'])){
		$editid = $GenericProps['editid'];
	}elseif(isset($GenericProps['adding'])){
		$adding = 1;
		$editid = '';
	}
		
	
	$prefArrayName	= $GenericProps['prefArrayName'];
	$itemTitle		= $GenericProps['itemTitle'];	
	$page_title		= $GenericProps['page_title'];
	$page_subtitle	= $GenericProps['page_subtitle'];
	$curr_page		= $GenericProps['curr_page'];
	$curr_page_sub	= $GenericProps['curr_page_sub'];	
	$PrevPage		= $GenericProps['PrevPage'];
	
//else if page data has already been posted from this page... (REQUEST properties already posted)
}elseif($_POST['editid'] || $_POST['adding']){
	if($_POST['editid']) $editid = $_POST['editid'];
	if($_POST['adding']) $adding = $_POST['adding'];
	

	$prefArrayName	= $_REQUEST['prefArrayName'];
	$itemTitle		= $_REQUEST['itemTitle'];	
	$page_title		= $_REQUEST['page_title'];
	$page_subtitle	= $_REQUEST['page_subtitle'];	
	$curr_page		= $_REQUEST['curr_page'];
	$curr_page_sub	= $_REQUEST['curr_page_sub'];	
	$PrevPage		= $_REQUEST['PrevPage'];
}
if(!$PrevPage){
	$JumpToThisPage = $_SERVER['PHP_SELF'];
}else{
	$JumpToThisPage = "Location: ".$adminroot.$PrevPage;
}
$JumpToThisPage .= "?success=true";
include("includes/classes/PageBuild.php");
include("includes/classes/TinyMCE.php");
$BuildPage .= $PageBuild->AddPageTitle($page_title);
$BuildPage .= $PageBuild->AddPageTip($page_subtitle);
$BuildPage .= $TinyMCE->LoadAdvanced();
$BuildPage .= $CMSForms->AddCalendarTags();
include("includes/admin_pageheader.php");

//print_r($GenericProps);
/////////// check to see if session is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
} else {

	$defaultDate = array('date'=>date('Y-m-d'),'datetime'=>date('Y-m-d H:i:s'));
	if($_GET['success']) echo '<p class="good">Successfully updated</p>';
	if($prefArrayName) $prefArray = ${$prefArrayName};//if this array exists and is populated then show form according to fields specified in this array
	
	$tablename = $prefArray['tablename'];
	$field_id = $prefArray['id']['field'];
	
	if($tablename){
		$FULLtablename = $db_client.".".$tablename;
		$query = mysql_query("SELECT * FROM $FULLtablename") or die (mysql_error());		
				
		$field_divs = $dbFields->mysql_field_array( $query );			
		$field_divs_type = $dbFields->mysql_field_type_array($query);		
		
			
		//IF ADDING?: ADD A BLANK ENTRY - this will then create an 'editid' so it can share the 'update' query
		if($adding && $_POST['adding']==1){
			$fieldsPopulated = 0;
			$addQuery = "INSERT INTO $FULLtablename ($field_id";
			$fields = '';
			$values = 'Values(0';
			for ($i = 0; $i < sizeof($field_divs); $i++) {
				${"panel_".$i} = 'panel_oneline';
				${"value_".$i} = '';
				
				
				if(isset($_REQUEST[$field_divs[$i]])){
					${"value_".$i} = $_REQUEST[$field_divs[$i]];
					/*if($i>1 && $i<sizeof($field_divs)) {
						$fields .= ", ";
						$values .= ", ";
					}*/
					$fields .= ", ${field_divs[$i]}";							
					if($field_divs_type[$i]=="int" && empty($_REQUEST[$field_divs[$i]])){
						$values .= ", 0";
					}else{
						$values .= ", '". mysql_real_escape_string($_REQUEST[$field_divs[$i]])."'";
					}
					if(!empty($_REQUEST[$field_divs[$i]])) $fieldsPopulated++;			
				}				
				
			}
			$fields .= ") ";
			$values .= ")";
			
			$addQuery.=$fields;
			$addQuery.=$values;
			//echo '<br>(FB):'.$addQuery;
			
			if($fieldsPopulated>2){
				$add_result = $db->mysql_query_log($addQuery);
				if($add_result){
					$adding++;
					$addedid = mysql_insert_id();
					
					header($JumpToThisPage);
					echo '<p class="good">Successfully added</p>';
					//$PrintForm = false;		
				}else{
					echo '<p class="error">PROCESSING ERROR<br>'.$addQuery.'</p>';
				}
			}else{
				echo '<p class="error">More information required please</p>';
			}
			
		}else{		
			
			
			for ($i = 0; $i < sizeof($field_divs); $i++) {
				${"panel_".$i} = 'panel_oneline';
				${"value_".$i} = '';
				
				if(isset($_POST[$field_divs[$i]])){
					if(empty($_POST[$field_divs[$i]]) && $field_divs_type[$i]=="date") $_POST[$field_divs[$i]]=$defaultDate['date'];
					if(empty($_POST[$field_divs[$i]]) && $field_divs_type[$i]=="datetime") $_POST[$field_divs[$i]]=$defaultDate['datetime'];
					//echo '<br>set '.$field_divs[$i].' to "'.$_POST[$field_divs[$i]].'"';
					$update_query = "UPDATE $FULLtablename SET ${field_divs[$i]}='${_POST[$field_divs[$i]]}' WHERE $field_id=$editid LIMIT 1";
					$update_result = $db->mysql_query_log($update_query);
					
					//echo '<br>'.$update_query;
					if($update_result && mysql_affected_rows()){
						$DatabaseUpdated = true;
						${"panel_".$i} = 'panel_good';
					}
				}
			}
			if($DatabaseUpdated && $_POST['editid']){
				header($JumpToThisPage);
				echo '<p class="good">Successfully updated</p>';
			}
		
			if($editid){
				$value_query = "SELECT * FROM $FULLtablename WHERE $field_id=$editid LIMIT 1";
				$value_result = mysql_query($value_query);
				if($value_result && mysql_num_rows($value_result)==1){
					$value_array = mysql_fetch_row($value_result);
					for ($i = 0; $i < sizeof($field_divs); $i++) {
						${"value_".$i} = $value_array[$i];
					}
				}else{
					$PrintForm = false;
				}
			}
			
		}		
		
		
		///////////////////////
		/// START PRINTING FORM
		if($PrintForm){
			$buildForm = '';
			$buildForm .= '<form enctype="multipart/form-data" method="POST" action="'.$_SERVER['PHP_SELF'].'" name="GenericForm">';
			$buildForm .= '<input type="hidden" name="editid" value="'.$editid.'">';
			$buildForm .= '<input type="hidden" name="tablename" value="'.$tablename.'">';
			$buildForm .= '<input type="hidden" name="itemTitle" value="'.$itemTitle.'">';		
			$buildForm .= '<input type="hidden" name="field_id" value="'.$field_id.'">';
			$buildForm .= '<input type="hidden" name="curr_page" value="'.$curr_page.'">';
			$buildForm .= '<input type="hidden" name="curr_page_sub" value="'.$curr_page_sub.'">';
			$buildForm .= '<input type="hidden" name="page_title" value="'.$page_title.'">';
			$buildForm .= '<input type="hidden" name="page_subtitle" value="'.$page_subtitle.'">';
			$buildForm .= '<input type="hidden" name="prefArrayName" value="'.$prefArrayName.'">';
			$buildForm .= '<input type="hidden" name="adding" value="'.$adding.'">';
			$buildForm .= '<input type="hidden" name="PrevPage" value="'.$PrevPage.'">';

			$fields = mysql_num_fields($query);		
			$StepNum = 1;
			for($i=1;$i<$fields;$i++) {
				//$row = mysql_fetch_row($result);
				$fname=mysql_field_name($query, $i);
				$ftype=mysql_field_type($query, $i);
				$flength = mysql_field_len($query, $i);
				$fflags = mysql_field_flags($query, $i);
				
				if(empty($prefArray) || (!empty($prefArray) && $dbFields->FormatFieldName($fname)) && !$prefArray[$fname]['skip']){//if using a pref array then limit to fields specified in prefs
					$buildForm .= '<div class="'.${"panel_".$i}.'">';
						$buildForm .= '<p><span class="steptitle">Step '.$StepNum.':</span> '.$dbFields->FormatFieldName($fname).'</p>';
						$buildForm .= '<div class="inner_right">';						
						
						
						if($prefArray[$fname]['SelectFromDB']){							
							
							$SelectTableName	= $db_client.".".$prefArray[$fname]['SelectFromDB']['tablename'];
							$SelectFieldID		= $prefArray[$fname]['SelectFromDB']['field_id'];
							$SelectFieldName	= $prefArray[$fname]['SelectFromDB']['field_name'];
							$SelectFieldValue	= $prefArray[$fname]['SelectFromDB']['field_value'];
							if(!$SelectFieldValue) $SelectFieldValue = $SelectFieldID;
							$SelectOrderBy		= $prefArray[$fname]['SelectFromDB']['orderby'];
							
							//echo $SelectTableName;							
							
							$buildForm .= '<select id="'.$fname.'" name="'.$fname.'">';
							$build_query = "SELECT DISTINCT $SelectFieldID,$SelectFieldName FROM $SelectTableName ORDER BY $SelectOrderBy";
							$build_result = mysql_query($build_query);
							//echo $build_query;
							if($build_result && mysql_num_rows($build_result)>=1){
								$buildForm .= '<option value="0">'.$prefArray[$fname]['defVal'].'</option>';
								for($ii=0;$ii<mysql_num_rows($build_result);$ii++){
									$row = mysql_fetch_array($build_result);
									$buildForm .= '<option value="'.$row[$SelectFieldValue].'"';
									if(strtolower(${"value_".$i}) == strtolower($row[$SelectFieldValue])) $buildForm .= ' selected';
									$buildForm .= '>'.$row[$SelectFieldName].'</option>';
								}
														
							}else{
								$buildForm .= '<option value="0">This list is empty</option>';
							}
							$buildForm .= '</select>';
							
						}else{

							switch($ftype){
												
								case "blob":	$buildForm .= '<textarea id="'.$fname.'" name="'.$fname.'" cols="80" rows="15">'.${"value_".$i}.'</textarea>';break;
								
								case "int":		if($prefArray[$fname]['boolean']){
													$buildForm .= '<input type="radio" name="'.$fname.'" class="radio" value="1"';
													if(${"value_".$i}>0) $buildForm .= ' checked';													
													$buildForm .= '>YES';
													$buildForm .= '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$fname.'" class="radio" value="0"';
													if(${"value_".$i}==0) $buildForm .= ' checked';
													$buildForm .= '>NO';
												}else{
													$buildForm .= '<input type="text" name="'.$fname.'" id="'.$fname.'" value="'.${"value_".$i}.'">';
												}								
												break;
								
								case "date":
								case "datetime":	
												$buildForm .= '<input type="text" name="'.$fname.'" id="'.$fname.'"';//start input field
												// NEED this hack to ensure data can be passed as empty(otherwise, argues with tigra calendar)
												if(!empty(${"value_".$i}) && ${"value_".$i} != $defaultDate['date'] && ${"value_".$i} != $defaultDate['datetime']){
													$buildForm .= 'value="'.${"value_".$i}.'"';
												}else{
													if($ftype=="datetime"){
														$buildForm .= 'value="'.$defaultDate['datetime'].'"';
														$pShowTime='true';
													}else{
														$buildForm .= 'value="'.$defaultDate['date'].'"';
														$pShowTime='false';
													}												
												}
												
												if($ftype=="date") $buildForm .= ' readonly=true';
												$buildForm .= '>';//finish input field
												
												$buildForm .= '<a href="javascript:NewCssCal(\''.$fname.'\',\'yyyymmdd\',\'arrow\','.$pShowTime.')";" class="DateSelect">Edit</a>';
												$buildForm .= ' &#124; <a href="Javascript:clearDate(\''.$fname.'\');" title="Clear Date">Clear</a>';
												
												break;
								default:		$buildForm .= '<input type="text" name="'.$fname.'" id="'.$fname.'" value="'.${"value_".$i}.'">';break;
							}
						}
						$buildForm .= '</div>';
					$buildForm .= '</div>';
					$StepNum++;
				}
			}
		
			/////////// SUBMIT BUTTON (MOBILE)
			$buildForm .= '<div class="panel_oneline">';
				$buildForm .= '<p><span class="steptitle">Update Details:</span></p>';
				$buildForm .= '<div class="inner_right">';
					//$buildForm .= '<a href="Javascript:document.forms.GenericForm.submit();" id="submit" name="Submit"><span>&#62;&nbsp;Update Details</span></a>';
					$buildForm .= '<input type="submit" id="submit" name="Submit" value="Update Details">';
					$buildForm .= '</form>';
				$buildForm .= '</div>';
			$buildForm .= '</div>';
			
			echo $buildForm;
			
			if($editid || $addedid){
				if(!$itemTitle) $itemTitle = "Unknown";
				$SharedParams = '&param[prefArrayName]='.$prefArrayName.'&param[curr_page]='.$curr_page;
				$SubNavInner = '<ul id="SubNavInner">';
				if($prefArrayName=="gp_branchTable") $SubNavInner .= '<li class="map"><a href="admin_branch_list.php?uid='.$editid.'" title="update map position">map position</a></li>';		
				//$SubNavInner .= '<li class="add"><a href="admin_genericUpdate.php?param[adding]=true'.$SharedParams.'&param[curr_page_sub]='.$curr_page_sub.'&param[page_title]=Add a job description to database&param[page_subtitle]=Add job description" title="Add">Add</a></li>';
				if($prefArrayName=="gp_membersTable"){
					$SubNavInner .= '<li class="delete"><a href="admin_member_delete.php?currentmember='.$editid.'" title="Delete">delete</a></li>';
				}else{
					$SubNavInner .= '<li class="delete"><a href="admin_genericDelete.php?param[deleteid]='.$editid.$SharedParams.'&param[itemTitle]='.$itemTitle.'&param[PrevPage]='.$PrevPage.'&param[page_title]=Delete&param[page_subtitle]=Delete this" title="Delete">delete</a></li>';
				}
				$SubNavInner .= '</ul>';
				echo $SubNavInner;
			}
		}
	}
	
}
	
include("includes/admin_pagefooter.php");

?>