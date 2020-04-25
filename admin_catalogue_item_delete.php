<?php
$suid_PageAccess = true;
if(isset($_REQUEST['prevpage'])) $my_prevpage = $_REQUEST['prevpage'];			
if(!empty($_REQUEST['fileOnly'])) $fileOnly = true;
		
$curr_page = "catalogue";
include("includes/classes/PageBuild.php");
require_once("prefs/catalogue_prefs.php");
$BuildTip = "Delete: ";
if (!empty($_REQUEST['uid'])) {
	$uid = $_REQUEST['uid'];
	$ParentID = $uid;
	$query 		= "SELECT * FROM $db_clientTable_catalogue WHERE id=$uid LIMIT 1";
	$result 	= mysql_query($query);
	if($result && mysql_num_rows($result) >= 1){
		$ret_array	= mysql_fetch_array($result);
		$my_id		= $ret_array['id'];
		if(!empty($ret_array['id_xtra'])){
			$ParentID = $ret_array['id_xtra'];
			$my_id_xtra = $ret_array['id_xtra'];
		}
		$my_name	= $ret_array['name'];
		$my_cat		= $ret_array['category'];
		$my_subcat	= $ret_array['subcategory'];
		
		$my_image_highres	= $siteroot.$gp_uploadPath['highres'].$ret_array['image_large'];
		$my_image_large		= $siteroot.$gp_uploadPath['large'].$ret_array['image_large'];
		$my_image_primary	= $siteroot.$gp_uploadPath['primary'].$ret_array['image_large'];
		$my_image_thumb		= $siteroot.$gp_uploadPath['thumbs'].$ret_array['image_large'];
		$my_filename		= $ret_array['image_large'];		
	}
	
	$tmpCatalogueData = $PageBuild->GetCatalogueData(0,0,$ParentID);	
	if($tmpCatalogueData['categoryName']) $BuildTip .= $tmpCatalogueData['categoryLink'];
	if($tmpCatalogueData['subcategoryName']) $BuildTip .= ' &gt; '.$tmpCatalogueData['subcategoryLink'];	
	if($tmpCatalogueData['itemName']) $BuildTip .= " &gt; ".$tmpCatalogueData['itemLink'];
	
	if($fileOnly){
		$BuildTitle = "Delete FILE only";		
	}else{
		if(!$my_id_xtra){
			$BuildTitle = "Delete PRIMARY item";
		}else{
			$BuildTitle = "Delete ATTACHED item";
		}
	}
	
	$BuildTip .= " &gt; <em>".$BuildTitle."</em>";
}
	
$BuildPage .= $PageBuild->AddPageTitle("Pages &#124; ".$BuildTitle);
$BuildPage .= $PageBuild->AddPageTip($BuildTip);
$BuildPage .= $PageBuild->AddThickbox();
include("includes/admin_pageheader.php");

