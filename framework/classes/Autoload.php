<?php

namespace GYBC;

use \GYBC\Response as Response;

require_once dirname(__DIR__).'/source/base/Controller.php';
require_once dirname(__DIR__).'/source/base/Model.php';

class Autoload {
	
	public function __construct() {

		add_action( 'parse_request', array( $this, 'apiLoaded' ), 10, 1 );		
	}

	public function apiLoaded() {
		
		global $wp;

		if( empty( $wp->query_vars[ROUTE] ) )
			
			return;

		spl_autoload_register( array($this, 'autoloader') );

		$new_class = '\\GYBC\\' . ucfirst( strtolower( getController() ) ) . 'Controller';

		$class = new $new_class();
		
		$action_name = str_replace('-', '_', getMethod()) . $_SERVER['REQUEST_METHOD'];


		if(  method_exists( $class, $action_name ) ){

			return $class->$action_name();

		} elseif( method_exists( $class, 'indexGET' ) ) {

			return $class->indexGET();

		} else {	

			if(WP_DEBUG){
				
				die('Method Name: '. $action_name . ' Not Found for Controller ' . $new_class);
			}
			
			$Response = new Response;

			$Response->view('404');
		}
	}

	public function autoloader( $class_name ) {
		
		if( strpos( $class_name, 'GYBC' ) !== false ) {
			
			$api_path = dirname( __DIR__ ) . '/source/' . VERSION;
			
			if( strpos( $class_name, 'Controller' ) !== false ) {
				
				$target_path = $api_path . '/controllers/' . str_replace( 'GYBC\\', '', $class_name ) . '.php';

				if( file_exists( $target_path ) ) {

					require_once $target_path;
			
					return true;

				} elseif( file_exists( $target_path = str_replace('/controllers/', '/base/', $target_path) ) ){

					require_once $target_path;

					return true;

				} else {

					die( json_encode(array( 
						'status' => 'failed', 
						'message' => 'Controller '. $class_name .' not found.' 
					)));
					
				}
			}
			elseif( strpos( $class_name, 'Model' ) !== false ) {
				
				$subpath = str_replace( 'GYBC\\', '', $class_name );
				
				if( file_exists( $api_path . '/models/' .$subpath . '.php' ) ) {
					
					require_once $api_path . '/models/' . $subpath . '.php';
			
					return true;
				}
				else {
					
					die( json_encode(array( 
						'status' => 'failed', 
						'message' => 'Model ' . $class_name . ' not found.' 
					)));
					
				}
			}
			else {
				
				$subpath = str_replace( 'GYBC\\', '', $class_name );
				
				if( file_exists( $api_path . '/' . $subpath . '.php' ) ) {
					
					require_once $api_path . '/' . $subpath . '.php';
			
					return true;
				}
				else {
					
					die( json_encode(array( 
						'status' => 'failed', 
						'message' => 'Class ' . $class_name . ' not found.' 
					)));
					
				}
			}
		}
		
		exit;
	}
}