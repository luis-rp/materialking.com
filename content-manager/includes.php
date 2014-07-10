<?php
session_start();
error_reporting(-1);	




/*define('DB_SERVER', 'localhost'); 
define('DB_SERVER_USERNAME', 'root');
define('DB_SERVER_PASSWORD', '');

*/


//live
define('DB_SERVER', 'localhost'); 
define('DB_SERVER_USERNAME', 'jon');
define('DB_SERVER_PASSWORD', 'TNbpCev!696hphe');


define('DB_DATABASE', 'pms');	


$link = mysql_connect(DB_SERVER, DB_SERVER_USERNAME,DB_SERVER_PASSWORD);  
mysql_select_db(DB_DATABASE, $link );  

function d($ddd)
{
	echo '<pre><-------Output starts------><br>';
	print_r($ddd);
	die();
}
define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'].'/');
define('SITE_URL', 'http://materialking.com/');
define('PRODUCT_IMAGE_ROOT', SITE_ROOT.'uploads/content-manager/');
define('PRODUCT_IMAGE_URL', SITE_URL.'uploads/content-manager/');

?>