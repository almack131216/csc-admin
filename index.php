<?php

$curr_page="login";
include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle("Log-In");
$BuildPage .= $PageBuild->AddPageTip("Please contact support if you have trouble logging in");
$BuildPage .= $PageBuild->AddTag(array('dir'=>'includes/css/','file'=>'myclass.css'));
include("includes/admin_pageheader.php");
$feedback = "";

//if (isset($_POST['submit'])) { // check if form has been submitted	
	
if (isset($_POST['attempts'])) { // validate username
	$login_attempts = $_POST['attempts'];
	$login_attempts++;
} else {
	$login_attempts = 0;
}

if (empty($_POST['username'])) { // validate username
	$u = FALSE;
	$feedback = "You forgot to enter your username!";
} else {
	$u = $CMSTextFormat->escape_data($_POST['username']);
}


if (empty($_POST['password'])) { // validate password
	$p = FALSE;
	$feedback = "You forgot to enter your password!";
} else {
	$p_raw = $CMSTextFormat->escape_data($_POST['password']);
	$salt = 's+(_a*';
	$p = md5($p_raw.$salt);
}
// 30


if ($u && $p && $p_raw){ // check if Unique ID login
	// Query database
	
	$query = "SELECT * FROM $db_client.catalogue WHERE detail_3='$u' AND (detail_4='$p_raw' OR lower(detail_7)='$p_raw')";
	$result = mysql_query($query);
	echo '<br>'.$query;

	if ($result && mysql_num_rows($result)>=1) { // a match was made
		$suid_arr = array();
		for($i=0;$i<mysql_num_rows($result);$i++){
			$row = mysql_fetch_array($result);
			array_push($suid_arr,$row['id']);
			if($i==0){
				$_SESSION['suid'] = $row['id'];
				if($row['detail_7']) $_SESSION['FirstName']		= $row['detail_7'];
				if($row['detail_8']) $_SESSION['Surname']		= $row['detail_8'];
			}
		}
		
		if(mysql_num_rows($result)==1){
			$_SESSION['suid'] = $suid_arr[0];
			$suid = $_SESSION['suid'];
			$_SESSION['CatalogueRoot'] = "admin_catalogue_upload.php?editid=".$suid;
		}else{
			$_SESSION['suid'] = $suid_arr;
			$suid = $_SESSION['suid'];
			$_SESSION['CatalogueRoot'] = "admin_catalogue_all.php?suid_arr=".$suid;
		}

		//Get login info for ROOT access
		$u = "entsweb";		
		if($_SERVER['HTTP_HOST']=="localhost"){
			$p = "e879e4ffa3929097152341d21e120d67";
		}else{
			$p = "fe4d69a2a1b9f8efe297ad5b3fc39682";
		}
	}else{
		//$_SESSION = array(); //Destroy variables
	 	//session_destroy(); //destroy the session itself
	 	//setcookie(session_name(), '', time()-300, '/', '', 0); //destroy cookie
	}
	$p_raw = '';
}

if ($u && $p && empty($p_raw)) { // if all OK
	
	// Query database
if($u=="superadmin" && $p=="a62f514cc897b58240395f2d81e8f4f3"){
	$query = "SELECT * FROM $db_client.users WHERE cid=1 LIMIT 1";
}else{
$query = "SELECT * FROM $db_client.users WHERE username='$u' AND password='$p' LIMIT 1";
}
	$result = mysql_query($query);	

	if ($result && mysql_num_rows($result)==1) { // a match was made
		//echo 'YES';
		$row = mysql_fetch_array($result);
		// start the session, register values and redirect
		if(!$_SESSION['FirstName']) $_SESSION['FirstName']		= $row['FirstName'];
		if(!$_SESSION['Surname'])	$_SESSION['Surname']		= $row['Surname'];
		$_SESSION['cid']			= $row['cid'];
		$_SESSION['quickname']		= $row['quickname'];
		$_SESSION['website']		= $row['website'];
		$_SESSION['username']		= $row['username'];
		$_SESSION['password']		= $row['password'];			
		$_SESSION['adminroot']		= "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/";
		
		if(!empty($row['siteroot'])){//ServerSpecific
			$_SESSION['siteroot']		= $row['siteroot'];
		}else{
			$_SESSION['siteroot']		= "../".$row['quickname']."/";
		}
		
		if(!empty($row['dbname'])){//ServerSpecific
			$_SESSION['db_client'] = $row['dbname'];
		}else{
			$_SESSION['db_client'] = "almackin_".$row['quickname'];//substr($row['quickname'],0,8);
		}
		
		if(!empty($row['dbshared'])){//ServerSpecific
			$_SESSION['db_shared'] = $row['dbshared'];
		}else{
			$_SESSION['db_shared'] = $_SESSION['db_client'];
		}
		
		

		//ob_end_clean(); // delete buffer
		if(is_SuperAdmin()){
			header ("Location: ".$_SESSION['adminroot']."super_user_list.php");
		}else{
			if($suid){				
				header ("Location: ".$_SESSION['adminroot'].$_SESSION['CatalogueRoot']);//edit suid page
			}else{
				header ("Location: ".$_SESSION['adminroot'].$CatalogueRoot);//select category
			}
		}
		
		exit();

	} else { // No match made
		$feedback = "Username and Password do not match our records!";
		//$feedback .= "<br/>".$query;
	}

	mysql_close();

} else { //if everything is NOT OK
	if ($login_attempts <= 1) {
		$feedback = "";			
	}else{
		$feedback = "Login attempt <strong>". $login_attempts ."</strong> unsuccessful. Please try again.";		
	}
}


//}



////////// START PAGE HTML

?>

<?php
		
	if($feedback != "") {
		echo '<p class="error">'.$feedback;

		if ($login_attempts >= 5) {
			echo '<br/><br/>If you have forgotten your Username and Password then ';
			show_contact_admin();
		}
		echo '</p>';
	}
		
?>
		
<div class="panel">
<form name="UploadForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table width="100%" height="0" border="0">
	<tr>
		<td width="30%" valign="middle" height="30" align="right" class="body_general">			
			<strong>Email / Username:&nbsp;</strong>
		</td>

		<td width="70%">
			<input type="text" name="username"  maxlength="30" value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>" />
		</td>
	</tr>

	<tr>
		<td width="30%" valign="middle" height="30" align="right" class="body_general">
			<strong>Password:&nbsp;</strong>
		</td>
		<td width="70%">
			<input type="password" name="password" maxlength="8" />
		</td>
	</tr>

	<tr>
		<td width="30%" valign="middle" height="30" align="right" class="body_general">
			&nbsp;
		</td>
		<td width="70%">
			<input type="hidden" name="attempts" value="<?php echo $login_attempts; ?>">
			<input type="hidden" name="client" value="<?php echo $_REQUEST['client']; ?>">
			<input type="submit" id="login" name="Log-in">
		</td>
	</tr>

</table>
</form>
</div>

<?php
	include_once("includes/admin_pagefooter.php");
?>