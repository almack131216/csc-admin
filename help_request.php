<?php

$curr_page = "help";

include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle("Help &#124; Issue Support Request");
$BuildPage .= $PageBuild->AddPageTip("All requests will be answered as soon as possible");
include("includes/admin_pageheader.php");

	$DefaultRequest = "Please specify the nature of your request...";
	
	if($_POST['submit']){
		
		$ErrorLog = array();
		$Message = ""; //For sending via online form
		$MessageB = ""; //For sending via email client
		
		if(!empty($_SESSION['FirstName']) || !empty($_SESSION['Surname'])){
			$Fullname = $_SESSION['FirstName'].' '.$_SESSION['Surname'];
			$Message.="Fullname: {$Fullname}\n\n";
			$MessageB.="Fullname: {$Fullname}%0A%0A";							
		}
		
		if(!empty($_POST['EmailAddress'])){
			$EmailAddress = $_POST['EmailAddress'];
			if(!$CMSForms->ValidEmail($EmailAddress)){
				array_push($ErrorLog, "Email address is not valid");
			}else{
				$Message.="Email Address: {$EmailAddress}\n\n";
				$MessageB.="Email Address: {$EmailAddress}%0A%0A";
			}
		}else{
			array_push($ErrorLog, "Email address is required");
		}
		
		if(!empty($_POST['Telephone'])){
			$Telephone = $_POST['Telephone'];
			$Message.="Telephone: {$Telephone}\n\n";
			$MessageB.="Telephone: {$Telephone}%0A%0A";
		}
		
		if(!empty($_POST['Request'])){
			$Request = $_POST['Request'];
			if(!$CMSForms->ValidLength($Request,10) || $Request==$DefaultRequest){
				array_push($ErrorLog, "Insufficient details for your Request");
			}else{
				$Message.="Request: {$Request}\n\n";
				$MessageB.="Request: {$Request}%0A%0A";
			}				
		}else{
			array_push($ErrorLog, "Request field is empty - please specify the nature of your query");
		}
		
		if(!empty($ErrorLog)){
			echo '<p class="error"><strong>Oops...</strong> Sorry, we cannot accept this form because...';
			for($ErrorCount=0;$ErrorCount<count($ErrorLog);$ErrorCount++){
				echo '<br /><br />&#40;&#33;&#41;&nbsp;'.$ErrorLog[$ErrorCount];
			}
			echo '</p>';
		}else{
			if(mail($amactive['email'],$amactive['version'].' Support Request Form', $Message, 'From: '.$EmailAddress)){
				$emailSent = true;				
				echo '<p class="good">Thank you for logging your request. Your query will be answered soon.</p>';	
	
				echo '<div class="panel_oneline">';
					echo '<p><span class="steptitle">Thank you...</span>';
					echo '<div class="inner_right">';
						echo '<a href="help_request.php" id="return" title="Return to Support Request Form"><span>&#60;&nbsp;Return</span></a>';
					echo '</div>';
				echo '</div>';
			}else{
				echo '<p class="error"><strong>Please try again. </strong>We were unable to receive your form this time.<br />Please try again or <a href="mailto:'.$amactive['email'].'?subject='.$amactive['version'].' Support Request Form&body='.$MessageB.'" title="Send Request using your personal email software">re-send using use your own email software</a>.</p>';
			}
		}

	}
	
	
	// PRINT FORM
	if(!$emailSent){			
		echo '<form name="UploadForm" method="post" action="'.$_SERVER['PHP_SELF'].'">';
?>	

			<div class="panel_oneline">	
			<p><span class="steptitle">Support Category: </span></p>
			<div id="editpref_select">
				<select name="Request">
					<option value="General">General</option>
					<option value="Login Trouble">Login Trouble</option>
					<option value="Add new item">Add new item</option>
					<option value="Edit Item">Edit Item</option>
					<option value="Manage Categories">Manage Categories</option>
					<option value="Edit Page Text">Edit Page Text</option>
					<option value="Contact Details">Contact Details</option>
					<option value="Links">Links</option>				
				</select>
			</div>	
		</div>
		
		
		<div class="panel_oneline">
			<p><span class="steptitle">Email Address: </span>This is the address I will reply to:</p>
			<div class="inner_right">
				
			<?php
				$CMSShared->SetContactDetails();
				echo '<input type="text" name="EmailAddress" value="'.$gp_email.'">';
			?>
				
	
			</div>
			
		</div>

		<div class="panel_oneline">
			<p><span class="steptitle">Request: </span>If request is <strong>URGENT</strong>, call <?php echo $amactive['tel']; ?></p>
			<div class="inner_right">		
				<textarea id="Request" name="Request" rows=6 cols=80 wrap><?php echo $DefaultRequest; ?></textarea>
			</div>			
		</div>
	
		<div class="panel_oneline">
			<p><span class="steptitle">Submit Request</span></p>
			<div class="inner_right">	
				<input type="submit" name="submit" id="submit" value="Send Request">
				<input type="reset" name="reset" value="Reset" id="reset">				
			</div>
		</div>
		
	</form>
		
			
<?php
}
include("includes/admin_pagefooter.php");
?>