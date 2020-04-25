<?php
	
	Class TinyMCE {		
		
		////////////////////////
		/// Start to build page
		function LoadSimple(){
			$TextArea = <<<EOD
			<script language="javascript" type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
			<script language="javascript" type="text/javascript">
				tinyMCE.init({
					mode : "textareas",
					plugins : "paste",
					theme : "advanced",
					content_css : "includes/css/tiny_mce_custom2.css",
					theme_advanced_buttons4 : "pastetext",
					document_base_url: "/",
					relative_urls : true,
				});					
			</script>
EOD;
			return $TextArea;
		}
		/// END ///
		
		////////////////////////
		/// Start to build page
		function LoadAdvanced(){
			$TextArea = <<<EOD
			<script language="javascript" type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
			<script language="javascript" type="text/javascript">
				tinyMCE.init({
					mode : "textareas",
					editor_selector : 'mceAdvanced',
					theme : "advanced",plugins : "paste",
					theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright,bullist,numlist,undo,redo,link,unlink,pastetext,pasteword",
					theme_advanced_buttons2 : "code",
					theme_advanced_buttons3 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : true,
					document_base_url: "/",
					relative_urls : true,
					extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]"
				});		
			</script>
EOD;
		return $TextArea;
			
		}
		/// END ///
		
		
	
	}
	$TinyMCE = new TinyMCE();

?>