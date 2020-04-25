<?php


//$attributes = array('tablename'=>$tmpTableName,'fieldname'=>$tmpFieldName);
function getCategories($getAttributes){
	$tmpTableName = $getAttributes['tablename'];
	$tmpFieldName = $getAttributes['fieldname'];

	if($tmpFieldName=="name"){
		$query = "SELECT id, $tmpFieldName AS name FROM $tmpTableName ORDER BY position_insubcat, lower($tmpFieldName)";
	}elseif($tmpFieldName=="position_initem"){
		$query = "SELECT id, $tmpFieldName AS name FROM $tmpTableName ORDER BY position_initem asc";
	}else{
		$query = "SELECT id, $tmpFieldName AS name FROM $tmpTableName ORDER BY position, lower($tmpFieldName)";
	}
		
	$result = mysql_query($query);
	
	$Categories = array();
	while ($row = mysql_fetch_object($result)) {
		$Categories[$row->id] = $row->name;
	}
	return $Categories;
}

function processCategoriesOrder($key,$getTableName,$getFieldName,$getDetail){
	global $db;
	
	$tmpTableName = $getTableName;//$db_clientTable_catalogue_cats;
	$tmpFieldName = $getFieldName;//$db_clientTable_catalogue_cats;
	$tmpDetail = $getDetail;

	//echo '(FB): processCategoriesOrder(): '.$key;
    if (!isset($_POST[$key]) || !is_array($_POST[$key])) return;
    
    $attributes = array('tablename'=>$tmpTableName,'fieldname'=>$tmpFieldName);
    $Categories = getCategories($attributes);
    $queries = array();
    $position = 1;

    foreach ($_POST[$key] as $id) {
        if (!array_key_exists($id, $Categories))
            continue;

        switch($tmpDetail){
	        case "position_initem": $query = sprintf("update $tmpTableName set position_initem = %d where id = %d", $position, $id);break;
	        case "position_incat": $query = sprintf("update $tmpTableName set position_incat = %d where id = %d", $position, $id);break;
	        case "position_insubcat": $query = sprintf("update $tmpTableName set position_insubcat = %d where id = %d", $position, $id);break;
        	default: $query = sprintf("update $tmpTableName set position = %d where id = %d", $position, $id);break;
    	}

        $db->mysql_query_log($query);
        $position++;
    }

}

?>