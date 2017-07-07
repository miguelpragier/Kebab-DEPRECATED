<?php namespace PHPKebab;

require 'kebab.config.php';

class KebabDatabase
{
	private $link = null;
	private $errorLog = array();
	private $lastInsertId = 0;
	private $config;
	
	public function __construct()
	{
		try {
			$this->link = new PDO(KebabConfig::DB_CONNECTION_STRING);
			
			$this->link->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			$this->link->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
            $this->link->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $this->link->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

			if ( KebabConfig::DB_DRIVE=='pgsql' )
				$stmt = $this->link->exec("SET CLIENT_ENCODING TO 'UTF8'");
			else # MySql
				$stmt = $this->link->exec('SET CHARACTER SET utf8, NAMES utf8');
		}
		catch ( PDOException $e )
		{
			print "Error!: " . $e->getMessage();
			die(-1);
		}
	}
	
	private function getPDOStatement( $sql_query, $numeric_or_associative = PDO::FETCH_ASSOC )
	{
		try
		{
			$stmt = $this->link->query($sql_query);
			
			$stmt->setFetchMode($numeric_or_associative);
			
			if ( !$stmt )
			{
				$this->registerPDOError();		
				return null;
			}
			
			return $stmt;
		}
		catch ( Exception $e )
		{
			$this->registerException($e->getMessage());
			return null;
		}
	}

	public function getInteger( $sql_query, $substitute_value = 0 )
	{
		$stmt = $this->getPDOStatement($sql_query);
		
		if ( !$stmt )
			return $substitute_value;
		
		try
		{
			$row = $stmt->fetch(PDO::FETCH_NUM);
			
			$stmt->closeCursor();
			
			if ( empty($row) || !is_array($row) ){
				$this->registerPDOError();
				return $substitute_value;
			}
			
			$i = $row[0];
			
			if ( ctype_digit("$i") )
				return $i;

			if ( !is_numeric($i) )
				return $substitute_value;
			
			if( preg_match('^-?\d+$/', $i) )
				return $i;
			else
				return $substitute_value;
		}
		catch ( Exception $e )
		{
			$this->registerException($e->getMessage());
			return $substitute_value;
		}
	}

	public function getString( $sql_query, $substitute_value = null )
	{
		$stmt = $this->getPDOStatement($sql_query);
		
		if ( !$stmt )
			return $substitute_value;

		try
		{
			$row = $stmt->fetch(PDO::FETCH_NUM);
			
			$stmt->closeCursor();
			
			if ( empty($row) || !is_array($row) ){
				$this->registerPDOError();
				return $substitute_value;
			}
			
			$s = $row[0];
			
			if ( is_string($s) )
				return $s;
			else
				return $substitute_value;
		}
		catch ( Exception $e )
		{
			$this->registerException($e->getMessage());
			return $substitute_value;
		}
	}
	
	public function execute( $sql_query ){
		return $this->exec($sql_query);
	}
	
	public function exec( $sql_query )
	{
		try{
			$affectedRows = $this->link->exec($sql_query);

			if ( substr(trim($sql_query),0,6)=='insert' ){
				if ( $affectedRows >= 1 )
					$this->lastInsertId = $this->link->lastInsertId();
				else
					$this->registerPDOError();
			}

			return $affectedRows;
		} catch ( Exception $e ){
			$this->registerException($e->getMessage());
			return (-1);
		}
	}

	public function getLastInsertId(){
		$r = $this->lastInsertId;
		$this->lastInsertId = 0;
		return $r;
	}
	
	public function getArrayAssociative( $sql_query )
	{
		$stmt = $this->getPDOStatement($sql_query);
		
		if ( !$stmt )
			return null;

		try{
			$a = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			return $a;
		} catch ( Exception $e ){
			$this->registerException($e->getMessage());
			return (-1);
		}
	}
	
	public function getRowAssociative( $sql_query )
	{
		$stmt = $this->getPDOStatement($sql_query);
		
		if ( !$stmt )
			return null;

		try{
			$a = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			return $a;
		} catch ( Exception $e ){
			$this->registerException($e->getMessage());
			return (-1);
		}
	}
	
	public function getArray( $sql_query )
	{
		$stmt = $this->getPDOStatement($sql_query);
		
		if ( !$stmt )
			return null;

		try{
			$a = $stmt->fetchAll(PDO::FETCH_NUM);
			$stmt->closeCursor();
			return $a;
		} catch ( Exception $e ){
			$this->registerException($e->getMessage());
			return (-1);
		}
	}
	
	public function getRow( $sql_query )
	{
		$stmt = $this->getPDOStatement($sql_query);
		
		if ( !$stmt )
			return null;

		try{
			$a = $stmt->fetch(PDO::FETCH_NUM);
			$stmt->closeCursor();
			return $a;
		} catch ( Exception $e ){
			$this->registerException($e->getMessage());
			return null;
		}
	}

	public function getTableCount( $table )
	{
		$stmt = $this->getPDOStatement("SELECT COUNT(*) FROM $table");
		
		if ( !$stmt )
			return (-1);
		
		try
		{
			$row = $stmt->fetch(PDO::FETCH_NUM);
			
			$stmt->closeCursor();
			
			if ( empty($row) || !is_array($row) )
				return (-1);
			
			$i = trim($row[0]);
			
			if ( ctype_digit("$i") )
				return intval($i);
			else
				return $substitute_value;
		}
		catch ( Exception $e )
		{
			$this->registerException($e->getMessage());
			return (-1);
		}
	}
	
	public function insert( String $table_name, Array $column_and_value_pairs )
	{
		$fields = implode(',',array_keys($column_and_value_pairs));
		
		$a = array_values($column_and_value_pairs);
		
		for ( $i=0; $i<count($a); $i++ ){
			if ( !is_null($a[$i]) )
				$a[$i] = $this->link->quote($a[$i]);
			else
				$a[$i] = 'NULL';				
		}
		
		$values = implode(',',$a);
		
		$sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table_name, $fields, $values);
		
		if ( $this->exec($sql) )
			return $this->link->lastInsertId();

		return 0;
	}

	/**
	*	Update 
	*
	*	Note that WHERE is mandatory
	*/
	public function update( String $table_name, Array $column_and_value_pairs, Array $where_pairs )
	{
		$pairs = [];
		
		foreach ( $column_and_value_pairs as $k=>$v ){
			$v = !is_null($v) ? $this->link->quote($v) : 'NULL';
			$pairs[] = sprintf('%s=%s', $k, $v);
		}

		$pairs = implode(',', $pairs);
		
		$where = [];
		
		foreach ( $where_pairs as $k=>$v )
			$where[] = sprintf('%s=%s', $k, $this->link->quote($v));

		$where = implode(',', $where);
		
		$sql = sprintf('UPDATE %s SET %s WHERE %s', $table_name, $pairs, $where);
		
		$this->exec($sql);
	}

	public function getLastError()
	{
		return end($this->errorLog);
	}
	
	private function registerPDOError()
	{
		$a = $this->link->errorInfo();
		array_push($this->errorLog, sprintf('%s - %s - %s', $a[0], $a[1], $a[2]));
	}
	
	private function registerException( $message )
	{
		array_push($this->errorLog, $message);
	}
}
