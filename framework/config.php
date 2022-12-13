<?php
/**
 * The VERSION is the folder which your 
 * Controllers, Models, and Views live
 * in the source folder
 */
if( !defined('VERSION') ){
define('VERSION', 'v1');

}

/**
 * The ROUTE is the query variable
 * Wordpress is set to match in the rewrite rule
 */
if( !defined('ROUTE') ){
	
define('ROUTE', 'api_route');
}

/**
 * The PATHNAME is the subpath of the domain 
 * and base path of our endpoints such as route/index/test
 */
if( !defined('PATHNAME') ){
	
define('PATHNAME', 'gybc');
}


if(in_array($_SERVER['REMOTE_ADDR'],array('127.0.0.1', '::1'))){
  if( !defined('DEBUG') ) define('DEBUG', TRUE);
	if( !defined('SITE_MODE') ) define('SITE_MODE', 'TEST');
} else {
	if( !defined('DEBUG') ) define('DEBUG', FALSE);
	if( !defined('SITE_MODE') ) define('SITE_MODE', 'LIVE');
}


if( !defined('ROOT_PATH') ){
	
define('ROOT_PATH', __DIR__ . '/');
}


if( !defined('LOGS_PATH') ){
	
define('LOGS_PATH', __DIR__ . '/logs');
}


if( !defined('PUPLOADS_PATH') ){
	
define('PUPLOADS_PATH', __DIR__ . '/public/uploads');
}


if( !defined('PUBLIC_PATH') ){
	
define('PUBLIC_PATH', __DIR__ . '/public');
}


if( !defined('VIEW_PATH') ){
	
define('VIEW_PATH', __DIR__ . '/source/'.VERSION.'/views');
}