/////////// check to see if option is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
}else if($_SESSION['suid'] && $suid_PageAccess && ($itemID && !suid_pageAccess($itemID)) ){
	echo suid_pageAccessMessage();
}else{
		
	$CancelButton = '';
	$CancelButton .= '<div class="panel">';
		$CancelButton .= '<div class="inner_right">';					
			$CancelButton .= '<a href="javascript:history.go(-1)" id="cancel"><span>&#60;&nbsp;Cancel</span></a>';
		$CancelButton .= '</div>';
	$CancelButton .= '</div>';
			
			
	if($my_id){		
			
		if (isset($_POST['delete'])) { // if 1
			
			$attributes = array('itemID'=>$my_id,'fileOnly'=>$fileOnly);
			$CMSDelete->DeleteItem($attributes);							
			
			
			$all_query = "SELECT * FROM $db_clientTable_catalogue ORDER by position";
			$all_result = mysql_query($all_query);				
			$all_num_rows = mysql_num_rows($all_result);
								
			
			for($tmp = 1;$tmp <= $all_num_rows;$tmp++){					
				$all_array 	= mysql_fetch_row($all_result);
				//echo $tmp .' / ' . $all_num_rows .' , ';
				$position_query = "UPDATE $db_clientTable_catalogue SET position='$tmp' WHERE id='$all_array[0]' LIMIT 1";
				$position_result = mysql_query($position_query);//$db->mysql_query_log
			}
			
			$ReturnButton = '';
			$ReturnButton .= '<div class="panel_good">';
			$ReturnButton .= '<p>Successfully deleted. Click return to continue</p>';
			
				$ReturnButton .= '<div class="inner_right">';
					switch($my_prevpage){								
						case "item_edit":	if($my_id_xtra){
												if($fileOnly){
													$ReturnButton .= '<a href="admin_catalogue_upload.php?editid='.$my_id.'" id="return"><span>&#60;&nbsp;Return</span></a>';//catalogue_options.php
												}else{
													$ReturnButton .= '<a href="admin_catalogue_upload.php?editid='.$ParentID.'" id="return"><span>&#60;&nbsp;Return</span></a>';//catalogue_options.php
												}
											}else{
												if($fileOnly){
													$ReturnButton .= '<a href="admin_catalogue_upload.php?editid='.$my_id.'" id="return"><span>&#60;&nbsp;Return</span></a>';
												}else{
													$ReturnButton .= '<a href="'.$CatalogueRoot.'" id="return"><span>&#60;&nbsp;Return</span></a>';
												}
											}
											break;
								
						//case "deletecat":	$ReturnButton .= '<a href="javascript:history.go(-2)" id="return"><span>&#60;&nbsp;Return</span></a>';break;
						
						case "item_list":	$ReturnButton .= '<a href="admin_catalogue_all.php?category=" id="return"><span>&#60;&nbsp;Return</span></a>';break;//catalogue_options.php
						
						default:			$ReturnButton .= '<a href="javascript:history.go(-2)" id="return"><span>&#60;&nbsp;Return</span></a>';break;
					}				
				$ReturnButton .= '</div>';
			$ReturnButton .= '</div>';
			echo $ReturnButton;
			
		} else { // ELSE if 1
		
			//////////// WARNING PANEL
			$WarningPanel = '<div class="panel_warning">';				
			if (!$my_id_xtra && !$fileOnly) {
				$WarningPanel .= '<p>This is a PRIMARY '.$CommonCustomWords['item'].'...<br/>';
			} else {
				if($fileOnly){
					$WarningPanel .= '<p>You are about to delete this file...<br/>';
				}else{
					$WarningPanel .= '<p>You are about to delete this '.$CommonCustomWords['item'].'...<br/>';
				}
			}
			$WarningPanel .= '<strong>Once deleted, this cannot be undone!</strong>';
				$WarningPanel .= '<div class="inner_right">';
					$WarningPanel .= $CMSImages->GetThumb($my_image_large, $my_image_thumb, $my_filename,"false");
					if($CMSShared->IsImage($my_filename)){
						if($fileOnly){
							$WarningPanel .= $CMSAddOns->Thickbox("filePreview",$my_image_large,$my_filename,"");
						}else{
							$WarningPanel .= $CMSAddOns->Thickbox("item",$moreinfopage.'?uid='.$my_id,$my_name,"");
						}
					}elseif(!$CMSShared->IsImage($my_filename) && !empty($my_filename) ){
						$WarningPanel .= '<a href="'.$my_image_large.'" title="Preview item:&nbsp;'.$my_name.'"><img src="includes/icons/icon_item_preview.gif" alt="Preview file:&nbsp;'.$my_name.'"></a>';
					}
					$WarningPanel .= '<br><br><form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
					if($fileOnly) $WarningPanel .= '<input type="hidden" name="fileOnly" value="1">';
					$WarningPanel .= '<input type="hidden" name="prevpage" value="'.$my_prevpage.'">';
					$WarningPanel .= '<input type="hidden" name="uid" value="'.$my_id.'">';
					$WarningPanel .= '<input type="submit" name="delete" value="Delete" title="Delete Item" id="delete">';
					$WarningPanel .= '</form>';		
				$WarningPanel .= '</div>';
			$WarningPanel .= '</div>';			
			
			
			echo $WarningPanel;
			echo $CancelButton;
		} // END if 1

	} else {
		echo '<p class="error">Operator error. Item has not been recognised and so cannot be deleted</p>';
		echo $CancelButton;
	}

}
include("includes/admin_pagefooter.php");
?>






