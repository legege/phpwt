<?php
// PHPWT Configuration

// How to handle errors?
ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set('display_errors', true);

// The web site root directory
$GLOBALS['app_root'] = dirname(__FILE__)."/../..";

// Define languages here (the default is the first)
$GLOBALS['language_array'] = array('en', 'fr');

// Config files for each languages
$GLOBALS['toolkit_config'] = dirname(__FILE__).'/config.xml';
?>