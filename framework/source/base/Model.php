<?php

namespace GYBC;

class Model
{	
	
	public function execute( $sql, $prepared_values = array() )
	{
		global $wpdb;
		
		if( $prepared_values )
		{
			$wpdb->query( $wpdb->prepare( $sql, $prepared_values ) );
		
		} else {

			$wpdb->query( $sql );
		}
		
		if(strlen($error = $wpdb->last_error) > 0){

			return $error;
		}

		return TRUE;
	}
	
	public function insert( $table, $data, $prepared_values = array() )
	{
		global $wpdb;
		
		$data = array_merge($data, array(
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time())
		));

		array_push($prepared_values, '%s', '%s');

		$wpdb->insert( $table, $data, $prepared_values );

		if(strlen($error = $wpdb->last_error) > 0){

			return $error;
		}

		return $wpdb->insert_id;
	}
	
	public function update( $table, $data, $where, $format = null, $where_format = null )
	{
		global $wpdb;
		
		$data = array_merge($data, array(
			'updated_at' => date('Y-m-d H:i:s', time())
		));

		if( is_array($format) ){

			array_push($format, '%s');
		}

		$wpdb->update( $table, $data, $where, $format, $where_format );

		if(strlen($error = $wpdb->last_error) > 0){

			return $error;
		}

		return TRUE;
	}
		
	public function get_results( $sql, $prepared_values = array() )
	{
		global $wpdb;
		
		if( $prepared_values )
		{
			return $wpdb->get_results( $wpdb->prepare( $sql, $prepared_values ) );
		}
		
		return $wpdb->get_results( $sql );
	}
	
	public function get_row( $sql, $prepared_values = array() )
	{
		global $wpdb;
		
		if( $prepared_values )
		{
			return $wpdb->get_row( $wpdb->prepare( $sql, $prepared_values ));
		}
		
		return $wpdb->get_row( $sql );
	}
	
}

?>
