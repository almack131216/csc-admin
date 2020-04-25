<?php
$UseQS=true;
$divider = '';

switch($currentpage){		
	case "classifieds":
		$my_category=$clientCats['Classifieds'];
		$QSTitle="Search our current stock...";//Classifieds
		break;
		
	case "gallery":
		$my_category=$clientCats['Gallery'];
		$QSTitle="Search our gallery...";//Gallery / Photo archive
		break;
		
	default:
		$my_category=$clientCats['Classifieds'];
		$QSTitle="Search";//Shop
		break;				
}
	
/////////////////////////////////////////////////////
/// QS : QUICKSEARCH FORM WITH SEARCH SUGGESTION TOOL
if($UseQS && ($currentpage=="catalogue" || $currentpage=="classifieds" || $currentpage=="gallery")){	
	
$BuildQS = '';

//$BuildQS .= '<div class="HalfBox" id="Flexi">';
	$BuildQS .= '<div id="quicksearch">';
		if(!empty($_GET['category']))		$category			= $_GET['category'];
		if(!empty($_GET['subcategory']))	$subcategory		= $_GET['subcategory'];
		if(!empty($_GET['qs_price_min']))	$qs_price_min		= $_GET['qs_price_min'];
		if(!empty($_GET['qs_price_max']))	$qs_price_max		= $_GET['qs_price_max'];
		if(!empty($_GET['qs_price_range']))	$qs_price_range		= $_GET['qs_price_range'];
		if(!empty($_GET['qs_order']))		$qs_order			= $_GET['qs_order'];
		if(!empty($_GET['qs_keywords']))	$qs_keywords		= $_GET['qs_keywords'];
		
		if($qs_price_range){
			$qs_price_range		=str_replace(",","",$qs_price_range);
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
		}
	
		$BuildQS .= '<h1 class="Search"><span>'.$QSTitle.'</span></h1>';
			    
		$BuildQS .= '<form name="qs" id="qs" class="qs_container" onsubmit="return false;" action="#">';
		//$BuildQS .= '<fieldset class="qs_container">';
			$BuildQS .= '<input type="hidden" name="status" value="1">';
			$BuildQS .= '<input type="hidden" name="category" value="'.$my_category.'">';
			//$BuildQS .= '<input type="hidden" name="subcategory" id="subcategory" value="'.$subcategory.'">';
			$BuildQS .= '<input type="hidden" name="currentpage_ss" id="currentpage_ss" value="'.$currentpage.'">';
				
			//$BuildQS .= '<div class="qs-options">';

				$BuildSubCategoryList = '<span class="subcategory">';
				$BuildSubCategoryList .= '<label for="subcategory">Property Type...'.$divider.'</label><br>';
				
				$scQuery="SELECT COUNT(*) AS MatchesPerSC, csc.id,csc.id AS subcategoryID,csc.subcategory AS subcategoryName,c.subcategory";
				$scQuery.=" FROM catalogue_subcats AS csc, catalogue AS c";
				$scQuery.=" WHERE c.category=${clientCats['Classifieds']} AND c.status!=0 AND csc.status=1 AND c.subcategory=csc.id";
				if($qs_price_min && $qs_price_max) $scQuery.=" AND c.price>=$qs_price_min AND c.price<=$qs_price_max";
				$scQuery.=" GROUP BY csc.id ORDER BY MatchesPerSC DESC";
				//echo $scQuery;
				$scResult = mysql_query($scQuery);
				if($scResult && mysql_num_rows($scResult)>=1){
					$BuildSubCategoryList .= '<select name="subcategory" id="subcategory" title="Please select..." onChange="updateHint()">';
					$BuildSubCategoryList .= '<option value="">ALL</option>';
					for($i=0;$i<mysql_num_rows($scResult);$i++){						
						$scRow = mysql_fetch_array($scResult);
						$BuildSubCategoryList .= '<option value="'.$scRow['subcategoryID'].'"';
						if($subcategory == $scRow['subcategoryID']) $BuildSubCategoryList .= ' selected';
						$BuildSubCategoryList .= '>'.$scRow['subcategoryName'].'</option>';
					}
					
					$BuildSubCategoryList .= '</select>';
				}
				$BuildSubCategoryList .= '</span>';
				
									
				$BuildOrderBy = '<span class="orderby">';
					$BuildOrderBy .= '<label for="qs_order">Order By...'.$divider.'</label><br>';
					$BuildOrderBy .= '<select name="qs_order" id="qs_order" title="Sort results by..." onChange="updateHint()">';
						
						for($tmpcount=0;$tmpcount<count($gp_qs_arr_order);$tmpcount++){
							$BuildOrderBy .= '<option value="'.$gp_qs_arr_order[$tmpcount][2].'"';
							if($qs_order == $gp_qs_arr_order[$tmpcount][2]) $BuildOrderBy .= ' selected';
							$BuildOrderBy .= '>'.$gp_qs_arr_order[$tmpcount][1].'</option>';							
						}
					$BuildOrderBy .= '</select>';
				$BuildOrderBy .= '</span>';
				
				/*
				$BuildMinPrice='<span class="minprice">';
				$BuildMinPrice.='<label for="qs_price_min">Min Price'.$divider.'</label><br>';
				$BuildMinPrice.='<select name="qs_price_min" id="qs_price_min" title="Set Minimum Price for your search" onChange="updateHint()">';
					
					if($currentpage=="catalogue"){
						$tmpArray = $gp_qs_arr_price_min;
					}elseif($currentpage=="classifieds" || $currentpage=="gallery"){
						$tmpArray = $gp_qs_arr_price_min_classifieds;
					}
											
					for($tmpcount=0;$tmpcount<count($tmpArray);$tmpcount++){
						$BuildMinPrice.='<option value="'.$tmpArray[$tmpcount].'"';
						if($qs_price_min == $tmpArray[$tmpcount]) $BuildMinPrice.=' selected';
						$BuildMinPrice.='>&#163;'.number_format($tmpArray[$tmpcount]).'</option>';							
					}
					
				//$BuildMinPrice.=$BuildMinPrice;
				$BuildMinPrice.='</select>';
				$BuildMinPrice.='</span>';				
				$BuildQS .= $BuildMinPrice;
				
				
				$BuildMaxPrice='<span class="maxprice">';
					$BuildMaxPrice.='<label for="qs_price_max">Max Price'.$divider.'</label><br>';
					$BuildMaxPrice.='<select name="qs_price_max" id="qs_price_max" title="Set Maximum Price for your search" onChange="updateHint()">';
						
						if($currentpage=="catalogue"){
							$tmpArray = $gp_qs_arr_price_max;
						}elseif($currentpage=="classifieds" || $currentpage=="gallery"){
							$tmpArray = $gp_qs_arr_price_max_classifieds;
						}
											
						for($tmpcount=0;$tmpcount<count($tmpArray);$tmpcount++){
							$BuildMaxPrice.='<option value="'.$tmpArray[$tmpcount].'"';
							if($qs_price_max == $tmpArray[$tmpcount]){
								$selected_maxprice = true;
								$BuildMaxPrice.='selected';
							}
							$BuildMaxPrice.='>&#163;'.number_format($tmpArray[$tmpcount]).'</option>';
						}						
						
						if(!$selected_maxprice) $BuildMaxPrice.='<option value="1000000" selected>&#163;'.number_format($tmpArray[count($tmpArray)-1]).'+</option>';						
						
					$BuildMaxPrice.='</select>';
				$BuildMaxPrice.='</span>';
				$BuildQS .= $BuildMaxPrice;
				*/
				
				
				$BuildPriceRange='<span class="price_range">';
					$BuildPriceRange.='<label for="qs_price_range">Price Range'.$divider.'</label><br>';
					$BuildPriceRange.='<select name="qs_price_range" id="qs_price_range" title="Set price range for your search" onChange="updateHint()">';
						
						$tmpArray = $gp_qs_arr_price_range;
						$BuildPriceRange .= '<option value="0 - 1,000,000">ALL</option>';
						for($tmpcount=0;$tmpcount<count($tmpArray);$tmpcount++){
							$BuildPriceRange.='<option value="'.$tmpArray[$tmpcount].'"';
							if($qs_price_range == $tmpArray[$tmpcount]){
								$selected_price_range = true;
								$BuildPriceRange.='selected';
							}
							$BuildPriceRange.='>&#163;'.$tmpArray[$tmpcount].'</option>';
						}						
												
					$BuildPriceRange.='</select>';
				$BuildPriceRange.='</span>';
				
				
				$BuildKeywords = '<span class="keywords">';
					$BuildKeywords .= '<label for="qs_keywords">In this area...'.$divider.'</label><br>';
					//$BuildKeywords .= '<input type="text" name="qs_keywords" id="qs_keywords" value="'.$qs_keywords.'" onkeyup="this.value=this.value.replace(/[^a-zA-Z0-9|\s]/g,\'\');showHint(this.value);" maxlength="20">';
					$BuildKeywords .= '<input type="text" name="qs_keywords" id="qs_keywords" value="'.$qs_keywords.'" maxlength="20">';
					$BuildKeywords .= '<input type="button" id="qssubmit" name="qs_submit" value="GO!" title="Submit Search" onClick="ForceUpdateHint()">';//id="qssubmit" 
				$BuildKeywords .= '</span>';
				
				
				$BuildQS .= $BuildSubCategoryList;
				$BuildQS .= $BuildPriceRange;
				$BuildQS .= $BuildOrderBy;
			    $BuildQS .= $BuildKeywords;
			
			//$BuildQS .= '</div>';			
			//$BuildQS .= '</fieldset>';
		$BuildQS .= '</form>';

		$BuildQS .= '</div>';
		
	//$BuildQS .= '</div>';
	//$BuildQS .= '<div class="HalfBoxBaseCover">&nbsp;</div>';
	return $BuildQS;

}
?>