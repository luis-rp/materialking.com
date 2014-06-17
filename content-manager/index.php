<?php
require_once("includes.php");

if(isset($_SESSION['content_admin_manager']))
{	
	//header("Location:home.php");
	echo "<script lang='javascript'>location.href='home.php'</script>";			
}	



$error_message = '';

if(isset($_POST['useremail']))
{

	$user_email = addslashes($_POST['useremail']);
	$password   = addslashes($_POST['password']);
    	
	$ssql = "SELECT * FROM `admin_content` WHERE  
  				`email`    = '$user_email' AND
				`password` = '$password'";
    $res  = mysql_query($ssql);
		
	if($res)
	{
		$row = mysql_fetch_assoc($res);
		if($row['email'] == $_POST['useremail'] && $row['password'] == $_POST['password'] )
		{
			$_SESSION['content_admin_manager'] = $row;
			//header("Location:home.php")	;
			echo "<script lang='javascript'>location.href='home.php'</script>";			
		}
		else
		{
			$error_message = 'Email or password is incorrect!';
		}		
	}
	else
	{
		$error_message = 'Email or password is incorrect!';
	}		
	
	
}



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />


<title>Login - CMS content manager - materialking.com</title>
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


<div style="float:left; width:400px; margin:50px; background-color: #DDDDDD; text-align:center; border:2px solid #C7C7C7; margin:30px; padding:30px; margin-left:300px; margin-top:100px; border-radius:8px;">



<form name="content_frm_login" onsubmit="return check_this_frm();" id="content_frm_login" method="post" action="" enctype="multipart/form-data">


<table width="400" height="280" border="0" cellpadding="0" cellspacing="0">

<tr><td colspan="3"><h2 style="padding:0px; margin:0px;">Login to content manager</h2></td></tr>






<?php if($error_message != ''){ ?>
<tr><td colspan="3" style="color:#CC0000; font-size:14px; font-weight:bold">Email or password is incorrect!</td></tr>
<?php }else{ ?>

<tr><td colspan="3" style="color:#009900; font-weight:bold">Please  enter your login details</td></tr>
<?php } ?>


<tr>

<td width="98">Email</td>
<td width="8">:</td>
<td width="294"><input type="text" tabindex="1" style="width:200px" id="useremail" placeholder="Email" name="useremail"></td>

</tr>


<tr>
<td>Password</td>
<td>:</td>
<td><input type="password" tabindex="1" id="password" style="width:200px" placeholder="Password" name="password"></td>
</tr>

<tr>
<td colspan="2">&nbsp;</td>
<td ><input type="submit" name="sub_btm" id="sub_tn" value="Login" style="font-size:14px; font-weight:bold" /></td></tr>


</table>


</form>


</div>


</div>







</body>
</html>
