<?php
session_start();

$filename = $_GET['file'];

$ctype = '';
// required for IE, otherwise Content-disposition is ignored
if(ini_get('zlib.output_compression'))
  ini_set('zlib.output_compression', 'Off');

// addition by Jorg Weske
$file_extension = strtolower(substr(strrchr($filename,"."),1));
$file_extension = "php";

if( $filename == "" ){
  echo "<html><title>Download Script</title><body>ERROR: download file NOT SPECIFIED.</body></html>";
  exit;
} elseif ( ! file_exists( $filename ) ) {
  //echo "<html><title>Download Script</title><body>ERROR: File not found.<br>FILE URL: $filename</body></html>";
  //exit;
};

 
switch( $file_extension )
{
	case "avi": $ctype="video/x-msvideo";break;
	case "mov": $ctype="video/quicktime";break;
	case "pdf": $ctype="application/pdf";break;
	case "exe": $ctype="application/octet-stream";break;
	case "zip": $ctype="application/zip";break;
	case "doc": $ctype="application/msword";break;
	case "xls": $ctype="application/vnd.ms-excel";break;
	case "ppt": $ctype="application/vnd.ms-powerpoint";break;
	case "gif": $ctype="image/gif";break;
	case "png": $ctype="image/png";break;
	case "txt": $ctype="image/text";break;
	case "jpeg":
	case "jpg": $ctype="image/jpg";break;
	case "mp3": $ctype="audio/mpeg3";break;
	case "mp3": $ctype="audio/mpeg";break;
	case "asf": $ctype="video/x-ms-asf";break;
	case "mpg": $ctype="video/mpeg";break;
	case "mpeg": $ctype="video/mpeg";break;
	case "rar": $ctype="encoding/x-compress";break;
	case "txt": $ctype="text/plain";break;
	case "wav": $ctype="audio/wav";break;
	case "wma": $ctype="audio/x-ms-wma";break;
	case "wmv": $ctype="video/x-ms-wmv";break;
	case "zip": $ctype="application/x-zip-compressed";break;
	case "php": $ctype="text/html";break;
  default: $ctype="application/force-download";break;
}

if($ctype){

	header("Pragma: public");// required
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);// required for certain browsers 
	header("Content-Type: application/force-download");
	// change, added quotes to allow spaces in filenames, by Rajkumar Singh
	header("Content-Disposition: attachment; filename=\"test.jpg\";" );
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($filename));
	
	readfile("$filename");
	//rename($filename,"http://localhost/_admin_v3x2/test.jpg");
	exit();
}

/*
$mime_types['ai']    ='application/postscript';
$mime_types['asx']   ='video/x-ms-asf';
$mime_types['au']    ='audio/basic';
$mime_types['avi']   ='video/x-msvideo';
$mime_types['bmp']   ='image/bmp';
$mime_types['css']   ='text/css';
$mime_types['doc']   ='application/msword';
$mime_types['eps']   ='application/postscript';
$mime_types['exe']   ='application/octet-stream';
$mime_types['gif']   ='image/gif';
$mime_types['htm']   ='text/html';
$mime_types['html']  ='text/html';
$mime_types['ico']   ='image/x-icon';
$mime_types['jpe']   ='image/jpeg';
$mime_types['jpeg']  ='image/jpeg';
$mime_types['jpg']   ='image/jpeg';
$mime_types['js']    ='application/x-javascript';
$mime_types['mid']   ='audio/mid';
$mime_types['mov']   ='video/quicktime';
$mime_types['mp3']   ='audio/mpeg';
$mime_types['mpeg']  ='video/mpeg';
$mime_types['mpg']   ='video/mpeg';
$mime_types['pdf']   ='application/pdf';
$mime_types['pps']   ='application/vnd.ms-powerpoint';
$mime_types['ppt']   ='application/vnd.ms-powerpoint';
$mime_types['ps']    ='application/postscript';
$mime_types['pub']   ='application/x-mspublisher';
$mime_types['qt']    ='video/quicktime';
$mime_types['rtf']   ='application/rtf';
$mime_types['svg']   ='image/svg+xml';
$mime_types['swf']   ='application/x-shockwave-flash';
$mime_types['tif']   ='image/tiff';
$mime_types['tiff']  ='image/tiff';
$mime_types['txt']   ='text/plain';
$mime_types['wav']   ='audio/x-wav';
$mime_types['wmf']   ='application/x-msmetafile';
$mime_types['xls']   ='application/vnd.ms-excel';
$mime_types['zip']   ='application/zip';
*/
?>