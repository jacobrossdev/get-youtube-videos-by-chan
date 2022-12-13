<?php

namespace GYBC;

class Route {

	public $json_endpoints;

	public function __construct(){

		global $wp;

		$wp->add_query_var( ROUTE );

		$this->json_endpoints = array();

	}
	
}