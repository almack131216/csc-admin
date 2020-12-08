<?php
if(!$dbFields){require_once('classes/dbFields.php');}
if(!$CMSShared){require_once('classes/CMSShared.php');}
if(!$CMSTextFormat){require_once('classes/CMSTextFormat.php');}
if(!$CMSSelectOptions){require_once('classes/CMSSelectOptions.php');}
if(!$CMSAddOns){require_once('classes/CMSAddOns.php');}
if(!$CMSDebug){require_once('classes/CMSDebug.php');}
if(!$CMSForms){require_once('classes/CMSForms.php');}
if(!$CMSCommon){require_once('classes/CMSCommon.php');}
if(!$CMSDelete){require_once('classes/CMSDelete.php');}
//if(!$CMSPanels){require_once('classes/CMSPanels.php');}
if(!$CMSImages){require_once('classes/CMSImages.php');}

////////////////////////////////////////////////////////////
////////  ADMIN  ////// CHECK USER IS LOGGED IN    /////////
function notloggedin(){
	global $suid_PageAccess;

	// if($_SERVER['HTTP_HOST']=="localhost:8080"){
	// 	return false;
	// 	echo '<br>1:suid: '.$_SESSION['suid'].'/'.$suid_PageAccess;
	// 	echo '<br>2:cid: '.$_SESSION['cid'].'/'.$suid_PageAccess;
	// }
	

	if ( ($_SESSION['suid'] && $suid_PageAccess) || (!$_SESSION['suid'] AND !empty($_SESSION['cid']) AND (substr($_SERVER['PHP_SELF'], -16) != 'admin_logout.php')) ) {
		return false;
	} else {
		return true;
	}
}

////////////////////////////////////////////////////////////
////////  ADMIN  ////// CHECK USER IS LOGGED IN    /////////
function suid_pageAccessMessage(){	
	$NoAccess = '<div class="panel_warning">';
	$NoAccess .= '<p><strong>You are not authorised to use this page</strong><br>';
	$NoAccess .= '<br>Please <a href="javascript:history.go(-1)">return to previous page</a></p>';
	$NoAccess .= '</div>';
	return $NoAccess;
}

////////////////////////////////////////////////////////////
////////  ADMIN  ////// CHECK USER IS LOGGED IN    /////////
function suid_PageAccess($getID){
	
	if(
	!is_array($_SESSION['suid']) && $_SESSION['suid']==$getID
	|| is_array($_SESSION['suid']) && in_array($getID,$_SESSION['suid'])
	){
		return true;
	}else{
		return false;
	}
}

////////////////////////////////////////////////////////////
////////  ADMIN  ////// CHECK USER IS LOGGED IN    /////////
function is_SuperAdmin(){
	if ( $_SESSION['quickname']=="accounts" AND (substr($_SERVER['PHP_SELF'], -16) != 'admin_logout.php') && $_SESSION['cid']==1) {
		return true;
	} else {
		return false;
	}
}


/*
* The letter l (lowercase L) and the number 1
* have been removed, as they can be mistaken
* for each other.
*/
function createRandomPassword() {
    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;

    while ($i <= 7) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }

    return $pass;
}

////////////////////////////////////////////////////////////
////////////////////////// 	 IMAGE DIRECTORIES    //////////
//echo '<br/>(FB): (functions) SITEROOT:'.$siteroot.$gp_uploadPath;

$missingimage = "_missingimage.jpg";
// $missingthumb = getImgDirSession('thumbs').$missingimage;
// $missingprimary = getImgDirSession('primary').$missingimage;
// $missinglarge = getImgDirSession('large').$missingimage;

if(empty($amactive)){
	global $amactiveDefault;
	$amactive['version'] = $amactiveDefault['version'];
	$amactive['tel'] = $amactiveDefault['tel'];
	$amactive['email'] = $amactiveDefault['email'];
}


////////////////////////////////////////////////////
///////  ADMIN  ////////  LIST STATUS IN SELECT MENU
function list_status_select() {
	global $db_shared;
	
	$query 		= "SELECT * FROM $db_shared.catalogue_status ORDER BY id";
	$result 	= @mysql_query($query);
	$num_rows 	= mysql_num_rows($result);		  
	
	$BuildList = '';	    
	if(mysql_num_rows($result)) {
		$BuildList .= '<select name="new_status" id="status" onChange="checkeditbut();">'; //onSelect="checkeditbut()" 
		$BuildList .= '<option value="">Please Select</option>';	
	
		for($tmpcount = 0;$tmpcount < $num_rows;$tmpcount++)  {
			$tmparray = mysql_fetch_array($result);
			$BuildList .= '<option value="'.$tmparray['id'].'">'.get_statusname($tmparray['id']).'</option>';			
		}  
		$BuildList .= '</select>';
	
	} else {
		$BuildList .= '<p>Status list has not been created - contact support via the help page</p>';
	}    

	return $BuildList;
}


