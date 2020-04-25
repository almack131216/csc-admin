<?php

	//Get field type and place into array (for use in generic update/delete pages)
	Class dbFields {
		
		/////////////////////////////////////////////
		// FIELDS // FORMAT FIELD NAMES FOR STEP TEXT
		function FormatFieldName($getFieldName){
			global $prefArray;
			if(!empty($prefArray)){
				foreach($prefArray as $key=>$value) {
					if($getFieldName == $value['field']){
						if(!empty($value['instruction'])){
							$key = $value['instruction'];
						}else{
							$key = str_replace("_"," ",$key['field']);
						}						
						return ucwords($key);
					}
				}
				//return $prefArray[$getFieldName];
			}else{
				$tmpFieldName = str_replace("_"," ",$getFieldName);//(converts 'job_store_name' to 'Job Store Name')
				return(ucwords($tmpFieldName));
			}
		}
		
		///////////////////////////////
		// FIELDS // GET FIELD NAMES //
		function mysql_field_array( $query ) {
			$field = mysql_num_fields( $query );
			for ( $i = 0; $i < $field; $i++ ) {
				$names[] = mysql_field_name( $query, $i );
			}
			return $names;	
		}

		/////////////////////////
		// FIELDS /// GET TYPE //
		function mysql_field_type_array( $query ) {
			$field = mysql_num_fields( $query );
			for ( $i = 0; $i < $field; $i++ ) {
				$types[] = mysql_field_type( $query, $i );
			}
			return $types;	
		}

		// Examples of use		
		//$fields = mysql_field_array( $query );
		// Count them - easy equivelant to 'mysql_num_fields'
		//echo '<br>COUNT: '.count( $fields ).' fields';
		// Show name of column 3	
		//echo '<br>COLUMN 3: '.$fields[3];		
		// Show them all	
		//echo '<br>Show ALL: '.implode( ', ', $fields );

	}
	$dbFields = new dbFields();

?>