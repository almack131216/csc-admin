<?php							

//******************//
//******************//
//		NOTES		//
//******************//
//******************//
// IE Does not like 'div' - use 'span' instead.

$MinChars = 2;
$clientCats = array(	'ClassifiedsWanted'	=> '6',
						'Gallery'			=> '4',
						'Classifieds'		=> '2');
											
if ( $_SERVER['HTTP_HOST'] == "localhost" ) {
	$SiteAdmin_ss = "../../../../_admin_v3x2/";
}else{
	$SiteAdmin_ss = "../../../../_admin_v3x2/";//ServerSpecific
}

$moreinfopage_ss_ext	="";
$colors					=array("light"=>'#cccccc', "lightest"=>'#ffffff'); //clours for database table

// DATABASE
$db_connect				='../../connect_2_db.php';//ServerSpecific
$db_query_begin			="SELECT * FROM catalogue";
if($_GET['currentpage_ss'])	$currentpage_ss = $_GET['currentpage_ss'];

$db_field_name			='name';
$db_field_description	='description';
$db_field_price			='price';
$db_field_category		='category';
$db_field_subcategory	='subcategory';
$db_field_detail_1		='detail_1';
$db_field_detail_2		='detail_2';
$db_field_detail_3		='detail_3';
$db_field_detail_4		='detail_4';
$db_field_detail_5		='detail_5';

$SiteFunctionsPath		="../SiteFunctions.php";
$CataloguePath			="../Catalogue.php";
$ImagesPath				="../Images.php";

if($currentpage_ss=="gallery"){	
	$db_query_begin		.=" WHERE status!=0 AND (category=${clientCats['Gallery']} OR category=${clientCats['Classifieds']})";
}else{	
	$db_query_begin		.=" WHERE status=1 AND category=${clientCats['Classifieds']}";
}
$moreinfopage_ss		="property-for-sale";//"catalogue.php?currPage=".$currentpage_ss; //more details page address

// RESULTS & FEEDBACK
$Str_FormatClass		=$SiteAdmin_ss."includes/classes/CMSTextFormat.php";
$CMSPrefsPath			=$SiteAdmin_ss."prefs/sweetmove_prefs.php";
$CMSSharedPath			=$SiteAdmin_ss."includes/classes/CMSShared.php";
$CMSDebugPath			=$SiteAdmin_ss."includes/classes/CMSDebug.php";
$Str_MatchesFound		="";
$Str_NoMatchesFound		="Please revise your search criteria or <a href=\"".$moreinfopage_ss."\" title=\"Reload Page\">reload page</a>.";
$Str_SearchingTitle		=array("<span class=\"h2\">Searching for&nbsp;&quot;", "&quot;</span>&nbsp;&nbsp;&nbsp;&#124;&nbsp;&nbsp;&nbsp;");
$Str_SearchingTitleRaw	="";//"<h2>Search Results</h2>";
$LimitResults			=50;//MAXIMUM results (LIMIT RESULTS)
$fb_LimitReached		=array("<strong>MORE THAN&nbsp;","</strong>&nbsp;matches found<br/>Refine search criteria for better results");
$fb_Single				=array("<strong>","</strong>&nbsp;match found");
$fb_Other				=array("<strong>","</strong>&nbsp;matches found");



////////////////////////////////////////////////////
////////////////////////////////////////////////////
/// Shouldn't need to change anything below here ///
////////////////////////////////////////////////////
////////////////////////////////////////////////////
/// NOTES: Doesn't like all tags on IE /////////////

/// FUNCTION: Get BodyText
function GetRowColor($getRowNum,$getColors){
	if (is_float($getRowNum / 2)) {
		return $getColors['light'];
	} else {
		return $getColors['lightest'];
	}
}
/// END ///	

/// FUNCTION: Generate Title ("Searching For...")
function BuildFeedback($getPrefix,$getString,$getSuffix){
	return $getPrefix.$getString.$getSuffix;
}
/// END ///



