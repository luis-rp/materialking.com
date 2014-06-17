<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);


define('ADMIN_PATH','admin'); 
define('SITE_NAME','raven koi');
define('ROTATE_IMAGES','uploads');
define('PRODUCT_IMAGES','uploads/products');

define('NOREPLY_SENDER','umesh@smartsolutionnepal.com');
define('CONTACT_SUBJECT','An contact Inquery');
define('VAT_PERCENT',13);
define('PRICE_CODE','$');

define('SHIPPING_PRICE',15.00);
define('TAX_PRICE',10.00);
define('DELIVERY_PRICE',7.00);

define('CUR_SIGN','$');

define("AUTHORIZENET_API_LOGIN_ID","58u2BHzMg");
define("AUTHORIZENET_TRANSACTION_KEY","3uE5GHk2595tBgRr");
define("AUTHORIZENET_SANDBOX",true);
define("METHOD_TO_USE","AIM");
define("site_root","http://localhost/ravenkoi/");
define("AUTHORIZENET_MD5_SETTING",""); 

define("AUTHORIZENET_SHOPURL","https://test.authorize.net/gateway/transact.dll"); 

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/* End of file constants.php */
/* Location: ./application/config/constants.php */