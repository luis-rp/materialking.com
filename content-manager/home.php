<?php
require_once("includes.php");

if(!isset($_SESSION['content_admin_manager']))
{	
	header("Location:index.php");	
}	

//------------fetch from DB--------------------


$ssql         = "SELECT * FROM  `cms_content` WHERE `id` = '1'";
$current_res  = mysql_query($ssql);	
$current_row  = mysql_fetch_assoc($current_res);
		

$curent_id    = $current_row['id']; 
$title        = $current_row['title']; 
$content      = $current_row['content']; 
$curent_image = $current_row['image']; 
$curent_video = $current_row['video']; 


//------------------------------------





$error_message = '';

if(isset($_POST['title']))
{

	$title      = addslashes($_POST['title']);
	$content    = addslashes($_POST['content']);
   	$video_code = addslashes($_POST['video_code']);
	
		
	$ssql = "UPDATE  `cms_content` SET  
  				`video`    = '$video_code', 
				`title`    = '$title',
				`content`  = '$content'
			WHERE `id` = '1'	
			";
    $res  = mysql_query($ssql);
	
	//--------------------------------------------------------------------------------------
	
	
	if(isset($_POST['del_image']))
	{
		 if(trim($curent_image) != '' &&  file_exists(PRODUCT_IMAGE_ROOT.$curent_image))
		  {
				unlink(PRODUCT_IMAGE_ROOT.$curent_image);
		  }		
	}
	
	if(isset($_FILES['c_image']) && trim($_FILES['c_image']['name']) != '')
	{
		$ext                = pathinfo($_FILES['c_image']['name'], PATHINFO_EXTENSION); 		
		$product_image_name = rand(111111,9999999);			
		$product_image_name.= time().'.'.$ext; 
														
		if(move_uploaded_file($_FILES['c_image']['tmp_name'],PRODUCT_IMAGE_ROOT.$product_image_name))
		{   
			  if(trim($curent_image) != '' && file_exists(PRODUCT_IMAGE_ROOT.$curent_image))
			  {
			   		unlink(PRODUCT_IMAGE_ROOT.$curent_image);
			  }			    
			  $query   = "UPDATE `cms_content` SET `image` = '$product_image_name' where id='1'";				
			  mysql_query($query);
		}			
	} 	
	$error_message = 'Content saved successfully';			
}




//------------fetch from DB--------------------


$ssql         = "SELECT * FROM  `cms_content` WHERE `id` = '1'";
$current_res  = mysql_query($ssql);	
$current_row  = mysql_fetch_assoc($current_res);
		

$curent_id    = $current_row['id']; 
$title        = $current_row['title']; 
$content      = $current_row['content']; 
$curent_image = $current_row['image']; 
$curent_video = $current_row['video']; 


//------------------------------------


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />


<title>CMS content manager - materialking.com</title>
<style>

body
{
font-size:16px;
color:#000;
font-family:Verdana, Arial, Helvetica, sans-serif;
}



td
{
text-align:left;
}



</style>

<script>

function check_this_frm()
{

	var e_mail = document.getElementById("useremail").value;
    var pwd = document.getElementById("password").value;
	
	if(e_mail == '')
	{
		alert("Please enter email");
		return false;
	}
	
	if(pwd == '')
	{
		alert("Please enter password");
		return false;
	}

	return true;

}







</script>








</head>



<body style="text-align:center">


<div style="width:900px; text-align:center; display:block; float:left">


<div style="float:left; width:700px; margin:50px; background-color: #DDDDDD; text-align:center; border:2px solid #C7C7C7; margin:30px; padding:30px; margin-left:50px; margin-top:50px; border-radius:8px;">



<form name="content_frm_login" id="content_frm_login" method="post" action="" enctype="multipart/form-data">


<table width="700"  border="0" cellpadding="4" cellspacing="4">

<tr><td colspan="3"  style="text-align:right">[ <a href="logout.php" style="font-size:12px; font-weight:bold">Logout</a> ]</td></tr>

<tr><td colspan="3"><h2 style="padding:0px; margin:0px;">Manage Content </h2></td></tr>





<?php if($error_message != ''){ ?>
<tr><td colspan="3" style="color:#CC0000; font-size:14px; font-weight:bold">Saved successfully</td></tr>
<?php } ?>



<tr>

<td width="98">Title</td>
<td width="8">:</td>
<td width="294"><input type="text" tabindex="1" style="width:500px" id="title" value="<?php echo $title ; ?>" name="title"></td>

</tr>


<tr>
<td >Content</td>
<td>:</td>
<td>

<?php
					
					include_once(SITE_ROOT."content-manager/fckeditor/fckeditor.php");
					
					$sBasePath	= SITE_URL."content-manager/fckeditor/";
					$oFCKeditor = new FCKeditor('content') ;
					$oFCKeditor->BasePath =  $sBasePath;
					$oFCKeditor->Height = '500';
					$oFCKeditor->Width = '100%';
					$oFCKeditor->Value = $content;
					$oFCKeditor->ToolbarSet = 'New_one';
					$oFCKeditor->Config['SkinPath'] = SITE_URL . '/content-manager/fckeditor/editor/skins/office2003/' ;
					$oFCKeditor->Create() ;		
	          ?>



</td>
</tr>


<tr>

<td width="98">Embed video code</td>
<td width="8">:</td>
<td width="294"><textarea style="width:450px; height:50px" id="video_code" name="video_code"><?php echo $curent_video ; ?></textarea></td>

</tr>



<tr>

<td width="98">Image</td>
<td width="8">:</td>
<td width="294">

<input type="file" name="c_image" id="c_image" /><br />

<?php if($current_row['image'] != '' && file_exists(PRODUCT_IMAGE_ROOT.$current_row['image'])){ ?>					
	
	<img src="<?php echo  PRODUCT_IMAGE_URL.$current_row['image']; ?>" height="80" width="80" border="0" />
	<br />
	Remove image <input type="checkbox" id="del_image" name="del_image" />				
					
<?php } ?>	

</td>

</tr>

<tr>
<td colspan="2">&nbsp;</td>
<td ><input type="submit" name="sub_btm" id="sub_tn" value="Save"  style="font-size:14px;" /></td></tr>


</table>


</form>


</div>


</div>







</body>
</html>
