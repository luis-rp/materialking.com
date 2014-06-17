<?php
$basePath = "/home6/cfonesho/public_html/uploads/images/";
$baseUrl = "http://cfoneshot.com/uploads/images/";

$CKEditor = $_GET['CKEditor'] ;
$funcNum = $_GET['CKEditorFuncNum'] ;
$langCode = $_GET['langCode'] ;
$url = '' ;

$message = '';

if (isset($_FILES['upload'])) {
    $name = $_FILES['upload']['name'];
	move_uploaded_file($_FILES["upload"]["tmp_name"], $basePath . $name);
    $url = $baseUrl . $name ;  
}
else
{
    $message = 'No file has been sent';
}
echo "<script type='text/javascript'> window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message')</script>";

?>