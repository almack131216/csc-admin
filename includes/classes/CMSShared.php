<?php

	Class CMSShared {
		
		//////////////////////////// Check File Details
		/// File Exists
		function FileExists($getFilePath){
			if(substr($getFilePath, strlen($getFilePath)-1, strlen($getFilePath))=="/") return false;
			if (@fclose(@fopen($getFilePath, "r"))) {
				return true;
			}else{
				return false;
			}	
		}
		/// END ///
		
		//////////////////////////// FORCE File Download
		/// File Exists
		function FileDownloadLink($getFilePath){
			global $adminroot;
			$FileLink = $adminroot.'includes/classes/ForceFileDownload.php?file='.$getFilePath;
			return $FileLink;	
		}
		/// END ///
		
		//////////////////////////////////
		/// Show absolute URL of this page
		function PageURL(){
			return urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		}
		/// END ///
		
		//////////////////////////// Check File Details
		/// check if image
		function IsImage($filename){
			global $CMSShared;
			$type = $CMSShared->GetFileType($filename);

			switch($type) {
				case "jpg":		return TRUE;break;
				case "JPG":		return TRUE;break;
				case "jpeg":	return TRUE;break;
				case "gif":		return TRUE;break;
				case "png":		return TRUE;break;
				case "bmp":		return TRUE;break;
				//case "tif":		return TRUE;break;		
				default:		return FALSE;break;		
			}
		}
		/// END ///
		
		function GetFileIcon($ret_type,$ret_found,$ret_title){
			global $adminroot,$SiteAdmin;
			if($adminroot){
				$adminDir = $adminroot;
			}elseif($SiteAdmin){
				$adminDir = $SiteAdmin;
			}else{
				$adminDir = 'admin/';
			}
			if(!$adminroot && !$SiteAdmin) $adminDir = 'admin/';
			if($ret_found){
				return '<img src="'.$adminDir.'includes/icons/fileTypes_'.$ret_type.'.png" alt="'.$ret_title.'">';
			}else{
				return '<img src="'.$adminDir.'includes/icons/fileTypes_missing'.$ret_suffix.'.gif" alt="'.$ret_title.'">';
			}
		}

		//////////////////////////// Check File Details
		// return filetype as string
		function GetFileType($filename) {
			$filename = strtolower($filename); //make lower case
			/*
			$len = strlen($filename); // get length
			$pos = strpos($filename, "."); // retrieve position of dot by counting chars upto dot
			$type = substr($filename, $pos + 1, $len);
			*/
			$tmp = explode(".",$filename);
			$type = $tmp[sizeof($tmp)-1];
			return $type;
		}
		/// END ///
		
		
		//$CMSShared->GetFileAttributes("src","filesize","mb");
		function GetFileAttributes($getSRC,$getAttr,$getFormat){
			$file = $getSRC;
			
			switch($getAttr){
				// FILE SIZES
				case "filesize":
					$filesize = filesize($file); // bytes
					if($filesize<=0){
						$attribute = 'unknown file size';
					}else{						
						switch($getFormat){
							case "kb":// bytes to KB
								$attribute = round(($filesize / 1024), 2).'KB';break;
								
							case "mb":// bytes to MB
								$attribute = round(($filesize / 1048576), 2).'MB';break;
								
							case "gb":// bytes to GB
								// WARNING: PHP does funny thing for files larger than 2GB
								$attribute = round(($filesize / 1073741824), 2).'GB';break;
								
							default:// if format is not specified...
								if($filesize < 100000){
									$attribute = round(($filesize / 1024), 0).'KB';
								}else{
									$attribute = round(($filesize / 1048576), 2).'MB';
								}
								break;
						}
						break;
					}
					
				
				// FILE TIME
				case "filetime":// displays in Seconds since the Unix Epoch
					$filetime = filemtime($file); // displays in Seconds since the Unix Epoch
					$attribute = date($getFormat, $filetime); // would display the file creation/modified date as Feb 3, 2007
					break;				
					
			}
			if($attribute) return $attribute;
		}
		
		//////////////////////////// Check File Details
		// return filetype as string
		function GetFileTypeAdvanced($getFilename) {
			global $CMSShared;
			
			$filenameRaw = strtolower($getFilename); //make lower case			
			$split = split("/",$filenameRaw); // get length
			
			$filename = $split[sizeof($split)-1]; //make lower case
			$len = strlen($filename); // get length
			$pos = strpos($filename, "."); // retrieve position of dot by counting chars upto dot
			$type = substr($filename, $pos + 1, $len);

			switch($type) {
				case "doc":		return "doc";break;
				case "pdf":		return "pdf";break;
				case "ppt":		return "ppt";break;
	
				default:		return "url";break;		
			}
		}
		/// END ///	

		//////////////////////////// If external link (used to determin window target)
		function ExternalLink($getHyperlink,$getBase){
			global $CMSTextFormat;
			/*$pos = strpos($getHyperlink,"ttp:");
			$pos2 = strpos($getHyperlink,"ttps:");
			$pos_internal = strpos($getHyperlink,$getBase);
			if($pos==true && $pos2==true && !$pos_internal) return true;*/
			if(!$CMSTextFormat->StringContains($getHyperlink,$getBase) && !$pos_internal) return true;
		}
	
		//////////////////////////// Check File Details
		/// return filename as string
		function GetFileName($filename) {
			$filename = strtolower($filename); //make lower case
			$len = strlen($filename); // get length
			$pos = strpos($filename, "."); // retrieve position of dot by counting chars upto dot
			$name = substr($filename, 0, $pos);	
			return $name;
		}
		/// END ///
		
		
		///////////////////////////////////////////
		/// CLIENT // FUNCTION: Get Contact Details
		function SetContactDetails(){
			global $clientname, $email, $tel_land, $fax, $tel_mob;
			global $db_shared, $cid;	
			
			$query = "SELECT * FROM $db_shared.contact_details WHERE cid=$cid LIMIT 1";
			$result = mysql_query($query);
			if($result && mysql_num_rows($result)==1){
				$ret_cont = mysql_fetch_array($result);			
				
				$clientname	= $ret_cont['name'];
				$email		= $ret_cont['email'];
				$tel_land	= $ret_cont['tel_h'];
				$fax		= $ret_cont['fax'];
				$tel_mob	= $ret_cont['tel_m'];
			}else{
				$clientname	= "";
				$email		= "";
				$tel_land	= "";
				$fax		= "";
				$tel_mob	= "";
			}
		}
		/// END ///
		

		//////////////////////////
		/// FUNCTION: Get BodyText
		function GetBodyText($ret_page_id){
			global $db_clientTable_bodytext;
			$query 			= "SELECT * FROM $db_clientTable_bodytext WHERE page='$ret_page_id' LIMIT 1";
			$result 		= mysql_query($query);
			if($result){		
				$ret_bodytext	= mysql_fetch_array($result);
				$mybodytext		= $ret_bodytext['text'];
				//$mybodytext 	= $CMSTextFormat->stripCrap2_out_body($ret_bodytext['text']); //$mybodytext 	= stripcrap2_out($ret_bodytext[2]);
				return $mybodytext;
			}
		}
		/// END ///
		
		
		//////////////////////////
		/// FUNCTION: Get BodyText
		function GetRowColor($getRowNum,$getColors){
			if (is_float($getRowNum / 2)) {
				return $getColors['light'];
			} else {
				return $getColors['lightest'];
			}
		}
		/// END ///
		
		///////////////////////////////////////////
		/// FUNCTION : Check article is ok to show
		function DateValid($getDateStart,$getDateEnd){
			global $TheDayToday;
			//echo '<br>Open: '.$getDateStart;
			//echo '<br>Close: '.$getDateEnd;
			//echo '<br>Today is: '.$TheDayToday.'('.date(G).'hr)';
			if(empty($getDateEnd) || $getDateEnd=='0000-00-00' || strpos($getDateEnd, "00-00")!=0) $SkipExpiry=true;
			if($getDateStart<=$TheDayToday && ($SkipExpiry || $getDateEnd>$TheDayToday))return true;
		}
		/// END ///
		
		//////////////////////////////////////////////////////////
		/// FUNCION // Build Queries from properties retrieved	
		function BuildQuery($property,$getArr){
			global $orderby, $asc_or_desc, $db_client;
			
			///////////// MAIN QUERIES FOR RETRIEVING ITEMS
			$linkBuild = $_SERVER['PHP_SELF']; //Build link for page navigation buttons
			$queryBuild = "SELECT * FROM $db_client.catalogue WHERE id_xtra=0"; //Build SQL query
			
			
			//if($getArr['status']){
			$linkBuild.="?status=".$getArr['status'];
			$queryBuild.=" AND status=".$getArr['status'];
			//}
			if($getArr['subcategory']){
				$linkBuild.="&subcategory=".$getArr['subcategory'];
				$queryBuild.=" AND subcategory=".$getArr['subcategory'];
			}
			if($getArr['category']){
				$linkBuild.="&category=".$getArr['category'];
				$queryBuild.=" AND category=".$getArr['category'];
			}
			if($getArr['qs_keywords']){
				$linkBuild.="&qs_keywords=".$getArr['qs_keywords'];
				$qs_keywords = $getArr['qs_keywords'];
				$qs_keywordsSplit = explode(" ",$qs_keywords);
				$qs_wordcount = count($qs_keywordsSplit);
				
				if($qs_wordcount<=1){//if just one keyword is used in search field...
					$queryBuild.=" AND (name LIKE \"%$qs_keywords%\" OR description LIKE \"%$qs_keywords%\" OR detail_3 LIKE \"%$qs_keywords%\")";
				}else{//if MORE THAN one word is entered then loop through all words to find matches
					$queryBuild.=" AND (detail_3 LIKE \"%$qs_keywords%\")";
					for($tmpcount=0;$tmpcount<$qs_wordcount;$tmpcount++){
						$queryBuild.=" OR (name LIKE \"%$qs_keywordsSplit[$tmpcount]%\" OR description LIKE \"%$qs_keywordsSplit[$tmpcount]%\" OR detail_3 LIKE \"%$qs_keywordsSplit[$tmpcount]%\")";
					}					
				}
			}
			if(!empty($getArr['qs_price_min'])){
				$linkBuild.="&qs_price_min=".$getArr['qs_price_min'];
				$queryBuild.=" AND price >=".$getArr['qs_price_min'];
			}
			if(!empty($getArr['qs_price_max'])){
				$linkBuild.="&qs_price_max=".$getArr['qs_price_max'];
				$queryBuild.=" AND price <=".$getArr['qs_price_max'];
			}			
			if(!empty($getArr['detail_1'])){
				$linkBuild.="&detail_1=".$getArr['detail_1'];
				$queryBuild.=" AND detail_1 = \"".$getArr['detail_1']."\"";
			}
			if(!empty($getArr['detail_2'])){
				$linkBuild.="&detail_2=".$getArr['detail_2'];
				$queryBuild.=" AND detail_2 = \"".$getArr['detail_2']."\"";
			}
			if(!empty($getArr['detail_3'])){
				$linkBuild.="&detail_3=".$getArr['detail_3'];
				$queryBuild.=" AND detail_3 = \"".$getArr['detail_3']."\"";
			}			
			if(!empty($getArr['detail_4'])){
				$linkBuild.="&detail_4=".$getArr['detail_4'];
				$queryBuild.=" AND detail_4 = \"".$getArr['detail_4']."\"";
			}
			if(!empty($getArr['detail_5'])){
				$linkBuild.="&detail_5=".$getArr['detail_5'];
				$queryBuild.=" AND detail_5 = \"".$getArr['detail_5']."\"";
			}
			if($getArr['qs_order']){
				$linkBuild.="&qs_order=".$getArr['qs_order'];
				global $gp_qs_arr_order;		
				$tmp_orderby = $gp_qs_arr_order[$getArr['qs_order']][2];
				$queryBuild.=" ORDER BY $tmp_orderby";
			}else{
				$queryBuild.=" ORDER BY $orderby $asc_or_desc";
			}
			
			switch($property){
				case "query":return $queryBuild;break;
				case "QSquery":return $queryBuild;break;
				case "pagefile":return $linkBuild;break;
			}			
		
		}
		/// END ///
		
		
	
	}
	$CMSShared = new CMSShared();
	$TheDayToday = date('Y-m-d');
	$queryDate = "((c.spare_date>'$TheDayToday' OR c.spare_date='0000-00-00')";
	if($cust_status) $queryDate .= " AND c.status=$cust_status";
	$queryDate .= ")";
	$queryDateExpired = "(c.status=2 OR c.spare_date<'$TheDayToday')";

?>