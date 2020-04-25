<?php
$curr_page = "help";
include("includes/classes/PageBuild.php");
$BuildPage .= $PageBuild->AddPageTitle("Help");
$BuildPage .= $PageBuild->AddPageTip("Contact Technical Support if you require assistance");
include("includes/admin_pageheader.php");
include("includes/classes/CMSHelp.php");
?>

	<!--//
    <div class="panel_oneline">
		<h2>Pages</h2>
        <p>Pages are the primary level of organisation within the system and relate to the primary sections listed in the main navigation. The only attribute of a page that can be edited is it's title and position. Pages contain the secondary level of organisation, sub-categories.</p>
	</div>

	<div class="panel_oneline">
        <h2>Sub-categories</h2>
        <p>Sub-categories are the secondary level of organisation within the system and relate to the sections listed as drop-downs in the main navigation. </p>
        <p>Sub-categories have the following attributes:</p>
        <ul style="margin:1.8em; list-style:inside;">
        	<li><strong>Name</strong> - this is the visible title given tot he subcategory that will appear in the navigation</li>
            <li><strong>Parent Category</strong> - The Page that the sub-category belongs to within the sitemap.</li>
            <li><strong>Order sub-category by</strong> - this attribute controls the way in which sub-items below the subcategory are ordered.</li>
            <li><strong>Show/Hide</strong> - this toggles the visibility of the sub-category.</li>
            <li><strong>Template</strong> - this controls the layout of all items beneath the sub-category.</li>
            <li><strong>Custom page</strong> - this is only set if you wish the link in the navigation to link to another page or URL. Leave this option blank if you do not require this functionality.</li>
            
        </ul>
	</div>

	<div class="panel_oneline">
        <h2>Items</h2>
        <p>Items are the final level of organisation in the system and contain all the actual content for your website.</p>
        <ul style="margin:1.8em; list-style:inside;">
        	<li><strong>File</strong> - this relates to a primary image for the current item - additional images can be added later once the section has been added.</li>
            <li><strong>Name</strong> - The visible name you sih to appear in the sub-category navigation.</li>
            <li><strong>Sub-category</strong> - This attribute defines the parent sub-category.</li>
            <li><strong>Online Status</strong> - This attribute toggles the visibility of the item within the navigation.</li>
             <li><strong>Hyperlink/Email Address</strong> - This attribute is a special case and only utilised in certain sections.</li>
             <li><strong>Teaser</strong> - This attribute is only utilised in certain circumstances depending on the layout specified for the parent sub-category. It is used wherever items are listed with a title and a short introductory paragraph.</li>
            <li><strong>Description</strong> - This attribute is the main content for the section and allows you to control the content much in the same way as a word document (e.g. bold, italic, underline, justify etc...).</li>
            
            <li><strong>Publish Date</strong> - This is date is more relevant for items that are intended to be listed as news items or events.</li>            
        </ul>
        
         <p>Once an item is added it will be listed beneath the sub-category specified as its parent. Additional option become active once you then edit an item. These extra options are normally listed in the top right-hand side of your cms.</p>
         <ul style="margin:1.8em; list-style:inside;">
        	<li><strong>Delete</strong> - This removes the section permanently from the cms.</li>
            <li><strong>Related</strong> - This option controls both related links and documents. These are the links that appear down the right-hand side of each section on the fron-end of the website. See later notes.</li>
            <li><strong>Attach File</strong> - This option allows you to attach additional images to the item.</li>
            <li><strong>add new item</strong> - This option allows you to add another item at the same level as the current item.</li>
        </ul>
	</div>

	<div class="panel_oneline">
        <h2>Related Files and Information</h2>
        <p>Within the edit screen of each item you have the ability of relating pre-defined sections, external links and documents to the current item</p>
        <p>These resources can be pre-defined by adding sub items to the 'Related Documents' hidden page listed on the Pages screen. For example you could add an external website as an item in the 'hyperlinks' sub-category of the 'Rekated Documents' Page.</p>
        
        <p>Related documents can be added in much the same way but are added to the other subcategories with the 'Related Documents' Page. (e.g. you could upload a pdf into Resources)</p>       
    </div>
	//-->

<?php
	$icons = $CMSHelp->GetIconArray();
	$buttons = $CMSHelp->PrintButtonPanel("");
	echo $buttons;

?>
	
	<div class="panel">
		<h2><a href="help_request.php" title="Email Technical Support">Technical support:</a></h2>
		<p class="nopad">If you have any questions or suggestions, please <?php show_contact_admin(); ?> 
		<br/>
		If your query is <strong>URGENT</strong> then please call: <?php echo $amactive['tel']; ?></p>
	</div>

			
<?php
include("includes/admin_pagefooter.php");
?>