//////////////////////////////////////////////////////
//////////// GET CATEGORY NAME FROM NUMBER IN DATABASE
function get_category($ret_catnum,$value,$ret_thisList){
	global $cat_name, $cat_num;
	global $db_clientTable_catalogue_cats, $db_clientTable_catalogue_subcats, $db_clientTable_catalogue;
	global $db_clientTable_catalogue;
	
	if($ret_thisList=="catalogue_cats"){
		$tablename = $db_clientTable_catalogue_cats;
		$tablename_items = $db_clientTable_catalogue;
		$table_fieldname = 'category';
	}elseif($ret_thisList=="catalogue_subcats"){
		$tablename = $db_clientTable_catalogue_subcats;
		$tablename_items = $db_clientTable_catalogue;
		$table_fieldname = 'subcategory';		
	}

	switch($value){
	
		case "name":
						$query_cat	= "SELECT * FROM $tablename WHERE id=$ret_catnum LIMIT 1";
						$result_cat = mysql_query($query_cat);
						if($ret_array = mysql_fetch_array($result_cat)) return $ret_array[$table_fieldname];
						break;
						
		case "orderby":
						$query_cat	= "SELECT id,orderby FROM $tablename WHERE id=$ret_catnum LIMIT 1";
						$result_cat = mysql_query($query_cat);
						if($ret_row	= mysql_fetch_row($result_cat)) return $ret_row[1];
						break;
						
						
		case $value=="stock_count" || $value=="count":
						$query_cat	= "SELECT * FROM $tablename_items WHERE $table_fieldname=$ret_catnum AND status=1";
						$result_cat	= mysql_query($query_cat);
						if($numrows = mysql_num_rows($result_cat)){
							return $numrows;
						}else{
							return '0';
						}
						break;
						
						
		case "stock_value":
						$query_cat	= "SELECT * FROM $tablename_items WHERE status=1 AND $table_fieldname=$ret_catnum";
						$result_cat	= mysql_query($query_cat);						
						$numrows = mysql_num_rows($result_cat);
						$stock_value = 0;
						for($tmpcount=0;$tmpcount<$numrows;$tmpcount++){
							$ret_array	= mysql_fetch_array($result_cat);
							$stock_value += $ret_array['price'];
						}
						return $stock_value;
						break;			

		
	}

}

function br2nl_html( $txt ) {
    return eregi_replace( "&lt;br /&gt;", "", $txt );
}

function br2nl( $txt ) {
    return eregi_replace( "<br />", "", $txt );
}

function sort_chars( $str ) {
	global $CMSTextFormat;
	$str2 = $CMSTextFormat->sort_chars($str);	
	return $str2;
}

function rep_tagchars( $text ) {
	global $CMSTextFormat;
	$text2 = $CMSTextFormat->rep_tagchars($text);                    
	return $text2;
}

/////// LINKS FUNCTIONS (GENERIC SHOW/HIDE STATUS)

function show_status($getStatus,$getDetail) {
	global $gp_arr_status;
	
	if($getDetail=="item"){
		switch($getStatus){
			case 0: $tmpString=$gp_arr_status[0];break;
			case 1: $tmpString='<span class="body_good">'.$gp_arr_status[1].'</span>';break;
			case 2: $tmpString='<span class="body_error">'.$gp_arr_status[2].'</span>';break;
		}
	}else{
		switch($getStatus){
			case 0: $tmpString='<span class="body_error">Hidden</span>';break;
			case 1: $tmpString='<span class="body_good">LIVE</span>';break;
		}
	}
	
	return $tmpString;
}

// GET catalogue STATUS NAMES	
// Performing SQL query
function get_statusname($ret_num){
	global $gp_arr_status;	

	if(!empty($gp_arr_status)){
		return $gp_arr_status[$ret_num];
	}else{
		global $db_shared;
		$status_query	= "SELECT * FROM $db_shared.catalogue_status WHERE id=$ret_num LIMIT 1";
		$status_result	= mysql_query($status_query);
		$status_array	= mysql_fetch_array($status_result);	
		return $status_array['status'];
	}
}

