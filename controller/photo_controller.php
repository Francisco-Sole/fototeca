<?php
/*DEPENDENCIAS*/
include_once "DB_controller.php";
/*DEPENDENCIAS*/

class Photo {

	function getProperties($data){
		$t = $data["path"];
		$pattern = '/\./i';
		$full = preg_replace($pattern, '..', $t, 1);
		
		$properties = getimagesize($full);
		$result = [
			"height" => $properties[1],
			"width" => $properties[0],
			"bits" => $properties["bits"],
			"mime" => $properties["mime"]
		];
		$result = json_encode($result);
		return $result;
	}

	function download($data){
		$album = preg_split("/\//", $data["path"])[0];
		$nombre = preg_split("/\//", $data["path"])[1];
		$nombre = preg_split("/\./", $nombre)[0];

		// Initialize archive object
		$zip = new ZipArchive();
		$zip->open("../media/zip/". $album . "-". $nombre .".zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);

     	// Add current file to archive
		$zip->addFile("../media/source/".$data["path"],basename("../media/source/".$data["path"]));

		// Zip archive will be created only after closing object
		$zip->close();
		$download = "../media/zip/".$data["path"].".zip";
		$downloadt = "media/zip/". $album . "-". $nombre .".zip";

		$result = [
			"status" => 1,
			"path_file" => $downloadt
		];

		$result = json_encode($result);
		return $result;
	}

	function deleteBIN($data){
		$t_path = preg_split("/\.\/media\/source\//", $data["path"])[1];
		$album = preg_split("/\//", $t_path)[0];
		$nombre_entero = preg_split("/\//", $t_path)[1];
		$nombre = preg_split("/\./", $nombre_entero)[0];
		$extension = preg_split("/\./", $nombre_entero)[1];

		if (!file_exists("../media/bin/" . $album)) {
			mkdir("../media/bin/" . $album, 0777, true);
		}
		copy("../media/source/" . $album . "/" . $nombre_entero , "../media/bin/" . $album . "/" . $nombre_entero);
		unlink("../media/source/" . $album . "/" . $nombre_entero);
		
		$db = new DB();
		$id = $data["id"];
		$test = $db->delete("foto", ["id='$id'"]);
		$obj = json_decode($test);
		if(intval($obj->data) == 1){
			$result = [
				"status" => 1,
				"idimagen" => $id
			];	
		}else{
			$result = [
				"status" => 0, //imposible borrar!
				"idimagen" => $id
			];
		}
		
		$result = json_encode($result);
		return $result;
		//falta hace lo referente a la base de datos FOTOS
	}

	function upload($data) {
		var_dump($data);
	}

	//descomprime un fichero rar o zip
	function decompres($data){
		$type = $data["type"];
		$name = $data["name"];
		$path_destino = "../media/uploads/";
		$characters = '0123456789ABCDEF';
		$charactersLength = strlen($characters);
		$nombre_dir = date("Ymd-his-");
		for ($i = 0; $i < 10; $i++) {
			$nombre_dir .= $characters[rand(0, $charactersLength - 1)];
		}
		$path_destino .= $nombre_dir;

		if (mb_strtoupper($type) == "RAR") {
			unlink($name);

			$result = [
				"status" => -2,
				"file" => $name
			];
		}else if (mb_strtoupper($type) == "ZIP") {
			$zip = new ZipArchive();
			$zip->open($name, ZipArchive::CREATE);
			$zip->extractTo($path_destino);
			$zip->close();
			unlink($name);

			$result = [
				"status" => 0,
				"file" => $path_destino
			];

		}else{
			unlink($name);

			$result = [
				"status" => -1,
				"file" => $name
			];
		}

		return json_encode($result);
	}


	function assignAlbum($data){

		$is_folder = $data["is_folder"];
		$name = $data["name"];
		$albums_name = $data["albums_name"];
		$nombres = [];

		//determinar si es una carpeta (unzip)
		if(intval($is_folder) == 1){
			$count = 0;
			$files = [];
			$supported_format = array('gif','jpg','jpeg','png');
			$handle = opendir($name);
			while($file = readdir($handle)){
				$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
				if (in_array($ext, $supported_format)){
					$count++;
					$nombre_archivo = $name ."/". $file;
					array_push($nombres, $file);
					array_push($files, $file);
				} else {
					continue;
				}
			}

			for ($x=0; $x < count($files); $x++) {
				for ($i=0; $i < count($albums_name) ; $i++) { 
					copy($name ."/". $files[$x] , "../media/source/" . $albums_name[$i] . "/" . $files[$x]);
				}
			}
			$this->delTree($name);
		}else{
			//1 mover el archivo al album
			for ($x=0; $x < count($name) ; $x++) {
				$nombre_archivo = preg_split("/\//", $name[$x])[count(preg_split("/\//", $name[$x]))-1];
				array_push($nombres, $nombre_archivo);
				for ($i=0; $i < count($albums_name) ; $i++) { 
					copy($name[$x] , "../media/source/" . $albums_name[$i] . "/" . $nombre_archivo);
				}
			}

			//2 borrar de la raiz
			for ($x=0; $x < count($name) ; $x++) {
				unlink($name[$x]);
			}
		}

		return $nombres;
		//3 asignar la foto en la BD
	}

	function assignAlbumDB($data, $files){
		$albums = $data["albums"];
		
		for ($x=0; $x < count($albums); $x++) { 
			for ($i=0; $i < count($files); $i++) { 
				$db = new DB();
				$test = $db->query(["nombre"], "foto", ["nombre='" . $files[$i] . "'", "id_album='" .  $albums[$x] . "'"]);
				$obj = json_decode($test);
				//Si no existe el registro lo meto, si ya existe paso (para evitar duplicados).
				if(empty($obj->data)){
					$db->insert(["nombre", "id_album"], [$files[$i], $albums[$x]], "foto");
				}
			}
		}

		$result = [
			"status" => 0,
		];

		return json_encode($result);
	}

	function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
		}
		rmdir($dir);
	}
}