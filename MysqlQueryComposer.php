<?php

class MysqlQueryComposer {
	private	$config	= array(
			'host'	=> 'localhost',
			'user'	=> 'root',
			'pass'	=> '',
			'port'	=> false,
			'base'	=> false
			);
	
	private		$link	= false;
	private		$dbLink	= false;
	
	function __construct(){
	}
	function __destruct(){
	
	}
	//************************
	//	Config
	//************************
	private	$params	= array(
		'host','user','pass','port','base'
	);
	
	function setConfig($param,$value){
		if(isset($this->config[$param]))
			$this->config[$param]	= $value;
	}
	
	function getConfig($param){
		if(isset($this->config[$param]))
			return $this->config[$param];
		return null;
	}
	//************************
	//	Connection
	//************************
	function connected(){	return ($this->link ? true : false);	}
	function connect() {
		$this->link	= mysql_connect(
					$this->config['host'] . ($this->config['port'] ? ':'.$this->config['port'] : ''),
					$this->config['user'],
					$this->config['pass']
				);
		$this->query(" SET NAMES 'utf8' ");
		$this->query(" SET CHARACTER SET 'utf8' ");
	}
	function connectDb($dbName = false){
		
		if(!$this->connected())	return false;
		
		if($dbName === false)	$dbName	= $this->config['base'];
		
		if($dbName)		$this->dbLink	= mysql_select_db($dbName, $this->link);
	}
	function disconect(){
		if($this->connected())	mysql_disconect($this->link);
		return true;
	}
	function getLink(){	return $this->link;	}
	function setLink($link){	$this->link =	$link;	}
	
	//************************
	//	Simple Query
	//************************
	private $_query	= null;
	private $_query_str	= "";
	function getQueryString(){	return $this->_query_str; }
	function getQuery(){		return $this->_query; }
	
	function	disableQueryExec($state){
		$this->disable_query_exec	= (bool)$state;
	}
	function	getQueryExecState(){
		return $this->disable_query_exec;
	}
	private	$disable_query_exec	= false;
	function query($str = false,$log = true){
		if($str === false){
			$str	= $this->_query_str;
		}
		
		if($this->connected())
		if($str) {
			if(!$this->disable_query_exec)
				$this->_query	= mysql_query($str,$this->link);
				$e	= mysql_error($this->link);
				if($e) {
					var_dump($e,$str);
				}
			if($log)
				$this->_query_str	= $str;
			return $this;
		}
		return $this;
	}
	
