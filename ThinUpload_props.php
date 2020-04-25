<?php

if(isset($_GET['siteroot'])){
	$siteroot = $_GET['siteroot'];
}else{
	$siteroot = $_SESSION['siteroot'];
}

$my_UploadPath = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/ThinUpload_upload.php?siteroot=".$siteroot;
$my_MessagePath = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/ThinUpload_message.htm";


?>

# Configuration file for Thin Image Upload
#
# Online documentation is available at http://upload.thinfile.com/docs/
# Lines begining with the '#' symbol denote comments. They will not
# be processed.
#

#
# The url is the upload destination. It should point to the script on
# your server that will accept the uploaded files.
#
# Example: 
#     url=http://upload.thinfile.com/demo/upload.php
#     url=http://upload.thinfile.com/cgi-bin/upload.cgi
#

url=<?php echo $my_UploadPath; ?>

#
# To change the welcome message displayed when the applet starts up,
# change the message property. It should be a valid url and should point
# to a web page.
#
# Example: 
#     message=http://upload.thinfile.com/demo/init.php
#
message=<?php echo $my_MessagePath; ?>
#
# If you want to impose a limit on the total size of the file upload
# enter a value in kilobytes for the max_upload parameter. A value
# of 0, the default means unlimited. Please make sure that the server
# side configuration does not impose a lower limit than what you choose
# for max_upload
#
# Example: 
#     max_upload=10240
#     will impose a limit of 10 Mega Bytes
#

max_upload=0

#
# The max_upload property checks the sum of file sizes. You can impose
# a limit on the size of individual files using the max_file property.
# The value is in kilobytes. 0 means no limit.
#
# Example: 
#     max_file=2048
#     for a limit of 2 Mega Bytes
#

max_file=0

#
# max_upload_message message will be displayed when either the max_upload or
# max_file setting has been exceeded. If you enter a text message it
# will be displayed as a popup. If you enter a URL, the chosen page be
# loaded with in the applet. (Note: size_exceeded is an alias for max_upload_message)
#
# Example: 
#     max_upload_message=http://upload.thinfile.com/demo/exceed.html
#

#
# The next section is used to configure client side filtering.
#
#
# Enter a comma separated list of file extensions in the allow_types
# field. The applet will refuse to go ahead with the upload if any of
# the selected files do not match the list of extensions.
# 
# Please use only lower case extensions. The applet will test for both
# cases as well as mixed case.
# 
# The default behaviour of the applet when it encounters an unwanted file
# can be changed by editing the filter_action property.
#
# Example: 
#     allow_types=jpg,gif,png,tif,xcf,psd
allow_types=jpg,gif,png,bmp,tif,mov,swf,ppt,pps,doc,xls,txt,pdf,mp3,m4a,rm,htm
#
# The filter_action property tells the applet what action to take if it
# encounters a file type that is not listed in the allow_types
# parameter. If you enter a value of 'reject' here the applet will
# refuse to go ahead with the file upload. Enter any other value and
# the applet will silently ignore the offending files.
# 
# This setting takes effect only if the allow_types property is set
#
# Example: 
#     filter_action=reject
#
filter_action=reject
#
# The reject_message will be shown when the user attempts to upload
# files that should not be allowed and the filter_action is set to
# reject.
# 
# If you enter a text message here it will be displayed as a popup.
# A URl, will result in the a page being loaded inside the applet.
#
# Example: 
#     reject_message=http://upload.thinfile.com/demo/reject.html
#

#
# As the name suggests the full_path setting determines if absolute pathnames
# should be sent to the server. If you switch this off, folder information will
# be stripped from the filenames.
#

full_path=yes
#
# When the translate_path setting is switched on, windows style pathnames will
# be converted to unix style paths. In other words '\' becomes '/'.  This
# setting is required for Resumable file upload.
#

translate_path=yes
#
# When encode_path setting is switched on, pathnames are URLEncoded. This is
# usefull if you are dealing with filenames that contain special characters.
# this setting is required for resumable file upload.
#

encode_path=yes

#
# The progress indicator can display a thumbnail of each image as it is being
# uploaded. To enable this feature uncomment the show_thumb property below.
#
# Example: 
#     show_thumb=1
#
show_thumb=1
#
# If you need to disable the multiple upload feature, and to upload files
# one at a time, switch to bachelor mode. When bachelor property is set
# the applet will complain if you try to upload more than one file. Use
# the angry_bachelor property to set the error message to be displayed.
#
# Example: 
#     bachelor=1
#     angry_bachelor=http://upload.thinfile.com/demo/single.html
#

#
# If you switch on the browse setting the applet listens for mouse clicks
# and brings up a file selection dialog. If instead of clicking on the drop
# target you wish to display a browse button set the browse_button
# property as well.
#
# Example: 
#     browse=1
#     browse_button=1
#

browse=1
browse_button=1

#
# The next bit is for image scaling. Images that are either wider than
# the img_max_width or taller than img_max_height will be scaled down.
# If scale_images=yes, you must set valid integer values for
# img_max_width and img_max_height.
# 
# It should be noted that the java language does not support creating
# GIF files as such all scaled images will be in the JPG format. You
# will need to set the allow_types to match gif,jpg and png if you wish
# to make use of this feature.
#
# Example: 
#     scale_images=yes
#     img_max_width=100
#     img_max_height=100
#

scale_images=yes
img_max_width=640
img_max_height=480

#
# If you wish to create several images of varying sizes you can make
# use of array notation.
#
# Example: 
#     scale_images=yes
#     img_max_width[0]=100
#     img_max_height[0]=100
#     img_max_width[1]=200
#     img_max_height[1]=200
#

#
# By default the progress indicator will be hidden (closed) when the upload
# completes. By uncommenting the following line you can continue to keep the
# progress bar visible even after upload has been completed. The user will
# then have to manually close the progress bar.
#
# Example: 
#     monitor_keep_visible=yes
#

#
# The applet or the entire browser can be redirected to another page
# when upload completes. Select the destination URL with the
# external_redir parameter.
# 
# If you do not enter a value for the external_target property, the URL
# given external_target will be loaded with in the applet. Otherwise
# the page will be loaded in the target frame. To redirect the entire
# browser window use '_top' as the target.
# 
# If you wish to delay the redirect, enter a value for the
# redirect_delay property (in milliseconds).
#
# Example: 
#     external_redir=http://upload.thinfile.com
#     external_target=_top
#     redirect_delay=1000
#




<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<?php

echo 'test';


?>