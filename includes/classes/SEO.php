<?php
	
	Class SEO {		
		
		////////////////////////
		/// Start to build page
		function sanitize($getAttributes){

			$To = $getAttributes['to'];
			$From = $getAttributes['from'];
			$tmp = trim($getAttributes['string']);
		
			if($To){
				$tmp = str_replace("+","&#126;",$tmp);
				$tmp = str_replace(" ","+",$tmp);
			}elseif($From){
				//echo '<p>Step 1: '.$tmp.' (get RAW data from URL)</p>';	
				$tmp = str_replace(array("é","Ã©"),"e",$tmp);
				$tmp = str_replace(array("~","&#126;"),"+",$tmp);
				//echo '<p>Step 2: '.$tmp.' (replace dodgy characters?)';
			}else{
				$tmp = $getString;
			}
			return $tmp;
		}
		/// END ///
		
		
	
	}
	$SEO = new SEO();

?>