	//*********************************
	//	Advanced Safe Queries
	//*********************************
	private function _StrToSql($s){
		$s	= (string) $s;
		if(strlen($s) == 0)	return '""';
		if(preg_match('/^[a-z0-9\.A-Z\-\040]+$/',$s))	return '"'.$s.'"';
		return '0x'.bin2hex($s);
	}
	########################################################
	#	How it works : method normalizeName( mixed $s );
	#	
	#	Input type: string or array
	#	Return	: formated string
	#	
	#	INPUT:	// in development
	#		"table_name LEFT JOIN table3"
	#	OUTPUT:
	#		"`db_df`.`table_name`"
	#	
	#	
	#	INPUT:
	#		"table_name"
	#	OUTPUT:
	#		"`db_df`.`table_name`"
	#	
	#	INPUT:
	#		"db2.table_name"
	#	OUTPUT:
	#		"`db2`.`table_name`"
	#	
	#	INPUT:
	#		array(
	#			'table_name',
	#			"tmp_name"	=> "`db2`.`table_name`",
	#			"tmp2_name"	=> " SELECT * FROM table2 where collumn_1 = 23 "
	#		)
	#	OUTPUT:
	#		" `db_df`.`table_name` , 
	#		  ( `db2`.`table_name` ) AS `tmp_name` ,
	#		  ( SELECT * FROM table2 where collumn_1 = 23 ) AS `tmp2_name`	";
	########################################################
	private function normalizeName($s,$is_table_name = false) {
			if(is_array($s) && isset($s[0]) && preg_match('/^\s*(left\s+join|right\s+join|inner\s+join)\s*$/i',$s[0],$m)) {
				$r	= array();
				$joiner	= " ".$m[1]." ";array_shift($s);
			
				$first_table	= true;
				
				foreach($s as $k => $v)
					if(is_array($v)) {
						$r[]	= " ON ( ".$this->compileFilter($v)." ) ";
					} else {
						if(preg_match('/^\d+$/',(string)$k)){
							$v	= $this->normalizeName($v);
						} else {
							$v	= ' ( '.$this->normalizeName($v,true).' AS '.$this->normalizeName($k).' ) ';
						}
						$r[] = ($first_table ? '' : "\n\t".$joiner."\n").' '.$v.' ';
						$first_table	= false;
					}
				if(count($r) == 0) {
					$r = ' * ';
				} else {
					$r = implode(' ',$r);
				}
			} else if(is_array($s)) {
				$joiner	= " , ";
				foreach($s as $v) break;
				if(preg_match('/^\s*(\,)\s*$/',(string)$v))
					$joiner	= array_shift($s);
				$r	= array();
				foreach($s as $k	=> $v)
				if(preg_match('/^\d+$/',(string)$k)){
					$r[]	= $this->normalizeName($v,$is_table_name);
				} else {
					if(preg_match('/^\s*[a-zA-Z0-9\.\-]+\s*$/',$v)) {
						$v	= $this->normalizeName($v,true);
					} else {
						$v	= ' ( '.$v.' ) ';
					}
					$r[]	= ' '.$v.' AS '.$this->normalizeName($k);
				}
				$r	= implode($joiner,$r);
			} else {
				if(preg_match('/^\s*[a-zA-Z0-9\_]*\(.*\)\s*$/', $s,$m)) {
					$r	= $s;
				} else {
					if($this->config['base'])
					if($is_table_name && strpos($s,'.') === false){
						//var_dump($this->config['base'],$s);
						$s	= $this->config['base'].'.'.$s;
					}
					$r	= str_replace('`*`','*',"`".preg_replace(array('/[\s\`]+/','/\.+/'),array('','`.`'),$s)."`");
				}
			}
			return $r;
		}
	
	private function checkQueryCondition($s){
		if(preg_match('/^\s*\(.+\)\s*$/',$s)) {
			return $s;
		} else if(preg_match('/\s*(\`|)(.+?)\1\s*(LIKE|REGEXP|NOT\s+LIKE|NOT\s+REGEXP|[\<\>\!]\=|[\<\>\=])\s*(.*?)\s*$/i',$s,$m)){
		
			$field		= $this->normalizeName($m[2]);
			$operator	= $m[3];
			$value		= $m[4];
			if(preg_match('/^[\d+]+(|\.\d+)$/',$value)) {
				$value	= '"'.$value.'"';
			} else if(preg_match('/^([\"\\\'])([\w\W]*)\1$/',$value,$m)) {
				$value	= $this->_StrToSql($m[2]);
			} else if(preg_match('/^[a-z0-9\`\.\_]+$/',$value,$m)) {
				$value	= $this->normalizeName($value);
			} else {
				return false;
			}
			return ' '.$field.' '.$operator.' '.$value.' ';
		} else if(preg_match('/^\s*(\d+|true|false|TRUE|FALSE)\s*$/',$s)) {
			return $s;
		}
		return false;
	}
	
