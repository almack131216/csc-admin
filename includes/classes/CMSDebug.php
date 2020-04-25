<?php

	Class CMSDebug {
		
		////////////////////////////
		/// File Exists
		function FileUnlink($getFilePath){
			if(!unlink($getFilePath)){
				echo '<br/>file could not be deleted ('.$getFilePath.')';
			}
		}
		/// END ///
		
		////////  ADMIN  ////// CHECK WINDOWS SERVER?  (debug_scandir)  ///////////
		function WindowsServer(){
			if(stristr($_SERVER['HTTP_USER_AGENT'],"Windows")){
				return true;
			}
		}		
		
		function debug_scandir($dir) {
		    $dh  = opendir($dir);
		    while(false !== ($filename = readdir($dh))) {
		        $files[] = $filename;
		    }
		    sort($files);
		    return $files;
		}
	
		function debug_websiteroot($root){
			if(substr($root, strlen($root)-1, strlen($root))=="/"){
				$website = $root;
			}else{
				$website = $root."/";
			}
			return $website;
		}
		
		function OnLocalhost(){
			if($_SERVER['HTTP_HOST']=="localhost"){//ServerSpecific
				return true;
			}else{
				return false;
			}
		}

		function SanitizeFileName($getFileName){	
			// STEP 1: Get File Type
			// add to filename once we remove periods
			$FileNameSplit = explode(".",$getFileName);
			$FileType = ".".$FileNameSplit[count($FileNameSplit)-1];
		
			// STEP 2: REMOVE bad characters
			$remove_these = array($FileType,'`','"','\'','\\','/','(',')','%','$','!');
			$GoodFileName = str_replace($remove_these,'',$getFileName);
			
			// STEP 3: REPLACE bad characters for dashes
			$swap_these = array('.',' ','#','&','+');
			$GoodFileName = str_replace($swap_these,'-',$GoodFileName);
			$GoodFileName .= $FileType;
		
			return strtolower($GoodFileName);
		}
		
		//Check PHP Version
		function phpMinV($v){
		    $phpV = PHP_VERSION;
		
		    if ($phpV[0] >= $v[0]) {
		        if (empty($v[2]) || $v[2] == '*') {
		            return true;
		        } elseif ($phpV[2] >= $v[2]) {
		            if (empty($v[4]) || $v[4] == '*' || $phpV[4] >= $v[4]) {
		                return true;
		            }
		        }
		    }
		
		    return false;
		}
		/*
		REF:
		---- Newer than 4 ----
		if (phpMinV('4')) {
		    // .....
		}
		// or
		if (phpMinV('4.*')) {
		    // .....
		}
		
		---- Newer than 5.1 ----
		if (phpMinV('5.1')) {
		    // .....
		}
		// or
		if (phpMinV('5.1.*')) {
		    // .....
		}
		
		---- Newer than 5.2.3 ----
		if (phpMinV('5.2.3')) {
		    // .....
		}
		*/
			
	}
	$CMSDebug = new CMSDebug();

?>