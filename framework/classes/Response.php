<?php

namespace GYBC;

class Response
{

	public static function json( $data = array(), $code = 200 ) {
		http_response_code( $code );
		header( 'Content-Type: application/json' );
		echo json_encode( $data );
		die;
	}

	public function fail($message, $data){
		$this->json(array('status'=>'fail', 'message'=>$message, 'error'=>$data));
	}
	
	public function success($message, $data = array()){
		$this->json(array('status'=>'success', 'message'=>$message, 'data'=>$data));
	}


	public static function view($template, $data=array()){		

		$template = str_replace('.', '/', $template);

		if( file_exists( dirname(__DIR__)  . '/source/v1/views/'.$template.'.php' ) ) {
			ob_start(); include dirname(__DIR__) . '/source/v1/views/'.$template.'.php'; 
			$template = ob_get_clean(); echo $template; die;
		} 
		elseif( file_exists( get_template_directory() . '/' . $template . '.php' ) ) {
			ob_start(); include get_template_directory() . '/' . $template . '.php'; 
			$template = ob_get_clean(); echo $template; die;
		}
		else {
			die('Unable to find view template file: '. $template);
		}
	}

	public function denied($message = '', $status = 403){

		if( file_exists( dirname(__DIR__)  . '/source/v1/views/denied.php' ) ) {
			ob_start(); include dirname(__DIR__) . '/source/v1/views/denied.php'; 
			$template = ob_get_clean(); echo $template; die;
		} 
		else {
			die('Unable to find view template file: '. $template);
		}
	}

	public function redirect($route, $data = array()){
		if( !empty($data)){
			$_SESSION = $data;
		}
		wp_redirect( route($route));
		exit;
	}
}