<?php
//$YouTube=true;
$suid_PageAccess = true;
if($_REQUEST['category'])			$cust_category		= $_REQUEST['category'];
if($_REQUEST['subcategory'])		$cust_subcategory	= $_REQUEST['subcategory'];

////////////////////////////////////
///////////////////////// (FUNCTION)					
/// ARE WE EDITING OR UPLOADING NEW?	
if (!empty($_REQUEST['editid'])) $editid = $_REQUEST['editid'];
		
		
$curr_page = "catalogue";
$curr_page_sub	= "items";

include("includes/classes/PageBuild.php");
include("includes/classes/TinyMCE.php");
include("includes/classes/CMSMakeImages.php");

if($editid || $_REQUEST['id_xtra'] || ($_REQUEST['can'] || $_REQUEST['sid'])){

	if($_REQUEST['id_xtra']){
		$AddingAttachment = true;		
		$ParentID = $_REQUEST['id_xtra'];
	}else{

		//Custom DEBUG Checking script
		if(!$editid && ($_REQUEST['can'] || $_REQUEST['sid'])){
			$query = "SELECT id,detail_3,detail_6 FROM $db_clientTable_catalogue WHERE category=6";
			
			if($_REQUEST['can']){
				$query .= " AND detail_6='{$_REQUEST['can']}'";
			}else{
				$query .= " AND detail_3='{$_REQUEST['sid']}'";
			}
			$query .= " ORDER BY id ASC LIMIT 1";
			echo $query;
			$result = mysql_query($query);
			if($result && mysql_num_rows($result)==1){
				$staffRow = mysql_fetch_array($result);
				header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?jumped=true&editid=".$staffRow['id']);
			}
		}elseif($editid && !$_REQUEST['sid']){
			$ParentID = $editid;
			$query = "SELECT id,id_xtra,category,subcategory FROM $db_clientTable_catalogue WHERE id=$editid";
			$result = mysql_query($query);
			if($result){
				$editidRow = mysql_fetch_array($result);
				$cust_category=$editidRow['category'];
				$cust_subcategory=$editidRow['subcategory'];
				if(!empty($editidRow['id_xtra']))	$ParentID = $editidRow['id_xtra'];
			}
		}
	}

		
	////////////////	
	$BuildTitle = "Pages &#124; Edit item";
	$BuildTip = "Edit item: ";
	$tmpCatalogueData = $PageBuild->GetCatalogueData(0,0,$ParentID);
	if($tmpCatalogueData['categoryName']) $BuildTip .= $tmpCatalogueData['categoryLink'];
	if($tmpCatalogueData['subcategoryName']) $BuildTip .= ' &gt; '.$tmpCatalogueData['subcategoryLink'];

	if(($AddingAttachment || $ParentID!=$editid) && $tmpCatalogueData['itemLink']) $BuildTip .= " &gt; ".$tmpCatalogueData['itemLink'];
	if((!$AddingAttachment && $ParentID==$editid) && $tmpCatalogueData['itemName']) $BuildTip .= " &gt; ".$tmpCatalogueData['itemName'];
	
	
	
	if(!$AddingAttachment){
		if($tmpCatalogueData['categoryID'])	$cust_category	= $tmpCatalogueData['categoryID'];
		if($tmpCatalogueData['subcategoryID'])	$cust_subcategory	= $tmpCatalogueData['subcategoryID'];
	
		$my_cat = $cust_category;
		$my_subcat = $cust_subcategory;
		$my_ParentCat = $my_cat;
		$my_ParentSubCat = $my_subcat;
	}else{
		$my_id_xtra = $tmpCatalogueData['id_xtra'];
	}	
	
	$my_ParentName = $tmpCatalogueData['itemNameRaw'];
				
}else{
	$BuildTitle = "Pages &#124; Add new item";
	$BuildTip = "Add to your catalogue using the form below";
}