	########################################################################
	#	How it works : compileFilter( mixed $filter )
	#	
	#	Input type: array or string (one condition)
	#	Return formated string
	#	
	#	INPUT:
	#		array(
	#			'and',
	#			'type' => "admin",
	#			'block != 1',
	#			'avatar' => '',
	#			'id'	=> array(1,2,3,4,5,6),
	#			array(
	#				'or',
	#				'name LIKE "A%"',
	#				'db2.user_descr LIKE "%hacker%"'
	#			)
	#		)
	#	OUTPUT
	#		db_df.type = 'admin'
	#		 AND
	#		db_df.block != "1"
	#		 AND
	#		db_df.avatar = ""
	#		 AND	(
	#				db_df.id = 1
	#				 or
	#				db_df.id = 2
	#				 or
	#				db_df.id = 3
	#				 or
	#				db_df.id = 4
	#				 or
	#				db_df.id = 5
	#				 or
	#				db_df.id = 6
	#		) AND (
	#			db_df.name LIKE "A%"
	#			 OR
	#			db2.user_descr
	#		)
	#######################################################################
	
	private	function compileFilter($filter) {
		if(is_array($filter)){
			$r	= array();
			$join	= ' and ';
			
			foreach($filter as $condition) {
				if(preg_match('/^\s*(\|\||\&\&|and|AND|OR|or)\s*$/',(string)$condition,$m)) {
					$join	= ' '.$m[1].' ';
					array_shift($filter);
				}
				break;
			}
			
			foreach($filter as $key => $condition) 
				if(is_array($condition)) {
					$r[]	= '( '.($this->compileFilter($condition)).' )';
				} else {
					if(preg_match('/^\d+$/',(string)$key)) {
					
					} else {
						$condition	= $key.' = "'.$condition.'"';
					}
					if(($c = $this->checkQueryCondition($condition)) !== false)
						$r[]	= $c;
				}
			return implode($join,$r);
		} else {
			$r	= $this->checkQueryCondition($filter);
			if($r === false)	return ' 0 ';
			return $r;
		}
	}
	
	########################################
	#	How to use:
	#	$_db	= new MysqlQueryComposer();
	#	...
	#	$_db->delete('users',array('and','id > 100','type' => 'admin'),"order by id desc limit 10")
	#	// Equivalent : mysql_query("DELETE FROM `main_db`.`users` WHERE id > 100 and type = 'admin' order by id desk limit 10");
	########################################
	function delete($table,	$filter,$query_end = "") {
		// fix table name
		$table	= $this->normalizeName($table,true);
		return $this->query("DELETE FROM {$table} WHERE ".$this->compileFilter($filter)." ".$query_end);
	}
	
