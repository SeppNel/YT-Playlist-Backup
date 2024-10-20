<?php
//include "lib/json.php"; //Custom JSON library because my server ¯\_(ツ)_/¯
//$json = new Services_JSON();
require_once("lib/json_decode.php"); //This library is WAY faster, but it's not perfect

if (isset($_GET["playlistId"])) {
	$playlistId = $_GET["playlistId"];
}
else{
	exit("Error");
}

//const playlistId = "PLfzsc9sP1kLDeFsKX7E_pcul3hH_WAoMc"; //Study
//$playlistId = "PLfzsc9sP1kLBtMN35nA4SY51iWAJoLJp7"; //Trabajo
$apiKey = "YOUR_API_KEY_HERE";

$response = Array();
$responseDec = Array();

$titles = Array();
$channels = Array();
$urls = Array();
$thumb = Array();
$deleted = false;


function saveBdAsXml($playlistId){
	global $titles, $channels, $urls, $thumb;
	$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Playlist></Playlist>");

	for ($i = 0; $i < count($titles); $i++) {
	    $track = $xml->addChild('video');
	    $track->title = $titles[$i];
	    $track->channel = $channels[$i];
	    $track->Id = $urls[$i];
	    $track->thumb = $thumb[$i];
	}
	$xml->saveXML("../bd/playlists/" . strval($playlistId) . ".xml");
}



//Again, shell_exec because my server ¯\_(ツ)_/¯
array_push($response, shell_exec('curl "https://youtube.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId=' . $playlistId . '&key=' . $apiKey . '"'));
//$resObject = $json->decode($response[0]);
$resObject = json_decode($response[0]);
array_push($responseDec, $resObject);
$nextPage = $resObject->nextPageToken;

if ($resObject->error->code == "404") {
	exit("Lista de reproducción inexistente. (¿Es pública la lista?)");
}
else if (isset($resObject->error->code)) {
	exit("Hay algún problema con esa ID");
}


$i = 1;
while (isset($nextPage)) {
  array_push($response, shell_exec('curl "https://youtube.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&pageToken=' . $nextPage . '&playlistId=' . $playlistId . '&key=' . $apiKey . '"'));
  $resObject = json_decode($response[$i]);
  array_push($responseDec, $resObject);
  $nextPage = $resObject->nextPageToken;
  $i++;
}


//Loop through all items in all pages
foreach ($responseDec as $key => $currentPage) {
	$items = $currentPage->items;
	foreach ($items as $key => $item) {
		array_push($titles, $item->snippet->title);
		array_push($channels, $item->snippet->videoOwnerChannelTitle);
		array_push($urls, $item->snippet->resourceId->videoId);
		if (isset($item->snippet->thumbnails->maxres)) {
			array_push($thumb, $item->snippet->thumbnails->maxres->url);
		}
		else if (isset($item->snippet->thumbnails->standard)) {
			array_push($thumb, $item->snippet->thumbnails->standard->url);
		}
		else{
			array_push($thumb, $item->snippet->thumbnails->high->url);
		}
		
	}
}

if (in_array("", $channels)) {
	$deleted = true;
}
else{
	saveBdAsXml($playlistId);
}

$urlsBD = [];
$bdExists = file_exists("../bd/playlists/" . $playlistId . ".xml");
if ($deleted && $bdExists) {
	$bd = simplexml_load_file("../bd/playlists/" . $playlistId . ".xml");
	foreach ($bd->video as $key => $video) {
		array_push($urlsBD, $video->Id);
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Playlist - PerServer</title>
	<meta charset="utf-8">
	<script src="/.resources/js/notify.js"></script>
	<style>
		body{
			background-color: #393939;
			color: black;
			font-family: Arial;
			font-size: 1.1em;
			margin: 0;
		}
		table{
			width: 80%;
			margin: auto;
			background-color: white;
		}
		table img {
			height: 5em;
			margin: auto;
			display: block;
			margin-top: 2px;
		}
		th{
			background-color: #353535;
			padding: 7px 0 7px 0;
			color: white;
			font-size: 1.2em;
		}
		th:nth-child(1){
			padding-left: 7px;
			padding-right: 7px;
		}
		tr:nth-child(even) {
			background: #f7f7f7;
		}
		td:nth-child(3),td:nth-child(4),td:nth-child(5){
			padding-left: 10px;
		}

		#under{
			width: 80%;
			margin: auto;
			display: flex;
			padding: 20px 0 20px 0;
			justify-content: center;
		}

		button{
			margin-right: 10px;
			min-width: 10em;
		}
		
	</style>
</head>
<body>
	<table border="0">
		<tr>
			<th>Id</th>
			<th>Imágen</th>
			<th>Título</th>
			<th>Canal</th>
			<th>Id de video</th>
		</tr>
	<?php
	for ($i = 0; $i < count($titles); $i++) {
		if ($channels[$i] == "") {
			$deleIndex = array_search($urls[$i], $urlsBD);
			echo "<tr style='background-color: #ff2929;'>";
		}
		else{
			echo "<tr>";
		}

		echo "<td style='text-align: center;'>" . ($i + 1) . "</td>";
		echo "<td><a href='https://www.youtube.com/watch?v=$urls[$i]' target='_blank'><img src='" . $thumb[$i] . "'></a></td>";

		if ($channels[$i] == "" && $deleIndex !== false) {
			echo "<td>" . $titles[$i] . " - Original: " . $bd->video[$deleIndex]->title . "</td>";
		}
		else{
			echo "<td>" . $titles[$i] . "</td>";
		}
		
		echo "<td>" . $channels[$i] . "</td>";
		echo "<td>" . $urls[$i] . "</td>";
		echo "</tr>";
	}
	?>
	</table>
	<div id="under">
		<button onclick="window.location.href = 'playlist_bd.php?playlistId=<?php echo $playlistId; ?>'">Ver datos guardados</button>
		<button onclick="window.location.href = '../bd/playlists/<?php echo $playlistId; ?>.xml'">Descargar datos guardados</button>
		<button onclick="window.location.href = 'playlist_del.php?playlistId=<?php echo $playlistId; ?>'">Borrar datos guardados</button>
	</div>

	<?php 
	if (!$deleted) {
		echo '<script>sendNotification("Datos Guardados :)");</script>';
	}
	else{
		echo '<script>sendNotification("Videos Eliminados :(", "red");</script>';
	}
	?>
</body>
</html>

