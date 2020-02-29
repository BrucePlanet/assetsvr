<?php
/**
 * Created by PhpStorm.
 * User: bruce.tomalin
 * Date: 01/04/2019
 * Time: 15:03
 */

/* DEVELOPMENT */

//setting up some paths to important directories
define("DS", DIRECTORY_SEPARATOR);
define("BASE_PATH", dirname(dirname(__DIR__)) . DS);
define("APP_PATH", BASE_PATH . "app" . DS);
define("CONF_PATH", APP_PATH . "config" . DS);
define("PUBLIC_PATH", BASE_PATH . "public" . DS);
define("IMAGE_CACHE_PATH", "cache/image" . DS);

define("MOUNT_FOLDER", "/mnt/LocalCache/fl-assets/");
define("MOUNT_FOLDER_REM", "/mnt/RemoteCache/fl-assets/");


//set server protocol variable
define("HTTP_PROTOCOL", ($_SERVER['SERVER_PORT']==443) ? 'https://' : 'http://');

//Set FIE dev url
define ("FIE_DEV", 'http://ds-fie.dkr.kondor.develop/');

//Set FIE test URL
define ("FIE_TEST", 'http://fie.dkr.kondor.tes/');

//Set FIE Live url
define ("FIE", 'featherlite.kondor.co.uk/');


// Set FIE call parameters
define ("CALL", 'export/productimage/');

ini_set("log_errors", 1);
error_reporting(E_ALL);
ini_set("error_log", "/var/log/php/php-asset-svr-errors.log");

define("EMAIL_ADDRESS", "bruce.tomalin@kondor.co.uk");
define("ENV", "DEV");