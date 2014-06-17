<?php 
error_reporting(1);	
session_start();
unset($_SESSION['content_admin_manager']);	
header("Location:index.php");																	
?>