/// CHECK: IF RECEIVING SEARCH STRING
//if(!empty($_GET["qs_keywords"])){
	//get the q parameter from URL
	global $q;
	$q					=$_GET["qs_keywords"];
	$qs_order			=$_GET["qs_order"];
	if(!$qs_order) $qs_order = "status DESC, upload_date DESC";
	$qs_price_min		=$_GET['qs_price_min'];
	$qs_price_max		=$_GET['qs_price_max'];
	$qs_price_range		=str_replace(",","",$_GET['qs_price_range']);
	if($qs_price_range){
		//example: 100000 - 200000
		$tmp = explode(" - ",$qs_price_range);
		if(sizeof($tmp)>1){
			$qs_price_min = $tmp[0];
			$qs_price_max = $tmp[1];
		}else{
			//example: 500000+
			$tmp = str_replace("+","",$qs_price_range);
			$qs_price_min = trim($tmp);
			$qs_price_max = "1000000";
		}
	}
	//$category			=$_GET['category'];
	$subcategory		=$_GET['subcategory'];	

	require_once($db_connect);//connect to database
	require_once($Str_FormatClass);//needed for performing functions on strings (see price & reduced title)
	require_once($CMSSharedPath);
	require_once($CMSPrefsPath);
	require_once($CMSDebugPath);
	require_once($ImagesPath);
	require_once($CataloguePath);
	require_once($SiteFunctionsPath);	
	$moreinfopage_ss_ext="&amp;qs_keywords=".$q."&amp;qs_order=".$qs_order."&amp;qs_price_min=".$qs_price_min."&amp;qs_price_max=".$qs_price_max."&amp;qs_price_range=".$qs_price_range."&amp;subcategory=".$subcategory."&amp;currPage=".$currentpage_ss;
//}
/// END CHECK ///



