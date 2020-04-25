<?php

	Class CMSCommon {

		//////////////////////////////////
		/// ITEM // FUNCTION: CLICK TO ADD
		function panel_Add($ret_link, $ret_string){
			echo '<p class="add"><a href="'.$ret_link.'" title="Add"><strong>Click here</strong></a>&nbsp;'.$ret_string.'</p>';
		}
		/// END ///
		
		//////////////////////////////////
		/// ITEM // FUNCTION: CLICK TO ADD
		function panel_Save($getURL,$getURL_append,$ret_string){
			global $db_clientTable_members;

			switch($getURL){
				case "SaveToExcel.php":
					$class="Excel";
					break;
				default:$class="Excel";break;
			}
			
			$Save = '';
			if(!empty($getURL_append)){
				$Save .= '<p class="'.$class.'"><a href="'.$getURL.'&customList=true';
				foreach($getURL_append AS $key => $value){
					$Save .= '&'.$key.'='.$value;
				}
				$Save .= '" title="Save list to file..."><strong>Click here</strong></a>&nbsp;'.$ret_string.'. If you wish to save <strong>THE WHOLE CATALOGUE</strong> then <a href="'.$getURL.'" title="Save full catalogue"><strong>Click here</strong></a>.</p>';
			}else{
				$Save .= '<p class="'.$class.'"><a href="'.$getURL.'" title="Save list to file..."><strong>Click here</strong></a>&nbsp;'.$ret_string.'.</p>';
			}
			
			return $Save;
			
		}
		/// END ///

	}
	$CMSCommon = new CMSCommon();

?>