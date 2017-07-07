<?php 
/**
  * Kebab's database access configuration file.
  *
  * You may want to have one file like this - with different info - for each environment/machine.
  *
  * @author Miguel pragier <miguelpragier@gmail.com>  
*/

namespace Kebab;

class KebabRequest
{
	public static function getString( $identifier, $max_length = null, $substitute_value = null, $sanitize = true )
	{
		if ( empty($_REQUEST[$identifier]) )
			return $substitute_value;

		$dummy = $_REQUEST[$identifier];

		if ( $sanitize )
		{
			$dummy = trim($dummy);

			$dummy = filter_var($dummy, FILTER_SANITIZE_STRING);

			$dummy = strip_tags($dummy);
		}

		if ( empty($dummy) )
			return $substitute_value;

		if ( intval($max_length) >= 1 )
			return substr($dummy,0,$max_length);

		return $dummy;
	}

	public static function getFloat( $identifier, $substitute_value = 0.0, $decimal_symbol = '.', $thousands_separator = ',' )
	{
		if ( empty($_REQUEST[$identifier]) )
			return $substitute_value;

		$dummy = trim($_REQUEST[$identifier]);

		if ( is_float($dummy) )
			return $dummy;

		if ( !preg_match("/^[0-9]*({$thousands_separator}[0-9]{3})*(\\{$decimal_symbol}[0-9]*)?$/", $dummy) )
			return $substitute_value;

		$dummy = str_replace($thousands_separator, '', $dummy);

		try
		{
			return floatval($dummy);
		}
		catch( Exception $e1 )
		{
			return $substitute_value;
		}
	}

	public static function getInteger( $identifier, $substitute_value = 0 )
	{
		if ( empty($_REQUEST[$identifier]) )
			return $substitute_value;

		$dummy = $_REQUEST[$identifier];

		if ( is_integer($dummy) || is_long($dummy) )
			return $dummy;

		if ( preg_match("/^[0-9]*$/", $dummy) )
			return intval($dummy);
		else
			return $substitute_value;
	}

	public static function getCharacter( $identifier, $substitute_value = null, $apply_trim = true )
	{
		if ( empty($_REQUEST[$identifier]) )
				return $substitute_value;

		$dummy = $_REQUEST[$identifier];

		if ( $apply_trim )
			$dummy = trim($dummy);

		if ( empty($dummy) )
			return $substitute_value;

		return substr($dummy,0,1);
	}

	public static function getArray( $identifier, $substitute_value = null )
	{
		if ( empty($_REQUEST[$identifier]) )
				return $substitute_value;

		if ( !is_array($_REQUEST[$identifier]) )
			return $substitute_value;

		return $_REQUEST[$identifier];
	}

	public static function getObjectFromJSON( $identifier )
	{
		if ( empty($_REQUEST[$identifier]) )
				return $substitute_value;

		$dummy = $_REQUEST[$identifier];

		$dummy = trim($dummy);

		if ( empty($dummy) )
			return $substitute_value;

		$j = json_decode($dummy);

		if ( empty($j) )
			return $substitute_value;
	}

	#	Shorthand form function names:
	public static function getStr( $identifier, $substitute_value = null, $max_length = null, $sanitize = true )
	{
		return self::getString( $identifier, $substitute_value = null, $max_length = null, $sanitize = true );
	}

	public static function getInt( $identifier, $substitute_value = 0 )
	{
		return self::getInteger( $identifier, $substitute_value = 0 );
	}

	public static function getChar( $identifier, $substitute_value = null, $apply_trim = true )
	{
		return self::getCharacter( $identifier, $substitute_value = null, $apply_trim = true );
	}

	public static function getArrayFromJSON( $identifier )
	{
		if ( empty($_REQUEST[$identifier]) )
				return null;

		$dummy = $_REQUEST[$identifier];

		$dummy = trim($dummy);

		if ( empty($dummy) )
			return null;

		$j = json_decode($dummy, true);

		if ( empty($j) )
			return null;
	}

	public static function getOnlyDigits( $identifier, $substitute_value = null, $max_length = null )
	{
		if ( empty($_REQUEST[$identifier]) )
			return $substitute_value;

		$dummy = trim($_REQUEST[$identifier]);

		$dummy = preg_replace('/\D/', '', $dummy);

		if ( empty($dummy) )
			return $substitute_value;

		if ( $max_length )
			return substr($dummy, 0, $max_length);

		return $dummy;
	}

	public static function getUnsafeRaw( $identifier, $substitute_value = null, $max_length = null )
	{
		if ( empty($_REQUEST[$identifier]) )
			return $substitute_value;

		$dummy = $_REQUEST[$identifier];

		return empty($max_length) ? $dummy : substr($dummy,0,$max_length);
	}

	public static function getRawPost()
	{
		try
		{
			$s = file_get_contents('php://input');
			return $s;
		}
		catch ( Exception $e1 )
		{
			return null;
		}
	}
}
