<?php

$page_title = "Restore positions";
$page_subtitle = "";

include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if( notloggedin()) {
	include('includes/admin_notloggedin.html');
} else {		
		
	if (isset($_GET['categoryID']) ) {
		$categoryID = $_GET['categoryID'];
	} else {
		$categoryID = "";
	}
	
	///////////////////////////////////////////
	/// the cancel button needs to know how many steps back to take
	/// (1 will be added with each update to avoid cancelling back to previous amend)
	if (isset($_POST['updater']) ) {
		$updated = $_POST['updater'];
		$updated++;
		//echo "UPDATED set to = ". $updated;
	} else{
		/// if we've just entered page(previous page (-1) will be allocated to cancel button
		$updated = 1;
		//echo "UPDATED set to 1";
	}
	
	
	if (isset($_POST['submit'])) { // handle form
		$all_query = "SELECT * FROM $db_clientTable_catalogue ORDER by position";
		$all_result = mysql_query($all_query);				
		$all_num_rows = mysql_num_rows($all_result);							
			
		for($tmp=0;$tmp<=$all_num_rows;$tmp++){		
			$all_array 	= mysql_fetch_row($all_result);
			//echo $tmp .' / ' .$all_num_rows.' , ';
			$position_query = "UPDATE $db_clientTable_catalogue SET position = '$tmp' WHERE id = '".$all_array[0]."'";
			$position_result = $db->mysql_query_log($position_query);
		}
		
		echo '<p class="good">Positions have been re-sorted. Problems should now be fixed</p>';
		
		echo '<div class="panel">';
		echo '<a href="javascript:history.go(-'.$updated.')" id="return"><span>&#60;&nbsp;Return</span></a>';
		echo '</div>';
			
	} else {
		
		echo '<div class="panel">';
		show_warning("Only use the Re-SORT feature if you notice problems with items listed (i.e. duplicate numbers, missing numbers), etc");			
		echo '</div>';
		
		echo '<div class="panel">';
		echo '<form enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="POST">';
		//echo '<input type="hidden" name="categoryID" value="'.$categoryID.'" />';
		echo '<input type="hidden" name="updater" value="'.$updated.'">';
		echo '<input type="submit" src="includes/btns/btn_submit.gif" id="submit" name="submit" value="Restore / Reset Positions" alt="Restore / Reset Positions"/>';	
		echo '<br/><br/><a href="javascript:history.go(-'.$updated.')" id="return"><span>&#60;&nbsp;Return</span></a>';
		echo '</form>';
		echo '</div>';			
			
	}
	
	//mysql_close();

}
include("includes/admin_pagefooter.php");

?>