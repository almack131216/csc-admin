<?php

$thickbox = true; // NEED THIS
$selectfile = "embed";
include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");

if(!empty($siteroot)){

	///////////////////////
	/// START TO PRINT PAGE	
	echo '<body id="thickbox">';
	
		if(!empty($_GET['file'])) { // ELSE if 1
			$my_filename = $_GET['file'];
		}
		require("file_selectfile_files.php");

	echo '</body>';
	echo '</html>';
	
}

?>