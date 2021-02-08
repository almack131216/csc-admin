<?php
$curr_page = "ThinUpload";
$curr_page_sub	= "catalogue_fileManage";
include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle("File Manage > Options");
$BuildPage .= $PageBuild->AddPageTip("Contact Technical Support if you require assistance");
include("includes/admin_pageheader.php");
include("includes/classes/CMSHelp.php");
?>

	<div class="panel_oneline">
        <h2>What are you uploading?</h2>
        <p style="display:block;float:none;"><strong>NOTE: </strong>Cars (stock) will be uploaded with high-resolution images. All other images will be 1024x768 pixels.</p>

        <strong>STOCK</strong> - <a href="plupload/examples/jquery/jquery_ui_widget_stock.html" title="" class="NoStyle AddItem">upload HIGH RESOLUTION images</a>

        <br>
        <br>
        <br>
        <br>
        <br>
        <br>

        <strong>Other</strong> - <a href="plupload/examples/jquery/jquery_ui_widget_other.html" title="" class="NoStyle AddItem">standard upload</a> (images will be resized to 1024x768 pixels)


	</div>
			
<?php
include("includes/admin_pagefooter.php");
?>