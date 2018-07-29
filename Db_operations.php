<?php

Class Db_operations{
	private $db_link;
	private $servername;
	private $username;
	private $password;
	private $dbname;
	public function __construct($servername,$username,$password,$dbname){
		$this->servername=$servername;
		$this->username=$username;
		$this->password=$password;
		$this->dbname=$dbname;
		$this->connection();
	}
	private function connection() {
		$this->db_link = new PDO("mysql:host=$this->servername; dbname=$this->dbname",$this->username,$this->password);
		return $this;
	}
	public function execute($sql,$parametrs_arr){
		$msg = $this->db_link->prepare($sql);
		return $msg->execute($parametrs_arr);
	}
	
	public function query($sql,$parametrs_arr){
		$msg = $this->db_link->prepare($sql);
		$msg->execute($parametrs_arr);
		
		$result =$msg->fetchAll(PDO::FETCH_ASSOC);
		if ($result===false){
			return[];
		} else {
			return $result;
		}
	}		
}

?>