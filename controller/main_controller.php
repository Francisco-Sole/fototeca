<?php
/*DEPENDENCIAS*/
include_once "album_controller.php";
include_once "photo_controller.php";


/*DEPENDENCIAS*/

$controller = intval($_POST["controller"]);
//1 album
//2 foto
$cmd = intval($_POST["cmd"]);
//1 get all
//...

switch ($controller) {
	//album
	case 1:
		switch ($cmd) {
			//get all
			case 1:
				$obj = new Album();
				$response = $obj->getAll();
				echo $response;
			break;
			//create new album
			case 2:
				$obj = new Album();
				$response = $obj->create($_POST["data"]);
				echo $response;
			break;
			//get info album
			case 3:
				$obj = new Album();
				$response = $obj->getInfo($_POST["data"]);
				echo $response;
			break;
			//modify name album
			case 4:
				$obj = new Album();
				$response = $obj->updateName($_POST["data"]);
				echo $response;
			break;
			//get properties album
			case 5:
				$obj = new Album();
				$response = $obj->getProperties($_POST["data"]);
				echo $response;
			break;
			//dowload album
			case 6:
				$obj = new Album();
				$response = $obj->download($_POST["data"]);
				echo $response;
			break;
			//delete album
			case 7:
				$obj = new Album();
				$response = $obj->deleteBIN($_POST["data"]);
				echo $response;
			break;
			//view photos
			case 8:
				$obj = new Album();
				$response = $obj->loadPhotos($_POST["data"]);
				echo $response;
			break;
			default:
				echo -1;
			break;
		}
	break;
	//fotos
	case 2:
		switch ($cmd) {
			//get all
			case 1:
				$obj = new Photo();
				$response = $obj->getProperties($_POST["data"]);
				echo $response;
			break;
			//download photo
			case 2:
				$obj = new Photo();
				$response = $obj->download($_POST["data"]);
				echo $response;
			break;
			//delete photo
			case 3:
				$obj = new Photo();
				$response = $obj->deleteBIN($_POST["data"]);
				echo $response;
			break;
			//decompres rar/zip
			case 4:
				$obj = new Photo();
				$response = $obj->decompres($_POST["data"]);
				echo $response;
			break;
			//asociate albums
			case 5:
				$obj = new Photo();
				$files = $obj->assignAlbum($_POST["data"]);
				$response = $obj->assignAlbumDB($_POST["data"], $files);
				echo $response;
			break;
		}
	break;
	
	default:
		echo 0;
	break;
}

?>