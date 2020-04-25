<?php

	Class CMSForms {
		
		function ValidEmail($email) {
		  // First, we check that there's one @ symbol, and that the lengths are right
		  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
		    // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
		    return false;
		  }
		  // Split it into sections to make life easier
		  $email_array = explode("@", $email);
		  $local_array = explode(".", $email_array[0]);
		  for ($i = 0; $i < sizeof($local_array); $i++) {
		     if (!ereg("^(([A-Za-z0-9!#$%&#038;'*+/=?^_`{|}~-][A-Za-z0-9!#$%&#038;'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
		      return false;
		    }
		  }  
		  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
		    $domain_array = explode(".", $email_array[1]);
		    if (sizeof($domain_array) < 2) {
		        return false; // Not enough parts to domain
		    }
		    for ($i = 0; $i < sizeof($domain_array); $i++) {
		      if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
		        return false;
		      }
		    }
		  }
		  return true;
		}
		
		function ValidLength($string,$length){
			if(strlen($string) < $length){
				return false;
			}else{
				return true;
			}
		}
		
		function createPassword($length) {
			$chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$i = 0;
			$password = "";
			while ($i <= $length) {
				$password .= $chars{mt_rand(0,strlen($chars))};
				$i++;
			}
			return $password;
		}

		function AddCalendarTags(){
			$CalendarTags = '<script language="JavaScript" src="includes/calendar/datetimepicker_css.js"></script>';			
			$CalendarTags .= '<link rel="stylesheet" href="includes/calendar/rfnet.css">';
			return $CalendarTags;
		}
		
	}
	$CMSForms = new CMSForms();

?>