/// MAIN QUERIES FOR RETRIEVING ITEMS
//$test=true;
//if(($q && strlen($q)>=$MinChars) || $test==true){
	
	if($q && strlen($q)>=$MinChars) $Str_SearchingTitleRaw.=BuildFeedback($Str_SearchingTitle[0],$q,$Str_SearchingTitle[1]);	
	$q = trim($q); //REMOVE outside space characters
	$qSplit = explode(" ",$q); //Make array of keywords entered, ignoring spaces
	$qs_wordcount = count($qSplit); //Count words in array (to be reduced later)
	
	/// BEGIN SEARCH FOR ONE OR MORE WORDS
	if($qs_wordcount>=1){ 
		// LOOP THROUGH WORDS COUNTING DOWN UNTIL WE GET MATCHES
		for($tmpcount=$qs_wordcount;$tmpcount>=1;$tmpcount--){
			// BUILD STRING
			$qs_SearchString="";
			$qs_space="";
			$qSplitStripped=array();
			for($tmpcountB=$qs_wordcount;$tmpcountB>=0;$tmpcountB--){
				if($qSplit[$tmpcountB]!=" " && strlen($qSplit[$tmpcountB])>=$MinChars && $qSplit[$tmpcountB]!="the"){
					array_push($qSplitStripped,$qSplit[$tmpcountB]);
					$qs_SearchString.=$qs_space.$qSplit[$tmpcountB];
					$qs_space=" ";
				}else{
					unset($qSplit[$tmpcountB]); //REMOVE excess from array
				}
			}
			$qs_wordcount=count($qSplitStripped);
			// BUILD QUERY
			$queryBuild=$db_query_begin;
			if($subcategory)$queryBuild.=" AND $db_field_subcategory=$subcategory";
			if($qs_price_min)$queryBuild.=" AND $db_field_price>=$qs_price_min";
			if($qs_price_max)$queryBuild.=" AND $db_field_price<=$qs_price_max";
			if(!empty($q)){
				$queryBuild.=" AND ($db_field_name LIKE \"%$qs_SearchString%\"";
				if($db_field_description) $queryBuild.=" OR $db_field_description LIKE \"%$qs_SearchString%\"";
				//if($db_field_detail_1) $queryBuild.=" OR $db_field_detail_1 LIKE \"%$qs_SearchString%\"";
				if($db_field_detail_2) $queryBuild.=" OR $db_field_detail_2 LIKE \"%$qs_SearchString%\"";
				if($db_field_detail_3) $queryBuild.=" OR $db_field_detail_3 LIKE \"%$qs_SearchString%\"";
				//if($db_field_detail_4) $queryBuild.=" OR $db_field_detail_4 LIKE \"%$qs_SearchString%\"";
				if($db_field_detail_5) $queryBuild.=" OR $db_field_detail_5 LIKE \"%$qs_SearchString%\"";
				$queryBuild.=")";
			}
			
			if($qs_order)$queryBuild.=" ORDER BY $qs_order";
			$queryBuild.=" LIMIT $LimitResults";
			$result = mysql_query($queryBuild);
			if($result && mysql_num_rows($result)>=1){
				$useThisResult = $result;		
				$match_arr=array('wordcount'=>$tmpcount,'word'=>$qs_SearchString,'query'=>$queryBuild);
				$tmpcount=0;
				//echo '<br/>---> Searching for "'.$match_arr['word'].'" yielded <strong>'.mysql_num_rows($result).' matches</strong>';
				//echo 'YES';
			//}else{
				//echo 'NO';
			}
			
		}		
		
		/// FINETUNE results
		if($result){			
			//print_r($qSplit); //compare arrays
			//print_r($qSplitStripped);
			
			
			$queryBuild=$db_query_begin;
			if($subcategory)$queryBuild.=" AND $db_field_subcategory=$subcategory";
			if($qs_price_min)$queryBuild.=" AND $db_field_price>=$qs_price_min";
			if($qs_price_max)$queryBuild.=" AND $db_field_price<=$qs_price_max";
			if(!empty($q)){
				$queryBuild.=" AND (($db_field_name LIKE \"%$qSplitStripped[0]%\"";
				if($db_field_description) $queryBuild.=" OR $db_field_description LIKE \"%$qSplitStripped[0]%\"";
				//if($db_field_detail_1) $queryBuild.=" OR $db_field_detail_1 LIKE \"%$qSplitStripped[0]%\"";
				if($db_field_detail_2) $queryBuild.=" OR $db_field_detail_2 LIKE \"%$qSplitStripped[0]%\"";
				if($db_field_detail_3) $queryBuild.=" OR $db_field_detail_3 LIKE \"%$qSplitStripped[0]%\"";
				//if($db_field_detail_4) $queryBuild.=" OR $db_field_detail_4 LIKE \"%$qSplitStripped[0]%\"";
				if($db_field_detail_5) $queryBuild.=" OR $db_field_detail_5 LIKE \"%$qSplitStripped[0]%\"";
				$queryBuild.=")";
			}
			
			for($tmpcount=1;$tmpcount<$qs_wordcount;$tmpcount++){
				if(strlen($qSplitStripped[$tmpcount])>=$MinChars)$queryBuild.=" AND ($db_field_name LIKE \"%$qSplitStripped[$tmpcount]%\" OR $db_field_description LIKE \"%$qSplitStripped[$tmpcount]%\")";
			}
			
			$queryBuild.=") ";
			if($qs_order)$queryBuild.=" ORDER BY $qs_order";
			$queryBuild.=" LIMIT $LimitResults";
			$result2 = mysql_query($queryBuild);		
			if($result2 && mysql_num_rows($result2)>=1){
				$useThisResult = $result2;
			}
		}
		/// END FINETUNE ///		
	}
	//echo '(FB):'.$queryBuild;
	

	/// PRINT RESULTS
	if($useThisResult && mysql_num_rows($useThisResult)>=1){
		$result_count = mysql_num_rows($useThisResult);
		if($result_count==$LimitResults){
			$Str_MatchesFound.=BuildFeedback($fb_LimitReached[0],$result_count,$fb_LimitReached[1]);			
		}elseif($result_count==1){
			$Str_MatchesFound.=BuildFeedback($fb_Single[0],$result_count,$fb_Single[1]);			
		}else{
			$Str_MatchesFound.=BuildFeedback($fb_Other[0],$result_count,$fb_Other[1]);			
		}
		$Str_MatchesFound.='<br/><br/>';

		for($tmpcount=1;$tmpcount<=$result_count;$tmpcount++){
			$result_arr = mysql_fetch_array($useThisResult);			
			
			$resultTitle = $result_arr[$db_field_name];
			$resultTitle = $CMSTextFormat->ReduceString($resultTitle,50);
			for($tmpcountB=0;$tmpcountB<$qs_wordcount && $qSplitStripped[$tmpcountB]!="";$tmpcountB++){
				$resultTitle = eregi_replace($qSplitStripped[$tmpcountB], '<b>'.strtoupper($qSplitStripped[$tmpcountB]).'</b>', trim($resultTitle));
			}
			//$Str_MatchesFound.=$resultTitle;
			$Str_MatchesFound.=$Catalogue->ItemPreview_SS($result_arr,"ss",$moreinfopage_ss_ext);

		}
		
	}
	/// (END) BEGIN SEARCH
//}



/// OUTPUT the results
if ($Str_MatchesFound == ""){//echo "NO MATCHES";	
	echo '<span class="innerPad_ss">';
	$response=$Str_NoMatchesFound;
	echo $Str_SearchingTitleRaw.$response;	
	echo '</span>';
}else{
	echo '<span class="innerPad_ss">';
	$response=$Str_MatchesFound;
	echo $Str_SearchingTitleRaw.$response;
	echo '</span>';
}
/// (END) OUTPUT


?>