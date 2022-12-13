<?php

	namespace GYBC;
	
	/**
	 * Validation class
	 * @package default
	 * @author Jacob Ross
	 */

	class Validate
	{
		public static $data;						// Data storage
		public static $rules;						// Rule storage
		public static $error;						// Error storage
		public static $print_errors = true;		// Toggle using self::print_errors(bool);
		public static $print_field_title = true;	// Toggle using self::print_fields(bool);		
		public static $default_rules = array(
			'required' 	=> ' is a required field',
			'max' 		=> ' must have a maximum character limit of ',
			'min' 		=> ' must have a minimum character limit of ',
			'exact' 	=> ' must have an exact character count of ',
			'match' 	=> ' must be checked to continue ',
			'alpha' 	=> ' must be alphabetical value ',
			'numeric' 	=> ' must be a numeric value',
			'alphanum' 	=> ' must be an alphanumeric ',
			'nospace' 	=> ' must have no spaces', 
			'email' 	=> ' must be a valid email address',
			'date' 		=> ' must be a valid date format',
			'user'		=> ' is already a registered user'
		);
		public static $instance;

		public function __construct(  ){


		}

		/**
		 * Singleton pattern
		 * @param array $data 
		 * @param array $rules 
		 * @return array
		 */

		public static function init( $data, $rules ){

			try {

				self::$rules = self::check_rules( $rules );

			} catch( Exception $e ) {

				echo '<strong>Exception: </strong>' . $e->getMessage();

			}

			try {

				self::$data = self::sanitize_data( $data );

			} catch( Exception $e ) {

				echo '<strong>Exception: </strong>' . $e->getMessage();
			}

			self::run( self::$data, self::$rules );

			if( ! isset( self::$instance ) ) {
				self::$instance = new Validate;
			}

			return self::$instance;
		}

		/**
		 * Grab array of error messages
		 * @return bool/array False if none found
		 */

		public static function getErrors(){

			return empty( self::$error ) ? FALSE : self::$error;
		}

		/**
		 * Run validation
		 * @param array $data
		 * @param array $rules
		 * @return bool
		 */

		public static function run( $data, $rules = NULL ){
			
			// Rules are set
			if( is_array( self::$rules ) )
			{

				// Check fields
				foreach( self::$rules as $k => $v )
				{

					self::check_field( $k );

				}
			}


			// Check if any errors were set
			if( empty( self::$error ) ){

				return FALSE;

			} else {

				return self::$error;
			}
		}

		/**
		 * Set validation rules
		 * Will merge over previously set rules
		 * @param array $rules
		 * @return bool
		 */
		protected static function check_rules( $rules ){

			if( is_array( $rules ) )
			{

				return $rules;

			} else {

				throw new Exception('Validation rules argument must be type <strong>Array</strong> - <strong> ' . ucfirst(gettype( $rules )) . '</strong> given.');

			}
		}

		/**
		 * Trims any values of leading and trailing spaces
		 * @return void
		 */
		protected static function sanitize_data( $data ){

			if( is_array( $data ) )
			{
				foreach( $data as $key => $field ){

					if( is_array( $field ) ){

						foreach( $field as $k=>$v )
						{
							if( ! empty( $v ) )
							{
								$data[$key] = trim($v);
							}
						}

					} else {

						$data[$key] = trim($field);
						
					}
				}

				return $data;

			} else {

				throw new Exception('Validation data argument must be type <strong>Array</strong> - <strong> ' . ucfirst(gettype( $data )) . '</strong> given.');
			}
		}

		/**
		 * Run field validation
		 * @param string $field_name
		 * @return bool
		 */
		protected static function check_field( $field_name ){


			// Loop through each rule
			foreach( self::$rules[$field_name] as $type => $error )
			{
				

				if( is_numeric( $type ) ){

					$type = $error;
					$error = NULL;
				}


				// Required
				if( $type == 'required' && strlen(self::$data[$field_name]) == 0 )
				{

					self::$error[$field_name][] = is_null( $error ) ? $field_name . self::$default_rules['required'] : $error;
				}

				// Max length
				if(preg_match('/max\[(\d+)\]/i', $type, $m))
				{

				if(strlen(self::$data[$field_name]) > $m[1])
					{
						self::$error[$field_name][] = is_null( $error ) ? $field_name . self::$default_rules['max'] : $error;
					}
				}

				// Min length
				if(preg_match('/min\[(\d+)\]/i', $type, $m))
				{
					if(strlen(self::$data[$field_name]) < $m[1])
					{
						self::$error[$field_name][] = is_null( $error ) ? $field_name . self::$default_rules['min'] : $error;
					}
				}

				// Exact length
				if(preg_match('/exact\[(\d+)\]/i', $type, $m))
				{
					if(strlen(self::$data[$field_name]) != $m[1])
					{
						self::$error[$field_name][] = is_null( $error ) ? $field_name . self::$default_rules['exact'] : $error;
					}
				}

				// Confirm
				if(preg_match('/match\[(.*?)\]/i', $type, $m))
				{
					if(self::$data[$field_name] != $m[1])
					{
						self::$error[$field_name][] = is_null( $error ) ? $field_name . self::$default_rules['match'] : $error;
					}
				}

				// Alpha
				if($type == 'alpha' && !ctype_alpha(str_replace(' ', '', self::$data[$field_name])))
				{
					self::$error[$field_name][] = is_null( $error ) ? $field_name . self::$default_rules['alpha'] : $error;
				}

				// Numeric
				if($type == 'numeric' && !ctype_digit(str_replace('.', '', self::$data[$field_name])))
				{
					self::$error[$field_name][] = is_null( $error ) ? $field_name . self::$default_rules['numeric'] : $error;
				}

				// Alphanumeric
				if($type == 'alphanum' && !ctype_alnum(str_replace(' ', '', self::$data[$field_name])))
				{
					self::$error[$field_name][] = is_null( $error ) ? $field_name . self::$default_rules['alphanum'] : $error;
				}

				// No spaces
				if($type == 'nospace' && self::$data[$field_name] != str_replace(' ', '', $field_name . self::$data[$field_name]))
				{
					self::$error[$field_name][] = is_null( $error ) ? $field_name . self::$default_rules['nospace'] : $error;
				}

				// Email
				if($type == 'email' && ! filter_var( self::$data[$field_name], FILTER_VALIDATE_EMAIL ) )
				{
					self::$error[$field_name][] = is_null( $error ) ? $field_name . self::$default_rules['email'] : $error;
				}

				// Date (5/10/09)(05/10/2009)
				if($type == 'date' && !eregi('^([0-1]{1})?([0-9]{1})/([0-3]{1})([0-9]{1})/([0-9]{2,4})$', self::$data[$field_name]))
				{
					self::$error[$field_name][] = is_null( $error ) ? $field_name . self::$default_rules['date'] : $error;
				}

				if($type == 'user')
				{
					$Users = new \Rest_API\UserModel;
					$Users->getUser( self::$data[$field_name] );

					if( $Users->getUser( self::$data[$field_name] ) ){
						self::$error[$field_name][] = is_null( $error ) ? $field_name . self::$default_rules['user'] : $error;
					}
				}
			}
		}
	}

?>