	function update($table,	$data,	$filter,$query_end = "") {
		// fix table name
		$table	= $this->normalizeName($table,true);
		$r	= array();
		foreach($data as $k => $v)
		if(preg_match('/^\d+$/',(string)$k)) {
			$r[]	= $v;
		} else {
			$r[]	= $this->normalizeName($k).' = '.$this->_StrToSql($v);
		}
		$r	= implode(' , ',$r);
		return $this->query("UPDATE {$table} SET ".$r." where ".$this->compileFilter($filter)." ".$query_end);
	}
	/*
	
	JOIN MODE
	
	$_db->select(
		array(
			'left join',
			'tbl_users'	=> 'users',
			'tbl_assessments' => 'assessments',
			array(
				'tbl_assessments.owner_id = tbl_users.id'
			),
			'tbl_access_assessment'	=> 'assessments--access',
			array(
				'tbl_access_assessment.assessment_id = tbl_assessments.id'
			)
		),
		array(
			'tbl_access_assessment.user_id' => 29,
			'tbl_users.type' => 'client'
		),
		array(
			'*'
		),
		"LIMIT 0 , 5");
	
	SELECT *
		FROM (
			`eval-center`.`users` AS `tbl_users`
		) LEFT JOIN (
			`eval-center`.`assessments` AS `tbl_assessments`
		) ON ( `tbl_assessments`.`owner_id` = `tbl_users`.`id`
		) LEFT JOIN (
			`eval-center`.`assessments--access` AS `tbl_access_assessment`
		) ON ( `tbl_access_assessment`.`assessment_id` = `tbl_assessments`.`id` )
	WHERE
		`tbl_access_assessment`.`user_id` = "29"
		AND `tbl_users`.`type` = "client"
	LIMIT 0 , 5

	
	*/
	function select($table,	$filter,	$normalize = false,$query_end = "") {
		// fix table name
		$table	= $this->normalizeName($table,true);
		
		// compile normalize array
		if(empty($normalize)){
			$normalize	= " * ";
		} else if(is_array($normalize)) {
			$r	= array();
			foreach($normalize as $k => $v)
			if(preg_match('/^\d+$/',(string)$k)){
				$r[]	= $this->normalizeName($v);
			} else {
				$r[]	= $this->normalizeName($v,true).' AS '.$this->normalizeName($k);
			}
			if(count($r) == 0) {
				$normalize = ' * ';
			} else {
				$normalize = implode(' , ',$r);
			}
		} else {
			$normalize	= $this->normalizeName($normalize);
		}
		
		return $this->query("SELECT ".$normalize." from {$table} where ".$this->compileFilter($filter)." ".$query_end);
	}
	/*
		$_db->multiselect(
			array(
				array(	// select users
					'table'	=> 'users',
					'filter'	=> array(
							'users.type' => 'client'
						),
					'normalize'	=> 'users.id'
				),
				array(	// select users
					'table'	=> 'users2',
					'filter'	=> array(
							'center != 0'
						),
					'normalize'	=> 'users2.id'
				)
			),
			"union",
			"LIMIT 0,10"
		)->toArray()
		=================================
		( SELECT `users`.`id` from `eval-center`.`users` where `users`.`type` = "client"   ) 
			union
		( SELECT `users`.`id` from `eval-center`.`users2` where  `center` != "0"   ) 
		LIMIT 0,10
	*/
	function multiselect($arr_sel,$joiner = " union ",$query_end = "") {
		if(!preg_match('/^\s*(union|union all)\s*$/',$joiner))
			$joiner	= " union ";
		$this->disableQueryExec(true);
			$r	= array();
			foreach($arr_sel as $v){
				$r[]	= ' ( '.$this->select(
					$v['table'],
					$v['filter'],
					isset($v['normalize']) ? $v['normalize'] : false,
					isset($v['query_end']) ? $v['query_end'] : ""
				)->getQueryString().' ) ';
			}
		$this->disableQueryExec(false);
		return $this->query(implode("\n\t".$joiner."\n", $r)."\n ".$query_end);
	}
	
	function insert($table,$data,$query_end = "") {
		// fix table name
		$table	= $this->normalizeName($table,true);
		
		$field	= array();
		$value	= array();
		
		foreach($data as $k => $v) {
			$field[]	= $this->normalizeName($k);
			$value[]	= $this->_StrToSql($v);
		}
		$field	= ' ( '.implode(' , ',$field).' ) ';
		$value	= ' ( '.implode(' , ',$value).' ) ';
		
		return $this->query("INSERT INTO {$table} ".$field." VALUES ".$value." ".$query_end);
	}
	
	//*********************************
	//	Data Extract Operations
	//*********************************
	function toArray($start	= 0, $length = 0,$mode = MYSQL_ASSOC){
		$r	= array();
		$k	= 0;
		if($this->_query)
		while($row	= mysql_fetch_array($this->_query,$mode)){
			if($k++ >= $start && ($length == 0 || $k < $length+$start)){
				$r[]	= $row;
			}
		}
		return $r;
	}
	function getArrayItem($key,$row = 0) {
		$data	= $this->toArray();
		if(!isset($data[$row]) || !is_array($data[$row]))	return null;
		return ( isset($data[$row][$key]) ? $data[$row][$key] : null );
	}

	function count(){
		if($this->_query)	return mysql_num_rows($this->_query);
		return false;
	}
	
	function getInsertedId(){
		return	mysql_insert_id($this->link);
	}
}

?>
