<?php
	
	$dir = $siteroot."uploads/";
	//$dir_folder = array($dir
	//echo $dir;

	if($selectfile=="embed"){//(end) IF EMBED
		//if($CMSDebug->OnLocalhost()){
			$filesrc=$CMSDebug->debug_websiteroot($_SESSION['website'])."uploads/".$my_filename;
		//}else{
		//	$filesrc=$siteroot."uploads/".$my_filename;
		//}
		//echo '<br />(FB): '.$filesrc;
		echo '<div class="panel_oneline">';
			echo '<p><span class="steptitle">Embed '.$CommonCustomWords['file'].':</span> Select '.$CommonCustomWords['file'].' address below and <strong>Right-click+Copy</strong> or use keyboard shortcut <strong>(Ctrl+C)</strong> to copy</strong>.</p>';
			echo '<p><input type="text" name="src" id="embed_src" onClick="SelectAll(\'embed_src\');" value="'.$filesrc.'" style="float:none;width:600px;">';
			echo '<a href="'.$filesrc.'" title="Preview file:&nbsp;'.$my_filename.'" target="_blank"><img src="includes/icons/icon_item_preview.gif" alt="Preview file:&nbsp;'.$my_filename.'"></a></p>';
			echo '<p>If you are having problems embedding files, or have any questions, please contact support via the help pages.</p>';
		echo '</div>';
		
		echo '<div class="panel_oneline">';
			echo '<div class="inner_right">';
				echo '<input type="submit" id="continue" value="Continue" onclick="self.parent.tb_remove();" />';
				echo '<input type="submit" id="cancel" value="Cancel" onclick="self.parent.tb_remove();" />';
			echo '</div>';
		echo '</div>';
		
		
	}else{//(ELSE) IF EMBED - PRINT ALL IMAGES

		if($CMSDebug->WindowsServer()){//ServerSpecific
			$files = $CMSDebug->debug_scandir($dir);
		} else {
			$files = scandir($dir,0);//"0" lists in ascending order, "1" lists descending
		}		
		
		if($selectfile=="select"){
			echo '<div class="panel_Smallbox">';		
			echo '<p>NO FILE / SKIP</p>';
			echo '<input type="submit" id="fileSelectSelect" name="UploadFile" value="Skip" title="Select: Skip"/></p>';
			echo '</div>';
		}
		//print_r($files1);

		for($tmpcount=0;$tmpcount<count($files);$tmpcount++){
			$my_filename = $files[$tmpcount];
			if(!empty($my_filename) && $my_filename!="." && $my_filename!=".." && $my_filename!="Thumbs.db"){
				echo '<div class="panel_Smallbox">';
					$filesrc=$dir.$my_filename;
					
					if($CMSShared->IsImage($my_filename)){					
						//echo '<img src="thumb.php?file='.$filesrc.'&size=66&quality=60&nocache=0" alt="'.$my_filename.'" title="'.$my_filename.'">';
						echo '<img src="timthumb.php?src='.$filesrc.'&w=66&q=60" alt="'.$my_filename.'" title="'.$my_filename.'">';
					}else{
						$fileType = $CMSShared->GetFileType($my_filename);
						if (!$CMSShared->FileExists($filesrc)) {
							echo $CMSImages->GetFileIcon($fileType,false,$my_filename);
						}else{
							echo $CMSImages->GetFileIcon($fileType,true,$my_filename);
						}
					}
					echo '<p>'.$CMSTextFormat->ReduceString($my_filename,15).'<br />';
	
					if($selectfile=="select"){
						echo '<input type="submit" id="fileSelectSelect" name="UploadFile" value="'.$my_filename.'" title="Select: '.$my_filename.'"/></p>';
					}elseif($selectfile == "manage"){
						if($CMSShared->IsImage($my_filename)){
							echo $CMSAddOns->Thickbox("filePreview",$filesrc,$my_filename,"");
						}else{
							echo '<a href="'.$filesrc.'" title="Preview file:&nbsp;'.$my_filename.'" target="_blank"><img src="includes/icons/icon_item_preview.gif" alt="Preview file:&nbsp;'.$my_filename.'" ></a>';
						}
						echo '&nbsp;&nbsp;';
						echo $CMSAddOns->Thickbox("fileEmbed","file_embed.php?file=".$my_filename,$my_filename,"");
						//echo '&nbsp;<a href="file_embed.php?file='.$my_filename.'" title="Embed file:&nbsp;'.$my_filename.'"><img src="includes/icons/icon_embed.gif" alt="Embed file:&nbsp;'.$my_filename.'"></a>';
						echo '&nbsp;&nbsp;';
						echo '<a href="file_delete.php?file='.$my_filename.'" title="Delete file:&nbsp;'.$my_filename.'"><img src="includes/icons/icon_item_delete.gif" alt="Delete file:&nbsp;'.$my_filename.'"></a>';
						//echo '<br /><input type="submit" id="fileSelectDelete" name="DeleteFileWarning" value="'.$my_filename.'" title="Delete: '.$my_filename.'"/>';
						echo '</p>';
					}
					echo '</p>';
				echo '</div>';
			}	
		}
	}//(end) IF EMBED

?>