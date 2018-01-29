<?php 
require_once('config.php');


class Department{
	private $department_id;
	private $department_short;
	private $department_long;
	



	/*  			GETTERS 			*/
	
	public function getID(){
		return $this->id;
	}	
	
	public function getShort(){
		return $this->department_short;
	}
	
	public function getLong(){
		return $this->department_long;
	}
	
	public function toArray(){
		return array(
			'department_id' => $this->id,
			'department_short' => $this->department_short,
			'department_long' => $this->department_long
		);
	}
	
	
	/*				SETTERS				*/
	
	public function setID($id){
		$this->id = $id;
	}
	
	public function setShort($short){
		$this->department_short = $short;
	}
	public function setLong($long){
		$this->department_long = $long;
	}

	
	/* 				DB METHODS			*/
		
	public function save(){
		$db = new Database();
		$department = isset($this->id);
		if($department === false){
			$sql = "INSERT INTO `department`(`short`, `long`) VALUES (?, ?)";
			$sql = $db->prepareQuery($sql, $this->department_short, $this->department_long);
			$db->query($sql); 
		}
	}
	
	public function fetch($id){
		$db = new Database();
		$sql = "SELECT * FROM department WHERE department_id=? ORDER BY department_id LIMIT 1";
		$sql = $db->prepareQuery($sql, $id);
		$department = $db->select($sql);
		if(count($department) != 0){
			$this->setID($department[0]['id']);
			$this->setShort($department[0]['department_short']);
			$this->setLong($department[0]['department_long']);
		}else return false;
	}

	public function delete(){
		$db = new Database();
		$sql = "DELETE FROM department WHERE department_id = ?";
		$sql = $db->preparyQuery($sql, $this->id);
		$db->query($sql);
	}
}
?>