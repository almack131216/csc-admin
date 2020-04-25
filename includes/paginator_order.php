<?php

	$BuildOrderOptions = '';
	$BuildOrderOptions .= '<div class="panel_oneline">';
	
	
		
		
	
	if($curr_page_sub=="items" && !$suid){
		$UseQuickSearch=true;
				
		$BuildOrderOptions .= '<form name="form1" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		$BuildOrderOptions .= '<p><span class="steptitle">Show:&nbsp;</span>';
		$ListPropsArr = array('name'=>'status','query'=>"SELECT * FROM $db_shared.catalogue_status ORDER BY id",'dbTable_field'=>'status','selected'=>$cust_status,'optionValue'=>$_SERVER['PHP_SELF'].'?category='.$cust_category.'&keyword='.$cust_keyword.'&orderby='.$cust_orderby.'&maxperpage='.$max_results.'&status=','jump'=>true);//,'query_qty'=>"SELECT * FROM $db_clientTable_catalogue AS c WHERE c.id_xtra=0 AND $queryDate AND c.status="
		$BuildOrderOptions .= $CMSSelectOptions->Build($ListPropsArr);
		
		$BuildOrderOptions .= '&nbsp;';
		$tmpCatQuery = "SELECT * FROM $db_clientTable_catalogue_cats ORDER BY category ASC";
		$ListPropsArr = array('name'=>'category','query'=>$tmpCatQuery,'dbTable_field'=>'category','query_qty'=>"SELECT * FROM $db_clientTable_catalogue AS c WHERE $queryDate AND c.id_xtra=0 AND c.category=",'selected'=>$cust_category,'optionValue'=>$_SERVER['PHP_SELF'].'?status='.$cust_status.'&keyword='.$cust_keyword.'&orderby='.$cust_orderby.'&maxperpage='.$max_results.'&category=','jump'=>true);
		$BuildOrderOptions .= $CMSSelectOptions->Build($ListPropsArr);
		$BuildOrderOptions .= '&nbsp;';
		if(!empty($cust_category)){
			$ListPropsArr = array('name'=>'subcategory','query'=>"SELECT * FROM $db_clientTable_catalogue_subcats WHERE category=1 OR category=$cust_category ORDER BY subcategory ASC",'dbTable_field'=>'subcategory','query_qty'=>"SELECT * FROM $db_clientTable_catalogue AS c WHERE $queryDate AND c.id_xtra=0 AND c.category=$cust_category AND c.subcategory=",'selected'=>$cust_subcategory,'optionValue'=>$_SERVER['PHP_SELF'].'?status='.$cust_status.'&keyword='.$cust_keyword.'&orderby='.$cust_orderby.'&maxperpage='.$max_results.'&category='.$cust_category.'&subcategory=','jump'=>true);
			$BuildOrderOptions .= $CMSSelectOptions->Build($ListPropsArr);
		}
		$BuildOrderOptions .= '</form></p>';	
		
	} elseif($curr_page=="members" && $curr_page_sub=="members_list"){//CUSTOM ACTIONS
		$UseQuickSearch=true;
		/*MemberRole
		$BuildOrderOptions .= '<form name="role_form" id="role_form" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		$BuildOrderOptions .= '<p><span class="steptitle">Show:&nbsp;</span>';
		
		$gp_array_members_select = array(	array('title'=>"All",		'value'=>""),
											array('title'=>"Policy Makers",		'value'=>"pm"),
											array('title'=>"System Builders",	'value'=>"sb"),
											array('title'=>"Coach Developers",	'value'=>"cd"),
											array('title'=>"Coaches",			'value'=>"c"));
										
		$ListPropsArr = array('name'=>'member_role','array'=>$gp_array_members_select,'selected'=>$cust_role,'optionValue'=>$_SERVER['PHP_SELF'].'?member_role=','jump'=>true);
		$BuildOrderOptions .= $CMSSelectOptions->Build($ListPropsArr);
		$BuildOrderOptions .= '</form></p>';
		*/
	} elseif($curr_page=="members" && $curr_page_sub=="newsletter_list"){//CUSTOM ACTIONS
		$UseQuickSearch=true;
		$BuildOrderOptions .= '<form name="role_form" id="role_form" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		$BuildOrderOptions .= '<p><span class="steptitle">Show:&nbsp;</span>';
		
		$SubscriptionStatus = array(	array('title'=>"All",		'value'=>""),
										array('title'=>"Subscribed",	'value'=>"0"),
										array('title'=>"UNsubscribed",	'value'=>"1"));
										
		$ListPropsArr = array('name'=>'member_role','array'=>$SubscriptionStatus,'selected'=>$cust_unsubscribe,'optionValue'=>$_SERVER['PHP_SELF'].'?unsubscribe=','jump'=>true);
		$BuildOrderOptions .= $CMSSelectOptions->Build($ListPropsArr);
		$BuildOrderOptions .= '</form></p>';	
	}
			
	//$BuildOrderOptions .= '<div class="inner_right">';		
	$BuildOrderOptions .= '<form name="form1" action="'.$_SERVER['PHP_SELF'].'" method="post">';				
	$BuildOrderOptions .= '<p>';
		
		
	if($OrderByPositionForced){
		$BuildOrderOptions .= '&nbsp;&nbsp;&nbsp;&nbsp;<img src="includes/icons/icon_item_move.gif">&nbsp;\'Drag-n-Drop\' '.$CommonCustomWords['item'].'s into place&nbsp;&nbsp;';
	}else{
		$BuildOrderOptions .= '&nbsp;&nbsp;<span class="steptitle">Order By:&nbsp;</span>';
		$ListPropsArr = array('name'=>'orderby','array'=>$cust_orderby_arr,'selected'=>$cust_orderby,'optionValue'=>$_SERVER['PHP_SELF'].'?status='.$cust_status.'&keyword='.$cust_keyword.'&category='.$cust_category.'&maxperpage='.$max_results.'&orderby=','jump'=>true);
		$BuildOrderOptions .= $CMSSelectOptions->Build($ListPropsArr);
	}
		
	$BuildOrderOptions .= '&nbsp;';		
	$arr_maxperpage = array(	array('title'=>"5 results per page",	'value'=>"5"),
									array('title'=>"10 results per page",	'value'=>"10"),
									array('title'=>"20 results per page",	'value'=>"20"),
									array('title'=>"50 results per page",	'value'=>"50"),
									array('title'=>"100 results per page",	'value'=>"100")								
								);
	$ListPropsArr = array('name'=>'maxperpage','array'=>$arr_maxperpage,'selected'=>$max_results,'optionValue'=>$_SERVER['PHP_SELF'].'?status='.$cust_status.'&category='.$cust_category.'&keyword='.$cust_keyword.'&orderby='.$cust_orderby.'&maxperpage=','jump'=>true);
	$BuildOrderOptions .= $CMSSelectOptions->Build($ListPropsArr);
	
	$BuildOrderOptions .= '</p>';
	$BuildOrderOptions .= '</form>';
	
	
	if($UseQuickSearch && gp_enabled('itemSearch')){
		$BuildOrderOptions .= '<form name="searchMember" action="'.$_SERVER['PHP_SELF'].'" method="post"><p>';
			$BuildOrderOptions .= '<input type="hidden" name="status" value="'.$cust_status.'">';
			$BuildOrderOptions .= '<input type="hidden" name="category" value="'.$cust_category.'">';
			$BuildOrderOptions .= '<input type="hidden" name="subcategory" value="'.$cust_subcategory.'">';
			$BuildOrderOptions .= '<input type="hidden" name="orderby" value="'.$cust_orderby.'">';
			$BuildOrderOptions .= '<input type="hidden" name="maxperpage" value="'.$cust_maxperpage.'">';
			$BuildOrderOptions .= '<span class="steptitle" style="float:left;">&nbsp;&nbsp;Keyword:&nbsp;</span>';		
			$BuildOrderOptions .= '<input type="text" name="keyword" style="width:60px;display:block;clear:none;float:left;" value="'.$cust_keyword.'"/>';
			$BuildOrderOptions .= '<input type="submit" name="search" value="filter" style="width:50px; display:block; clear:none; float:left;"/>';
		$BuildOrderOptions .= '</p></form>';
	}
	
	
	//$BuildOrderOptions .= '</div>';
	$BuildOrderOptions .= '</div>';
	
?>