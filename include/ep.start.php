<?php 

define('EP_LOGIN','');
define('EP_PASSWORD','');
define('EP_LANG','en');// ru en de it es fr zh

define('EP_CLIENT_DIR', dirname(__FILE__).'/..');

require(EP_CLIENT_DIR.'/vendors/RedBean/rb.php');
require(EP_CLIENT_DIR.'/include/ep.class.php');


EP::setup('sqlite:'.EP_CLIENT_DIR.'/db/dbfile_'.EP_LANG.'.txt'); 
//EP::setup('mysql:host=127.0.0.1;dbname=client','user','password');
