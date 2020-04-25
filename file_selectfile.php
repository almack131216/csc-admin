<?php

$thickbox = true; // NEED THIS
$selectfile = "select";
include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");

/////////// check to see if option is set
if(isset($_REQUEST['editid']))		$editid = $_REQUEST['editid'];
if(isset($_REQUEST['category']))	$my_cat = $_REQUEST['category'];
if(isset($_REQUEST['subcategory']))	$my_subcat = $_REQUEST['subcategory'];
if(isset($_REQUEST['id_xtra']))		$my_id_xtra = $_REQUEST['id_xtra'];


if(!empty($siteroot)){
	///////////////////////
	/// START TO PRINT PAGE	
	echo '<body id="thickbox">';

		echo '<form action="admin_catalogue_upload.php" method="post" target="_parent">';
		
		// if editing
		if($editid) echo '<input type="hidden" name="editid" value="'.$editid.'">';		
		// if category selected
		if($my_cat) echo '<input type="hidden" name="category" value="'.$my_cat.'">';
		// if subcategory selected
		if($my_cat) echo '<input type="hidden" name="subcategory" value="'.$my_subcat.'">';		
		// if id_xtra
		if($my_id_xtra) echo '<input type="hidden" name="id_xtra" value="'.$my_id_xtra.'">';
		
		require("file_selectfile_files.php");
		
		echo '</form>';

	echo '</body>';
	echo '</html>';
}

?>