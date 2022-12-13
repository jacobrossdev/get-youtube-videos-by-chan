<?php
	
	namespace GYBC;

	class Encrypt
	{
		/*
		 * Encrypt a string
		 * @param string $text
		 * @return string
		 */
		static function encrypt($key,$text)
		{
			$key = substr($key, 0, 32);
			return trim(base64_encode(\mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
		}

		/*
		 * Decrypt a string
		 * @param string $text
		 * @return string
		 */
		static function decrypt($key,$text)
		{
			$key = substr($key, 0, 32);
			return trim(\mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
		}
	
	}