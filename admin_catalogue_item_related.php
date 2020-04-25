<?php
$editid = $_REQUEST['editid'];

$curr_page = "catalogue";
$curr_page_sub = "items";
include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle("Pages &#124; Related Information");

$tmpCatalogueData = $PageBuild->GetCatalogueData($cust_category,$cust_subcategory,$editid);
$BuildTip = "Add related information to: ";
if($tmpCatalogueData['categoryName']) $BuildTip .= $tmpCatalogueData['categoryLink'];
if($tmpCatalogueData['subcategoryName']) $BuildTip .= ' &gt; '.$tmpCatalogueData['subcategoryLink'];
if($tmpCatalogueData['itemName']) $BuildTip .= ' &gt; <a href="admin_catalogue_upload.php?editid='.$editid.'" title="Continue editing '.$tmpCatalogueData['itemNameRaw'].'">'.$tmpCatalogueData['itemName'].'</a>';
 $BuildTip .= ' &gt; <em>Add related</em>';

$BuildPage .= $PageBuild->AddPageTip($BuildTip);
//$BuildPage .= $PageBuild->AddTag('motionpack.js');
$BuildPage .= $PageBuild->AddTag(array('dir'=>'addingajax/','file'=>'sack.js'));
$BuildPage .= $PageBuild->AddTag(array('dir'=>'addingajax/','file'=>'sackSubCategories.js'));
$BuildPage .= $PageBuild->AddTag(array('dir'=>'addingajax/','file'=>'addingajax.js'));
include("includes/admin_pageheader.php");

