<?php
/**
 *
 * @author     Ciprian Mocanu <http://www.mbe.ro> <ciprian@mbe.ro>
 * @author     Lai Ho Yeung
 * @license    Do whatever you like, just please reference the author
 * @version    1.2
 */
error_reporting(E_ALL);

class mysql extends mysqli_class {
	
	function __construct($db=array()){
		parent::__construct($db);
	}
}

class mysqli_class {
	var $conn;
	
	function __construct($db=array()) {
		$default = array(
			'host' => 'localhost',
			'user' => 'root',
			'pass' => '',
			'db' => 'test'
		);
		
		$db = array_merge($default,$db);		
		$this->conn = new mysqli($db['host'], $db['user'], $db['pass'], $db['db']);
		// check connection
		if ($this->conn->connect_error) {
		  trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
		}
	}
	function __destruct() {
		$this->conn->close();
	}
	function query($s='',$rows=false,$organize=true) {
		if (!$result = $this->conn->query($s)) return false;
		if ($rows!==false) $rows = intval($rows);
		$rez=array(); $count=0;
		$type = $organize ? MYSQLI_NUM : MYSQLI_ASSOC;
		while (($rows===false || $count<$rows) && $line=$result->fetch_array($type)) {
			if ($organize) {
				foreach ($line as $field_id => $value) {
					$finfo = $result->fetch_field_direct($field_id);
					$table = $finfo->table;
					if ($table==='') $table=0;
					$field = $finfo->name;
					$rez[$count][$table][$field]=$value;
				}
			} else {
				$rez[$count] = $line;
			}
			++$count;
		}
		$result->free_result();
		return $rez;
	}
	function execute($s='') {
		if ($this->conn->query($s)) return true;
		return false;
	}
	function select($options) {
		$default = array (
			'table' => '',
			'fields' => '*',
			'condition' => '1',
			'order' => '1',
			'limit' => 50
		);
		$options = array_merge($default,$options);
		$sql = "SELECT {$options['fields']} FROM {$options['table']} WHERE {$options['condition']} ORDER BY {$options['order']} LIMIT {$options['limit']}";
		return $this->query($sql);
	}
	function row($options) {
		$default = array (
			'table' => '',
			'fields' => '*',
			'condition' => '1',
			'order' => '1'
		);
		$options = array_merge($default,$options);
		$sql = "SELECT {$options['fields']} FROM {$options['table']} WHERE {$options['condition']} ORDER BY {$options['order']}";
		$result = $this->query($sql,1,false);
		if (empty($result[0])) return false;
		return $result[0];
	}
	function get($table=null,$field=null,$conditions='1') {
		if ($table===null || $field===null) return false;
		$result=$this->row(array(
			'table' => $table,
			'condition' => $conditions,
			'fields' => $field
		));
		if (empty($result[$field])) return false;
		return $result[$field];
	}
	function update($table=null,$array_of_values=array(),$conditions='FALSE') {
		if ($table===null || empty($array_of_values)) return false;
		$what_to_set = array();
		foreach ($array_of_values as $field => $value) {
			if (is_array($value) && !empty($value[0])) $what_to_set[]="`$field`='{$value[0]}'";
			else $what_to_set []= "`$field`='".$this->conn->real_escape_string($value)."'";
		}
		$what_to_set_string = implode(',',$what_to_set);
		return $this->execute("UPDATE $table SET $what_to_set_string WHERE $conditions");
	}
	function insert($table=null,$array_of_values=array()) {
		if ($table===null || empty($array_of_values) || !is_array($array_of_values)) return false;
		$fields=array(); $values=array();
		foreach ($array_of_values as $id => $value) {
			$fields[]=$id;
			if (is_array($value) && !empty($value[0])) $values[]=$value[0];
			else $values[]="'".$this->conn->real_escape_string($value)."'";
		}
		$s = "INSERT INTO $table (".implode(',',$fields).') VALUES ('.implode(',',$values).')';
		if ($this->conn->query($s)) return $this->conn->insert_id;
		return false;
	}
	function delete($table=null,$conditions='FALSE') {
		if ($table===null) return false;
		return $this->execute("DELETE FROM $table WHERE $conditions");
	}
}