$BuildPage .= $PageBuild->AddPageTitle($BuildTitle);
$BuildPage .= $PageBuild->AddPageTip($BuildTip);
$BuildPage .= $PageBuild->AddThickbox();
$BuildPage .= $PageBuild->AddTag('JumpForms.js');
$BuildPage .= $PageBuild->AddTag('mootools2.css');
$BuildPage .= $PageBuild->AddTag(array('dir'=>'addingajax/','file'=>'addingajax.js'));
if(!$editid) $BuildPage .= $PageBuild->AddTag('forms_clickclear.js');
$BuildPage .= $TinyMCE->LoadAdvanced();
$BuildPage .= $PageBuild->AddTag(array('dir'=>'addingajax/','file'=>'sack.js'));
$BuildPage .= $PageBuild->AddTag(array('dir'=>'addingajax/','file'=>'sackSubCategories.js'));
$BuildPage .= $PageBuild->AddTag(array('dir'=>'addingajax/','file'=>'addingajax.js'));
include("includes/admin_pageheader.php");


/////////// check to see if session is set
if( notloggedin() ) {
	include('includes/admin_notloggedin.html');
}else if($_SESSION['suid'] && $suid_PageAccess && !suid_pageAccess($ParentID) ){
	echo suid_pageAccessMessage();
}else{
	//echo '<p class="prompt">This page is not currently available</p>';
	//exit();
	$catcheck_query		= "SELECT * FROM $db_clientTable_catalogue_cats";
	$catcheck_result	= mysql_query($catcheck_query);
	$catcheck_count		= mysql_num_rows($catcheck_result);
	//echo '<br />(FB): '.$siteroot.$gp_uploadPath['large'];
	if ($catcheck_count == 0) { // IF CATCHECK
		echo '<p class="prompt">There are currently no categories listed in your database</p>';
		if(gp_enabled("add_category")){
			$SubNavInner = '';
			$SubNavInner .= '<ul id="SubNavInner">';
			$SubNavInner .= '<li class="add"><a href="admin_category_add.php?thisList=catalogue_cats" title="add new category">add new category to proceed</a></li>';
			$SubNavInner .= '</ul>';
			echo $SubNavInner;
		}
	} else { // ELSE CATCHECK
	
		//echo '<br>(FB):EDIT ID (GET)= '.$editid;
		/// END ///
		
		
		///////////////////////////////////////////////////////////////////
		/////// the cancel button needs to know how many steps back to take
		/////// (1 will be added with each update to avoid cancelling back to previous amend)
		if (isset($_POST['updater']) ) {
			$updated = $_POST['updater'];
			$updated++;
		} else{
			/// if we've just entered page(previous page (-1) will be allocated to cancel button
			$updated = 1;
			//echo "UPDATED set to 1";
		}		
		
		
		// DEFAULT messages
		if($editid){
			$message_largeimage		= 'Replace '.$CommonCustomWords['file'].'?';
			$message_CustomThumb	= 'Replace Custom Thumbnail image';
		}else{
			$message_largeimage		= 'Attach '.$CommonCustomWords['file'].' to this '.$CommonCustomWords['item'].'?';
			$message_CustomThumb	= 'Attach Custom Thumbnail image';
		}		
		// DEFAULT panels
		$panel_largeimage		= 'panel_oneline';
		$panel_CustomThumb		= 'panel_oneline';
		$panel_name				= 'panel_oneline';
		$panel_price			= 'panel_oneline';
		$panel_price2			= 'panel_oneline';
		
		/////////////////////////////////////////// CRITICAL : GET CATEGORY (STEP 1) to determine subsequent steps
		/////////////////////////////////////////// ALSO, this determines the details used		
		// GET CATEGORY (FROM STEP 1)
		if (isset($_REQUEST['category']))		$my_cat = $_REQUEST['category'];
		if (isset($_REQUEST['subcategory']))	$my_subcat = $_REQUEST['subcategory'];

		if(!empty(${"gp_arr_details_cat".$my_cat})){
			$CustomDetails = ${"gp_arr_details_cat".$my_cat};						
		}else{
			$CustomDetails = $gp_arr_details;
		}
		
		// Set titles
		for($i=0;$i<count($CustomDetails);$i++){
			if($CustomDetails[$i]['inuse']==1) $gp_defVal_item['detail_'.$CustomDetails[$i]['id']] = $CustomDetails[$i]['name'].' (e.g. '.$CustomDetails[$i]['defVal'].')';
		}

		
		/// END ///
		
		include("admin_catalogue_item_data.php");
		
		// GET ID of item... used when adding additional images, which rely on 'id_xtra' being identical to 'id' of primary item
		if(!$my_id_xtra){
			if (isset($_REQUEST['id_xtra']) && !empty($_REQUEST['id_xtra'])){
				$my_id_xtra = $_REQUEST['id_xtra'];
			}else{
				$my_id_xtra = 0;// IF no id is set then we assume this to be a NEW item
			}
		}
		
		// SKIP image upload process
		if (isset($_REQUEST['UploadFileSkipped'])) $UploadFileSkipped = true;		
		
									
		/////////////////////////////////////////////
		/// CHECK DATA SUBMITTED AND UPLOAD FORM DATA
		if (isset($_POST['updater'])) { // IF DATA HAS BEEN SUBMITTED
			$post_success	= 1; // form submission is successful until told otherwise ("$post_success = 1")
			
			//INITIALISE empty values in-case nothing is picked up (avoids 'else' conditions on EVERY field)
			$description	= '';
			$keywords		= '';
			$name			= '';
			for($i=0;$i<count($CustomDetails);$i++){
				if($CustomDetails[$i]['inuse']==1) ${"detail_".$CustomDetails[$i]['id']} = '';
			}
		
			$category		= 0;
			$subcategory	= 0;
			$status			= 1;
			$price			= 0;
			$price2			= 0;
			$price_details	= '';
			
			//////////////////////////////////////////////////////////////////////
			/// ENSURE default values do not get processed / pass as OK (required)			
			
			if($_POST['name']			== $gp_defVal_item['name'] || $_POST['name'] == $gp_defVal_item['name_xtra']) $_POST['name'] = "";
			if($_POST['price']			== $gp_defVal_item['price']){			$_POST['price'] = 0;}
			if($_POST['price2']			== $gp_defVal_item['price2']){			$_POST['price2'] = 0;}
			if($_POST['price_details']	== $gp_defVal_item['price_details']){	$_POST['price_details'] = "";}
			for($i=0;$i<count($CustomDetails);$i++){
				if($CustomDetails[$i]['inuse']==1 && $_POST['detail_'.$CustomDetails[$i]['id']] == $gp_defVal_item['detail_'.$CustomDetails[$i]['id']])		$_POST['detail_'.$CustomDetails[$i]['id']] = "";
			}
			if($_POST['description']	== $gp_defVal_item['description']){		$_POST['description'] = "";}
			if($_POST['keywords']		== $gp_defVal_item['keywords']){		$_POST['keywords'] = "";}			
			
			/////////////////////////////
			/// check for name (required)
			$NameChanged=false;			
			if ( !empty($_POST['name']) ) {				
				if($my_name != $_POST['name']){
					$NameChanged=true;
					$name = $CMSTextFormat->escape_data($_POST['name']);
				}else{
					$name = $my_name;
				}
			} else {
				if($my_id_xtra == 0){
					$post_success = 0;
					$panel_name	= 'panel_error';
				}				
			}
			
				
			//////////////////////
			/// CHECK PRICE CHANGE			
			if ($my_id_xtra == 0 && isset($_POST['price']) && !empty($_POST['price']) ) {		
				$price = $_POST['price'];
				$price = $CMSTextFormat->stripCrap2_in($price);				
				$price = $CMSTextFormat->Price_ForceNumeric($price);										
			}
			if ($my_id_xtra == 0 && isset($_POST['price2']) && !empty($_POST['price2']) ) {		
				$price2 = $_POST['price2'];
				$price2 = $CMSTextFormat->stripCrap2_in($price2);				
				$price2 = $CMSTextFormat->Price_ForceNumeric($price2);										
			}
					
			/// check for description (not mandatory)
			if (!empty($_POST['description']) && $post_success!=0) {
				//$description = $CMSTextFormat->stripCrap2_in($_POST['description']);				
				$desc_original = $_POST['description'];
				$description = $CMSTextFormat->stripCrap2_in_body($desc_original);	
				$description = $CMSTextFormat->LanguageFilter($description);			
			}
			/// check for keywords (not mandatory)
			if (!empty($_POST['keywords']) && $post_success!=0) {
				//$keywords = $CMSTextFormat->stripCrap2_in($_POST['keywords']);				
				$keywords_original = $_POST['keywords'];
				$keywords = $CMSTextFormat->stripCrap2_in_body($keywords_original);	
				$keywords = $CMSTextFormat->LanguageFilter($keywords);			
			}
			/// check for details (not required)
			for($i=0;$i<count($CustomDetails);$i++){
				if ($CustomDetails[$i]['inuse']==1 && isset($_POST['detail_'.$CustomDetails[$i]['id']]) && $post_success!=0) ${"detail_".$CustomDetails[$i]['id']} = $CMSTextFormat->stripCrap2_in_body($_POST['detail_'.$CustomDetails[$i]['id']]);
			}
			
			/// check for category
			if (!empty($_POST['category']) && $post_success!=0) $category = $_POST['category'];
			
			/// check for category
			if (!empty($_POST['subcategory']) && $post_success!=0) $subcategory = $_POST['subcategory'];
			
			/// check for status
			if (isset($_POST['status']) && $post_success!=0) $status = $_POST['status'];
			
			/// price details		
			if (isset($_POST['price_details']) && $post_success!=0) $price_details = $CMSTextFormat->stripCrap2_in($_POST['price_details']);
			
			$tmpday = '00';
			$tmpmonth = '00';
			$tmpyear = '0000';
			/// Upload Date
			if (!empty($_POST['upload_date_day']))		$tmpday = $_POST['upload_date_day'];
			if (!empty($_POST['upload_date_month']))	$tmpmonth = $_POST['upload_date_month'];
			if (!empty($_POST['upload_date_year']))		$tmpyear = $_POST['upload_date_year'];
			$upload_date = $tmpyear.'-'.$tmpmonth.'-'.$tmpday; //2009-01-03
			
			/// Publish Date
			$tmpdaySpare = '00';
			$tmpmonthSpare = '00';
			$tmpyearSpare = '0000';
			if (!empty($_POST['spare_date_day']))	$tmpdaySpare = $_POST['spare_date_day'];
			if (!empty($_POST['spare_date_month']))	$tmpmonthSpare = $_POST['spare_date_month'];
			if (!empty($_POST['spare_date_year']))	$tmpyearSpare = $_POST['spare_date_year'];
			$spare_date = $tmpyearSpare.'-'.$tmpmonthSpare.'-'.$tmpdaySpare; //2009-01-03
	

			//////////////////////////						
			/// UPLOAD IMAGES
			/// add record to database					
			if ($post_success != 0) {
				if($editid){
					$query = "UPDATE $db_clientTable_catalogue SET name='$name'";
					for($i=0;$i<count($CustomDetails);$i++){
						//if(${"detail_".$i}) 
						if($CustomDetails[$i]['inuse']==1) $query .= ", detail_".$CustomDetails[$i]['id']."='${"detail_".$CustomDetails[$i]['id']}'";
					}
					$query .= ", price='$price', price2='$price2', category='$category', subcategory='$subcategory', description='$description', keywords='$keywords', upload_date='$upload_date', spare_date='$spare_date', status='$status', price_details='$price_details' WHERE id='$editid'";
					$result = $db->mysql_query_log($query);
					$uid = $editid;
				}else{
					if($category=='any') $category=0;
					//REFERENCE: $query = "INSERT into $db_clientTable_catalogue (id_xtra, position_incat, position_initem, position, name, detail_1, detail_2, detail_3, detail_4, detail_5, detail_6, detail_7, detail_8, detail_9, detail_10, price, price2, category, subcategory, description, keywords, image_large, image_small, upload_date, spare_date, status, price_details) VALUES ('$my_id_xtra', '0', '999', '0', '$name', '$detail_1', '$detail_2', '$detail_3', '$detail_4', '$detail_5', '$detail_6', '$detail_7', '$detail_8', '$detail_9', '$detail_10', '$price', '$price2', '$category', '$subcategory', '$description', '$keywords', '$UploadFileName', '$UploadFileName', '$upload_date', '$spare_date', '$status', '$price_details')";
					
					$query = "INSERT into $db_clientTable_catalogue (id_xtra,image_large";//,image_small
					for($i=0;$i<sizeof($FieldNames);$i++){
						$query .= ", ${FieldNames[$i]}";
					}
					$query .= ") VALUES ('$my_id_xtra','$UploadFileName'";//,'$UploadFileName'
					for($i=0;$i<sizeof($FieldNames);$i++){
						$query .= ", '${$FieldNames[$i]}'";
					}
					$query .= ")";
					//echo $query;
					//exit();
					$result = $db->mysql_query_log($query);
					$uid = mysql_insert_id();					
				}
								
				if ($result) {
					
					////////////////////////////						
					/// UPLOAD CUSTOM THUMB IMAGE												
					if($_FILES['upload_thumb']['name'] && (!$my_id_xtra || $YouTube==true)){
						if($_POST['GenerateFileName']){
							$tmpName = $name;
						}else{
							$tmpName = $CMSShared->GetFileName($_FILES['upload_thumb']['name']);
						}
						$tmpName = $CMSMakeImages->GenerateFileName($tmpName);
						$filetype = $CMSShared->GetFileType($_FILES['upload_thumb']['name']);
						
						$UploadThumb = $_FILES['upload_thumb']['tmp_name'];
						$UploadFileName = "th_".$tmpName."_".$uid.".".$filetype;
						$my_CustomThumb_withpath	= $siteroot.$gp_uploadPath['thumbs'].$UploadFileName;
						move_uploaded_file($UploadThumb,$my_CustomThumb_withpath);
						
						if($CMSShared->FileExists($my_CustomThumb) && $my_CustomThumb != $my_CustomThumb_withpath) unlink($my_CustomThumb);
						
						////////////////////////////////////////////////////////////
						//// CHANGE FILENAME in database (as we needed to add 'uid' to image name to avoid duplication)
						$query  = "UPDATE $db_clientTable_catalogue SET image_small='$UploadFileName' WHERE id='$uid'";
						$result = $db->mysql_query_log($query);
					}

					
					//echo '<br/>(FB):QUERY IS GOOD';
					//////////////////////						
					/// UPLOAD LARGE IMAGE												
					if( (!empty($_POST['UploadFile']) || $_FILES['upload_large']['name']) && $_POST['UploadFile']!="Skip"){ // (upload): if 1 ...if blank / empty
						//echo '<br/>(FB) 1:'.$post_success;
						if($_FILES['upload_large']['name'] && $_FILES['upload_large']['size'] > $gp_maxfilesize_large ) { // (upload): if 2 (if filesize is ok)
						
							$message_largeimage = 'FILE SIZE is too big - please use image '.round($gp_maxfilesize_large/1024).'kb or smaller';
							$panel_largeimage = 'panel_error';
							//$post_success = 0;
						
						}else{
							if($_FILES['upload_large']['name']){//UPLOADED VIA BROWSE BUTTON
								if($_POST['GenerateFileName']){
									$tmpName = $name;
								}else{
									$tmpName = $CMSShared->GetFileName($_FILES['upload_large']['name']);
								}
								$tmpName = $CMSMakeImages->GenerateFileName($tmpName);
								
								$filetype = $CMSShared->GetFileType($_FILES['upload_large']['name']);
								
								$ThinUpload = false;
								$UploadFile = $_FILES['upload_large']['tmp_name'];					
							}else{
								if($_POST['GenerateFileName']){
									$tmpName = $name;
								}else{
									$tmpName = $CMSShared->GetFileName($_POST['UploadFile']);																
								}
								$tmpName = $CMSMakeImages->GenerateFileName($tmpName);
								$filetype = $CMSShared->GetFileType($_POST['UploadFile']);
								$ThinUpload = true;
								$UploadFile = $siteroot.'uploads/'.$_POST['UploadFile'];
							}
							
							//if id_xtra (attachment) doesn't have a name - get name from PRIMARY item
							if(!empty($my_id_xtra) && empty($tmpName)) $tmpName = $CMSMakeImages->GenerateFileName($my_ParentName);
							
													
							$UploadFileName = $tmpName."_".$uid.".".$filetype;
							
							////////////// Move the file over
							$my_largeimage_withpath	= $siteroot.$gp_uploadPath['large'].$UploadFileName;
							$my_highresimage_withpath = $siteroot.$gp_uploadPath['highres'].$UploadFileName;				
							//echo '<br />(FB)FROM: '.$UploadFile.'<br />';//SHOW PATHS WHEN UPLOAD FAILS
							//echo '<br />(FB)TO: '.$my_largeimage_withpath.'<br />';//SHOW PATHS WHEN UPLOAD FAILS
							
							// Make thumbnails, primary images and compress large image if necessary
							if (
							($ThinUpload==true && copy($UploadFile, $my_largeimage_withpath))
							|| ($ThinUpload==false && move_uploaded_file($UploadFile,$my_largeimage_withpath))						
							) { // (upload): if 3 ...move_uploaded_file()
								//touch ( $my_largeimage_withpath, filemtime ( $UploadFile ) ); // set the file time to the new file
								if($ThinUpload==true && $CMSShared->FileExists($UploadFile)) unlink($UploadFile); // REMOVE FILE FROM UPLOADS DIRECTORY
								
								// REMOVE previous image
								if($CMSShared->FileExists($my_image_large) && $my_image_large != $my_largeimage_withpath) unlink($my_image_large);
								if($CMSShared->FileExists($my_image_highres) && $my_image_highres != $my_highresimage_withpath) unlink($my_image_highres);
								
								if ($CMSShared->IsImage($UploadFileName)) {// (upload): if 4 ...isimage()
									//echo '<br />(FB) MAKE THUMBNAILS';
									if($CMSShared->FileExists($my_image_thumb)) unlink($my_image_thumb);
									if($CMSShared->FileExists($my_image_primary)) unlink($my_image_primary);
									
									//echo '<br/>MAKE THUMB IMAGE:'.$my_largeimage_withpath.'/'.$UploadFileName;
									$CMSMakeImages->MakeImage($my_largeimage_withpath,$UploadFileName,"thumb");
									//if($my_id_xtra == 0){
										//echo '<br/>MAKE PRIMARY IMAGE:'.$my_largeimage_withpath.'/'.$UploadFileName;
										$CMSMakeImages->MakeImage($my_largeimage_withpath,$UploadFileName,"primary");
									//}
									
									$tmpDimensions = @getimagesize($my_largeimage_withpath);// get original (ACTUAL) dimensions								
									if($ThinUpload==false && (filesize($my_largeimage_withpath)>$gp_maxfilesize_large || ($tmpDimensions[0]>$gp_large_width || $tmpDimensions[1]>$gp_large_height))){
										
										if(gp_enabled("highres") && $CMSMakeImages->MakeImage($my_largeimage_withpath,$UploadFileName,"highres")){										
											$CMSMakeImages->MakeImage($my_highresimage_withpath,$UploadFileName,"large");
										}else{
											$CMSMakeImages->MakeImage($my_largeimage_withpath,$UploadFileName,"large");
										}
									}
								} // (upload): end if 3 ...isimage()
								
								////////////////////////////////////////////////////////////
								//// CHANGE FILENAME in database (as we needed to add 'uid' to image name to avoid duplication)
								$query  = "UPDATE $db_clientTable_catalogue SET image_large='$UploadFileName' WHERE id='$uid'";//, image_small='$UploadFileName'
								$result = $db->mysql_query_log($query);
								
								
								// ORDER POSITIONS
								if (empty($my_id_xtra) && !$editid) {									
									$all_query = "SELECT * FROM $db_clientTable_catalogue ORDER by position";
									$all_result = mysql_query($all_query);					
									$all_num_rows = mysql_num_rows($all_result);												
									
									for($tmpcount = 0;$tmpcount < $all_num_rows;$tmpcount++) {									
										$all_array 	= mysql_fetch_row($all_result);
										//echo $tmpcount .' / ' . $all_num_rows .' , ';
										$position_query = "UPDATE $db_clientTable_catalogue SET position=$tmpcount WHERE id = '".$all_array[0]."'";
										$position_result = $db->mysql_query_log($position_query);	
									}
								}
								
								$message_largeimage = 'File successfully uploaded';
								$panel_largeimage = 'panel_good';		
							} else {  // (upload): else 3 ...move_uploaded_file()
								// remove record from database(not needed/excess if failed to upoad ACTUAL file			
								if(!$editid){
									$query = "DELETE FROM $db_clientTable_catalogue WHERE id = $uid";
									$result = $db->mysql_query_log($query);										
								}
								$message_largeimage = 'FILE NOT FOUND';
								$panel_largeimage = 'panel_error';
								$post_success = 0;
							} // (upload): end 3 ...move_uploaded_file()
						} // (upload): end 2 ...filesize
						
					}// (upload): end if 1 ...if blank / empty							
					/// (END OF) UPLOAD LARGE IMAGE
			
					//////
					//If name has changed and SEO filename needs regenerating...
					if($editid && $_POST['GenerateFileName'] && $NameChanged){
						$oldName = $siteroot.$gp_uploadPath['large']."_test6.jpg";
						$newName = $siteroot.$gp_uploadPath['large']."_test7.jpg";
						$attributes = array('oldName'=>$oldName,'newName'=>$newName);
						$CMSMakeImages->RenameFile($attributes);
						//$getAttributes['oldName'],$getAttributes['newName']
						/*
						$tmpName = $CMSMakeImages->GenerateFileName($tmpName);
						$filetype = $CMSShared->GetFileType($_FILES['upload_thumb']['name']);
						
						$UploadThumb = $_FILES['upload_thumb']['tmp_name'];
						$UploadFileName = "th_".$tmpName."_".$uid.".".$filetype;
						$my_CustomThumb_withpath	= $siteroot.$gp_uploadPath['thumbs'].$UploadFileName;
						move_uploaded_file($UploadThumb,$my_CustomThumb_withpath);
						
						if($CMSShared->FileExists($my_CustomThumb) && $my_CustomThumb != $my_CustomThumb_withpath) unlink($my_CustomThumb);
						
						////////////////////////////////////////////////////////////
						//// CHANGE FILENAME in database (as we needed to add 'uid' to image name to avoid duplication)
						$query  = "UPDATE $db_clientTable_catalogue SET image_small='$UploadFileName' WHERE id='$uid'";
						$result = $db->mysql_query_log($query);
						*/
					}
					
				} else { // if query did not run OK
					//echo '<br/>(FB):'.$siteroot;
					//echo '<br/>(FB):'.$query;
					$post_success = 0;
				}				
	
			}else{
				echo '<p class="error">Form was not processed... Please check for errors and try again.</p>';
			}// END IF POSTED DATA		

			
		} // (END) IF DATA HAS BEEN SUBMITTED
		
		/////////////////////////////////////
		/// IF POSTED DATA IS SUCCESSFUL....
		/// GO TO COMPLETE SCREEN
		if (isset($post_success)) {
			if ($post_success != 0) {
			
				mysql_close();
				$_FILES = array(); //Destroy variables
				
				if (empty($my_id_xtra) || $my_id_xtra == 0) {
					$ParentID = $uid;
				}else{
					$ParentID = $my_id_xtra;
				}
				//echo "UploadFileName = ".$UploadFileName;
				header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?success=true&editid=".$uid."&oldName=".$oldName);
			}else{
				/*
				if($CMSShared->FileExists($my_largeimage_withpath) && $my_filename != ''){
					if(!$editid){
					unlink($my_largeimage_withpath);
					if($CMSShared->FileExists($my_image_thumb)){unlink($my_image_thumb);}
					if($CMSShared->FileExists($my_image_primary)){unlink($my_image_primary);}
					$message_largeimage = 'Upload aborted due to form error.';
					$panel_largeimage = 'panel_error';
				}
				*/
				include("admin_catalogue_upload_form.php");
			}
			
		} else { /// ELSE IF POSTED DATA IS (NOT) SUCCESSFUL....
			include("admin_catalogue_upload_form.php");
		} /// END IF POSTED DATA IS SUCCESSFUL....
		
		
		////////////////////////////////////////////////
		// INNER NAVIGATION (WHEN EDITING AN ITEM ONLY)
		if($editid){
			$SubNavInner = '';
			$SubNavInner .= '<ul id="SubNavInner">';			
			if(!$suid) $SubNavInner .= '<li class="add"><a href="'.$_SERVER['PHP_SELF'].'?category='.$my_ParentCat.'&subcategory='.$my_ParentSubCat.'" title="add new '.$CommonCustomWords['item'].'">add new '.$CommonCustomWords['item'].'</a></li>';
			if(gp_enabled('social_media')){
				$checkQuery = "SELECT id,itemID FROM tbl_socialmedia WHERE itemID=$ParentID LIMIT 1";
				$checkResult = mysql_query($checkQuery);
				
				$SubNavInner .= '<li class="social_media"><a href="admin_genericUpdate.php?';
				if($checkResult && mysql_num_rows($checkResult)==1){
					$row = mysql_fetch_row($checkResult);
					$SubNavInner .= 'param[editid]='.$row[0];
				}else{
					$SubNavInner .= 'param[adding]=true';
				}
				$SubNavInner .= '&param[itemID]='.$ParentID.'&param[itemTitle]='.$name.'&param[curr_page]=catalogue&param[curr_page_sub]=items&param[prefArrayName]=gp_socialmediaTable&param[page_title]=Update Social Media details" title="Edit social media details">socialise</a></li>';
			}
			
			if(gp_enabled('preview_item')) $SubNavInner .= '<li class="preview"><a href="'.$moreinfopage.'?uid='.$ParentID.'" target="_blank" title="preview online">preview</a></li>';
			
			if(!$suid){
				if($gp_defVal_mixbag['attachments']>1){
					$attach_title = 'attach '.$CommonCustomWords['file'].'s to this '.$CommonCustomWords['item'];
					$attach_title_lite = 'attach '.$CommonCustomWords['file'].'s';
					if(gp_enabled("ThinUpload")){
						$SubNavInner .= '<li class="attach"><a href="file_selectfile.php?id_xtra='.$ParentID.'&TB_iframe=true&height=400&width=900" title="'.$attach_title.'" class="thickbox">'.$attach_title_lite.'</a></li>';
					}elseif(gp_enabled("BrowseUpload")){
						$SubNavInner .= '<li class="attach"><a href="admin_catalogue_upload.php?UploadFileSkipped=true&id_xtra='.$ParentID.'" title="'.$attach_title.'">'.$attach_title_lite.'</a></li>';
					}		
				}
			}
			
			if(!$suid && gp_enabled("related")) $SubNavInner .= '<li class="related"><a href="admin_catalogue_item_related.php?editid='.$ParentID.'" title="related information & documents">related</a></li>';
			//$SubNavInner .= '<li class="list"><a href="admin_catalogue_all.php?thisList=catalogue_cats&category='.$my_ParentCat.'" title="this category">this category</a></li>';
			if(!$suid) $SubNavInner .= '<li class="delete"><a href="admin_catalogue_item_delete.php?uid='.$editid.'&prevpage=item_edit" title="delete '.$CommonCustomWords['item'].'">delete</a></li>';		
			$SubNavInner .= '</ul>';
			echo $SubNavInner;
		}
		// (END) INNER NAVIGATION
	
	} // END IF (CATCHECK)
	
}

include("includes/admin_pagefooter.php");

?>