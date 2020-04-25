<?php

if(!$gp_uploadPath) require_once("prefs/catalogue_prefs.php");
$FirstTags = $PageBuild->StartMetaTags();

// CSS (ON ALL PAGES)
//$BuildPage .= $PageBuild->AddTag('myclass.css');
//$BuildPage .= $PageBuild->AddTag(array('file'=>'myclassPrint.css','media'=>'print'));
//$BuildPage .= $PageBuild->AddTag('forms.css');

// JS (ON ALL PAGES)
//$BuildPage .= $PageBuild->AddTag('common.js');
//$BuildPage .= $PageBuild->AddTag('browserdetect.js');


// FAVICON
$BuildPage .= $PageBuild->AddIconTag($adminroot.'layout/');

echo $FirstTags;
echo $BuildPage;
echo '</head>';

if(!$thickbox){
	echo '<body id="default">';//class="visible"
	
	echo '<div id="wrap_all">'; // WRAP ALL
	
		// HEADER
		echo '<div id="header">';			
			
			$buildSpec='<p id="spec">'.$amactive['version'];//[01.01.08]			
			if(!empty($cid)) $buildSpec.='<br>Logged in:&nbsp;'.$_SESSION['FirstName'].'&nbsp;'.$_SESSION['Surname'].'&nbsp;['.$cid.']';			
			
			if( notloggedin() ) {			
				if (substr($_SERVER['PHP_SELF'],-9) != 'index.php') { // if we aint on logout page already
					$buildSpec.='<br><a href="'.$adminroot.'admin_logout.php?client='.$_GET['client'].'" class="log_in">LOGIN</a>';
				}				
			} else {	
				if (substr($_SERVER['PHP_SELF'], -16) != 'admin_logout.php') { // if we aint on logout page already
					$buildSpec.='<br><a href="'.$adminroot.'admin_logout.php?client='.$cid_QuickName.'" class="log_out">LOGOUT</a>';
				} else {
					$buildSpec.='<br><a href="'.$adminroot.'admin_logout.php?client='.$cid_QuickName.'" class="log_in">LOGIN AGAIN</a>';
				}
			}
			$buildSpec.='</p>';
			echo $buildSpec;
			
			echo '<h1>'.ucfirst($PageTitle).'</h1>'."\r\n";
			
			
		echo '</div>';		
		echo '<img src="layout/bg_header_icon.gif" class="CustomIcon">';
		include("nav.php");
		echo '<div id="content">';
}	
?>