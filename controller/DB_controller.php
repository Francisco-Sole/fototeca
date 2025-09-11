<?php

class DB {

	function query($select, $from ,$conditions, $limits = "") {
		$db = $this->create_con();
		$temp = [];
		$consulta = "SELECT id,";
		
		for ($i=0; $i < count($select); $i++) { 
			if($i == 0){
				$consulta .= " ";	
			}else{
				$consulta .= ", ";	
			}
			$consulta .= $select[$i];	
		}
		$consulta .= " FROM $from ";
		
		$consulta .= "WHERE";

		for ($i=0; $i < count($conditions); $i++) { 
			if($i == 0){
				$consulta .= " ";	
			}else{
				$consulta .= " AND ";	
			}
			$consulta .= $conditions[$i];	
		}

		if($limits != ""){
			for ($i=0; $i < count($limits); $i++) { 
				if($i == 0){
					$consulta .= " ";	
				}else{
					$consulta .= ", ";	
				}
				$consulta .= $limits[$i];	
			}	
		}
		$result = mysqli_query($db, $consulta);
	
		while ($pos = mysqli_fetch_array($result)) {
			$t=[];
			for ($i=0; $i < count($select); $i++) { 
				$t[$select[$i]] = $pos[$select[$i]];
			}
			$t["id"] = $pos["id"];
			array_push($temp, $t);
		}

		$response = [
			"data" => $temp
		];
		
		return json_encode($response);
	}

	function insert($fields, $values, $table){
		$db = $this->create_con();
		$temp = [];
		$consulta = "INSERT INTO $table (";
		
		for ($i=0; $i < count($fields); $i++) { 
			if($i == 0){
				$consulta .= "";	
			}else{
				$consulta .= ",";	
			}
			$consulta .= $fields[$i];	
		}
		$consulta .= ") VALUES (";
		
		for ($i=0; $i < count($values); $i++) { 
			if($i == 0){
				$consulta .= "'";	
			}else{
				$consulta .= "','";	
			}
			$consulta .= $values[$i];	
		}
		$consulta .= "');";
		$result = mysqli_query($db, $consulta);
		$response = [
			"data" => mysqli_affected_rows($db)
		];
		
		return json_encode($response);
	}

	function update($table, $data, $where){
		$db = $this->create_con();
		$temp = [];
		$consulta = "UPDATE $table SET ";
		
		for ($i=0; $i < count($data); $i++) { 
			if($i == 0){
				$consulta .= "";	
			}else{
				$consulta .= ",";	
			}
			$consulta .= $data[$i];	
		}
		$consulta .= " WHERE ";
		
		for ($i=0; $i < count($where); $i++) { 
			if($i == 0){
				$consulta .= "";	
			}else{
				$consulta .= " AND ";	
			}
			$consulta .= $where[$i];	
		}
		$consulta .= ";";
		$result = mysqli_query($db, $consulta);
		$response = [
			"data" => mysqli_affected_rows($db)
		];
		
		return json_encode($response);
	}

	function create_con(){
		include "../config/host.php";
		
		$link = new mysqli($ip, "root", "", "biblioteca"); 
		$link->set_charset("utf8") or die();
		return $link;
	}

	function delete ($table, $where){
		$db = $this->create_con();
		$temp = [];
		$consulta = "DELETE FROM $table WHERE ";
		
		for ($i=0; $i < count($where); $i++) { 
			if($i == 0){
				$consulta .= "";	
			}else{
				$consulta .= " AND ";	
			}
			$consulta .= $where[$i];	
		}
		
		$consulta .= ";";
		$result = mysqli_query($db, $consulta);
		$response = [
			"data" => mysqli_affected_rows($db)
		];
		
		return json_encode($response);
	}
}

?>