function show_window($get_window) {
	switch($get_window){
		case 0: return "Same Window";break;
		case 1: return "New Window";break;
		case 2: return "YouTube - EMBED";break;
	}
}
	
function get_rowcolor($get_rownum){
	global $CMSShared,$colors;
	return $CMSShared->GetRowColor($get_rownum,$colors);
}

// ADDED: October 06 (17/10/06)
function show_contact_admin() {
	global $adminroot;
	echo '<a href="'.$adminroot.'help_request.php" title="Send Support Request">contact CMS Support</a>';
}

// 201129

function GenerateImgDirName($getDate){
	global $TheDayToday,$debug;

	$dateX = explode("-",$getDate);
    $newDirStr = $dateX[0]."/".$dateX[1];    
			
	$debug .= '<br>[GenerateImgDirName] TheDayToday: '.$newDirStr;
	return $newDirStr;
}

function initImgDir($getDir){
	global $TheDayToday,$debug;

	$debug .= '<p>!!!!!!!!!!!!!!!!!!!!!';
	$debug .= '<br>!!! initImgDir !!!';
	if(!$_SESSION['ParentImgDir']){
		$_SESSION['ParentImgDir'] = GenerateImgDirName($TheDayToday);
		$debug .= '<br>!!! initImgDir: $_SESSION[ParentImgDir] SET (TODAY): '.$_SESSION['ParentImgDir'];
	}elseif($_SESSION['ParentImgDir'] && $getDir != $_SESSION['ParentImgDir']){
		$_SESSION['ParentImgDir'] = $getDir;
		$debug .= '<br>!!! initImgDir: $_SESSION[ParentImgDir] CHANGE: '.$_SESSION['ParentImgDir'];
	}elseif($_SESSION['ParentImgDir'] && $getDir == $_SESSION['ParentImgDir']){
		$debug .= '<br>!!! initImgDir: $_SESSION[ParentImgDir] NO CHANGE: '.$getDir.' ('.$_SESSION['ParentImgDir'].')';
	}
	$debug .= '<br>!!!!!!!!!!!!!!!!!!!!!</br>';
}

function switchDirName($getSize){
	switch($getSize){
		case 'highres':
			return 'hi';
			break;		
		case 'large':
			return 'lg';
			break;
		case 'thumbs':
			return 'th';
			break;
		default:
			return 'pr';		
	}
}

function getImgDirSession($getSize){
	global $siteroot,$ParentID,$debug;

	// if(!$_SESSION['ParentImgDir']) $debug .= '<p>------------------------> getImgDirSession: image_dir NOT SET</p>';
	// if($_SESSION['ParentImgDir']) $debug .= '<p><------------------------ getImgDirSession: image_dir SET: '.$_SESSION['ParentImgDir'].'</p>';

	if($_SESSION['ParentImgDir']){
		$thisImgDir = $_SESSION['ParentImgDir'];

		$swDir = switchDirName($getSize);
		// switch($getSize){
		// 	case 'thumbs':
		// 		$swDir = 'th';
		// 		break;
		// 	case 'large':
		// 		$swDir = 'lg';
		// 		break;
		// 	case 'highres':
		// 		$swDir = 'hi';
		// 		break;
		// 	default:
				
		// 	$swDir = 'pr';		
		// }
		$imgDir = $siteroot.'images/'.$thisImgDir.'/'.$swDir.'/';
	}else{
		$_SESSION['ParentImgDir'] = '';
		$imgDir = $siteroot.'images_catalogue/'.$getSize.'/';
	}
	return $imgDir;
}

function getImgDir($getImgDir,$getSize){
	global $siteroot,$ParentID;

	// if(!$_SESSION['ParentImgDir']) echo '<p>------------------------> getImgDir: image_dir NOT SET</p>';
	// if($_SESSION['ParentImgDir']) echo '<p><------------------------ getImgDir: image_dir SET: '.$_SESSION['ParentImgDir'].'</p>';

	if($getImgDir){
		// switch($getSize){
		// 	case 'thumbs':
		// 		$swDir = 'th';
		// 		break;
		// 	case 'large':
		// 		$swDir = 'lg';
		// 		break;
		// 	case 'highres':
		// 		$swDir = 'hi';
		// 		break;
		// 	default:
				
		// 	$swDir = 'pr';		
		// }
		$swDir = switchDirName($getSize);
		$imgDir = $siteroot.'images/'.$getImgDir.'/'.$swDir.'/';
	}else{
		$imgDir = $siteroot.'images_catalogue/'.$getSize.'/';
	}
	return $imgDir;
}


