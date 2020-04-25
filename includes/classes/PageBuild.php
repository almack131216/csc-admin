<?php

	$BuildPage = '';
	
	// start output buffering and initialise a session
	ob_start();
	// "session_start" must FOLLOW "ob_start" as nothing has been sent to browser yet!
	session_start();
	// time page load (continued in footer)
	/*
	$time = microtime();
	$time = explode(" ", $time);
	$time = $time[1] + $time[0];
	$start = $time;*/
	// check for page_title value(set if not already)
	if (!$page_title) $page_title = '(ADMIN) LOG-IN';
	if($_SESSION['db_client']){
		$adminDir		= "admin";
		$adminroot		= $_SESSION['adminroot'];
		$siteroot		= $_SESSION['siteroot'];
		$cid			= $_SESSION['cid'];
		$cid_QuickName	= $_SESSION['quickname'];
		$cid_Website	= $_SESSION['website'];
		$db_client		= $_SESSION['db_client'];
	}
	//echo '<br/>(pageheader) _SESSION[adminroot]:'.$adminroot;
	//echo '<br/>(pageheader) _SESSION[siteroot]:'.$siteroot;
	//echo '<br/>(pageheader) _SESSION[db_client]:'.$db_client;
	//echo '<br/>(pageheader) _SESSION[cid]:'.$cid;
	
	
	// This is needed as the help pages are still accessible for users struggling to log in
	if(!$adminroot) $adminroot = "";
	
	/////////// CONNECT TO DATABASE
	require_once('includes/config.inc');
	require_once('includes/admin_connect_2_db.php');
	//require_once('classes/PageBuild.php');
	//require_once("prefs/catalogue_prefs.php");
	require_once('includes/functions.php');
	
	////////////////////////////////////////////
	///    COLORS     //////    BLUES  /////////
	$colors = array("darkest" => '#336699', "dark" => '#6699CC', "medium" => '#cccccc', "light" => '#f3f3f3', "lightest" => '#ffffff');
	
	
	
	Class PageBuild {		
		
		////////////////////////
		/// Start to build page
		function StartMetaTags(){
			global $adminroot;
			
			$MetaTags = '';
			$MetaTags .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\r\n";
			$MetaTags .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\r\n";
			$MetaTags .= '<head>'."\r\n";
			$MetaTags .= '<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />';
			$MetaTags .= '<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />'."\r\n";			
			$MetaTags .= '<meta name="robots" content="noindex,nofollow"/>'."\r\n";
			$MetaTags .= '<script src="'.$adminroot.'includes/js/common.js" type="text/javascript"></script>'."\r\n";
			$MetaTags .= '<script src="'.$adminroot.'includes/js/browserdetect.js" type="text/javascript"></script>'."\r\n";
			$MetaTags .= '<script src="'.$adminroot.'includes/js/forms.js" type="text/javascript"></script>'."\r\n";
			$MetaTags .= '<link rel="stylesheet" href="'.$adminroot.'includes/css/myclass.css" type="text/css" media="screen"/>'."\r\n";
			$MetaTags .= '<link rel="stylesheet" href="'.$adminroot.'includes/css/forms.css" type="text/css" media="screen"/>'."\r\n";
			$MetaTags .= '<style>#content .panel_Smallbox img {float:none;max-height:44px;}</style>'."\r\n";
			$MetaTags .= '<link rel="shortcut icon" href="'.$adminroot.'layout/favicon.ico" />'."\r\n";
			$MetaTags .= '<link rel="icon" href="'.$adminroot.'layout/favicon_anigif_red.gif" type="image/gif" />'."\r\n";
			return $MetaTags;
		}
		/// END ///
		
		////////////////////////
		/// Start to build page
		function AddPageTitle($getPageTitle){
			global $amactive,$PageTitle;
			$PageTitle = $getPageTitle;
			$PageTitleReturn = '<title>[ '.$amactive['version'].' ] : '.$getPageTitle.'</title>'."\r\n";
			return $PageTitleReturn;
		}
		/// END ///
		
		////////////////////////
		/// Start to build page
		function AddPageTip($getSubTip){
			global $PageTip;
			$PageTip = $getSubTip;
		}
		/// END ///
		
		////////////////////////
		/// COMMON CSS
		function AddTag($getFile){
			global $CMSShared,$adminroot;
			
			if(is_array($getFile)){
				
				if($getFile['dir']) $dir = $getFile['dir'];
				if($getFile['file']) $file = $getFile['file'];
				if($getFile['media']) $media = $getFile['media'];
				$getType = $CMSShared->GetFileType($file);
				//echo '<br>ARRAY:'.$dir.$file.'('.$media.')';
			}else{
				$getType = $CMSShared->GetFileType($getFile);
				/////////////////////
				// DEFAULT PROPERTIES
				if($getType=='css'){
					$dir = 'includes/css/';
					$media = 'screen';
					$file = $getFile;
				}elseif($getType=='js'){
					$dir = 'includes/js/';
					$media = 'N/A';//Not Applicable (ignored for .js)
					$file = $getFile;
				}
			}
			
			//echo '<br>(FB) DIR:'.$dir;
			//echo '<br>(FB) FILE:'.$file;
			
			
			if($dir && $file){
				if($getType=='css'){					
					$tag = '<link rel="stylesheet" href="'.$adminroot.$dir.$file.'" type="text/css" media="'.$media.'"/>'."\r\n";
				}elseif($getType=='js'){
					$tag = '<script src="'.$adminroot.$dir.$file.'" type="text/javascript"></script>'."\r\n";					
				}
				return $tag;
			}
			
		}
		/// END ///
		
		////////////////////////
		/// third-party Lightbox / Thickbox
		/// from; http://jquery.com/demo/thickbox/
		function AddThickbox(){
			global $adminroot;			
			$Thickbox = '<script type="text/javascript" src="'.$adminroot.'includes/thickbox/jquery-1.2.3.min.js"></script>'."\r\n";
			$Thickbox .= '<script type="text/javascript" src="'.$adminroot.'includes/thickbox/thickbox.js"></script>'."\r\n";
			$Thickbox .= '<link rel="stylesheet" href="'.$adminroot.'includes/thickbox/thickbox.css" type="text/css" media="screen" />'."\r\n";
			return $Thickbox;
		}
		/// END ///
		
		////////////////////////
		/// add Google Map Tags
		function AddGoogleMagTags(){
			global $CMSDebug,$adminroot;
			
			$GoogleMapTags = '';
			
			switch($_SERVER['HTTP_HOST']){//ServerSpecific
				case "localhost":				$gKey = "ABQIAAAAvVBk7xOgEj-MJaaECzD0kBRnXyL79Gf8PwjJPly3ZDbFQrWQLBQiG8OrhcBJnElDAuam5Ek3HdD7qQ";break;
			}
			$GoogleMapTags .= '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$gKey.'" type="text/javascript"></script>';
			$GoogleMapTags .= '<script src="http://www.google.com/uds/api?file=uds.js&v=1.0&key='.$gKey.'" type="text/javascript"></script>';
			$GoogleMapTags .= '<script type="text/javascript" src="'.$adminroot.'includes/js/gmap.js"></script>'."\r\n";
	
			return $GoogleMapTags;
		}
		/// END ///
		
		/////////////////////////
		/// add icon
		function AddIconTag($getDir){
			global $CMSDebug;
			$IconTag = '';
			$IconTag .= '<link rel="shortcut icon" href="'.$getDir.'favicon.ico" />';
			if($CMSDebug->OnLocalhost()){
				$IconTag .= '<link rel="icon" href="'.$getDir.'favicon_anigif_red.gif" type="image/gif" />';
			}else{
				$IconTag .= '<link rel="icon" href="'.$getDir.'favicon_anigif.gif" type="image/gif" />';
			}
			return $IconTag;
		}
		/// END ///
		
		/*
		function GetItemData($getTmpItemID){
			global $db_clientTable_catalogue;
			if($getTmpItemID){
				$query = "SELECT c.id,c.name AS itemName,c.category,c.subcategory FROM $db_clientTable_catalogue AS c WHERE c.id=$getTmpItemID LIMIT 1";
				$result = mysql_query($query);
				if($result && mysql_num_rows($result)==1){
					$ItemData = mysql_fetch_array($result);
				}else{
					$ItemData = array('name'=>"this item");
				}
				return $ItemData;
			}	
		}
		*/
		
		function GetCatalogueData($getTmpCategoryID,$getTmpSubCategoryID,$getTMPitemID){
			global $queryDate,$suid,$CMSTextFormat,$db_clientTable_catalogue,$db_clientTable_catalogue_cats,$db_clientTable_catalogue_subcats;
			//if($getTmpCategoryID){
				$querySelect = "SELECT count(*) AS count";
				$queryFrom = " FROM $db_clientTable_catalogue AS c";
				$queryWhere = " WHERE c.id_xtra=0";
				if($queryDate) $queryWhere .= " AND ".$queryDate;
				$queryGroup = "";				
				
				
				if($getTMPitemID){
					$querySelect .= ", c.name AS itemName, cat.category AS categoryName, cat.id AS categoryID, csc.subcategory AS subcategoryName, csc.id AS subcategoryID";
					$queryFrom .= ", $db_clientTable_catalogue_cats AS cat";
					$queryFrom .= ", $db_clientTable_catalogue_subcats AS csc";
					$queryWhere .= " AND c.id=$getTMPitemID AND cat.id=c.category AND csc.id=c.subcategory";
					$queryGroup = " GROUP BY c.id";
				}
				
				if($getTmpCategoryID && !$getTMPitemID){
					$querySelect .= ", cat.id AS categoryID, cat.category AS categoryName";
					$queryFrom .= ", $db_clientTable_catalogue_cats AS cat";
					$queryWhere .= " AND c.category=cat.id AND cat.id=$getTmpCategoryID";
					$queryGroup = " GROUP BY c.category";										
				}
				
				if($getTmpSubCategoryID && !$getTMPitemID){
					$querySelect .= ", csc.id AS subcategoryID, csc.subcategory AS subcategoryName";
					$queryFrom .= ", $db_clientTable_catalogue_subcats AS csc";
					$queryWhere .= " AND (c.subcategory=csc.id AND csc.category=$getTmpCategoryID AND csc.id=$getTmpSubCategoryID)";										
					$queryGroup = " GROUP BY c.subcategory";
				}				
				
				
				$query = $querySelect.$queryFrom.$queryWhere.$queryGroup;
				$result = mysql_query($query);
				
				//echo '<br>(FB):'.$getTmpCategoryID;
				//echo '<br>(FB):'.$getTmpSubCategoryID;
				//echo '<br>(FB):'.$getTMPitemID;
				//echo '<br>(FB):'.$query;
				
				if($result && mysql_num_rows($result)>=1){
					$CatalogueData = mysql_fetch_array($result);					
					
					$CatalogueData['categoryID'] = $CatalogueData['categoryID'];
					$CatalogueData['subcategoryID'] = $CatalogueData['subcategoryID'];
					
					$CatalogueData['itemNameRaw'] = $CatalogueData['itemName'];
					$CatalogueData['categoryNameRaw'] = $CMSTextFormat->ReduceString($CatalogueData['categoryName'],30);
					$CatalogueData['subcategoryNameRaw'] = $CMSTextFormat->ReduceString($CatalogueData['subcategoryName'],30);
					
					$CatalogueData['itemName'] = '<em>'.$CMSTextFormat->Abbreviate(array('string'=>$CatalogueData['itemName'],'trim_start'=>5,'trim_middle'=>"...",'trim_end'=>0)).'</em>';
					$CatalogueData['categoryName'] = '<em>'.$CMSTextFormat->ReduceString($CatalogueData['categoryName'],30).'</em>';
					$CatalogueData['subcategoryName'] = '<em>'.$CMSTextFormat->ReduceString($CatalogueData['subcategoryName'],30).'</em>';
					
					$CatalogueData['itemLink'] = '<a href="admin_catalogue_upload.php?editid='.$getTMPitemID.'" title="Continue editing '.$CatalogueData['itemNameRaw'].'"><em>'.$CMSTextFormat->Abbreviate(array('string'=>$CatalogueData['itemNameRaw'],'trim_start'=>3,'trim_middle'=>"...",'trim_end'=>2)).'</em></a>';
					$CatalogueData['categoryLink']='<a href="admin_catalogue_all.php?thisList=catalogue_cats&category='.$CatalogueData['categoryID'].'" title="View all items in this category"><em>'.$CMSTextFormat->ReduceString($CatalogueData['categoryNameRaw'],30).'</em></a>';
					$CatalogueData['subcategoryLink']='<a href="admin_catalogue_all.php?thisList=catalogue_cats&category='.$CatalogueData['categoryID'].'&subcategory='.$CatalogueData['subcategoryID'].'" title="View all items in this subcategory"><em>'.$CMSTextFormat->ReduceString($CatalogueData['subcategoryNameRaw'],30).'</em></a>';
					//print_r($CatalogueData);
				}else{
					$CatalogueData = array();
					
					
					
					
					$CatalogueData['categoryNameRaw'] = "this category";
					$CatalogueData['categoryName'] = '<em>this category</em>';
					$CatalogueData['categoryLink']=$CatalogueData['categoryNameRaw'];
					
					$CatQuery = "SELECT id,category FROM $db_clientTable_catalogue_cats WHERE id=$getTmpCategoryID LIMIT 1";
					$CatResult = mysql_query($CatQuery);
					if($CatResult && mysql_num_rows($CatResult)==1){
						$CatRow = mysql_fetch_row($CatResult);
						$CatalogueData['categoryID'] = $CatRow[0];
						$CatalogueData['categoryNameRaw'] = $CMSTextFormat->ReduceString($CatRow[1],30);
						$CatalogueData['categoryName'] = '<em>'.$CMSTextFormat->ReduceString($CatRow[1],30).'</em>';
						$CatalogueData['categoryLink']=$CMSTextFormat->ReduceString($CatalogueData['categoryNameRaw'],30);
					}
					
					$SubCatQuery = "SELECT id,subcategory FROM $db_clientTable_catalogue_subcats WHERE id=$getTmpSubCategoryID LIMIT 1";
					$SubCatResult = mysql_query($SubCatQuery);
					if($SubCatResult && mysql_num_rows($SubCatResult)==1){
						$SubCatRow = mysql_fetch_row($SubCatResult);
						$CatalogueData['subcategoryID'] = $SubCatRow[0];
						$CatalogueData['subcategoryNameRaw'] = $CMSTextFormat->ReduceString($SubCatRow[1],30);
						$CatalogueData['subcategoryName'] = '<em>'.$CMSTextFormat->ReduceString($SubCatRow[1],30).'</em>';
						$CatalogueData['subcategoryLink']=$CMSTextFormat->ReduceString($CatalogueData['subcategoryNameRaw'],30);
					}

				}
				
				if($suid){
					if($CatalogueData['categoryLink']) $CatalogueData['categoryLink'] = $CatalogueData['categoryName'];
					if($CatalogueData['subcategoryLink']) $CatalogueData['subcategoryLink'] = $CatalogueData['subcategoryName'];
				}
				return $CatalogueData;
			//}
		}
	
	}
	$PageBuild = new PageBuild();

?>