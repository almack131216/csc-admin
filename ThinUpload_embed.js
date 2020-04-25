var cid=document.getElementById("cid").value;
var quickname=document.getElementById("quickname").value;
var adminroot=document.getElementById("adminroot").value;

document.writeln('<object classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93"');
document.writeln(' width="350" height="309"');
document.writeln('codebase="http://java.sun.com/update/1.5.0/jinstall-1_5-windows-i586.cab#version=1,4,1">');
document.writeln('<param name="archive" value="ThinImage.jar">');
document.writeln('<param name="code" value="com.thinfile.upload.ThinImageUpload">');
document.writeln('<param name="props_file" value="'+adminroot+'ThinUpload_props.php?cid='+cid+'&quickname='+quickname+'">');//ServerSpecific
document.writeln('<param name="name" value="Thin Image Upload">');