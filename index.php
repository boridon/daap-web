<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width" />
<title>daap-play sample</title>
</head>
<body>

<div id="playlist">
<select id="songs" size=12 onChange="play();">
	<option value="0">dummy</option>
</select>
</div>

<div id="player">
<audio controls id="audio_player">
</audio>
</div>

<script type="text/javascript">
<?php
require_once('daap.php');
$host = 'http://' . $_SERVER['SERVER_NAME'] . ':3689';
$daap = new Daap();

$url = $daap->getSongUrl( $host );
print "var daap = \"$url/\";\n";

$list = $daap->getSonglist( $host );
print( 'var item = ' . json_encode($list) . ";\n" );
?>

var songsList = document.getElementById('songs');
songsList.length = item.length;
for ( var i = 0; i < item.length; i++ )
{
	songsList.options[ i ].value = item[ i ].miid + "." + item[ i ].asfm;
	songsList.options[ i ].text  =
		( item[ i ].asar ).substr( 0, 20 ) + " : " +
		( item[ i ].asal ).substr( 0, 20 ) + " : " +
		item[ i ].astn + " : " +
		( item[ i ].minm ).substr( 0, 20 );
}

function play()
{
	var songsList = document.getElementById('songs');
	var id = songsList.options[ songsList.selectedIndex ].value;
	var url = daap + id;
	var player = document.getElementById('audio_player');
	player.src = url;
	player.play();
}
</script>

</body>
</html>
