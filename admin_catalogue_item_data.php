<?php

	if(!$FieldNames) $FieldNames = array(	'position_incat','position_initem','position',
							'name',
							'price','price_details','price2',
							'category','subcategory',
							'description',
							'keywords',
							'upload_date','spare_date',
							'status'
						);
						for($i=0;$i<count($CustomDetails);$i++){
							if($CustomDetails[$i]['inuse']==1) $FieldNames[] = 'detail_'.$CustomDetails[$i]['id'];
						}
						//$FieldNames[] = 'detail_1';
						//$FieldNames[] = 'detail_2';
	//echo '<p>(FB): FieldName: '.print_r($FieldNames).'</p>';					
	// VALUES ('$id_xtra', '0', '999', '0', '$name', '$detail_1', '$detail_2', '$detail_3', '$detail_4', '$detail_5', '$detail_6', '$detail_7', '$detail_8', '$detail_9', '$detail_10', '$price', '$price2', '$category', '$subcategory', '$description', '$keywords', '$UploadFileName', '$UploadFileName', '$upload_date', '$spare_date', '$status', '$price_details')

				
				
	$edit_query		= "SELECT * FROM $db_clientTable_catalogue WHERE id='$editid' LIMIT 1";
	$edit_result	= mysql_query($edit_query);
	if ($editid && $edit_result && mysql_num_rows($edit_result)) {
		$edit_arr			= mysql_fetch_array($edit_result);
		$my_id				= $edit_arr['id'];
		$my_id_xtra 		= $edit_arr['id_xtra'];
		$my_d				= $edit_arr['description'];
		if($CMSTextFormat->StringContains($my_d,"<w:WordDocument>")) $my_d = '<p><strong>ERROR: Text failed to save - please use the "Paste as Plain Text" button when pasting text</strong></p>';

		$my_keywords		= $edit_arr['keywords'];
		$my_name			= stripslashes($edit_arr['name']);
		$my_name_xtra		= $edit_arr['name'];
		
		for($i=1;$i<count($CustomDetails);$i++){
			if($CustomDetails[$i]['inuse']==1) ${"my_detail_".$CustomDetails[$i]['id']} = $edit_arr['detail_'.$CustomDetails[$i]['id']];
		}

		$my_cat				= $edit_arr['category'];
		$my_subcat			= $edit_arr['subcategory'];
		$my_status			= $edit_arr['status'];
		$my_price			= $edit_arr['price'];
		$my_price2			= $edit_arr['price2'];
		$my_price_details	= $edit_arr['price_details'];
		
		$my_date	= $edit_arr['upload_date'];
		$my_year	= substr($my_date, 0, 4);						
		$my_month	= substr($my_date, 5, 2);
		$my_day		= substr($my_date, 8, 10);
		
		$my_date_spare	= $edit_arr['spare_date'];
		$my_year_spare	= substr($my_date_spare, 0, 4);						
		$my_month_spare	= substr($my_date_spare, 5, 2);
		$my_day_spare	= substr($my_date_spare, 8, 10);
		
		if(!empty($edit_arr['image_small']) && $edit_arr['image_small']!=$edit_arr['image_large']){
			$HasCustomThumb = true;
		}else{
			$HasCustomThumb = false;
			$my_CustomThumb = '';
			$my_CustomThumb_withpath = '';
			$my_CustomThumb_filename = '';
		}
		
		$my_image_highres		= $siteroot.$gp_uploadPath['highres'].$edit_arr['image_large'];
		$my_image_large			= $siteroot.$gp_uploadPath['large'].$edit_arr['image_large'];
		$my_image_primary		= $siteroot.$gp_uploadPath['primary'].$edit_arr['image_large'];
		$my_image_thumb			= $siteroot.$gp_uploadPath['thumbs'].$edit_arr['image_large'];
		if($HasCustomThumb) $my_CustomThumb = $siteroot.$gp_uploadPath['thumbs'].$edit_arr['image_small'];
		
		$my_largeimage_withpath	= $my_image_large;
		if($HasCustomThumb) $my_CustomThumb_withpath = $my_CustomThumb;
		
		$my_filename			= $edit_arr['image_large'];
		if($HasCustomThumb) $my_CustomThumb_filename = $edit_arr['image_small'];
	}else{
		$my_id_xtra 			= 0;
		$my_d					= '';//$gp_defVal_item['description'];
		$my_keywords			= '';//$gp_defVal_item['keywords'];
		$my_name				= '';//$gp_defVal_item['name'];
		$my_name_xtra			= '';//$gp_defVal_item['name_xtra'];
		$my_detail_1			= $_REQUEST['detail_1'];//$gp_defVal_item['detail_1'];
		$my_detail_2			= $_REQUEST['detail_2'];//$gp_defVal_item['detail_2'];
		$my_detail_3			= $_REQUEST['detail_3'];//$gp_defVal_item['detail_3'];
		$my_detail_4			= $_REQUEST['detail_4'];//$gp_defVal_item['detail_4'];
		$my_detail_5			= $_REQUEST['detail_5'];//$gp_defVal_item['detail_5'];
		$my_detail_6			= $_REQUEST['detail_6'];
		$my_detail_7			= $_REQUEST['detail_7'];
		$my_detail_8			= $_REQUEST['detail_8'];
		$my_detail_9			= $_REQUEST['detail_9'];
		$my_detail_10			= $_REQUEST['detail_10'];
		$my_detail_11			= $_REQUEST['detail_11'];
		$my_detail_12			= $_REQUEST['detail_12'];
		$my_detail_13			= $_REQUEST['detail_13'];
		$my_detail_14			= $_REQUEST['detail_14'];
		$my_detail_15			= $_REQUEST['detail_15'];			
		if(!$my_cat)			$my_cat	= '';//$gp_defVal_item['category'];
		if(!$my_subcat)			$my_subcat	= '';//$gp_defVal_item['subcategory'];
		$my_status				= 1;
		$my_price				= '';//$gp_defVal_item['price'];
		$my_price2				= '';//$gp_defVal_item['price2'];
		$my_price_details 		= '';//$gp_defVal_item['price_details'];
		
		$my_day = date(d);
		$my_month = date(m);
		$my_year = date(Y);
		
		$my_day_spare = '00';
		$my_month_spare = '00';
		$my_year_spare = '0000';	
		
		$my_image_highres		= '';
		$my_image_large			= '';
		$my_image_primary		= '';
		$my_image_thumb			= '';
		$my_CustomThumb			= '';
		
		$my_largeimage_withpath	= '';
		$my_CustomThumb_withpath	= '';
		$my_filename			= '';
		$my_CustomThumb_filename = '';
	}
	
	if($my_cat) $cust_category = $my_cat;
	if($my_subcat) $cust_subcategory = $my_subcat;
		
?>