<?php

namespace GYBC;

class Setup {

	public $options;

	public function __construct() {
		
		global $wpmvc_main;

		add_action( 'redirect_404', array( $this, 'redirect_404'));
		wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css' );
		wp_enqueue_style( 'google-lato', 'https://fonts.googleapis.com/css?family=Lato:400,300,700,900' );
	}
	
	public function redirect_404() {
	  	global $wp_query;
	    $wp_query->set_404();
	    status_header(404);
	}

}