<?php

$thickbox = true; // NEED THIS
include("includes/classes/PageBuild.php");
include("includes/admin_pageheader.php");

if(!empty($siteroot)){

	///////////////////////
	/// START TO PRINT PAGE	
	echo '<body id="thickbox">';
	
		echo '<div class="panel_oneline" id="thickbox">';
			echo '<div class="inner_left">';
				echo '<p><span class="steptitle">Step 1:</span> Enter FULL URL to capture and click the \'Capture URL\' button</p>';
				echo '<p><span class="steptitle">Step 2:</span> When the capture is fully loaded, simply right-click (or control-click on Mac) and \'save image as...\'</p>';
				echo '<p><span class="steptitle">Step 3:</span> Close this window or press Esk Key...<br>On returning to the previous screen, you should now be able to use this image by uploading it with the \'Browse...\' button on your profile page.</p>';
				//echo '<p><strong>NOTE:</strong> If you are unable to generate a thumbnail preview on this page then please <s href="http://www.thumboo.com/" target="_blank" title="Visit the Thumboo website">Visit the Thumboo website</a></p>';
			echo '</div>';
			
			echo '<div class="inner_right">';
				if($_REQUEST['domain']){
					$getDomain = $_REQUEST['domain'];
				}else{
					$getDomain = "http://www.weborchardtest.co.uk";
				}
				echo '<form name="CaptureUrl" action="'.$_SERVER['PHP_SELF'].'" method="post">';
				echo '<input type="text" name="domain" value="'.$getDomain.'">';
				echo '<input type="submit" name="Submit" value="Capture URL">';
				echo '</form>';
				echo '<div class="inner_right">';
				if($getDomain){
					$thumboo_api = "4528118579785b5d5aa0a38f4543579f";
					$thumboo_url = $getDomain;
					$thumoo_params = "u=".urlencode("http://".$_SERVER["HTTP_HOST"].
					$_SERVER["REQUEST_URI"])."&su=".urlencode($thumboo_url)."&c=large&api=".$thumboo_api;
					@readfile("http://counter.goingup.com/thumboo/snapshot.php?".$thumoo_params);
				}
			echo '</div>';
		echo '</div>';

	echo '</body>';
	echo '</html>';
	
}

?>