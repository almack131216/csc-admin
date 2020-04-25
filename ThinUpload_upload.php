<?php
session_start();
function SanitizeFileName($getFileName){
	
	$remove_these = array('`','"','\'','\\','/','(',')','%','$','!');
	$GoodFileName = str_replace($remove_these,'',$getFileName);
	
	$swap_these = array(' ','#','&','+');
	$GoodFileName = str_replace($swap_these,'-',$GoodFileName);
	$BetterFileName = str_replace(array("--","---","----"),'-',$GoodFileName);

	return strtolower($BetterFileName);
}

/**
 * PHP file upload handler.
 * Copyright Thin File (Pvt) Ltd. 2006.
 * http://upload.thinfile.com
 */
?>
<html>
<head>
<title>Thin File Upload</title>
<link rel="stylesheet" type="text/css" href="includes/css/myclass.css" media="screen">
</head>
<body  bgcolor="FFFFFF">
<table border="0" cellpadding="5" width="100%" align="center" bgcolor="#F0F0FF">
<tr><td colspan="2" align="center" bgcolor="#000000" color="#ffffff"><strong>Files Successfully Uploaded</strong></td></tr>
<tr bgcolor="#cccccc"><td><strong>File Name</strong></td>
	<td align="right"><strong>File size</strong></td></tr>
<?php

/*
 * SET THE SAVE PATH by editing the line below. It should be a folder
 * that your webserver can write to. Make sure that the path name ends
 * with the correct file system path separator ('/' in linux and
 * '\\' in windows servers (eg "c:\\temp\\uploads\\" ).
 * save_path MUST BE A RELATIVE PATH to avoid header issues (open stream errors)
 */

//$save_path="includes/thinfile/test/";
$save_path=$_REQUEST['siteroot']."uploads/";
//?if($siteroot=="./") $save_path="../uploads/";
$userfile_parent=$_REQUEST['userfile_parent'];
$file = $_FILES['userfile'];
$k = count($file['name']);


for($i=0 ; $i < $k ; $i++)
{
	if($i %2)
	{
		echo '<tr bgcolor="#f3f3f3"> ';
	}
	else
	{	
		echo '<tr>';
	}
	
	$name = split('/',urldecode($file['name'][$i]));
	$StrippedFileName = SanitizeFileName($name[count($name)-1]);

	echo '<td align="left">' . $StrippedFileName ."</td>\n";
	echo '<td align="right">' . number_format($file['size'][$i] / 1024,1) ."kb</td></tr>\n";

	if(isset($save_path) && $save_path!="")
	{
		
		move_uploaded_file($file['tmp_name'][$i], $save_path.$StrippedFileName);
	}
	
}

if(! isset($save_path) || $save_path =="")
{
	echo '<tr style="color: #0066cc"  bgcolor="#FCFCFC" ><td colspan=2 align="left">Files have been uploaded but not saved because the destination folder has not been set. Please change the $save_path in ThinUpload_upload.php</td></tr>';
}

if(isset($userfile_parent))
{
	//echo "<tr bgcolor='#FCFCFC' style='color: #0066cc'><td colspan=2>Top level folder hint : $userfile_parent</td></tr>";
}

?>
</table>
<p>&nbsp;</p>

</body>
</html>
