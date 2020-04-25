<?php
$curr_page = "ThinUpload";
$curr_page_sub	= "ThinUpload_fileUpload";


include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle("Upload Files");
$BuildPage .= $PageBuild->AddPageTip("To upload your files, simply drop files onto the box provided");
include("includes/admin_pageheader.php");


/////////// check to see if session is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
} else {
	if(!gp_enabled("ThinUpload")) exit();
	echo '<div class="panel_oneline">';
	echo '<p class="middle"><span class="steptitle">Upload Files:</span> Follow on-screen instructions</p>';
	echo '<div class="inner_right">';
		
	    echo '<div  style="border: 1px solid #006699; padding:2px; width:350px;">';
	   	
	    if(!on_localhost()){
		    
			$useApplet=0;		
			$user_agent =$_SERVER['HTTP_USER_AGENT'];
			
		   	//echo '<br />ID='.$my_id.' / ID_XTRA='.$my_id_xtra.'<br />';
			if(stristr($user_agent,"konqueror") || stristr($user_agent,"macintosh") || stristr($user_agent,"opera"))
			{ 		
				$useApplet=1;
				echo '<applet name="Thin Image Upload"
						archive="ThinImage.jar"
						code="com.thinfile.upload.ThinImageUpload"
						width="350" MAYSCRIPT="yes"
						height="309">';
				
			}
			else
			{			   
				if(strstr($user_agent,"MSIE")) {
					echo '<input type="hidden" id="adminroot" value="'.$adminroot.'">';
	                echo '<script language="javascript" src="ThinUpload_embed.js" type="text/javascript"></script>';
						
				} else {
					echo '<object type="application/x-java-applet;version=1.4.1"
						width= "350" height= "309"  id="thin" name="Thin Upload">';
	                echo '<param name="archive" value="ThinImage.jar">
	                    <param name="code" value="com.thinfile.upload.ThinImageUpload">
	                    <param name="MAYSCRIPT" value="yes">
						<param name="props_file" value="'.$adminroot.'ThinUpload_props.php?siteroot='.$_SESSION['siteroot'].'">
	                    <param name="name" value="Thin Image Upload">';                     
				} 
			}
	
			if(isset($_SERVER['PHP_AUTH_USER']))
			{
				printf('<param name="chap" value="%s">',
					base64_encode($_SERVER['PHP_AUTH_USER'].":".$_SERVER['PHP_AUTH_PW']));
			}
			
			if($useApplet == 1)
			{
				echo '</applet>';
			}
			else
			{
				echo '</object>';
			}
		}else{ // (ELSE) if on_localhost
			include("Thinupload_message.htm");
		} // (END) if on_localhost
	
		echo '</div>';
		
	echo '</div>';
	echo '</div>';

}

include("includes/admin_pagefooter.php");

?>