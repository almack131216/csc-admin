<?php

	Class CMSAddOns {
		
		//////////////////////////////////////////////////////////
		/// ThickBox
		function IsThickbox(){
			switch($_SERVER['PHP_SELF']){
				case "file_selectfile.php":return true;break;
			}	
		}

		function ThickBox($ret_element,$ret_href,$ret_string,$ret_imagePath){
			global $CommonCustomWords;
			
			switch($ret_element){
				case "item":			return '<a href="'.$ret_href.'" title="Preview item:&nbsp;'.$ret_string.'" class="Preview" onmouseover="showtrail(0,0,\''.$ret_imagePath.'\');" onmouseout="hidetrail();" target="_blank"><img src="includes/icons/icon_item_preview.gif" alt="Preview '.$CommonCustomWords['file'].'"></a>';break;
				//case "item":			return '<a href="'.$ret_href.'&TB_iframe=true&height=400&width=900" title="Preview '.$CommonCustomWords['item'].':&nbsp;'.$ret_string.'" class="thickbox Preview" onmouseover="showtrail(0,0,\''.$ret_imagePath.'\');" onmouseout="hidetrail();"><span>Preview '.$CommonCustomWords['item'].'</span></a>';break;
				
				//case "item":			return '<a href="'.$ret_href.'&TB_iframe=true&height=400&width=900" title="Preview '.$CommonCustomWords['item'].':&nbsp;'.$ret_string.'" class="thickbox" onmouseover="showtrail(0,0,\''.$ret_imagePath.'\');" onmouseout="hidetrail();"><img src="includes/icons/icon_item_preview.gif" alt="Preview '.$CommonCustomWords['item'].'"></a>';break;
				//case "item":			return '<a href="'.$ret_href.'&TB_iframe=true&height=400&width=900" title="Preview '.$CommonCustomWords['item'].':&nbsp;'.$ret_string.'" class="thickbox"><img src="includes/icons/icon_item_preview.gif" alt="Preview '.$CommonCustomWords['item'].'"></a>';break;
				case "link":			return '<a href="'.$ret_href.'" title="Preview '.$CommonCustomWords['item'].':&nbsp;'.$ret_string.'" target="_blank"><img src="includes/icons/icon_item_preview.gif" alt="Preview Link"></a>';break;
				case "fileSelect":		return '<a href="'.$ret_href.'&TB_iframe=true&height=400&width=950" title="'.$ret_string.'" class="thickbox" id="fileSelect">'.$ret_string.'</a>';break;
				case "fileReplace":		return '<a href="'.$ret_href.'&TB_iframe=true&height=400&width=950" title="'.$ret_string.'" class="thickbox" id="fileSelectReplace">'.$ret_string.'</a>';break;
				case "fileSelectXtra":	return '<p class="addFiles"><a href="'.$ret_href.'&TB_iframe=true&height=400&width=900" title="'.$ret_string.'" class="thickbox"><strong>Click here</strong></a>&nbsp;to '.$ret_string.'</p>';break;
				case "filePreview":		return '<a href="'.$ret_href.'?TB_iframe=true&height=400&width=950" title="Preview '.$CommonCustomWords['file'].':&nbsp;'.$ret_string.'" class="thickbox" rel="Slideshow"><img src="includes/icons/icon_item_preview.gif" alt="Preview '.$CommonCustomWords['file'].'"></a>';break;
				case "fileEmbed":		return '<a href="'.$ret_href.'&TB_iframe=true&height=400&width=950" title="Embed '.$CommonCustomWords['file'].':&nbsp;'.$ret_string.'" class="thickbox"><img src="includes/icons/icon_embed.gif" alt="Embed '.$CommonCustomWords['file'].'"></a>';break;													
				//case "fileUpload":	echo '<a href="'.$ret_href.'&TB_iframe=true&height=400&width=900" title="'.$ret_string.'" class="thickbox">'.$ret_string.'</a>';break;
			}
		}
		/// END ///	
			
	}
	$CMSAddOns = new CMSAddOns();

?>