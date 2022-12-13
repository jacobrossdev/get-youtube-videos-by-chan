<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

global $wpmvc_main;
global $path_to_error;
global $framework_routes;

$wpmvc_main = __FILE__;
$path_to_error = __DIR__ . '/error/';

include 'config.php';
include 'include/helpers.php';
include 'include/actions.php';
include 'classes/Autoload.php';
include 'classes/Route.php';
include 'classes/Encrypt.php';
include 'classes/Response.php';
include 'classes/Setup.php';
include 'classes/Validate.php';
include 'classes/YoutubeVideos.php';
require 'vendor/autoload.php';

function run_get_youtube_by_channel() {
	
	add_rewrite_rule( '^'.PATHNAME.'/?$','index.php?'.ROUTE.'=/','top' );
	add_rewrite_rule( '^'.PATHNAME.'(.*)?', 'index.php?'.ROUTE.'=$matches[1]','top' );

	new \GYBC\Route;
	new \GYBC\Setup;
	new \GYBC\Autoload;
}

add_action('init', 'run_get_youtube_by_channel');
