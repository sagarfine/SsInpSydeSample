<?php
/*
Plugin Name: Inpsyde Sample
Plugin URI: https://inpsyde.com/
Description: This plugin demonstrates, sending HTTP requests to third party API,
process the received data and display.
Version: 1.0
Author: Sagar Shinde.
Author URI: https://www.linkedin.com/in/sagarfine/
License: GPLv2 or later
Text Domain: ssinspyde
Domain Path: /languages
*/
declare(strict_types=1);

define('SS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SS_PLUGIN_DIR', plugin_dir_path(__FILE__));
include_once 'SsClassInpSyde.php';
register_activation_hook(SS_PLUGIN_DIR, ['SsClassInpSyde', 'ssFnCheckVersions']);
$ssObjInpSyde=new \SsClassInpSyde\SsClassInpSyde();
$ssObjInpSyde->ssFnInitialization();
$ssObjInpSyde->fnCallHooks();
