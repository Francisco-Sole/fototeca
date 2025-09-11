<?php
/*DEPENDENCIAS*/
include_once "DB_controller.php";
/*DEPENDENCIAS*/

class Album {
	
	//devuelve todos los registros
	function getAll(){
		$db = new DB();
		$result = $db->query(["nombre"], "album", ["activo=1"]);
		return $result;
	}

	//crea un nuevo album
	function create($name){
		//miramos si ya existe
		$db = new DB();
		$test = $db->query(["nombre"], "album", ["nombre='$name'"]);

		$obj = json_decode($test);
		if(empty($obj->data)){
			//pedimos crearlo
			$result = $db->insert(["nombre"], ["$name"], "album"); //si fuera 0 es que no ha podido insertarlo
			$obj2 = json_decode($result);
			if(intval($obj2->data) == 1){
				//si es 1 consultamos los insertado, para devolverlo
				$result = $db->query(["nombre"], "album", ["nombre='$name'"]);
				$this->create_directory($name);
			}else{
				$result = [
					"data" => "-10" 	//imposible capturar datos
				];	
				$result = json_encode($result);
			}
		}else{
			$result = [
				"data" => "-1" //ya existe
			];
			$result = json_encode($result);
		}
		return $result;
	}

	function create_directory($name){
		mkdir("../media/source/" . $name);
	}

	function update_directory($name, $old){
		rename("../media/source/" . $old, "../media/source/" . $name);
	}

	function getInfo($data){
		$count = 0;
		$total_size = 0;
		$max_size = 0;
		$name = "";
		$supported_format = array('gif','jpg','jpeg','png');
		$handle = opendir("../media/source/".$data["path"]);
		while($file = readdir($handle)){
			$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
			if (in_array($ext, $supported_format)){
				$count++;
			} else {
				continue;
			}
			$full = "../media/source/".$data["path"]."/".$file;
			$total_size += filesize($full);

			if(floatval(filesize($full)) > floatval($max_size)){
				$max_size = filesize($full);
				$name = $file;
			}
		}

		$response = [
			"count" => $count,
			"total" => $total_size,
			"higger" => $max_size,
			"name" => $name
		];

		return json_encode($response);
	}

	function updateName($data){
		$name = $data["nombre"];
		$id = $data["id"];
		$old = $data["prenombre"];

		$db = new DB();
		$test = $db->update("album",["nombre='$name'"], ["id='$id'"]);
		$obj = json_decode($test);
		
		if(intval($obj->data) == 1){
			//consultamos los actualizado, para devolverlo
			$result = $db->query(["nombre"], "album", ["id='$id'"]);
			$this->update_directory($name, $old);
		}else{
			$result = [
				//no se ha actualizado 
				"data" => "-1" 
			];
			$result = json_encode($result);
		}
		return $result;
	}

	function getProperties($data){
		$full = "../media/source/".$data["path"];
		$result = [
			"owner" => fileowner($full),
			"group" => filegroup($full),
			"last_access" => date("d-m-Y", fileatime($full)),
			"last_modified" => date("d-m-Y", filemtime($full)),
			"permisions" => sprintf('%o', fileperms($full))
		];
		$result = json_encode($result);
		return $result;
	}

	function download($data){
		$count = 0;
		// Get real path for our folder
		$rootPath = realpath("../media/source/".$data["path"]);

		// Initialize archive object
		$zip = new ZipArchive();
		$zip->open("../media/zip/".$data["path"].".zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);

		// Create recursive directory iterator
		$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);

		foreach ($files as $name => $file){
    		// Skip directories (they would be added automatically)
			if (!$file->isDir()){
        		// Get real and relative path for current file
				$filePath = $file->getRealPath();
				$relativePath = substr($filePath, strlen($rootPath) + 1);

        		// Add current file to archive
				$zip->addFile($filePath, $relativePath);
				$count++;
			}
		}
		// Zip archive will be created only after closing object
		$zip->close();
		$download = "../media/zip/".$data["path"].".zip";
		$downloadt = "media/zip/".$data["path"].".zip";

		$result = [
			"status" => $count,
			"zip_file" => $downloadt
		];
		
		// header('Content-Type: application/zip');
		// header("Content-Disposition: attachment; filename = $download");
		// header('Content-Length: ' . filesize($download));
		// header("Location: $download");

		$result = json_encode($result);
		return $result;
	}

	function deleteBIN($data){
		$this->rcopy("../media/source/" . $data["path"] , "../media/bin/" . $data["path"]);
		$this->rrmdir("../media/source/" . $data["path"]);
		$db = new DB();
		$id = $data["id"];
		$test = $db->delete("album", ["id='$id'"]);
		$obj = json_decode($test);
		if(intval($obj->data) == 1){
			$result = [
				"status" => 1,
				"idalbum" => $id
			];	
		}else{
			$result = [
				"status" => 0, //imposible borrar!
				"idalbum" => $id
			];
		}
		
		$result = json_encode($result);
		return $result;
		//falta hace lo referente a la base de datos FOTOS
	}

	// Function to remove folders and files 
	function rrmdir($dir) {
		if (is_dir($dir)) {
			$files = scandir($dir);
			foreach ($files as $file) {
				if ($file != "." && $file != ".."){ 
					$this->rrmdir("$dir/$file");
				}
			}
			rmdir($dir);
		}
		else if (file_exists($dir)) {
			unlink($dir);
		}
	}

    // Function to Copy folders and files       
	function rcopy($src, $dst) {
		if (file_exists ($dst)){
			$this->rrmdir ($dst);
		}
		if (is_dir ($src)) {
			mkdir ($dst);
			$files = scandir ($src);
			foreach ($files as $file){
				if ($file != "." && $file != ".."){
					$this->rcopy ("$src/$file", "$dst/$file");
				}
			}
		} else if (file_exists($src)){
			copy($src, $dst);			
		}
	}

	function loadPhotos($data){
		$id = $data["id"];
		$db = new DB();
		$t = $db->query(["nombre"], "foto", ["id_album='$id'"]);
		$result = [
			"photos"=> json_decode($t),
			"album" => $data["path"]
		];
		$result = json_encode($result);
		return $result;
	}
}


?>