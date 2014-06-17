<?php
session_start();
error_reporting(-1);	
define('DB_SERVER', 'findsupplyhousecom.ipagemysql.com'); // eg, localhost - should not be empty for productive servers
define('DB_SERVER_USERNAME', 'jon');
define('DB_SERVER_PASSWORD', 'Jon120758');
define('DB_DATABASE', 'jon');	
$$link = mysql_connect(DB_SERVER, DB_SERVER_USERNAME,DB_SERVER_PASSWORD);  
mysql_select_db(DB_DATABASE);  
  ?> 