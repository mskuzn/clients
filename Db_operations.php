<?php
//описание класса для сокращения кода при использовании в подключении к БД и выполнения манипуляций с БД
Class Db_operations{ 
	private $db_link;
	private $servername;
	private $username;
	private $password;
	private $dbname;
	public function __construct($servername,$username,$password,$dbname){ //конструктор устанавливает соединение
		$this->servername=$servername;
		$this->username=$username;
		$this->password=$password;
		$this->dbname=$dbname;
		$this->connection();
	}
	private function connection() { //функция соединения
		$this->db_link = new PDO("mysql:host=$this->servername; dbname=$this->dbname",$this->username,$this->password);
		return $this;
	}
	public function execute($sql,$parametrs_arr){//функция выполнения скрипта
		$msg = $this->db_link->prepare($sql);
		return $msg->execute($parametrs_arr);
	}
	
	public function query($sql,$parametrs_arr){//функция запроса к БД возвращает результат в двумерный массив
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