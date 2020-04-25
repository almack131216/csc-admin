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

	switch($_GET['step']){
		case '1': $field = "category";break;
		case '2': $field = "subcategory";break;
		case '3': $field = "detail_22";break;
	}
	
	$field_conv = $field."_conv";
	$tbl_catalogue = "catalogue";
	
	
	if($field == "category"){
		$get_query = "SELECT id,category FROM catalogue_cats ORDER BY $field ASC";
	}elseif($field == "subcategory"){		
		$get_query = "SELECT id,subcategory,category FROM catalogue_subcats WHERE id>249 AND id<500 ORDER BY id ASC";
	}elseif($field == "detail_22"){		
		$get_query = "SELECT id,iso FROM tbl_countries ORDER BY iso ASC";
	}

	
	
	if($_GET['step']){	
	
		$get_result = mysql_query($get_query);
		if($get_result && mysql_num_rows($get_result)>=1){		
			$RowsAffected = 0;
			$FailedList = '';
			
			for($i=0;$i<mysql_num_rows($get_result);$i++){
				$row = mysql_fetch_row($get_result);
				
				$updateQuery = "UPDATE $tbl_catalogue SET $field='${row[0]}' WHERE lower($field_conv)=\"".strtolower($row[1])."\"";//LIKE \"%${row[$field]}%\"
				if($field == "subcategory") $updateQuery .= " AND category=${row[2]}";
	
				//$updateResult = true;
				$updateResult = mysql_query($updateQuery);
				if($updateResult && mysql_affected_rows()>=1){
					$RowsAffected += mysql_affected_rows();
					echo '<br>(x'.mysql_affected_rows().') '.$updateQuery;
				}else{
					$FailedList .= '<br>------>'.$updateQuery;
				}
				//echo '<br><br>(FB):'.$insert_query;
			} 
		}
		echo '<p>&nbsp;</p>';
		echo '<p><strong>';
		echo 'Rows affected:'.$RowsAffected;
		echo '<br>Rows failed:'.number_format(7023-$RowsAffected,0);
		echo '</strong></p>';
		
		echo $FailedList;
		
	}

	/*
	$cockupfix_query = "SELECT id, id_xtra FROM $tbl_catalogue WHERE category=5 AND upload_date = '2009-02-03' ORDER BY detail_1";
	$cockupfix_result = mysql_query($cockupfix_query);
	if($cockupfix_result && mysql_num_rows($cockupfix_result)>=1){		
		for($i=0;$i<mysql_num_rows($cockupfix_result);$i++){
			$row = mysql_fetch_row($cockupfix_result);
			$query = "UPDATE $tbl_catalogue SET detail_1=${row[1]}, id_xtra=0 WHERE id=${row[0]} LIMIT 1";
			//$result = mysql_query($query);
			echo '<br><br>(FB):'.$query;			
		}
	}
	*/	
	
}
include("includes/admin_pagefooter.php");
?>