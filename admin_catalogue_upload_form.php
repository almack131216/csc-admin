<?php

	$Content = '';
	
	//////////////////////////////////
	///////////////////////////////////
	/////// REPLACE IMAGE?
	if($editid){
		require_once('includes/loadCategories.php');//drag-n-drop
		$fieldname = "position_initem";
		$tablename = $db_clientTable_catalogue;
		$sortableDetail = "position_initem";
		
		$attributes = array('tablename'=>$tablename,'fieldname'=>$fieldname);
		$Categories = getCategories($attributes);

		if($_GET['success']){
			echo '<p class="good">Catalogue successfully updated</p>';
		}
		// SHOW ADDITIONAL IMAGES	
		//$Content.= '<br>(FB) my_id:'.$my_id;
		//$Content.= '<br>(FB) my_id_xtra:'.$my_id_xtra;
		$more_query		= "SELECT * FROM $db_clientTable_catalogue";
		if ($my_id_xtra != 0) { //if we've selected one of the additional images to edit			
			$more_query	.= " WHERE id_xtra=$my_id_xtra OR id=$my_id_xtra"; // select ALL, including 1st image (one to whom the id_xtra is created from)
		} else {
			$more_query	.= " WHERE id_xtra=$my_id OR id=$my_id"; // select ALL
		}
		$more_query	.= " ORDER BY position_initem ASC, id asc";
		$more_result = mysql_query($more_query);
		//$Content.= '(FB):'.$more_query;
		
		if ($more_result){ // if there are any results...
			$more_count = mysql_num_rows($more_result); //get number of results
			
			if ($more_count > 1) { //if there are more than 1 then create a table				
				$FilePanel = '';
				$FilePanel .= '<div class="panel">';
				$FilePanel .= '<p><span class="steptitle">'.$CommonCustomWords['item'].' Attachments: </span>This '.$CommonCustomWords['item'].' has '.$more_count.' '.$CommonCustomWords['file'].'s attached. Please ensure you select the correct '.$CommonCustomWords['file'].' before editing.</p>';
				
				$OrderByPositionForced = true;
				$FilePanel .= '<script type="text/javascript" src="'.$adminroot.'includes/js/scriptaculous/lib/prototype.js"></script>'."\r\n";//drag-n-drop
				$FilePanel .= '<script type="text/javascript" src="'.$adminroot.'includes/js/scriptaculous/src/scriptaculous.js"></script>'."\r\n";//drag-n-drop
				$FilePanel .= '<ul id="SortableList" class="sortable-list">';//drag-n-drop
				
				for($tmp_more=1;$tmp_more<=$more_count;$tmp_more++) {											
					$SelectItem = "";
					$FileReplace = "";
					$more_array			= mysql_fetch_array($more_result);
					$more_my_id			= $more_array['id'];
					//$more_my_cat		= $more_array['category'];
					$more_my_name		= $more_array['name'];
					$my_image_large	= $siteroot.$gp_uploadPath['large'].$more_array['image_large'];
					$my_image_thumb	= $siteroot.$gp_uploadPath['thumbs'].$more_array['image_large'];
					$more_filename		= $more_array['image_large'];
					$TableID = $more_my_id;

					//$Content.= '<br>(FB): more_filename:'.$more_filename;
					//$Content.= '<br>(FB): my_image_thumb:'.$my_image_thumb;
					//$Content.= '<br>(FB): my_image_large:'.$my_image_large;

					$my_image_thumb	= $CMSImages->CheckImageExists("thumb",$my_image_large,$more_filename);
						
					$FilePanel .= '<li id="Category_'.$TableID.'"';
					
					if ($my_id != $more_my_id) { // check this aint a duplicate
						// NON-SELECTED THUMBS
						$tmpID = $more_my_id;
						$FilePanel .= '>';
						$SelectItem = '&nbsp;<a href="admin_catalogue_upload.php?editid='.$more_my_id.'" title="Edit '.$CommonCustomWords['item'].'"><img src="includes/icons/icon_item_edit.gif" border="0"></a>';
					}else{
						// SELECTED THUMB
						$tmpID = $my_id;
						if(empty($my_id_xtra)){
							$FilePanel .= ' class="primarythumb_selected">';
						}else{
							$FilePanel .= ' class="thumb_selected">';
						}
						if(!$suid && gp_enabled("ThinUpload")) $FileReplace = '<div align="center">'.$CMSAddOns->Thickbox("fileReplace","file_selectfile.php?editid=".$editid,"Replace file","").'</div>';
					}
					
					$FilePanel .= '<a href="admin_catalogue_upload.php?editid='.$more_my_id.'">';
					$FilePanel .= $CMSImages->GetThumb($my_image_large, $my_image_thumb, $more_filename, "false");
					$FilePanel .= '</a>';
					
					$FilePanel .= '<br/>';
					if($SelectItem) $FilePanel .= $SelectItem;
					if($FileReplace) $FilePanel .= $FileReplace;
					$FilePanel .= '<a href="#" title="Move '.$CommonCustomWords['item'].'" class="Move"><img src="includes/icons/icon_item_move.gif" border="0"></a>';											
					if($CMSShared->FileExists($my_image_large)) $FilePanel .= '&nbsp;<a href="admin_catalogue_item_delete.php?uid='.$tmpID.'&fileOnly=1&prevpage=item_edit" title="Delete File"><img src="includes/icons/icon_item_delete.gif" border="0"></a>';
					$FilePanel .= '</li>';		
				}
				$FilePanel .= '</ul>';
				$FilePanel .= '</div>';
				$Content.= $FilePanel;
				echo $Content;
				include("includes/sortable-list.php");//drag-n-drop

				if (!empty($my_id_xtra)) {					
					$getcat_query	= "SELECT * FROM $db_clientTable_catalogue WHERE id='$my_id_xtra' LIMIT 1";
					$getcat_result	= mysql_query($getcat_query);
					if ($getcat_result){				
						$getcat_array	= mysql_fetch_array($getcat_result);
						$my_category	= $getcat_array['category'];
						$my_status		= $getcat_array['status'];
						$my_status_old	= $my_status;
						$my_cat_old 	= $c; //this is needed otherwise it shows as title change
					}
				}
					
			} else {
				//echo '(FB): '.$my_image_large.' ('.$my_image_thumb.')';
				// IF JUST ONE FILE IS ATTACHED
				$FilePanel = '';
				$FilePanel .= '<div class="panel_oneline">';
					$FilePanel .= '<p><span class="steptitle">'.$CommonCustomWords['item'].' Attachment</span></p>';
					$FilePanel .= '<div class="inner_right">';
						$FilePanel .= $CMSImages->GetThumb($my_image_large, $my_image_thumb, $my_filename, "false");
						if($CMSShared->FileExists($my_image_large)){							
							$FilePanel .= '&nbsp;<a href="admin_catalogue_item_delete.php?uid='.$my_id.'&fileOnly=1&prevpage=item_edit" title="Remove this file from '.$CommonCustomWords['item'].'"><img src="includes/icons/icon_item_delete.gif" border="0"></a>';
							if(!$suid && gp_enabled("ThinUpload")) $FilePanel .= $CMSAddOns->Thickbox("fileReplace","file_selectfile.php?editid=".$editid,"Replace file","");
						}else{
							if(!$suid && gp_enabled("ThinUpload")) $FilePanel .= $CMSAddOns->Thickbox("fileSelect","file_selectfile.php?editid=".$editid,"Replace file","");
						}
					$FilePanel .= '</div>';
				$FilePanel .= '</div>';
				echo $FilePanel;
			}
	
		}
		
		
		if ( $my_id_xtra != 0 ) {
			$ParentID = $my_id_xtra;
		} else {
			$ParentID = $my_id;			
		}

	}
	
	
	$BuildForm.= '<form enctype="multipart/form-data" action="admin_catalogue_upload.php" method="POST" name="UploadForm">';
	
	//hidden fields
	if($my_id_xtra) $BuildForm.= '<input type="hidden" name="id_xtra" value='.$my_id_xtra.'>';
	if($editid) $BuildForm.= '<input type="hidden" name="editid" value="'.$editid.'">';
	$BuildForm.= '<input type="hidden" name="updater" value="'.$updated.'">';
		
	// stepnum needs to be a variable as some features will be hidden when uploading to existing item(additional images)
	$stepnum = 1;
	
	//////////////////////////////////
	//////////////////////////////////
	//STEP 1: UPLOAD FILE
	if(!empty($_POST['UploadFile']) || $editid || $UploadFileSkipped && ($cust_category && $cust_subcategory||$_REQUEST['id_xtra']) ){

		if($suid || gp_enabled("BrowseUpload")){
			$BuildForm.= '<div class="'.$panel_largeimage.'">';
			$BuildForm.= '<p><span class="steptitle">Step '.$stepnum.':</span> '.$message_largeimage.'</p>';
				$BuildForm.= '<div class="inner_right">';
				$BuildForm.= '<input type="file" name="upload_large" id="file_upload"/>';
				$BuildForm.= '<p><a href="file_url_capture.php?uid='.$ParentID.'&TB_iframe=true&height=420&width=900" title="Capture website as image" class="thickbox" id="capture">Capture Web URL</a></p>';
				$BuildForm.= '</div>';				
			$BuildForm.= '</div>';
			$stepnum++;
		}else{
		
			if(!$UploadFileSkipped && !empty($_POST['UploadFile']) && gp_enabled("ThinUpload")){
				$BuildForm.= '<div class="'.$panel_largeimage.'">';
				$BuildForm.= '<p><span class="steptitle">Step '.$stepnum.':</span> '.$message_largeimage.'</p>';
				$BuildForm.= '<div class="inner_right">';
					$BuildForm.= '<span class="body_good">Ready to Submit (file:</span> '.$_POST['UploadFile'].')<br />';			
					$BuildForm.= '<input type="hidden" name="UploadFile" value="'.$_POST['UploadFile'].'">';
					
					// IF ADDING NEW ITEM : SHOW REPLACE BUTTON
					if(!$editid && gp_enabled("ThinUpload")) $BuildForm.= $CMSAddOns->Thickbox("fileReplace","file_selectfile.php?id_xtra=".$my_id_xtra."&category=".$my_cat."&subcategory=".$my_subcat."&editid=".$editid,"[ Replace ".$CommonCustomWords['file']." ]","");
					
					$BuildForm.= '</div>';
				$BuildForm.= '</div>';
				$stepnum++;			
			}
			
		}
	
		
		//////////////////////////////////
		//////////////////////////////////
		//STEP 1.2: CUSTOM THUMBNAIL IMAGE		
		if(!$suid && gp_enabled("CustomThumb") && (empty($my_id_xtra) || $YouTube==true)){
			$BuildForm.= '<div class="'.$panel_CustomThumb.'">';
			$BuildForm.= '<p><span class="steptitle">Step '.$stepnum.':</span> '.$message_CustomThumb.'</p>';
				$BuildForm.= '<div class="inner_right">';
					$BuildForm.= '<input type="file" name="upload_thumb" id="thumb_upload" />';
					if($CMSShared->FileExists($my_CustomThumb)){
						$ThumbPath = $gp_uploadPath['thumbs'].$my_CustomThumb_filename;
						$BuildForm.= '<div id="CustomThumb">';
							$BuildForm.= '<img src="'.$my_CustomThumb.'">';
							$BuildForm.= '<a href="javascript:removeImage(\'CustomThumb\',\''.$my_id.'\',\''.$ThumbPath.'\')" title="Remove this Custom Thumbnail image?"><img src="includes/btns/generic_delete_sm.gif"></a>';
						$BuildForm.= '</div>';
					}
				$BuildForm.= '</div>';
			$BuildForm.= '</div>';
			$stepnum++;
		}
		// END CUSTOM THUMB
		
		//////////////////////////////////
		//////////////////////////////////
		//STEP 2: GIVE NAME
		//if ( empty($my_id_xtra) ) {
			if($editid && !empty($my_name)){
				$tmpTitle = 'Rename \'<strong>'.$CMSTextFormat->Abbreviate(array('string'=>$my_name,'trim_start'=>3,'trim_middle'=>"...",'trim_end'=>2)).'</strong>\'';
			}else{
				$tmpTitle = 'Name for this '.$CommonCustomWords['item'];
			}
			if($my_id_xtra) $tmpTitle = 'Alt text/title for this '.$CommonCustomWords['file'];
			
			$BuildForm.= '<div class="'.$panel_name.'">';
			$BuildForm.= '<p><label for="name">Step '.$stepnum.':</label> '.$tmpTitle.'</p>';		
				$BuildForm.= '<div class="inner_right">';
				if(empty($my_id_xtra)){
					$BuildForm.= '<input type="text" id="name" name="name" value="'.$my_name.'"/>';
				}else{
					$BuildForm.= '<input type="text" id="name" name="name" value="'.$my_name_xtra.'"/>';
				}
				$BuildForm.= '</div>';
				
				$BuildForm.= '<div class="inner_right">';
				$BuildForm.= '<input type="checkbox" name="GenerateFileName" value="1" checked="checked" class="checkbox"/>SEO: Generate file name from this name (above)?';
				$BuildForm.= '</div>';
			$BuildForm.= '</div>';
			$stepnum++;
		//}	
			
		
		//////////////////////////////////
		//////////////////////////////////
		//STEP 3: GIVE PRICE
		if ( gp_enabled("price") && empty($my_id_xtra) && !FieldDisabled(array('category'=>$my_cat,'fieldname'=>'price'))) {
			$BuildForm.= '<div class="'.$panel_price.'">';
			$BuildForm.= '<p><label for="price">Step '.$stepnum.':</label> Price. Use <strong>numbers only</strong>, no commas.</p>';
				$BuildForm.= '<div class="inner_right">';
				$BuildForm.= '&pound;&nbsp;<input type="text" id="price" name="price" value="'.$my_price.'" id="price"/>';
				$BuildForm.= '<label for="price_details">&nbsp;</label>';
				$BuildForm.= '&nbsp;Comments:&nbsp;<input type="text" id="price_details" name="price_details" value="'.$my_price_details.'"/>';
				$BuildForm.= '</div>';
			$BuildForm.= '</div>';
			$stepnum++;
			
			if(gp_enabled("price2")){
				$BuildForm.= '<div class="'.$panel_price2.'">';
				$BuildForm.= '<p><label for="price2">Step '.$stepnum.':</label> Price 2. Use <strong>numbers only</strong>, no commas.</p>';
					$BuildForm.= '<div class="inner_right">';
					$BuildForm.= '&pound;&nbsp;<input type="text" id="price" name="price2" value="'.$my_price2.'"/>';
					$BuildForm.= '</div>';
				$BuildForm.= '</div>';
				$stepnum++;
			}
		}			
		
		//////////////////////////////////
		//////////////////////////////////
		//STEP 4: GIVE CATEGORY
		// show category option (if adding NEW item) - don't need for additional uploads		
		if ( $editid && empty($my_id_xtra) ) {			
			$BuildForm.= '<div class="panel_oneline">';
			$BuildForm.= '<p class="middle"><span class="steptitle">Step '.$stepnum.':</span> Category</p>';
				$BuildForm.= '<div class="inner_right">';
				//list_categories("add",$my_cat);
				//$ListPropsArr = array('name'=>'category','query'=>"SELECT * FROM $db_clientTable_catalogue_cats ORDER BY category",'dbTable_field'=>'category','query_qty'=>"SELECT * FROM $db_clientTable_catalogue WHERE category=",'selected'=>$my_cat,'optionValue'=>'','adding'=>true);
				//$BuildForm.= $CMSSelectOptions->Build($ListPropsArr);
				$CatQuery = "SELECT * FROM $db_clientTable_catalogue_cats ORDER BY position ASC";
				$CatResult = mysql_query($CatQuery);		
				if($CatResult && mysql_num_rows($CatResult)>=1){					
					$CategorySelectNew = '<select class="medium" id="category" name="category" onchange="getSubCategoriesList(this,\'subcategory\',0);" onload="getSubCategoriesList(this,\'subcategory\',0);">';
					for($c=0;$c<mysql_num_rows($CatResult);$c++){
						$cRow = mysql_fetch_array($CatResult);
						$CategorySelectNew .= '<option value='.$cRow['id'].'';
						if($cRow['id']==$my_cat) $CategorySelectNew .= ' selected';
						$CategorySelectNew .= '>'.$cRow['category'].'</option>';
					}
					$CategorySelectNew .= '</select>';
					$BuildForm.= $CategorySelectNew;
					
				}
				$BuildForm.= '</div>';
			$BuildForm.= '</div>';	
			$stepnum++;
		}else{
			$BuildForm.= '<input type="hidden" name="category" value='.$my_cat.'>';
		}

		
		//////////////////////////////////
		//////////////////////////////////
		//STEP 4.2: GIVE SUBCATEGORY
		// show subcategory option (if adding NEW item) - don't need for additional uploads
		if ( empty($my_id_xtra) && gp_enabled("subcategory") && !FieldDisabled(array('category'=>$my_cat,'fieldname'=>'subcategory')) ) {
			$BuildForm.= '<div class="panel_oneline">';
			$BuildForm.= '<p class="middle"><span class="steptitle">Step '.$stepnum.':</span> Assign sub-category</p>';
				$BuildForm.= '<div class="inner_right">';			
					//$BuildForm.= '<select class="medium" id="subcategory" name="subcategory" style="display:none;">';
					//$BuildForm.= '</select>';
					$tmpCatQuery = "SELECT * FROM $db_clientTable_catalogue_subcats WHERE category=$my_cat OR category=1 ORDER BY position_incat asc";
					$tmpCatResult = mysql_query($tmpCatQuery);
					if($tmpCatResult && mysql_num_rows($tmpCatResult)==0) $tmpCatQuery = "SELECT * FROM $db_clientTable_catalogue_subcats ORDER BY subcategory ASC";
					$ListPropsArr = array('name'=>'subcategory','query'=>$tmpCatQuery,'dbTable_field'=>'subcategory','query_qty'=>"SELECT * FROM $db_clientTable_catalogue WHERE subcategory=",'selected'=>$my_subcat,'optionValue'=>'','adding'=>true);
					$BuildForm.= $CMSSelectOptions->Build($ListPropsArr);
				$BuildForm.= '</div>';
			$BuildForm.= '</div>';	
			$stepnum++;
		}else{
			$BuildForm.= '<input type="hidden" name="subcategory" value='.$my_subcat.'>';
		}
		//////////////////////////////////
		//////////////////////////////////
		//STEP 5: STATUS
		// show category option (if adding NEW item) - don't need for additional uploads
		if (gp_enabled("status") && empty($my_id_xtra) ) {
			$BuildForm.= '<div class="panel_oneline">';		
			$BuildForm.= '<p><span class="steptitle">Step '.$stepnum.':</span> Online status</p>';
				$BuildForm.= '<div class="inner_right">';			
					$ListPropsArr = array('name'=>'status','query'=>"SELECT * FROM $db_shared.catalogue_status ORDER BY id asc",'dbTable_field'=>'status','selected'=>$my_status,'optionValue'=>'','adding'=>true);
					$BuildForm.= $CMSSelectOptions->Build($ListPropsArr);
				$BuildForm.= '</div>';	
			$BuildForm.= '</div>';	
			$stepnum++;
		}
		
		//////////////////////////////////
		//////////////////////////////////
		//STEP 6: GIVE detail
		$BuildList = '';
		//182 is the newsreel so needs additional data
		if(!empty($CustomDetails)){ // && gp_enabled('details')	
			
			for($tmpcount=0;$tmpcount<count($CustomDetails);$tmpcount++){					
								
				
				if($CustomDetails[$tmpcount]['inuse'] != 0 && ($my_id_xtra==0 || ($my_id_xtra!=0 AND $CustomDetails[$tmpcount]['inuse_id_index']))){			
					$tmp_value = ${'my_detail_' . $CustomDetails[$tmpcount]['id']};
					$detail_id = $CustomDetails[$tmpcount]['id'];
					$BuildList .= '<div class="panel_oneline">';
					$BuildList .= '<p><label for="details">Step '.$stepnum.':</label> '.$CustomDetails[$tmpcount]['name'].'</p>';
					$BuildList .= '<div class="inner_right">';
					
					if($CustomDetails[$detail_id]['SelectFromDB']){							
						$SelectTableName	= $db_client.".".$CustomDetails[$detail_id]['SelectFromDB']['tablename'];
						$SelectFieldID		= $CustomDetails[$detail_id]['SelectFromDB']['field_id'];
						$SelectFieldName	= $CustomDetails[$detail_id]['SelectFromDB']['field_name'];
						$SelectOrderBy		= $CustomDetails[$detail_id]['SelectFromDB']['orderby'];
						$SelectQuery		= $CustomDetails[$detail_id]['SelectFromDB']['query'];
						
						//$BuildForm.= $SelectTableName;							
						
						$BuildList .= '<select id="detail_'.$detail_id.'" name="detail_'.$detail_id.'">';
						if($SelectQuery){
							$build_query = $SelectQuery;
						}else{
							$build_query = "SELECT DISTINCT $SelectFieldID,$SelectFieldName FROM $SelectTableName ORDER BY $SelectOrderBy";
						}
						
						$build_result = mysql_query($build_query);
						//$BuildForm.= $build_query;
						if($build_result && mysql_num_rows($build_result)>=1){
							$BuildList .= '<option value="0">'.$CustomDetails[$detail_id]['defVal'].'</option>';
							for($i=0;$i<mysql_num_rows($build_result);$i++){
								$row = mysql_fetch_row($build_result);
								$BuildList .= '<option value="'.$row[0].'"';
								if($tmp_value == $row[0]) $BuildList .= ' selected';
								$BuildList .= '>'.$row[1].'</option>';
							}
													
						}else{
							$BuildList .= '<option value="0">This list is empty</option>';
						}
						$BuildList .= '</select><br/>';
					}elseif($CustomDetails[$detail_id]['SelectFromArray']){
						
						$SelectName = $CustomDetails[$detail_id]['SelectFromArray']['tablename'];
						$SelectArray = $CustomDetails[$detail_id]['SelectFromArray']['array'];
						
						$BuildList .= '<select id="'.$SelectName.'" name="'.$SelectName.'">';
						$BuildList .= '<option value="">'.$CustomDetails[$detail_id]['defVal'].'</option>';
						for($i=0;$i<sizeof($SelectArray);$i++){
							if($SelectArray[$i]['field_id']){
								$BuildList .= '<option value="'.$SelectArray[$i]['field_id'].'"';
								if($tmp_value == $SelectArray[$i]['field_id']) $BuildList .= ' selected';
								$BuildList .= '>'.$SelectArray[$i]['field_value'].'</option>';
							}
						}
						$BuildList .= '</select><br/>';
						
					}elseif($CustomDetails[$detail_id]['boolean']){
						$BuildList .= '<input type="radio" name="detail_'.$detail_id.'" class="radio" value="1"';
						if($tmp_value>0) $BuildList .= ' checked';													
						$BuildList .= '>YES';
						$BuildList .= '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="detail_'.$detail_id.'" class="radio" value="0"';
						if($tmp_value==0) $BuildList .= ' checked';
						$BuildList .= '>NO';
					}else{
						if($CMSTextFormat->StringContains($CustomDetails[$detail_id]['name'],"brief description") || $CMSTextFormat->StringContains($CustomDetails[$detail_id]['name'],"keywords")){							
							$BuildList .= '<textarea id="detail_'.$detail_id.'" name="detail_'.$detail_id.'" cols="80" rows="7">'.$tmp_value.'</textarea>';
						}else{
							$BuildList .= '<input type="text" id="detail_'.$detail_id.'" name="detail_'.$detail_id.'" value="'.$tmp_value.'" class="details" title="'.$CustomDetails[$detail_id]['name'].'?">';
						}
					}
					
					$BuildList .= '</div>';
					$BuildList .= '</div>';
					$stepnum++;
				}	
				
			}			
		}
		if(!empty($BuildList)) $BuildForm.= $BuildList;
		
		
		//////////////////////////////////
		///////////////////////////////////
		//STEP 7: GIVE DESCRIPTION
		//if(gp_enabled("description") &&!FieldDisabled(array('category'=>$my_cat,'fieldname'=>'description'))){
		if (($my_id_xtra && gp_enabled("attachments_description")) || (empty($my_id_xtra) && gp_enabled("description") && !FieldDisabled(array('category'=>$my_cat,'fieldname'=>'description')))) {
			$BuildForm.= '<div class="panel_oneline">';
			$BuildForm.= '<p><label for="description">Step '.$stepnum.':</label> Enter description<br /><br /><strong>TIP:</strong> '.$gp_defVal_item['description'].'</p>';
			//$BuildForm.= $CMSAddOns->Thickbox("fileSelect","file_selectfile.php?editid=".$editid,"Select file to embed into description content","");
				//$BuildForm.= '<div class="inner_right">';
				$BuildForm.= '<div style="clear:both;float:left;width:700px;margin-top:20px;">';
				$BuildForm.= '<textarea id="description" name="description" class="mceAdvanced" cols="125" rows="25">'.$my_d.'</textarea>';
				$BuildForm.= '</div>';
				//$BuildForm.= '</div>';
			$BuildForm.= '</div>';
			$stepnum++;
		}
		
		//////////////////////////////////
		///////////////////////////////////
		//STEP 8: GIVE KEYWORDS
		if(empty($my_id_xtra) && gp_enabled('keywords') && !FieldDisabled(array('category'=>$my_cat,'fieldname'=>'keywords'))){
			$BuildForm.= '<div class="panel_oneline">';
			$BuildForm.= '<p><label for="keywords">Step '.$stepnum.':</label> Enter keywords (<a href="javascript:OpenClose(\'KeywordsField\');" title="add keywords to this item">Show Keywords Field</a>)<br /><br />';
				$BuildForm.= '<strong>TIP:</strong> '.$gp_defVal_item['keywords'].'</p>';
				$BuildForm.= '<div id="KeywordsField" style="display:none;clear:both;float:left;width:700px;margin-top:20px;">';
				$BuildForm.= '<textarea id="keywords" name="keywords" cols="125" rows="5">'.$my_keywords.'</textarea>';
				$BuildForm.= '</div>';
			$BuildForm.= '</div>';
			$stepnum++;
		}
		
		
		if(!$suid && empty($my_id_xtra)){
			//STEP ?: ENTER DATE CREATED / ADDED
			
			//////////////////////
			///  ITEM PUBLISH DATE								
			$BuildForm.= '<div class="panel_oneline">';
			$BuildForm.= '<p><span class="steptitle">Step '.$stepnum.':</span> Publish date';
			$BuildForm.= '<div class="inner_right">';
			
			///////////////////////
			/// PUT DAYS INTO ARRAY
			$firstday = 1;
			$lastday = 31;
			
			$days = array();
			for($tmpcount = $firstday;$tmpcount <= $lastday;$tmpcount++) {
				$days[] = array('value'=>$tmpcount,'title'=>$tmpcount);			
			}
			array_push($days,array('value'=>'00','title'=>'--'));
			
			/////////////////////////
			/// PUT MONTHS INTO ARRAY
			$curr_month = date(m);
			$months = array(	array('value'=>'01',	'title'=>'January'),
								array('value'=>'02',	'title'=>'February'),
								array('value'=>'03',	'title'=>'March'),
								array('value'=>'04',	'title'=>'April'),
								array('value'=>'05',	'title'=>'May'),
								array('value'=>'06',	'title'=>'June'),
								array('value'=>'07',	'title'=>'July'),
								array('value'=>'08',	'title'=>'August'),
								array('value'=>'09',	'title'=>'September'),
								array('value'=>'10',	'title'=>'October'),
								array('value'=>'11',	'title'=>'November'),
								array('value'=>'12',	'title'=>'December')
							);
			array_push($months,array('value'=>'00','title'=>'--'));//NEED THIS for spare dates
			
			////////////////////////
			/// PUT YEARS INTO ARRAY
			$oldest = 2000;
			$curr_year = date(Y)+2;			
			$years = array();
			for($tmpcount = $curr_year;$tmpcount >= $oldest;$tmpcount--) {
				$years[] = array('value'=>$tmpcount,'title'=>$tmpcount);			
			}
			array_push($years,array('value'=>'0000','title'=>'--'));
			
			$ListPropsArr = array('name'=>'upload_date_day','array'=>$days,'selected'=>$my_day,'optionValue'=>'');
			$BuildForm.= $CMSSelectOptions->Build($ListPropsArr);
			

			/////////////////////////////////////
			/// ENTRIES ADDED WITHIN WHICH MONTH?			
			$ListPropsArr = array('name'=>'upload_date_month','array'=>$months,'selected'=>$my_month,'optionValue'=>'');
			$BuildForm.= $CMSSelectOptions->Build($ListPropsArr);
		
			////////////////////////////////////
			/// ENTRIES ADDED WITHIN WHICH YEAR?			
			$ListPropsArr = array('name'=>'upload_date_year','array'=>$years,'selected'=>$my_year,'optionValue'=>'');
			$BuildForm.= $CMSSelectOptions->Build($ListPropsArr);
			
			$BuildForm.= '</div>';
			$BuildForm.= '</div>';						
			$stepnum++;
			
			if(gp_enabled("spare_date")){
				/////////////////////
				/////////////////////
				/////////////////////
				///  SPARE DATE FIELD								
				$BuildForm.= '<div class="panel_oneline">';
				$BuildForm.= '<p><span class="steptitle">Step '.$stepnum.':</span> Spare date';
				$BuildForm.= '<div class="inner_right">';
	
				///////////////
				/// SPARE DAY	
				$ListPropsArr = array('name'=>'spare_date_day','array'=>$days,'selected'=>$my_day_spare,'optionValue'=>'');
				$BuildForm.= $CMSSelectOptions->Build($ListPropsArr);			
	
				///////////////
				/// SPARE MONTH
				$ListPropsArr = array('name'=>'spare_date_month','array'=>$months,'selected'=>$my_month_spare,'optionValue'=>'');
				$BuildForm.= $CMSSelectOptions->Build($ListPropsArr);
			
				///////////////
				/// SPARE YEAR?			
				$ListPropsArr = array('name'=>'spare_date_year','array'=>$years,'selected'=>$my_year_spare,'optionValue'=>'');
				$BuildForm.= $CMSSelectOptions->Build($ListPropsArr);
				
				$BuildForm.= '</div>';
				$BuildForm.= '</div>';						
				$stepnum++;
			}
		}

	
		//FINAL STEP: SUBMIT
		$BuildForm.= '<div class="panel_oneline">';
			$BuildForm.= '<p><span class="steptitle">Step '.$stepnum.':</span> Process '.$CommonCustomWords['item'];
			$BuildForm.= '<div class="inner_right">';				
				$BuildForm.= '<input type="submit" id="submit" name="submit">';
				$BuildForm.= '<a href="javascript:history.go(-'.$updated.')" id="return"><span>&#60;&nbsp;Return</span></a>';				
				$BuildForm.= '</form>';
			$BuildForm.= '</div>';
		$BuildForm.= '</div>';
		
	}else{
		
		//////////////////////////////////
		//////////////////////////////////
		//STEP 1: GIVE CATEGORY
		// show category option (if adding NEW item) - don't need for additional uploads
		if ( !$editid && empty($my_id_xtra) ) {
			$BuildForm.= '<div class="panel_oneline">';
			$BuildForm.= '<p class="middle"><span class="steptitle">Step '.$stepnum.':</span> Assign category</p>';
				$BuildForm.= '<div class="inner_right">';
				$ListPropsArr = array('name'=>'category','query'=>"SELECT * FROM $db_clientTable_catalogue_cats ORDER BY category",'dbTable_field'=>'category','query_qty'=>"SELECT * FROM $db_clientTable_catalogue WHERE category=",'selected'=>$_REQUEST['category'],'pleaseselect'=>'','optionValue'=>$_SERVER['PHP_SELF'].'?category=','adding'=>true,'jump'=>true);
				if(empty($_REQUEST['category'])) $ListPropsArr['pleaseselect']='Please choose...';
				$BuildForm.= $CMSSelectOptions->Build($ListPropsArr);				
				$BuildForm.= '</div>';
			$BuildForm.= '</div>';	
			$stepnum++;
		}
		
		if(!empty($_REQUEST['category'])){
			
			if(empty($_REQUEST['subcategory'])){
				$BuildForm.= '<div class="panel_oneline">';
				$BuildForm.= '<p class="middle"><span class="steptitle">Step '.$stepnum.':</span> Assign sub-category</p>';
					$BuildForm.= '<div class="inner_right">';
					$ListPropsArr = array('name'=>'subcategory','query'=>"SELECT * FROM $db_clientTable_catalogue_subcats WHERE category={$cust_category} OR category=1 ORDER BY subcategory ASC",'dbTable_field'=>'subcategory','query_qty'=>"SELECT * FROM $db_clientTable_catalogue WHERE category={$cust_category}&subcategory=",'selected'=>$_REQUEST['subcategory'],'pleaseselect'=>'','optionValue'=>$_SERVER['PHP_SELF'].'?category='.$cust_category.'&subcategory=','adding'=>true,'jump'=>true);
					if(empty($_REQUEST['subcategory'])) $ListPropsArr['pleaseselect']='Please choose...';
					$BuildForm.= $CMSSelectOptions->Build($ListPropsArr);				
					$BuildForm.= '</div>';
				$BuildForm.= '</div>';
			}
			
			
			if($suid ||gp_enabled("BrowseUpload")){
				$JumpToPage = $_SERVER['PHP_SELF'].'?UploadFileSkipped=true&category='.$my_cat.'&subcategory='.$_GET['subcategory'];
				if(!empty($_REQUEST['subcategory'])) header ("Location: http://" . $_SERVER['HTTP_HOST'].$JumpToPage);
				/*$BuildForm.= '<div class="panel_oneline">';
					$BuildForm.= '<p><span class="steptitle">Step '.$stepnum.':</span> Continue...';
					$BuildForm.= '<div class="inner_right">';
						$BuildForm.= '<a href="'.$JumpToPage.'" title="Continue...">Continue</a>';
					$BuildForm.= '</div>';
				$BuildForm.= '</div>';
				*/
			}else{
				$stepnum++;
				$BuildForm.= '<div class="panel_oneline">';
					$BuildForm.= '<p><span class="steptitle">Step '.$stepnum.':</span> Attach '.$CommonCustomWords['file'].' to this '.$CommonCustomWords['item'].'?';
					$BuildForm.= '<div class="inner_right">';
						$BuildForm.= $CMSAddOns->Thickbox("fileSelect","file_selectfile.php?id_xtra=".$my_id_xtra."&category=".$my_cat."&subcategory=".$my_subcat,"Select file","");
						$BuildForm.= '<a href="'.$_SERVER['PHP_SELF'].'?UploadFileSkipped=true&amp;category='.$my_cat.'&amp;subcategory='.$my_subcat.'" title="No '.$CommonCustomWords['file'].' needed" id="fileSelectSkip">No '.$CommonCustomWords['file'].' needed</a>';
					$BuildForm.= '</div>';
				$BuildForm.= '</div>';				
			}
		}
		
	}

	echo $BuildForm;
?>