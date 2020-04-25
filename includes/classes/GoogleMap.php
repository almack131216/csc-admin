<?php

	Class GMap {
		
		function FieldInit(){
			global $gp_branchTable;
			global $tablename,$field_id,$field_status,$field_name,$field_town,$field_address,$field_address2,$field_address3;
			global $field_postcode,$field_county,$field_contact,$field_telephone,$field_fax,$field_email,$field_openingtimes;
			global $field_map_lat,$field_map_lng,$field_positionset,$field_date;
			
			$tablename			= $gp_branchTable['tablename'];
			$field_id			= $gp_branchTable['id']['field'];
			$field_status		= $gp_branchTable['status']['field'];
			$field_name			= $gp_branchTable['name']['field'];
			$field_town			= $gp_branchTable['town']['field'];
			$field_address		= $gp_branchTable['address']['field'];
			$field_address2		= $gp_branchTable['address2']['field'];
			$field_address3		= $gp_branchTable['address3']['field'];
			$field_postcode		= $gp_branchTable['postcode']['field'];
			$field_county		= $gp_branchTable['county']['field'];
			$field_contact		= $gp_branchTable['contact']['field'];
			$field_telephone	= $gp_branchTable['telephone']['field'];
			$field_fax			= $gp_branchTable['fax']['field'];
			$field_email		= $gp_branchTable['email']['field'];
			$field_map_lat		= $gp_branchTable['map_lat']['field'];
			$field_map_lng		= $gp_branchTable['map_lng']['field'];
			$field_positionset	= $gp_branchTable['positionset']['field'];
			$field_date			= $gp_branchTable['date']['field'];
		}
		
		
		function GetGoogleKey(){
			global $CMSDebug,$GoogleMap;
			if($CMSDebug->OnLocalhost()){//ServerSpecific
				$gKey = $GoogleMap['key'];
			}else{
				$gKey = $GoogleMap['key'];
			}
			return $gKey;
		}

		
		////////////////////////////////////////////
		/// Format Address, ready for sending to map
		function FormatPostcode($getPostcode){
			if(!empty($getPostcode))	$Formatted = '"'.str_replace('  ',' ',$getPostcode).'"';		
			if($Formatted) return strtolower($Formatted);
		}
		
		
		/////////////////////////////////////////////////
		/// Format Address, ready for map pop-up infoText
		function FormatAddressInfoBox($getAddressArray){
			global $PopUpStyle, $CMSTextFormat, $GMap, $field_address, $field_address2, $field_address3, $field_id, $field_name, $field_town, $field_county, $field_postcode, $field_telephone, $field_fax, $field_date, $HeadOfficeID;
			
			$Formatted = '';
			$Formatted .= '<p>';
			//if(!empty($getAddressArray[$field_town]))	$Formatted .= $getAddressArray[$field_town]."<br/>";
			//if(!empty($getAddressArray[$field_name]))		$Formatted .= "<a href=\"../workshopDetails.php?uid=".$getAddressArray[$field_id]."\" title=\"More information on this workshop\">".$getAddressArray[$field_name]."</a><br/>";
			if(!empty($getAddressArray[$field_name]))		$Formatted .= "<a href=\"index.php?id=".$getAddressArray[$field_id]."\" title=\"More information on this workshop\" target=\"_self\">".$getAddressArray[$field_name]."</a><br/>";
			if(!empty($getAddressArray[$field_address]))	$Formatted .= addslashes($getAddressArray[$field_address])."<br/>";
			if(!empty($getAddressArray[$field_address2]))	$Formatted .= addslashes($getAddressArray[$field_address2])."<br/>";
			if(!empty($getAddressArray[$field_address3]))	$Formatted .= addslashes($getAddressArray[$field_address3])."<br/>";
			if(!empty($getAddressArray[$field_town]) && $getAddressArray[$field_town]!=$getAddressArray[$field_name])		$Formatted .= $getAddressArray[$field_town]."<br/>";
			if(!empty($getAddressArray[$field_county]))		$Formatted .= $getAddressArray[$field_county]."<br/>";
			if(!empty($getAddressArray[$field_postcode]))	$Formatted .= $getAddressArray[$field_postcode]."<br/>";
			if(!empty($getAddressArray[$field_date]))	$Formatted .= "Date: ".$CMSTextFormat->FormatDate($getAddressArray[$field_date],'d/m/y, g:ia')."<br/>";
			if(!empty($getAddressArray[$field_telephone]) && strtolower($getAddressArray[$field_telephone])!="no")	$Formatted .= 'Tel: '.$getAddressArray[$field_telephone]."<br/>";
			//if(!empty($getAddressArray[$field_fax]) && strtolower($getAddressArray[$field_fax])!="no")		$Formatted .= 'Fax: '.$getAddressArray[$field_fax]."<br/>";
			$Formatted .= '</p>';
			
			if($Formatted) return str_replace("'","",$Formatted);
		}
		
		
		function PrintStoreDetails($getStoreID){
			global $CMSTextFormat,$GMap,$gp_branchTable;
			
			$query = "SELECT * FROM workshops WHERE id=$getStoreID LIMIT 1";
			$result = mysql_query($query);
			$BuildTable = '';
			
			if($result && mysql_num_rows($result)==1){
				$row = mysql_fetch_array($result);					
								
				$BuildTable .= '<div class="ResultsTable">';
					
					$BuildTable .= '<div class="TitleRow">';
						$BuildTable .= '<div class="RowLeft"><h2>Workshop Details</h2></div>';
						$BuildTable .= '<div class="RowRight"><h2>&nbsp;</h2></div>';
					$BuildTable .= '</div>';
					$BuildTable .= '<div class="GenericRow">';					
						$BuildTable .= '<div class="RowLeft">Workshop Title</div>';
						$BuildTable .= '<div class="RowRight">'.$row[$gp_branchTable['name']['field']].'</div>';
					$BuildTable .= '</div>';
					$BuildTable .= '<div class="ShadeRow">';					
						$BuildTable .= '<div class="RowLeft">Workshop Code</div>';
						$BuildTable .= '<div class="RowRight">'.$row[$gp_branchTable['code']['field']];
						$BuildTable .= '</div>';
					$BuildTable .= '</div>';
					$BuildTable .= '<div class="GenericRow">';					
						$BuildTable .= '<div class="RowLeft">Date</div>';
						$BuildTable .= '<div class="RowRight">'.$CMSTextFormat->FormatDate($row[$gp_branchTable['date']['field']],'d/m/y, g:ia').'</div>';
					$BuildTable .= '</div>';
					$BuildTable .= '<div class="ShadeRow">';					
						$BuildTable .= '<div class="RowLeft">Organiser</div>';
						$BuildTable .= '<div class="RowRight">'.$row[$gp_branchTable['organiser']['field']];
						//if(!empty($row['organisernumber'])) $BuildTable .= ', '.$row['organisernumber'];
						$BuildTable .= '</div>';
					$BuildTable .= '</div>';
					$BuildTable .= '<div class="GenericRow">';
						$BuildTable .= '<div class="RowLeft">Contact for Bookings</div>';
						$BuildTable .= '<div class="RowRight">'.$GMap->GetContactDetails($row[$gp_branchTable['id']['field']]).'</div>';
					$BuildTable .= '</div>';
					$BuildTable .= '<div class="ShadeRow">';					
						$BuildTable .= '<div class="RowLeft">Workshop Location</div>';
						$BuildTable .= '<div class="RowRight">'.$GMap->GetStoreAddress($row[$gp_branchTable['id']['field']]).'</div>';
					$BuildTable .= '</div>';
					$BuildTable .= '<div class="GenericRow">';					
						$BuildTable .= '<div class="RowRight"><p><a href="javascript:history.go(-1)" title="Return to previous page" class="BackBut">Back to workshop list</a></p></div>';
					$BuildTable .= '</div>';
				$BuildTable .= '</div>';
			}
					
			return $BuildTable;		
		}
		
		///////////////////////////////////////////
		//Default labels for input / select options
		function GetLabels(){
			$labels = array(	'addressInput'=>'Your Town:',
								'title'=>'Workshops:',
								'order'=>'Order By:',
								'date'=>'Dates:',
								'radius'=>'Distance:',
								'submit'=>'Update Map');
			return $labels;
		}
		
		//////////////////
		// Workshop Titles
		function GetTitles(){
			$Titles = array(
						array('title'=>"Safeguarding and Protecting Children",				'icon'=>"iconDarkRed",		'color'=>'#FF352B', 'page'=>'SafeguardingAndProtectingChildren.htm'),
						array('title'=>"A Guide To Mentoring Sports Coaches",				'icon'=>"iconPaleGrey",		'color'=>'#C7C7C7', 'page'=>'AGuideToMentoringSportsCoaches.htm'),
						array('title'=>"An Introduction To Long Term Athlete Development",	'icon'=>"iconDarkBrown",	'color'=>'#A2603C', 'page'=>'AnIntroductionToLongTermAthleteDevelopment.htm'),
						array('title'=>"An Introduction To FUNdamentals Of Movement",		'icon'=>"iconPaleBlue",		'color'=>'#BBE3FF', 'page'=>'AnIntroductionToFUNdamentalsOfMovement.htm'),
						array('title'=>"An Introductory To Core Stability",					'icon'=>"iconBrown",		'color'=>'#CB9C7B', 'page'=>''),
						array('title'=>"Analysing Your Coaching",							'icon'=>"iconGreen",		'color'=>'#96EC7C', 'page'=>'AnalyseYourCoaching.htm'),
						array('title'=>"Coaching Children And Young People",				'icon'=>"iconDarkGreen",	'color'=>'#01BE00', 'page'=>'CoachingChildrenAndYoungPeople.htm'),
						array('title'=>"Coaching Disabled Performers",						'icon'=>"iconOrange",		'color'=>'#FD8C08', 'page'=>''),
						array('title'=>"Equity In Your Coaching",							'icon'=>"iconPurple",		'color'=>'#C89AFF', 'page'=>'EquityInYourCoaching.htm'),
						array('title'=>"Fuelling Performers",								'icon'=>"iconBlack",		'color'=>'#ffffff', 'page'=>''),
						array('title'=>"How To Coach Disabled People In Sport",				'icon'=>"iconYellow",		'color'=>'#FFED5B', 'page'=>'HowToCoachDisabledPeopleInSport.htm'),
						array('title'=>"Multi Skill Clubs In Practice",						'icon'=>"iconDarkBlue",		'color'=>'#315FFF', 'page'=>'MultiSkillClubsInPractice.htm'),
						array('title'=>"Multi Skills Inclusion 3 Hours",					'icon'=>"iconBlue",			'color'=>'#6A97FF', 'page'=>'MultiSkillInclusion.htm'),
						array('title'=>"Multi Skill Induction",								'icon'=>"iconRed",			'color'=>'#FF766A', 'page'=>'MultiSkillClubInduction.htm'),
						array('title'=>"Planning and Periodisation",						'icon'=>"X",					'color'=>'#000000', 'page'=>'PlanningAndPeriodisation.htm'),
						array('title'=>"FUNdamentals of Agility",							'icon'=>"X",					'color'=>'#000000', 'page'=>'FUNdamentalsOfAgility.htm'),
						array('title'=>"FUNdamentals of Coordination",						'icon'=>"X",					'color'=>'#000000', 'page'=>'FUNdamentalsOfCoordination.htm'),
						array('title'=>"Planning for PHV",									'icon'=>"X",					'color'=>'#000000', 'page'=>'PlanningForPHV.htm')
					);
			return $Titles;
		}
		// END //
		
		////////////////////////////////////////////////////
		//Build title select options with marker graphics
		//ALSO - populate the 'customIcons' array at same time
		function BuildTitleSelect($getPath){
			global $labels,$Titles,$varCustomIcons;

			$TitleSelected = $_REQUEST['title'];
			
			if(empty($getPath)) $varCustomIcons = 'var customIcons = [];'."\r\n";//start javascript array
			$BuildTitleSelect .= '<label for="title">'.$labels['title'].'</label>';
			$BuildTitleSelect .= '&nbsp;<select name="title" id="title" title="Please select..." onchange="searchLocations();">';
			$BuildTitleSelect .= '<option value="" selected>Show ALL workshops</option>';
			for($i=0;$i<sizeof($Titles);$i++){
				$color = strtolower(substr($Titles[$i]['icon'], 4, strlen($Titles[$i]['icon'])));
				$marker = $color.'_Marker_sm.png';
				$BuildTitleSelect .= '<option style="padding:5px 5px 0px 25px;background:#fff url('.$getPath.'markers/'.$marker.') no-repeat 5px 50% !important;color:#000 !important;"';
				$BuildTitleSelect .= ' value="'.$Titles[$i]['title'].'"';
				if($TitleSelected==$Titles[$i]['title']) $BuildTitleSelect .= ' selected';
				$BuildTitleSelect .= '>'.$Titles[$i]['title'].'</option>';
				if(empty($getPath)) $varCustomIcons .= 'customIcons[\''.strtolower($Titles[$i]['title']).'\'] = '.$Titles[$i]['icon'].';'."\r\n";
			}
			$BuildTitleSelect .= '</select>&nbsp;&nbsp;&nbsp;';
			
			return $BuildTitleSelect;
		}
		// END //


		//////////////
		//RADIUS ARRAY
		function BuildRadiusSelect(){
			global $labels;

			$RadiusSelected = $_REQUEST['radiusSelect'];

			$BuildRadiusArray = array(5,10,25,50,100,200,1000);
			$BuildRadiusSelect .= '<label for="radiusSelect">'.$labels['radius'].'</label>';
			$BuildRadiusSelect .= '&nbsp;<select id="radiusSelect" name="radiusSelect" onchange="searchLocations();">';
			for($i=0;$i<sizeof($BuildRadiusArray);$i++){
				if($i==sizeof($BuildRadiusArray)-1){
					$BuildRadiusSelect .= '<option value="'.$BuildRadiusArray[$i].'"';
					if($RadiusSelected=="1000" || empty($RadiusSelected)) $BuildRadiusSelect .= ' selected';
					$BuildRadiusSelect .= '>nationwide</option>';
				}else{
					$BuildRadiusSelect .= '<option value="'.$BuildRadiusArray[$i].'"';
					if($RadiusSelected==$BuildRadiusArray[$i]) $BuildRadiusSelect .= ' selected';
					$BuildRadiusSelect .= '>'.$BuildRadiusArray[$i].' miles</option>';
				}	
			}
			$BuildRadiusSelect .= '</select>';
			return $BuildRadiusSelect;
		}
		
		////////////////////////////////
		//Build date selection dropdown
		function BuildDateSelect(){
			global $db_field_date,$db_table,$labels;
			$dateSelected = $_REQUEST['when'];
			
			$DateFormat = 'Y-m-d';
			$TheDayToday = date($DateFormat);
			//Tomorrow
			$tmpTomorrow = mktime(0, 0, 0, date("m"), date("d")+1, date("y"));
			$Tomorrow = date($DateFormat, $tmpTomorrow);
			//This Week
			$tmpLastDayOfWeek = 7-date("N");//the "N" denotes day of week (1-7)... so by subtracting it from 7, we should be able to count days to the end of week
			$tmpThisWeekTo = mktime(0, 0, 0, date("m"), date("d")+$tmpLastDayOfWeek, date("y"));
			$ThisWeekTo = date($DateFormat, $tmpThisWeekTo);
			//This Month
			$tmpThisMonthTo = mktime(0, 0, 0, date("m"), 31, date("y"));
			$ThisMonthTo = date($DateFormat, $tmpThisMonthTo);
			
			
			$DateQuery = "SELECT DISTINCT MONTHNAME($db_field_date) AS MonthName, MONTH($db_field_date) AS MonthNum, YEAR($db_field_date) AS YearNum FROM $db_table WHERE $db_field_date>='$TheDayToday' GROUP BY YearNum, MonthNum ORDER BY $db_field_date ASC";
			$DateResult = mysql_query($DateQuery);
			$BuildDateSelect = '';
			if($DateResult && mysql_num_rows($DateResult)>1){
				$BuildDateSelect .= '<input type="hidden" name="TheDayToday" id="TheDayToday" value="'.$TheDayToday.'">';
				$BuildDateSelect .= '<label for="when">'.$labels['date'].'</label>';
				$BuildDateSelect .= '<select name="when" id="when" title="Please select..." onchange="searchLocations();">';
				$BuildDateSelect .= '<option value="" selected>Dates</option>';
				$BuildDateSelect .= '<option value="'.$Tomorrow.'"';
				if($dateSelected==$Tomorrow) $BuildDateSelect .= ' selected';
				$BuildDateSelect .= '>Today</option>';
				
				
				if(date("N")<7){
					$tmpDateValue = $TheDayToday.'::'.$ThisWeekTo;
					$BuildDateSelect .= '<option value="'.$tmpDateValue.'"';
					if($dateSelected==$tmpDateValue) $BuildDateSelect .= ' selected';
					$BuildDateSelect .= '>';
					if(date("N")==6){
						$BuildDateSelect .= 'This weekend';
					}else{
						$BuildDateSelect .= 'This week';
					}
					$BuildDateSelect .= '</option>';//date("M")		
				}
			
				//Loop through months
				for($i=0;$i<mysql_num_rows($DateResult);$i++){
					$DateRow = mysql_fetch_array($DateResult);
					$MonthStart = $DateRow['YearNum'].'-'.str_pad($DateRow['MonthNum'], 2, '0', STR_PAD_LEFT).'-01';//2009-05-01
					$MonthEnd = $DateRow['YearNum'].'-'.str_pad($DateRow['MonthNum'], 2, '0', STR_PAD_LEFT).'-31';//2009-05-31
					
					$MonthName = $DateRow['MonthName'];//May
					if($DateRow['YearNum']!=date("Y")) $MonthName .= '&nbsp;'.$DateRow['YearNum'];//if year is greater than this year then add it to month name(May 2010)
					$tmpDateValue = $MonthStart.'::'.$MonthEnd;
					$BuildDateSelect .= '<option value="'.$tmpDateValue.'"';
					if($dateSelected==$tmpDateValue) $BuildDateSelect .= ' selected';
					$BuildDateSelect .= '>'.$MonthName.'</option>';
				}
				
				$BuildDateSelect .= '</select>';
				//$BuildDateSelect .= '</div>';
				
				return $BuildDateSelect;
			}
		}
		// END //
	
		function BuildOrderSelect(){
			global $labels, $db_field_date;
			$orderSelect = $_REQUEST['orderSelect'];
			
			$BuildOrderSelect = '';
			$BuildOrderSelect .= '<label for="SelectOrder">'.$labels['order'].'</label>';
			$BuildOrderSelect .= '<select id="orderSelect" name="orderSelect" onchange="searchLocations();">';
				$BuildOrderSelect .= '<option value="distance"';
				if(empty($orderSelect)) $BuildOrderSelect .= ' selected';
				$BuildOrderSelect .= '>distance</option>';
				$BuildOrderSelect .= '<option value="'.$db_field_date.' asc"';
				if($orderSelect==$db_field_date." asc") $BuildOrderSelect .= ' selected';
				$BuildOrderSelect .= '>date</option>';
			$BuildOrderSelect .= '</select>';
			return $BuildOrderSelect;
		}
		
		
		//////////////////////////
		/// FUNCTION: GetStoreAddress
		function GetStoreAddress($getID){
			global $GMap,$field_address, $field_address2, $field_address3, $field_id, $field_name, $field_town, $field_county, $field_postcode, $field_telephone;
			if(!$field_address) $GMap->FieldInit();
			//$db = new dbConnect();
			
			$query = "SELECT * FROM workshops WHERE $field_id=$getID LIMIT 1";
			$result = mysql_query($query);
			if($result){
				$row = mysql_fetch_array($result);
				$tmpAddress = '';
				if($row[$field_address]) $tmpAddress.=$row[$field_address].'<br>';
				if($row[$field_town]) $tmpAddress.=$row[$field_town].'<br>';	
				if($row[$field_county]) $tmpAddress.=$row[$field_county].'<br>';
							
				if($row[$field_postcode]){
					$tmpAddress.=$row[$field_postcode].' <a href="javascript:OpenClose(\'GetDirections\');" title="Get directions to this location">[directions]</a><br>';

					$tmpAddress.='<div id="GetDirections" style="display:none;">';
					$tmpAddress.='<form action="http://maps.google.co.uk/maps" method="get" target="_blank">';
					$tmpAddress.='<label for="saddr">Enter your postcode and hit GO!</label><br>';
					$tmpAddress.='<input type="text" name="saddr" id="saddr" value="" />';
					$tmpAddress.='<input type="submit" value="GO!" class="gmapGo" />';
					$tmpAddress.='<input type="hidden" name="daddr" value="'.$row[$field_postcode].'" />';
					$tmpAddress.='<input type="hidden" name="hl" value="en" />';
					$tmpAddress.='</form>';
					$tmpAddress.='</div>';
				}
				return $tmpAddress;
			}
			
		}
		/// END ///
		
		//////////////////////////
		/// FUNCTION: GetContactDetails
		function GetContactDetails($getID){
			//$db = new dbConnect();
			global $tablename,$field_name,$field_id,$GMap,$field_contact,$field_email,$field_telephone;
			if(!$field_contact) $GMap->FieldInit();
			
			$query = "SELECT $field_name,$field_contact,$field_telephone,$field_email FROM $tablename WHERE $field_id=$getID LIMIT 1";
			$result = mysql_query($query);
			if($result){
				$row = mysql_fetch_array($result);
				$ContactDetails = '';
				if($row[$field_contact]) $ContactDetails.=$row[$field_contact].'<br>';
				if($row[$field_telephone]) $ContactDetails.='Telephone: '.$row[$field_telephone].'<br>';
				if($row[$field_email]) $ContactDetails.='Email: <a href="mailto:'.$row[$field_email].'?subject='.$row[$field_name].'" title="Contact '.$row[$field_contact].'">'.$row[$field_email].'</a>';				
				return $ContactDetails;
			}
			
		}
		/// END ///
		
		
		//////////////////////////
		/// FUNCTION: GetStorePostCode
		function GetStorePostCode($getID){
			global $tablename,$field_id,$field_postcode;
			
			$query = "SELECT $field_postcode FROM $tablename WHERE $field_id=$getID LIMIT 1";
			$result = mysql_query($query);
			if($result){
				$row = mysql_fetch_row($result);
				$postcode = str_replace('  ',' ',$row[0]);
				return $postcode;
			}
			
		}
		/// END ///
		
		
	}
	
	$GMap = new GMap();

?>