<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('UPLOAD_DIR','uploads/');
define('THUMBS_DIR',UPLOAD_DIR . 'thumbs/');

//Thumbnail generator
define('ZEBRA_IMAGE_BOXED', 0);
define('ZEBRA_IMAGE_NOT_BOXED', 1);
define('ZEBRA_IMAGE_CROP_TOPLEFT', 2);
define('ZEBRA_IMAGE_CROP_TOPCENTER', 3);
define('ZEBRA_IMAGE_CROP_TOPRIGHT', 4);
define('ZEBRA_IMAGE_CROP_MIDDLELEFT', 5);
define('ZEBRA_IMAGE_CROP_CENTER', 6);
define('ZEBRA_IMAGE_CROP_MIDDLERIGHT', 7);
define('ZEBRA_IMAGE_CROP_BOTTOMLEFT', 8);
define('ZEBRA_IMAGE_CROP_BOTTOMCENTER', 9);
define('ZEBRA_IMAGE_CROP_BOTTOMRIGHT', 10);

//Magico setData
define('MAGICO_DRAGGABLE', 0);
define('MAGICO_SORTABLE', 1);
define('MAGICO_CUSTOM', 2);
define('MAGICO_AUTO', 'auto');

//Mercado Pago
define('MP_CLIENT_ID','7173707757363626');
define('MP_CLIENT_SECRET','6LYxkZr2lCmsITaivBi3dfWkgw107Zlu');
define('MP_REFERENCE_REGULAR','r');
define('MP_REFERENCE_ESPECIAL','e');

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