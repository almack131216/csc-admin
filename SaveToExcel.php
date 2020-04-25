<?php

// DB Connection here
include('includes/admin_connect_2_db.php');
if(!empty($_GET['db_table']) && !empty($_GET['filename'])){
	$db_table = $_GET['db_table'];
	
	$filename = $_GET['filename'];
	$appendDate = date("Y").date("m").date("d");
	$filename.="_".$appendDate;
	
	$select = "SELECT * FROM $db_table";
	if($_GET['customList']){
		$select .= " WHERE id_xtra=0";
		if(isset($_REQUEST['status']))	$select .= " AND status=${_GET['status']}";
		if($_REQUEST['category'])		$select .= " AND category=${_GET['category']}";
		if($_REQUEST['orderby']) 		$select .= " ORDER BY ${_GET['orderby']}";
		if($_REQUEST['asc_or_desc'])	$select .= " ${_GET['asc_or_desc']}";	
		if($_REQUEST['maxperpage'])		$select .= " LIMIT ${_GET['maxperpage']}";
	}
	
	$export = mysql_query($select) or die("Sql error : " . mysql_error());
	
	$fields = mysql_num_fields($export);
	
	for($i = 0; $i < $fields; $i++)
	{
	    $header .= mysql_field_name($export , $i). "\t";
	}
	
	while($row = mysql_fetch_row($export))
	{
	    $line = '';
	    foreach($row as $value)
	    {                                            
	        if(!isset($value) || trim($value) == "")
	        {
	            $value = "\t";
	        }
	        else
	        {
	            $value = str_replace('"' , '""' , $value);
	            $value = '"' . $value . '"' . "\t";
	        }
	        $line .= $value;
	    }
	    $data .= trim($line). "\n";
	}
	$data = str_replace("\r" , "" , $data);
	
	if(trim($data) == "")
	{
	    $data = "\n(0)Records Found!\n";                        
	}
	
	header("Content-type: application/msexcel");
	header("Content-Disposition: attachment; filename=$filename.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	print "$header\n$data";
	//TIPS (IF PROBLEMATIC)
	//Try renaming Content-type to "application/octet-stream" or "application/ms-excel".
}
?>