/////////// check to see if session is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
} else {

	if(!gp_enabled('related')) exit();
	$tablename = "tbl_related_subcats";
	
	/////////// SUBMIT BUTTON (MOBILE)
	$SubmitButton = '';
	$SubmitButton .= '<div class="panel_oneline" id="SubmitDiv" style="display:none;">';
		$SubmitButton .= '<p><span class="steptitle">Update Details:</span></p>';
		$SubmitButton .= '<div class="inner_right related">';
			$SubmitButton .= '<input type="submit" id="submit" name="Submit">';
		$SubmitButton .= '</div>';
	$SubmitButton .= '</div>';
	
	if($_POST['Submit']){
	//if($_POST['info_Submit']){		
		if(!empty($_REQUEST['category_new'])){
			if($_POST['category_new'] && $_POST['subcategory_new']){
				$InsertQuery = "INSERT INTO $db_client.$tablename (id,itemID,categoryID,subcategoryID) VALUES (0,$editid,".$_POST['category_new'].",".$_POST['subcategory_new'].")";
				$InsertResult = $db->mysql_query_log($InsertQuery);
				header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?success=true&editid=".$editid);
			}else{
				echo '<p class="error">Please select category and subcategory to add to related information</p>';
			}
		}
		/*for($i=0;$i<10;$i++){
			$FieldName1 = 'relatedID_'.$i;
			$FieldName2 = 'category_'.$i;
			$FieldName3 = 'subcategory_'.$i;
			
			$UpdateQuery = "UPDATE $db_client.$tablename SET categoryID=${_POST[$FieldName2]}, subcategoryID=${_POST[$FieldName3]} WHERE id=${_POST[$FieldName1]} LIMIT 1";
			$UpdateResult = $db->mysql_query_log($UpdateQuery);
			if($UpdateResult && mysql_affected_rows()) header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?success=true&editid=".$editid);
		}*/	
	//}
	
	//if($_POST['rd_Submit']){		
		if(!empty($_REQUEST['rd_subcategory_new'])){
			if($_POST['rd_subcategory_new'] && $_POST['document_new']){
				$InsertQuery = "INSERT INTO $db_client.$tablename (id,itemID,itemID2) VALUES (0,$editid,".$_POST['document_new'].")";
				$InsertResult = $db->mysql_query_log($InsertQuery);
				header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?success=true&editid=".$editid);
			}else{
				echo '<p class="error">Please select subcategory and document to add to related documents</p>';
			}
		}
		
		for($i=0;$i<20;$i++){
			$FieldName1 = 'relatedID_'.$i;//the actual ID of this row(item) within 'tbl_related_subcats'
			$FieldName2 = 'category_'.$i;//category ID
			$FieldName3 = 'subcategory_'.$i;//subcategory ID
			$FieldName4 = 'rd_subcategory_'.$i;//related document subcategory
			$FieldName5 = 'document_'.$i;//related document(id of item to link to)
			
			//Update related INFORMATION
			if($_POST[$FieldName1] && ($_POST[$FieldName2] || $_POST[$FieldName3]) ){
				$UpdateQuery = "UPDATE $db_client.$tablename SET categoryID=".$_POST[$FieldName2].", subcategoryID=".$_POST[$FieldName3]." WHERE id=".$_POST[$FieldName1]." LIMIT 1";
				$UpdateResult = $db->mysql_query_log($UpdateQuery);
				if($UpdateResult && mysql_affected_rows()) header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?success=true&editid=".$editid);
			}
			
			//Update related DOCUMENTS
			if($_POST[$FieldName1] && ($_POST[$FieldName4] || $_POST[$FieldName5]) ){
				$UpdateQuery = "UPDATE $db_client.$tablename SET subcategoryID=".$_POST[$FieldName4].", itemID2=".$_POST[$FieldName5]." WHERE id=".$_POST[$FieldName1]." LIMIT 1";
				$UpdateResult = $db->mysql_query_log($UpdateQuery);
				if($UpdateResult && mysql_affected_rows()) header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?success=true&editid=".$editid);
			}
		}		
		
	//}
	}
	
	if($_REQUEST['success']) echo '<p class="good">Database successfully updated</p>';
	
	$FormStart = '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
	$FormStart .= '<input type="hidden" name="editid" value="'.$editid.'">';
	
	
	/////////////////////////////////////////////////////
	//GET ITEM NAME (CHECK ITEM EXISTS BEFORE WE PROCEED)
	$GetItemQuery = "SELECT id,name FROM $db_clientTable_catalogue WHERE id=$editid LIMIT 1";
	$GetItemResult = mysql_query($GetItemQuery);
	if($GetItemResult && mysql_num_rows($GetItemResult)==1){
		$GetItemRow = mysql_fetch_row($GetItemResult);
		$ItemName = $GetItemRow[1];//FROM catalogue table
	}
	///////////////////////////////////////////////////
	///////////////////////////////////////////////////
	//STEP ?: GIVE RELATIONSHIP / RELATED SUBCATEGORIES
	if($editid && empty($editid_xtra) && $ItemName){
		
		
		
		
		
		
		//////////////////////////
		// ADD Related Information
		$CatQuery = "SELECT cc.id AS categoryID,cc.category AS categoryName";
		$CatQuery .= " FROM $db_clientTable_catalogue_cats AS cc, $db_clientTable_catalogue_subcats AS csc";
		$CatQuery .= " WHERE cc.id=csc.category OR csc.category=1";
		$CatQuery .= " GROUP BY cc.id ORDER BY cc.position ASC";
		$CatResult = mysql_query($CatQuery);		
		if($CatResult && mysql_num_rows($CatResult)>=1){					
			$SelectOptionsNew = '<div class="inner_right related">';
			$SelectOptionsNew .= '<div id="AddRI" class="hidden">';
			$CategorySelectNew = '<select class="medium" id="category_new" name="category_new" onchange="getSubCategoriesList(this,\'subcategory_new\',0);ShowDiv(\'SubmitDiv\');">';
			for($c=0;$c<mysql_num_rows($CatResult);$c++){
				$cRow = mysql_fetch_array($CatResult);
				$CategorySelectNew .= '<option value="'.$cRow['categoryID'].'">'.$cRow['categoryName'].'</option>';
			}
			$CategorySelectNew .= '<option value="" selected>Select Category</option>';
			$CategorySelectNew .= '</select>';
			$SelectOptionsNew .= $CategorySelectNew;
			
			// SUBCATEGORIES				
			$SelectOptionsNew .= '<select class="medium" id="subcategory_new" name="subcategory_new" style="display:none;">';
			$SelectOptionsNew .= '</select>';
			//$SelectOptionsNew .= '<input type="submit" id="AddBut" class="AddBut" name="info_Submit" style="display:none;">';
			$SelectOptionsNew .= '</div>';		
			$SelectOptionsNew .= '</div>';
		}
		
		
		///////////////////////
		// ADD Related Document
		$SubCatQuery = "SELECT * FROM $db_clientTable_catalogue_subcats WHERE category=1 ORDER BY position_incat ASC";
		$SubCatResult = mysql_query($SubCatQuery);		
		if($SubCatResult && mysql_num_rows($SubCatResult)>=1){					
			$rd_SelectOptionsNew = '<div class="inner_right related">';
			$rd_SelectOptionsNew .= '<div id="AddRD" class="hidden">';
			$SubCategorySelectNew = '<select class="medium" id="rd_subcategory_new" name="rd_subcategory_new" onchange="getSubCategoriesList(this,\'document_new\',0);ShowDiv(\'SubmitDiv\');">';
			for($sc=0;$sc<mysql_num_rows($SubCatResult);$sc++){
				$scRow = mysql_fetch_array($SubCatResult);
				$SubCategorySelectNew .= '<option value="'.$scRow['id'].'">'.$scRow['subcategory'].'</option>';
			}
			$SubCategorySelectNew .= '<option value="" selected>Select Sub-Category</option>';
			$SubCategorySelectNew .= '</select>';
			$rd_SelectOptionsNew .= $SubCategorySelectNew;
			
			// SUBCATEGORIES				
			$rd_SelectOptionsNew .= '<select class="medium" id="document_new" name="document_new" style="display:none;">';
			$rd_SelectOptionsNew .= '</select>';
			//$rd_SelectOptionsNew .= '<input type="submit" id="rd_AddBut" class="AddBut" name="rd_Submit" style="display:none;">';
			$rd_SelectOptionsNew .= '</div>';		
			$rd_SelectOptionsNew .= '</div>';
		}
			
		
		
		
		
		
		
		
		
		
		
		
		
		// ALREADY added (list)
		$SelectOptions = '';
		$RelatedQuery = "SELECT rsc.id AS itemIDDebug,rsc.itemID,rsc.categoryID,rsc.subcategoryID,c.id,cc.id,cc.position,csc.id,csc.position_incat";
		$RelatedQuery .= " FROM $db_client.$tablename AS rsc, $db_clientTable_catalogue AS c, $db_clientTable_catalogue_cats AS cc, $db_clientTable_catalogue_subcats AS csc";
		$RelatedQuery .= " WHERE rsc.categoryID=cc.id AND rsc.subcategoryID=csc.id AND c.id=$editid AND rsc.itemID=$editid ORDER BY cc.position ASC, csc.position_incat ASC";
		//echo '<br>(FB):'.$RelatedQuery;
		$RelatedResult = mysql_query($RelatedQuery);
		if($RelatedResult && mysql_num_rows($RelatedResult)>=1){									
			$SelectOptions .= '<div class="panel_oneline">';
			$SelectOptions .= '<p><label for="description">Related Information:</label></p>';
			$SelectOptions .= '<div class="inner_right related"><label class="medium">Category</label><label class="medium">Sub-category</label></div>';
			$SelectOptions .= $SelectOptionsNew;
			for($rscCount=0;$rscCount<mysql_num_rows($RelatedResult);$rscCount++){			
				$rscRow = mysql_fetch_array($RelatedResult);
				$rscID = $rscRow['itemIDDebug'];
				$rscCategoryID = $rscRow['categoryID'];
				$rscSubCategoryID = $rscRow['subcategoryID'];
				
				
				$SelectOptions .= '<input type="hidden" name="relatedID_'.$rscCount.'" value="'.$rscID.'">';
				
				////////////////////////////////////////////////////////////////////
				// LIST CATEGORIES & SUBCATEGORIES AS SELECT OPTIONS ALREADY ENTERED
				$CatQuery = "SELECT cc.id AS categoryID,cc.category AS categoryName";
				$CatQuery .= " FROM $db_clientTable_catalogue_cats AS cc, $db_clientTable_catalogue_subcats AS csc";
				$CatQuery .= " WHERE (cc.id=csc.category OR csc.category=1)";
				$CatQuery .= " GROUP BY cc.id ORDER BY cc.position ASC";
		
				$CatResult = mysql_query($CatQuery);
				if($CatResult && mysql_num_rows($CatResult)>=1){
					$SelectOptions .= '<div class="inner_right related" id="RI_'.$rscID.'">';
					$CategorySelect = '<select class="medium" id="category_'.$rscCount.'" name="category_'.$rscCount.'" onchange="getSubCategoriesList(this,\'subcategory_'.$rscCount.'\','.$rscSubCategoryID.');ShowDiv(\'SubmitDiv\');">';
					for($c=0;$c<mysql_num_rows($CatResult);$c++){
						$cRow = mysql_fetch_array($CatResult);
						$CategorySelect .= '<option value="'.$cRow['categoryID'].'"';
						if($rscCategoryID==$cRow['categoryID']) $CategorySelect .= ' selected';//selected
						$CategorySelect .= '>'.$cRow['categoryName'].'</option>';
					}
					$CategorySelect .= '</select>';
					$SelectOptions .= $CategorySelect;
					
					// SUBCATEGORIES				
					$SubCatQuery = "SELECT * FROM $db_clientTable_catalogue_subcats WHERE (category=$rscCategoryID OR category=1) ORDER BY position_incat ASC";
					$SubCatResult = mysql_query($SubCatQuery);
					if($SubCatResult && mysql_num_rows($SubCatResult)>=1){
						$SelectOptions .= '<select class="medium" id="subcategory_'.$rscCount.'" name="subcategory_'.$rscCount.'" onchange="ShowDiv(\'SubmitDiv\');">';
						for($sc=0;$sc<mysql_num_rows($SubCatResult);$sc++){
							$scRow = mysql_fetch_array($SubCatResult);
							$SelectOptions .= '<option value="'.$scRow['id'].'"';
							if($rscSubCategoryID==$scRow['id']) $SelectOptions .= ' selected';//selected
							$SelectOptions .= '>'.$scRow['subcategory'].'</option>';
						}
						$SelectOptions .= '</select>';
					}

					$SelectOptions .= '<a href="javascript:DeleteRelatedItem(\''.$tablename.'\',\''.$rscID.'\')" name="'.$TableID.'" title="Delete this related item"><img src="includes/icons/icon_item_delete.gif" border="0"></a>';
					$SelectOptions .= '</div>';
				}
			}
			
			$SelectOptions .= '</div>';					
		}else{
			$SelectOptions .= '<div class="panel_oneline prompt"><p><strong>'.$ItemName.'</strong> currently has no related information</p>'.$SelectOptionsNew.'</div>';
		}
		
		
		////////////////////
		// RELATED DOCUMENTS
		if(gp_enabled('related_item')){
			$RelDocs = '';
			$RelDocQuery = "SELECT rsc.id AS itemIDDebug,c.category AS itemCategory,c.subcategory AS subcategoryID,c.id AS itemID,c.name AS itemName,c.image_large FROM $db_client.$tablename as rsc, $db_clientTable_catalogue AS c";
			$RelDocQuery .= " WHERE rsc.itemID=$editid AND rsc.itemID2=c.id";
			//WHERE rsc.categoryID=cat.id AND rsc.subcategoryID=subcat.id AND c.id=$editid AND rsc.itemID=$editid ORDER BY cat.position ASC, subcat.position_incat ASC";
			$RelDocResult = mysql_query($RelDocQuery);
			if($RelDocResult && mysql_num_rows($RelDocResult)>=1){
				
				$RelDocs .= '<div class="panel_oneline">';
				$RelDocs .= '<p><label for="description">'.mysql_num_rows($RelDocResult).' Related Documents:</label></p>';
				$RelDocs .= '<div class="inner_right related"><label class="medium">Sub-Category</label><label class="medium">Document</label></div>';
				$RelDocs .= $rd_SelectOptionsNew;
				
				for($rdCount=0;$rdCount<mysql_num_rows($RelDocResult);$rdCount++){				
					$rdRow = mysql_fetch_array($RelDocResult);
					$rdID = $rdRow['itemIDDebug'];
					$rdItemID = $rdRow['itemID'];
					$rdItemName = $rdRow['itemName'];
					$rdItemCategory = $rdRow['itemCategory'];
					$rdItemFileName = $rdRow['image_large'];
					
					$rdCategoryID = $rdRow['categoryID'];
					$rdSubCategoryID = $rdRow['subcategoryID'];
					
					$RelDocs .= '<input type="hidden" name="relatedID_'.$rdCount.'" value="'.$rdID.'">';				
					$RelDocs .= '<div class="inner_right related" id="RI_'.$rdID.'">';
					////////////////////////////////////////////////////////////////////
					// LIST DOCUMENT SUBCATEGORIES AS SELECT OPTIONS ALREADY ENTERED
					$RelDocSubCatQuery = "SELECT csc.* FROM $db_clientTable_catalogue_subcats AS csc WHERE csc.category=$rdItemCategory ORDER BY csc.position_incat ASC";
					$RelDocSubCatResult = mysql_query($RelDocSubCatQuery);
					if($RelDocSubCatResult && mysql_num_rows($RelDocSubCatResult)>=1){					
						$RelDocs .= '<select class="medium" id="rd_subcategory_'.$rdCount.'" name="rd_subcategory_'.$rdCount.'" onchange="getSubCategoriesList(this,\'document_'.$rdCount.'\','.$rdSubCategoryID.');ShowDiv(\'SubmitDiv\');">';
						for($c=0;$c<mysql_num_rows($RelDocSubCatResult);$c++){
							$cRow = mysql_fetch_array($RelDocSubCatResult);
							$RelDocs .= '<option value="'.$cRow['id'].'"';
							if($rdSubCategoryID==$cRow['id']) $RelDocs .= ' selected';//selected
							$RelDocs .= '>'.$cRow['subcategory'].'</option>';
						}
						$RelDocs .= '</select>';
						//$RelDocs .= '<a href="javascript:DeleteRelatedItem(\''.$tablename.'\',\''.$rdID.'\')" name="'.$TableID.'" title="Delete this related item"><img src="includes/icons/icon_item_delete.gif" border="0"></a>';
					}
					
					// DOCUMENTS				
					$DocsQuery = "SELECT * FROM $db_clientTable_catalogue WHERE subcategory=$rdSubCategoryID ORDER BY position_insubcat ASC";
					$DocsResult = mysql_query($DocsQuery);
					//echo '<BR>(FB):'.$DocsQuery;
					if($DocsResult && mysql_num_rows($DocsResult)>=1){
						$RelDocs .= '<select class="medium" id="document_'.$rdCount.'" name="document_'.$rdCount.'" onchange="ShowDiv(\'SubmitDiv\');">';
						for($d=0;$d<mysql_num_rows($DocsResult);$d++){
							$dRow = mysql_fetch_array($DocsResult);
							$RelDocs .= '<option value="'.$dRow['id'].'"';
							if($rdItemID==$dRow['id']) $RelDocs .= ' selected';//selected
							$RelDocs .= '>'.$dRow['name'].'</option>';
						}
						$RelDocs .= '</select>';
					}
					
					//$RelDocs .= $rdItemName.'('.$rdItemFileName.')';
					$RelDocs .= '<a href="javascript:DeleteRelatedItem(\''.$tablename.'\',\''.$rdID.'\')" name="'.$TableID.'" title="Delete this related document"><img src="includes/icons/icon_item_delete.gif" border="0"></a>';
					$RelDocs .= '</div>';			
				}			
				$RelDocs .= '</div>';
			}else{
				$RelDocs .= '<div class="panel_oneline prompt"><p><strong>'.$ItemName.'</strong> currently has no related documents'.$rd_SelectOptionsNew.'</p></div>';
			}
		}
	}
	
	echo $FormStart;
	if(gp_enabled('related_subcategory')) echo $SelectOptions;
	if(gp_enabled('related_item')) echo $RelDocs;
	echo $SubmitButton;
	echo '</form>';
			
	/////////// SubNavInner (top right)
	$SubNavInner = '';
	$SubNavInner .= '<ul id="SubNavInner">';
	$SubNavInner .= '<li class="edit"><a href="admin_catalogue_upload.php?editid='.$editid.'" title="edit">continue editing</a></li>';
	if(gp_enabled('related_subcategory')) $SubNavInner .= '<li class="add"><a href="javascript:OpenClose(\'AddRI\');" title="add related information">add related information</a></li>';
	if(gp_enabled('related_item')) $SubNavInner .= '<li class="add"><a href="javascript:OpenClose(\'AddRD\');" title="add related documents">add related documents</a></li>';
	$SubNavInner .= '</ul>';
	echo $SubNavInner;
		


}
include("includes/admin_pagefooter.php");
?>