function isAttachment($getIdXtra){
    if($getIdXtra==0 || $getIdXtra=='') return false;
    return true;
}
// CHECK folder exists...

function returnToDir($getSize){
    switch($getSize){
        case "thumb":
            $getSize = '/th/';
            break;
        case "large":
            $getSize = '/lg/';
            break;
        default:
            $getSize = '/pr/';
    }
    return $getSize;
}

function checkFolderExists($getSize,$newDir){
    global $foldersCreated,$tableTitle;

    $getSize = returnToDir($getSize);

    if($getSize){
        $newDir = $newDir.$getSize;//thumb/primary/large
    }
    // CHECK / folders exist..
    if(file_exists( $newDir ) ){
        $foldersCreated ++;
        // $tableTitle .= '<p class="info">['.$newDir.'] FOLDER EXISTS</p>';
    }else{//if not, mkdir
        if(!file_exists( $newDir ) && mkdir($newDir, 0755, true)){
            $foldersCreated ++;
            $tableTitle .= '<p class="success">['.$newDir.'] FOLDER CREATED</p>';
        }else{
            $tableTitle .= '<p class="fatal">BASE FOLDER NOT CREATED</p>';
        }                       
    }
}
// (END) CHECK folder exists...

// CHECK file exists...
function checkFileExists( $getSize, $getFilename ){
    global $imgPath,$newDir,$filesMoved,$filesMovedTotal,$table;

    switch($getSize){
		case "highres":
			$imgPathLive = 'https://www.classicandsportscar.ltd.uk/images_catalogue/large/'.$getFilename;
			$imgPathFrom = $imgPath.'highres/'.$getFilename;
			$imgPathTo = $newDir.'/hi/'.$getFilename;
			break;
		case "large":
			$imgPathLive = 'https://www.classicandsportscar.ltd.uk/images_catalogue/large/'.$getFilename;
			$imgPathFrom = $imgPath.'large/'.$getFilename;
			$imgPathTo = $newDir.'/lg/'.$getFilename;
			break;
        case "thumb":
            $imgPathLive = 'https://www.classicandsportscar.ltd.uk/images_catalogue/thumbs/'.$getFilename;
            $imgPathFrom = $imgPath.'thumbs/'.$getFilename;
            $imgPathTo = $newDir.'/th/'.$getFilename;
            break;		
        default:
            $imgPathLive = 'https://www.classicandsportscar.ltd.uk/images_catalogue/'.$getFilename;
            $imgPathFrom = $imgPath.$getFilename;
            $imgPathTo = $newDir.'/pr/'.$getFilename;
    }

    if(file_exists( $imgPathFrom )){
        $table .= '<span class="info">moving...'.$imgPathTo.'</span>';
        if(rename($imgPathFrom, $imgPathTo)) $filesMoved ++;$filesMovedTotal ++;
    }else{
        if(file_exists( $imgPathTo )){
            $filesMoved ++;
            $filesMovedTotal ++;
            $table .= '<span class="good">['.$getSize.'] file already moved</span>';
        }else{
            $table .= '<img src="'.$imgPathLive.'" class="'.$getSize.'">';
            $table .= '<span class="error">['.$getSize.'] cannot find file</span>';
        }
    }
}
// (END) CHECK file exists...

// CHECK file exists...
function returnLiveImage( $getSize, $getDir, $getFilename ){
    global $imgPath,$newDir,$filesMoved,$table;

	$dirs = [];
	$dirs['highres'] = "highres/";
	$dirs['large'] = "large/";
	$dirs['primary'] = "";
	$dirs['thumb'] = "thumbs/";    

    switch($getDir){
        case "from":
            $switchDir = $imgPath;            
            break;
        case "to":
			$switchDir = $newDir;
			$dirs['highres'] = "/hi/";
			$dirs['large'] = "/lg/";            
            $dirs['primary'] = "/pr/";
            $dirs['thumb'] = "/th/";
            break;
        case "live":
            $switchDir = 'https://www.classicandsportscar.ltd.uk/images_catalogue/';           
            break;
    }

    return '<img src="'.$switchDir.$dirs[$getSize].$getFilename.'" class="'.$getSize.'">';
}
// (END) CHECK file exists...

?>