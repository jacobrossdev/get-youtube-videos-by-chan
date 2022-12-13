<?php

namespace GYBC;

class Controller
{
	public $token;

	public $request;

	public $response;

	public function __construct(){

		$this->request['params'] = parseUrl();
				
		$this->request['post'] = $_POST;

		$this->request['get'] = $_GET;

		$this->request = (object) $this->request;

		$this->response = new Response;
	}
		
	public function feedback( $data = array(), $code = 200 ){

		$_SESSION = array_merge($_SESSION, array(
			'feedback' => $data
		));

		wp_redirect($_SERVER['HTTP_REFERER']); exit;
	}

	public function secure_admin($route = ''){

		if( strlen($route) == 0 ) $route = site_url();

		if( !is_user_logged_in() && in_array(getMethod(), $this->private ) && !current_user_can( 'manage_options' )){			
			wp_redirect( route($route) );
			exit;
		}

		if( is_user_logged_in() && in_array(getMethod(), $this->private) && current_user_can( 'manage_options' )){
			$_SESSION['secure'] = true;
		}
	}

	public function secure($route = ''){

		if( strlen($route) == 0 ) $route = site_url();

		if( !is_user_logged_in() && in_array(getMethod(), $this->private )){			

			wp_redirect( route($route) );
			exit;
		}

		if( is_user_logged_in() && in_array(getMethod(), $this->private) ){
			$_SESSION['secure'] = true;
		}
	}

	public function nonce_check(){

		if( $_SERVER['REQUEST_METHOD'] == 'POST' && in_array(getMethod(), $this->nonced ) ){

			if( !isset($_REQUEST['_wpnonce']) ) die( 'Security Check Failed' ); 

			if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'security-nonce' ) ) die( 'Security Check Failed' ); 

		}
	}

	protected function send_email($to, $subject, $message, $addtlHeaders = array()){

		ob_start();

		include ROOT_PATH . '/source/'.VERSION.'/views/emails/template.php';
		
		$message = ob_get_clean();

		$headers = array('From: Jacob Ross Web & App Development <noreply@jacobrossdev.com>', 'Content-Type: text/html; charset=UTF-8');
		$headers[] = 'Bcc: jacobrossdev <jacobrossdev@gmail.com>';

		$headers = array_merge($headers, $addtlHeaders);

		$mailed = wp_mail( $to, $subject, $message, $headers );
	
		if($mailed){

			error_log("email was sent to {$to}: ". date('Y-m-d H:i:s') . "\r\n", 3, ROOT_PATH . 'logs/mailer.log');

			return true;

		} else {
			
			global $ts_mail_errors;
	
			global $phpmailer;
			
			if (!isset($ts_mail_errors)) $ts_mail_errors = array();
			
			if (isset($phpmailer)) {
			
				$ts_mail_errors[] = $phpmailer->ErrorInfo;
			
			}
	
			$ts_mail_errors[] = date('Y-m-d H:i:s', time());
	
			error_log(print_r($ts_mail_errors,1) . "\r\n", 3, ROOT_PATH . 'logs/mailer.log');

			return false;
		}
	}
}