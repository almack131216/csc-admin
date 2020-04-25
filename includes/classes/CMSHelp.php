<?php

	Class CMSHelp {

		function GetIconArray(){
			global $IconArray;
			
			$IconArray = array(	
								array('id'=>'item_index_lge','title'=>"Index",'copy'=>"Set as index page to category / sub-category."),
								array('id'=>'item_preview_lge','title'=>"Preview",'copy'=>"View selection as it appears on your website."),
								array('id'=>'item_edit_lge','title'=>"Edit",'copy'=>"Amend &amp; Update details where this icon appears."),
								array('id'=>'item_move_lge','title'=>"Move item",'copy'=>'Move selected item so that it appears in a different position / page.<br><strong><span class=&quot;mandatory&quot;>NOTE: This feature is only available if the catalogue is sorted by position.</span></strong>'),
								array('id'=>'item_delete_lge','title'=>"Delete",'copy'=>'This is a generic delete button.<br><strong><span class=&quot;mandatory&quot;>WARNING: Deleted features cannot be restored without manually re-adding.</span></strong>'),
								array('id'=>'warning','title'=>"Warning",'copy'=>'The warning icon appears when you are about to delete your selection.<br><strong><span class=&quot;mandatory&quot;>i.e. &quot;Once deleted, this operation cannot be undone(!)&quot;</span></strong>'),
								array('id'=>'add','title'=>"add",'copy'=>'Use this for navigating to screens where adding is required.'),
								array('id'=>'empty','title'=>"Prompt / Instructions",'copy'=>'This icon appears as an indication only and is often followed by an advisory suggestion...<br/>For example; Usually if a category/sub-category is empty and requires populating'),					
								array('id'=>'good','title'=>"Good / Complete",'copy'=>'Informs you the operation you have just performed was completed successfully.'),
								array('id'=>'error','title'=>"Error",'copy'=>'The error icon will appear with details of the error to alert you of a processing error... For example if you are trying to add a new category which already exists within your catalogue')
			);
			
			return $IconArray;
		}
		/// END ///	
		
		function PrintButtonPanel($get_curr_page){
			global $IconArray;
			
			switch($get_curr_page){
				case "catalogue_cats": $AppendUrl = '?thisList=catalogue_cats&selection=';break;
				default: $AppendUrl = '?selection=';
			}
			
			// Print icon and explanation
			$BuildIconsPanel = '';
			$BuildIconsPanel .= '<div class="panel" id="icons">';								

				$BuildIconsPanel .= '<div align="center">';
				for($i=0;$i<sizeof($IconArray);$i++ ) {			
					$BuildIconsPanel .= '<a href="#" onmouseover="javascript:SwapText(\'IconsExp\',\''.$IconArray[$i]['copy'].'\');" onmouseout="javascript:SwapText(\'IconsExp\',\'Icons Explained\');" title="'.$IconArray[$i]['title'].'"><img src="includes/icons/icon_'.$IconArray[$i]['id'].'.gif" border="0"></a>';
					$BuildIconsPanel .= '&nbsp;&nbsp;';
				}
				$BuildIconsPanel .= '<br/><p id="IconsExp">Icons Explained</p>';
				$BuildIconsPanel .= '</div>';		
			$BuildIconsPanel .= '</div>';
			return $BuildIconsPanel;
		}
			
	}
	$CMSHelp = new CMSHelp();

?>