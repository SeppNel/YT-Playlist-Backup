<?php
if (isset($_GET["playlistId"])) {
	$playlistId = $_GET["playlistId"];
}
else{
	exit("Error");
}
if (file_exists("../bd/playlists/" . $playlistId . ".xml")) {
	$bd = simplexml_load_file("../bd/playlists/" . $playlistId . ".xml");
}
else{
	exit("No hay datos guardados. Borra los videos no disponibles y visita la página de nuevo para guardarlos.");
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Datos Guardados - PerServer</title>
	<meta charset="utf-8">
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
	$i = 0;
	foreach ($bd->video as $key => $video) {
		echo "<tr>";
		echo "<td style='text-align: center;'>" . ($i + 1) . "</td>";
		echo "<td><a href='https://www.youtube.com/watch?v=$video->Id' target='_blank'><img src='" . $video->thumb . "'></a></td>";
		echo "<td>" . $video->title . "</td>";
		echo "<td>" . $video->channel . "</td>";
		echo "<td>" . $video->Id . "</td>";
		echo "</tr>";
		$i++;
	}
	?>
	</table>
	<div id="under">
		<button onclick="window.history.back();">Volver</button>
		<button onclick="window.location.href = '../bd/playlists/<?php echo $playlistId; ?>.xml'">Descargar datos guardados</button>
		<button onclick="window.location.href = 'playlist_del.php?playlistId=<?php echo $playlistId; ?>'">Borrar datos guardados</button>
	</div>
</body>
</html>
