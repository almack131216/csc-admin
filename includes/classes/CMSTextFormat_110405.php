<?php

	Class CMSTextFormat {

		//////////////////////////////////
		/// ITEM // FUNCTION: Format Price
		function Price_StripDecimal($pricestring){
			$pricestring = number_format($pricestring,2);
			$pos = strpos($pricestring, "."); // retrieve position of dot by counting chars upto dot
			$len = strlen($pricestring);
			$pricestring_end = substr($pricestring, $pos, $len);
			
			//echo 'END='.$pricestring_end;
			if($pricestring_end == ".00"){
				$pricestring_stripped = substr($pricestring, 0, $pos);
				return '&pound;'.$pricestring_stripped;
			}else{
				if($pricestring<1){
					$pennies = str_split($pricestring,"2");
					return number_format($pennies[1],0).'p';
				}else{
					return '&pound;'.$pricestring;
				}		
			}
		}
		/// END ///
		
		// ADDED: February 07 (25/02/07)
		////////////////////////////////////////////////////////////
		////////////////	 REMOVE COMMAS FROM PRICES     /////////
		function Price_ForceNumeric($string) {
			$removethese = array(",","-","£","$");
				
			for($tmpcount=0;$tmpcount<count($removethese);$tmpcount++){
				$bad_char = trim($removethese[$tmpcount]);
				//echo( "\n'".$bad_char."'\n" );
				if (stristr(trim($string),$bad_char )) {
					$length = strlen($bad_char);
					for ($i = 1; $i <= $length; $i++) {
						$new_char.= "";
					}
					$string = str_replace($bad_char, "", trim($string));
					$new_char = "";
				}		
				
			}
			return $string;	
		}
		
		
		//////////////////////////////////
		/// ITEM // FUNCTION: Format Price
		/// StringContains("string","ing")
		function StringContains($str, $content, $ignorecase=true){
			$retval = false;
			
			if(is_array($content)){
				for($i=0;$i<sizeof($content);$i++){
					if ($ignorecase){
						$str = strtolower($str);
						$content = strtolower($content[$i]);
					}
					
					// php type system sucks so we may need a "special check"...
					$_strpos = strpos($str, $content[$i]);
					if ( $_strpos === 0 || $_strpos > 0 ) $retval = true;
				}
			}else{
				if ($ignorecase){
					$str = strtolower($str);
					$content = strtolower($content);
				}
				
				// php type system sucks so we may need a "special check"...
				$_strpos = strpos($str, $content);
				if ( $_strpos === 0 || $_strpos > 0 ) $retval = true;
			}
			
			
			return $retval;
		}
		/// END ///


		///////////////////////////////////////////
		/// CLIENT // FUNCTION: Get Contact Details
		function FormatDate($getDate,$format){
			//date_default_timezone_set('Europe/London');
			//$dateArray=explode('-',$my_date);
			// $dateArray[0]= 2007
			// $dateArray[1] = 02
			// $dateArray[2] = 05
			//	"M j, Y"	= Jan 03, 09
			//	"d-m-Y"		= 03-01-2009
			//
			
			if($format=="cms"){
				$format = "d/m/Y";
				$cms = true;
			}
			
			if(!empty($format)){
				$dateArray=trim($getDate);
				//$dateArray=explode(',',$getDate);
				$dateArray=explode('-',$getDate);
				//$dateArray=explode('/',$getDate);
				$y = $dateArray[0];
				$m = $dateArray[1];
				$d = $dateArray[2];
				
				if(strlen($d)>2){
					$timeArray = explode(':',substr($d,3,strlen($d)));//DATETIME
					$hrs = $timeArray[0];
					$mins = $timeArray[1];
					$secs = $timeArray[2];
				}else{
					$hrs = 0;
					$mins = 0;
					$secs = 0;
				}

				if($cms && ($y=="0000" || $m=="00" || $d=="00")){
					$DateFormatted = $d.'/'.$m.'/'.$y;
				}else{
					$DateFormatted = date($format,mktime($hrs, $mins, $secs, $m, $d, $y));
				}
				
			}else{
				// DEFAULT FORMAT ()
				$dateArray=explode('-',$getDate);
				$DateFormatted = date('j-M-y', mktime(0, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]));
			}
			if($DateFormatted=="00-00-0000" || $getDate=="0000-00-00") $DateFormatted="--";
			return $DateFormatted;
		}
		/// END ///
		
		function SpareDateEmpty($getSpareDate){
			if($getSpareDate=="0000-00-00" || $getSpareDate=="") return true;
			return false;
		}
		
		/// SHOW | reduced string
		function ReduceString($ret_string, $ret_number) {
			if ( strlen($ret_string) > $ret_number ) {
				$ret_string_shortened = substr($ret_string, 0, $ret_number)."..." ;
				return 	$ret_string_shortened;		
			} else {
				return $ret_string;
			}
		}
		/// END ///
		


		///////////////////
		/// LANGUAGE FILTER
		function LanguageFilter($string) {
			//$obscenities = array("curse", "word");	
			$obscenities = @file("includes/obscenities.txt");
			//print "LF: $obscenities )) ";
			foreach($obscenities as $curse_word) {
				
				$curse_word = trim($curse_word);
				//echo( "\n'".$curse_word."'\n" );
				if (stristr(trim($string),$curse_word )) {
					$length = strlen($curse_word);
					for ($i = 1; $i <= $length; $i++) {
						$stars .= "*";
					}
					$string = eregi_replace($curse_word, $stars, trim($string));
					$stars = "";
				}
			}
			return $string;	
		}
		/// END ///
		
		///////////////////////////////
		/// RETURN STRING: Strip 'Crap'
		//////////////////////
		//////// RPG -> AM
		function stripCrap( $txt ) {
			global $CMSTextFormat;
		    $op = $txt;		    
		    $op = strip_tags( $op );// remove html		    
		    $op = $CMSTextFormat->rep_tagchars( $op );// Set html char codes		    
		    $op = nl2br( $op );// new lines to '<br />'
		    //$op = mysql_escape_string( $op );// Make MySQL safe
		    return $op;
		}
		
		function stripCrap2_in( $txt ) {
			global $CMSTextFormat;
		    $op = $txt;		    
		    $op = $CMSTextFormat->rep_tagchars( $op );//$op = strip_tags( $op );
		    //$op = sort_chars($op);
		    //$op = nl2br( $op );
		    return $op;
		}
		
		function stripCrap2_in_body( $txt ) {
		    $op = $txt;
		
		    $pattern = array (	"|[\']|",
		                        "|[\`]|" );
		    
			$replace = array (  "&#39;",
		                        "&lsquo;" );
		                    
			return preg_replace( $pattern, $replace, $op );
		    return $op;
		}
		
		function stripCrap2_out( $txt ){
			global $CMSTextFormat;
		    $op = $txt;
		    $op = strip_tags( $op );
		    //$op = rep_tagchars( $op );
		    //$op = sort_chars($op);
		    $op = $CMSTextFormat->br2nl_html($op);
		    $op = $CMSTextFormat->br2nl($op);
		    $op = nl2br( $op );
		    return $op;
		}
		
		function stripCrap2_out_body( $txt ){
			global $CMSTextFormat;
		    $op = $txt;
		    //$op = rep_tagchars( $op );
		    //$op = sort_chars($op);
		    $op = $CMSTextFormat->br2nl_html($op);
		    $op = $CMSTextFormat->br2nl($op);
		    $op = nl2br( $op );
		    return $op;
		}		
		
		function br2nl_html( $txt ) {
		    return eregi_replace( "&lt;br /&gt;", "", $txt );
		}
		
		function br2nl( $txt ) {
		    return eregi_replace( "<br />", "\r\n", $txt );
		}
		
		function sort_chars( $str ) {
			//$str2 = addslashes( $str );
			$pattern = array(	"|[\^]|",
								"|[$]|",
								"|[\(]|",
								"|[\)]|",
								"|[\@]|",
								"|[\[]|",
								"|[\{]|",
								"|[\}]|",
								"|[\]]|",
								"|[\?]|",
								"|[\.]|",
								"|[\+]|", 
								"|[\*]|" );
								
			$replace = array(	"\\^",
								"\\\\\$",
								"\\(",
								"\\)",
								"\\@",
								"\\[",
								"\\{",
								"\\}",
								"\\]",
								"\\?",
								"\\.",
								"\\+", 
								"\\*" );
			
			return preg_replace( $pattern, $replace, $str2 );
			
			//return 0;
		}
		
		function rep_tagchars( $text ) {
			// DESCRIPTION: remove < and >
			$pattern = array (	"|[&]|",
			       				"|[>]|",
								"|[<]|",
		                        "|[\']|",
		                        "|[\`]|" );
		    
			$replace = array (	"&amp;", 
								"&gt;",
								"&lt;",
		                        "&#39;",
		                        "&lsquo;" );
		                    
			return preg_replace( $pattern, $replace, $text );
		}
		
		///////////////
		/// Escape Data
		/// Function for escaping and trimming form data
		function escape_data($data) {			
			/*
			$patterns = array("/(&)/", "/</", "/>/", "/'/" );
			$replaces = array("+", "&lt;", "&gt;", "&#39;" );
			*/
			$patterns = array("/(&)/", "/</", "/>/", "/'/", "/£/" );
			$replaces = array("&amp;", "<", ">", "&#39;", "&pound;" );
			$data2 = preg_replace( $patterns, $replaces, $data );
			$data2 = nl2br($data2);
			
			
			if (ini_get('magic_quotes_gpc')) {		
				$data2 = stripslashes($data2);
			}
			
			//return mysql_real_escape_string (trim ($data2), $dbc);
			return trim($data2);
		} // end of escape_data() function
		
		
		//Added: 17/02/10
		//to cut down big names / title
		//For example: "the quick brown fox jumped over the lazy dog"
		//would read: "the quick brown... lazy dog"
		//$attributes = array('string'=>$string,'trim_start'=>3,'trim_middle'=>"...",'trim_end'=>2);
		function Abbreviate($getAttributes){
			
			$getString = $getAttributes['string'];
			$getStart = $getAttributes['trim_start'];
			$getMiddle = $getAttributes['trim_middle'];
			$getEnd = $getAttributes['trim_end'];
			
			$words = explode(" ",$getString);
			$wordCount = sizeof($words);
			
			$startstring = '';
			$endstring = '';
			for($i=0;$i<$wordCount;$i++){
				if($i<0+$getStart){
					$startstring.=$words[$i];
					if($i+1<$getStart) $startstring.=" ";
				}elseif(($i>=$wordCount-$getEnd) && ($wordCount>$getStart + $getEnd)){
					$endstring.=" ".$words[$i];
				}
			}
			
			$JoinString = $startstring;
			if($getStart<$wordCount) $JoinString.=$getMiddle;
			if(($getStart + $getEnd)<$wordCount) $JoinString.=$endstring;
			
			return $JoinString;
			
		}
		/// END ///
		
		/**
		 * Simple function gets first paragraph of text, supports HTML or plain text.
		 *
		 * @author Kamran Ayub
		 * @param {String} $data The string to summarize
		 * @param {Boolean} $isHTML Whether or not the string contains HTML
		 */
		function GetFirstParagraph($data, $isHTML = true) {    
		    
		    $result = $data;
		    
		    if($isHTML) {
		        
		        // convert line breaks/paragraphs
		        $result = str_replace("\n", "", $result); // remove extra
		        $result = str_replace("<br>", "\n", $result);
		        $result = str_replace("<br/>", "\n", $result);
		        $result = str_replace("<br />", "\n", $result);
		        $result = str_replace("</p>", "\n\n", $result);
		    
		        // strip all remaining tags
		        $result = strip_tags($result);
		    }
		    
		    // try and return the first paragraph, if I can't, return all of it
		    $paragraphs = explode("\n\n", trim($result));
		    
		    if(count($paragraphs) > 1) {
		        return nl2br(trim($paragraphs[0]));
		    } else {
		        return $data;
		    }
		}
		// END //

		//Often a client will send data in an excel file
		//the descriptions will be plain text so need paragraph tags adding
		function MakeParagraph($getString){
			if(strpos($getString,"<p")){
				return $getString;
			}else{
				return '<p>'.$getString.'</p>';
			}
		}
		// END //
		
		/*** example usage ***/
		//$string = 'This text will highlight PHP and SQL and sql but not PHPRO or MySQL or sqlite';
		/*** an array of words to highlight ***/
		//$words = array('php','sql');
		/*** highlight the words ***/
		//$string =  highlightWords($string, $words);
		/*** highlightWords ***/
		function highlightWords($string, $words){
			if(is_array($words)){
				foreach ( $words as $word ){
					$string = str_ireplace($word, '<span class="highlightWords">'.$word.'</span>', $string);
				}
			}else{
				$string = str_ireplace($words, '<span class="highlightWords">'.$words.'</span>', $string);
			}
			
			/*** return the highlighted string ***/
			return $string;
		}
		// END //
		
		//////////////
		// Swap Breaks (avoids compliance warnings)
		function SwapBreak($getString){
			return str_replace("<br />","<br>",$getString);
		}
		// END //

	}
	$CMSTextFormat = new CMSTextFormat();

?>