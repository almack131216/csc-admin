<?php

	Class CMSDelete {
		
		function DeleteItem($getAttributes){
			global $db_clientTable_catalogue,$CMSShared,$CMSDebug;
			global $db,$db_client,$siteroot,$gp_uploadPath;
			
			$getItemID = $getAttributes['itemID'];
			$getFileOnly = $getAttributes['fileOnly'];
				
			$more_query = "SELECT * FROM $db_clientTable_catalogue WHERE id=$getItemID";
			if(!$getFileOnly) $more_query .=" OR id_xtra=$getItemID";
			$more_result = mysql_query($more_query);			
			
			if($more_result && mysql_num_rows($more_result)>=1) { // if 1									
				for($tmp=0;$tmp<mysql_num_rows($more_result);$tmp++) { // FOR LOOP 1
					$more_array 	= mysql_fetch_array($more_result);
					$more_id		= $more_array['id'];
					$more_id_xtra	= $more_array['id_xtra'];
					$more_file		= $more_array['image_large'];
					$more_file_small	= $more_array['image_small'];							

					// DELETE ITEM FROM DATABASE
					if($getFileOnly){
						$more_delete_query = "UPDATE $db_clientTable_catalogue SET image_large='' WHERE id=$more_id LIMIT 1";//, image_small=''
					}else{
						$more_delete_query = "DELETE from $db_clientTable_catalogue WHERE id=$more_id LIMIT 1";
					}
					$more_delete_result = $db->mysql_query_log($more_delete_query);
					
					// DELETE FILES ATTACHED					
					$more_image_thumb 	= $siteroot.$gp_uploadPath['thumbs'].$more_file;
					$more_image 		= $siteroot.$gp_uploadPath['primary'].$more_file;
					$more_image_large 	= $siteroot.$gp_uploadPath['large'].$more_file;
					$more_image_highres = $siteroot.$gp_uploadPath['highres'].$more_file;
					$more_CustomThumb = $siteroot.$gp_uploadPath['thumbs'].$more_file_small;
					
					//echo 'MORE_ID: '.$getItemID;
					if($CMSShared->FileExists($more_image_highres))	$CMSDebug->FileUnlink($more_image_highres);				
					if($CMSShared->FileExists($more_image_large))	$CMSDebug->FileUnlink($more_image_large);
					if($CMSShared->FileExists($more_image_thumb))	$CMSDebug->FileUnlink($more_image_thumb);
					if($CMSShared->FileExists($more_image))			$CMSDebug->FileUnlink($more_image);
					if($CMSShared->FileExists($more_CustomThumb))	$CMSDebug->FileUnlink($more_CustomThumb);
					
				} // END FOR LOOP 1
			} // END if 1
			
			if(!$getFileOnly){
				$RelatedQuery = "DELETE FROM $db_client.tbl_related_subcats WHERE itemID=$getItemID OR itemID2=$getItemID";
				$RelatedResult = $db->mysql_query_log($RelatedQuery);
			}
			return true;
		}
		/// END ///
		
		////////////////////
		/// DELETE MEMBER // Also deletes associated membership access details
		function DeleteMember($getID){
			global $db_clientTable_members;
			global $db,$db_client;
			
			$getItemID = $getID;
			
			// DELETE ITEM FROM DATABASE
			$deleteQuery = "DELETE from $db_clientTable_members WHERE Id=$getItemID LIMIT 1";
			$deleteResult = $db->mysql_query_log($deleteQuery);
			
			$accessQuery = "DELETE FROM $db_client.tbl_members_access WHERE memberID=$getItemID";
			$accessResult = $db->mysql_query_log($accessQuery);
			
			return true;
		}
		/// END ///
		
		
		////////////////////
		/// DELETE MEMBER // Also deletes associated membership access details
		function GenericDelete($getID,$getTable,$getID_field){
			global $db;
			
			if(!$getID_field) $getID_field = "id";
			// DELETE ITEM FROM DATABASE
			$deleteQuery = "DELETE from $getTable WHERE $getID_field=$getID LIMIT 1";
			$deleteResult = $db->mysql_query_log($deleteQuery);
			
			return true;
		}
		/// END ///


	}
	$CMSDelete = new CMSDelete();

?>