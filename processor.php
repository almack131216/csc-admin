<?php

session_start();//NEED this to ensure database include recognises session name for database

if($_GET['tablename'])	$tablename = $_GET['tablename'];
if($_GET['fieldname'])	$fieldname = $_GET['fieldname'];
if($_GET['uid'])		$uid = $_GET['uid'];
if($_GET['detail'])		$detail = $_GET['detail'];
if($_GET['detail2'])	$detail2 = $_GET['detail2'];

//if(!$db) require_once('includes/classes/DB.php');
require_once('includes/admin_connect_2_db.php');
//echo $detail;
if($detail=="gmap"){	    
    ////////////////
    // see 'gmap.js'
    if($_GET['map_lat'])	$map_lat = $_GET['map_lat'];
    if($_GET['map_lng'])	$map_lng = $_GET['map_lng'];	
    
    if($map_lat && $map_lng){
	    //echo '<br>'.$uid.':'.$tablename;
	    $query = "UPDATE $tablename SET map_lat='$map_lat', map_lng='$map_lng' WHERE id=$uid LIMIT 1";
	    //$query = "UPDATE $tablename SET map_lat='', map_lng='' WHERE id=$uid LIMIT 1";//empty
	    $result = $db->mysql_query_log($query);
    }
}else{
    require_once('includes/loadCategories.php'); 
	processCategoriesOrder('SortableList',$tablename,$fieldname,$detail);
}
    
?>