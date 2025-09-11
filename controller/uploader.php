<?php 

$cadena = $_POST["img"];

$tipo = $_POST["tipo"];

$characters = '0123456789ABCDEF';
$charactersLength = strlen($characters);
$nombre = date("Ymd-his-");
for ($i = 0; $i < 10; $i++) {
	$nombre .= $characters[rand(0, $charactersLength - 1)];
}

file_put_contents("../media/uploads/" . $nombre . "." . $tipo, base64_decode($cadena));

echo "../media/uploads/" . $nombre . "." . $tipo;