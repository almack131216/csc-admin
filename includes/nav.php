<?php		

if(!$CatalogueRoot) $CatalogueRoot = 'admin_category_list.php?thisList=catalogue_cats';
if($_SESSION['CatalogueRoot']) $CatalogueRoot = $_SESSION['CatalogueRoot'];

$arr_page = array();
$arr_page_catalogue	= array();

array_push($arr_page, array('name'=>'superadmin','title'=>'users','href'=>'super_user_list.php'));
array_push($arr_page, array('name'=>'catalogue','title'=>'pages','href'=>$CatalogueRoot));
//array_push($arr_page, array('name'=>'contactdetails',	'title'=>'contact details',	'href'=>'admin_contactdetails.php'));
					
if(!$_SESSION['suid']){
	array_push($arr_page, array('name'=>'ThinUpload','title'=>'file manager','title_x'=>'View files already uploaded and ready to use.','href'=>'file_manage.php'));
	array_push($arr_page, array('name'=>'links','title'=>'Friendly URLs','href'=>'admin_links_list.php'));
	array_push($arr_page, array('name'=>'members','title'=>'members','href'=>'admin_member_list.php'));
	array_push($arr_page, array('name'=>'workshops','title'=>'workshops','href'=>'admin_workshops_list.php'));
	
	// SUB PAGES
	array_push($arr_page_catalogue, array('name'=>'categories_list','title'=>'categories &amp; sub-categories','title_x'=>'Add, rename, move or delete categories.','href'=>$CatalogueRoot));
	array_push($arr_page_catalogue, array('name'=>'items','title'=>$CommonCustomWords['item'].'s','title_x'=>'View all '.$CommonCustomWords['item'].'s in your catalogue','href'=>'admin_catalogue_all.php?status=1'));
}else{
	array_push($arr_page_catalogue, array('name'=>'items','title'=>'Edit my profile','title_x'=>'View and edit my profile','href'=>$CatalogueRoot));
}
array_push($arr_page, array('name'=>'help','title'=>'help','href'=>'help_options.php'));
					
//////////////////////// SUB PAGES
//array_push($arr_page_catalogue, array('name'=>'subcategories_list','title'=>'sub-categories','title_x'=>'Add, rename, move or delete sub-categories.','href'=>'admin_category_list.php?thisList=catalogue_subcats'));
//array_push($arr_page_catalogue, array('name'=>'catalogue_addnew','title'=>'add new '.$CommonCustomWords['item'],'title_x'=>'Add a new '.$CommonCustomWords['item'].' to your catalogue.','href'=>'admin_catalogue_upload.php'));
								
								

$arr_page_ThinUpload = array( array('name'=>'catalogue_fileManage',	'title'=>'file manager',	'title_x'=>'View files already uploaded and ready to use.',	'href'=>'file_manage.php'));

//if($_SERVER['HTTP_HOST'] == "localhost") array_push($arr_page_catalogue, array('name'=>'catalogue_prefs',		'title'=>'preferences',		'title_x'=>'Alter the way your catalogue will be displayed to your customers.',		'href'=>'catalogue_prefs_edit.php'));
								
$arr_page_members = array(	array('name'=>'members_list',		'title'=>'members list',	'href'=>'admin_member_list.php'),
						);
if(gp_enabled("newsletter")) array_push($arr_page_members, array('name'=>'newsletter_list',	'title'=>'newsletter list',	'href'=>'admin_newsletter_list.php'));
						
$arr_page_links	= array(	array('name'=>'links_list',			'title'=>'Friendly URL list',		'href'=>'admin_links_list.php')
						);
						
$arr_page_workshops	= array(array('name'=>'workshops_list', 'title'=>'workshops','href'=>'admin_workshops_list.php'));

														
//////////////////////// START HTML						
$buildLeftNav = '';
$buildLeftNav .= '<div id="MainNav">';

	if ( !empty($_SESSION['cid']) AND (substr($_SERVER['PHP_SELF'], -16) != 'admin_logout.php') ) {
		$tmptotal = count($arr_page);
		$tmpstartat = 1;
		if(is_SuperAdmin()) $tmpstartat--;
	}else{
		$tmptotal = count($arr_page);
		$tmpstartat = $tmptotal-1;
	}
	
	/// LEFT NAV (START)
	
	$buildLeftNav .= '<ul>';	
	for($i=$tmpstartat;$i<$tmptotal;$i++){		
		if($curr_page == $arr_page[$i]['name'] && gp_enabled($arr_page[$i]['name'])){			
			$buildLeftNav .= '<li><a href="'.$adminroot.$arr_page[$i]['href'].'" class="current">'.$arr_page[$i]['title'].'</a></li>'."\r\n";			
		}else{
			if(gp_enabled($arr_page[$i]['name'])){
				$buildLeftNav .= '<li><a href="'.$adminroot.$arr_page[$i]['href'].'">'.$arr_page[$i]['title'].'</a></li>'."\r\n";
			}
		}			
	}

	if(!empty($gp_customPages) && gp_enabled('customPages')){
		for($i=0;$i<count($gp_customPages);$i++){
			$buildLeftNav .= '<li><a href="'.$gp_customPages[$i]['href'].'" title="'.$gp_customPages[$i]['title'].'"';
			if($curr_page == $gp_customPages[$i]['name']){
				 $buildLeftNav .= ' class="current"';
			}
			$buildLeftNav .= '>'.$gp_customPages[$i]['title'].'</a></li>'."\r\n";
		}
	}	
	$buildLeftNav .= '</ul>';
	/// END /// LEFT NAV
	
	switch($curr_page) {					
		case "catalogue":	$arr_SubPages = $arr_page_catalogue;break;
		case "ThinUpload":	$arr_SubPages = $arr_page_ThinUpload;break;					
		case "links":		$arr_SubPages = $arr_page_links;break;				
		case "members":		$arr_SubPages = $arr_page_members;break;
		case "workshops":	$arr_SubPages = $arr_page_workshops;break;
		//case "contactdetails":	if(gp_enabled('branches')) $arr_SubPages = $arr_page_contact;break;	
	}
	if(!notloggedin() && $arr_SubPages){
		$buildLeftNav .= '<ul id="SubNav">';					
		for($i=0;$i<count($arr_SubPages);$i++){
			$buildLeftNav .= '<li><a href="'.$adminroot.$arr_SubPages[$i]['href'].'"';
			if($curr_page_sub == $arr_SubPages[$i]['name'] || ($curr_page_sub == "subcategories_list" && $arr_SubPages[$i]['name']=="categories_list")){
				 $buildLeftNav .= ' class="current"';
			}
			$buildLeftNav .= '>'.$arr_SubPages[$i]['title'].'</a></li>'."\r\n";
		}
		$buildLeftNav .= '<li>&nbsp;</li>';
		$buildLeftNav .= '</ul>';
	}
	
	$buildLeftNav .= '</div>'."\r\n";
	$buildLeftNav .= '<h2 id="Descriptor">'.ucfirst($PageTip).'</h2>'."\r\n";
	echo $buildLeftNav;	

?>