<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

define('DIR', DIRECTORY_SEPARATOR);
define('ROOT', $_SERVER['DOCUMENT_ROOT'].DIR);

require_once ROOT."vendor".DIR."autoload.php";
require_once "helpers.php";