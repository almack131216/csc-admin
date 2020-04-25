<?php

$page_title = "Help";
$page_subtitle = "Select one of the options below if you require assistance";
$curr_page = "help";

include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");

?>

								

	<p class="good">Thank you for logging your request. Your query will be answered as soon as possible.</p>	
	
	<div class="panel_oneline">
		<p><span class="steptitle">Thank you...</span>
		<div class="inner_right">
			<a href="help_request.php" id="return"><span>&#60;&nbsp;Return</span></a>
		</div>
	</div>

			
<?php
include("includes/admin_pagefooter.php");
?>