<?php

	Class CMSSelectOptions {		
		
		////////////////////////////////////////////////////////////
		///////  ADMIN  ///////////  LIST SELECT OPTIONS IN DROPLIST  //
		// SYNTAX: $ListPropsArr = array('name'=>'category','dbTable'=>$db_client.'catalogue_cats','dbTable_orderby'=>'category','selected'=>$my_category,'pleaseselect'=>true,'jump'=>'0');
		function Build($ret_ListProps) {	
			
			global $echo,$orderby, $asc_or_desc;
			global $CMSSelectOptions;
			
			$p_table				= $ret_ListProps['dbTable'];
			$p_table_orderby		= $ret_ListProps['dbTable_orderby'];
			$p_tablefield			= $ret_ListProps['dbTable_field'];
			$p_tablefield_append	= $ret_ListProps['dbTable_field_append'];
			$p_name					= $ret_ListProps['name'];
			$p_selected				= $ret_ListProps['selected'];
			$p_optionValue			= $ret_ListProps['optionValue'];
			$p_jump					= $ret_ListProps['jump'];
			$p_onchange				= $ret_ListProps['onchange'];
			$p_query				= $ret_ListProps['query'];
			$p_query_qty			= $ret_ListProps['query_qty'];
			$p_array				= $ret_ListProps['array'];
			$p_adding				= $ret_ListProps['adding'];
			$p_pleaseselect			= $ret_ListProps['pleaseselect'];
			$p_id					= $ret_ListProps['id'];
			$p_field_id				= $ret_ListProps['field_id'];

			$echo .= $p_query_qty;
			if(empty($p_field_id)) $p_field_id = 'id';//id field default name
			
			$query 		= $p_query;
			$result 	= @mysql_query($query);
			// is this a JUMP select (onChange) or standard select?
			$tmpSelectTag='';
			$tmpSelectTag .= '<select name="'.$ret_ListProps['name'].'" id="'.$ret_ListProps['name'].'" title="Please select..."';
			if($p_id) $tmpSelectTag .= ' id="'.$p_id.'"';
			if($p_jump && !$p_onchange) $tmpSelectTag .= ' onChange="MM_jumpMenu(\'parent\',this, 0)"';
			if(!$p_jump && $p_onchange) $tmpSelectTag .= ' onChange="'.$p_onchange.'"';		
			$tmpSelectTag .= '>';
			
			if(!empty($p_pleaseselect)) $tmpSelectTag .= '<option value="" selected>'.$p_pleaseselect.'</option>';
				
			if($p_query && $result){
				$num_rows 	= mysql_num_rows($result);

				// print options
				if($p_name!="status" && $p_name!="new_status" && !$p_adding) $tmpSelectTag .= '<option value="'.$p_optionValue.'">ALL</option>';
				
				for($tmpcount=0;$tmpcount<$num_rows;$tmpcount++){
					$ret_array = mysql_fetch_array($result);
					$thisID = $ret_array[$p_field_id];
					$thisName = $ret_array[$p_tablefield];
					if(!empty($ret_array[$p_tablefield_append])) $thisName .= ', '.$ret_array[$p_tablefield_append];//append option name with a second value
					
					if($p_name=="status" || $p_name=="new_status") $thisName = get_statusname($thisID);
					
					if($p_query_qty){
				        $quant_query = $p_query_qty.$thisID;				        	

			        	$quant_result.=" ORDER BY $orderby $asc_or_desc";
			        	$quant_result = mysql_query($quant_query);
			        	if($quant_result && mysql_num_rows($quant_result)>=1){$thisName.="(".mysql_num_rows($quant_result).")";}
					}
					
					$tmpSelectTag .= '<option value="'.$p_optionValue.htmlspecialchars($ret_array[$p_field_id]).'"';
					if($thisID==$p_selected) $tmpSelectTag .= ' selected';
					$tmpSelectTag .= '>'.$thisName.'</option>';					
				}  
			
			}elseif($p_array){
				for($tmpcount=0;$tmpcount<count($p_array);$tmpcount++){
					$tmpSelectTag .= '<option value="'.htmlspecialchars($p_optionValue.$p_array[$tmpcount]['value']).'"';
					if($p_array[$tmpcount]['value'] == $p_selected) $tmpSelectTag .= ' selected';
					$tmpSelectTag .= '>'.$p_array[$tmpcount]['title'].'</option>';
				}
		     }else{
			     $tmpSelectTag .= '<option value="">'.$p_name.' list is empty</option>';
		     }
		     
		     
		     $tmpSelectTag .= '</select>';
		     return $tmpSelectTag;
		}
		/// END ///	
			
	}
	$CMSSelectOptions = new CMSSelectOptions();

?>