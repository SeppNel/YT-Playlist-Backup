<?php
if (isset($_GET["playlistId"])) {
	$playlistId = $_GET["playlistId"];
}
else{
	exit("Error");
}
if (file_exists("../bd/playlists/" . $playlistId . ".xml")) {
	unlink("../bd/playlists/" . $playlistId . ".xml");
}
else{
	exit("No hay datos guardados.");
}
header('Location: /playlist_backups.html');
?>
