<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width" />
<script src="http://code.jquery.com/jquery-1.8.3.js"></script>
<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css"/>
<script>
$(function() {
$( "#slider" ).slider({min:0, max:100, step:1, value:100, range:"min", change:changeVolume,});
});
</script>
<title>daap-play sample</title>
</head>
<body>

<div id="playlist">
<select id="songs" size=12 onChange="play();">
<option value="0">dummy</option>
</select>
</div>

<div id="playerbox" style="margin: 4px">
<div id="player" style="float: left; margin: 4px;"><audio controls id="audio_player"></audio></div>
<div id="slider" style="float: left; width: 200px; margin: 8px;"></div>
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

var lastid = -1;

setup();

function setup()
{
	var player = document.getElementById('audio_player');
	player.addEventListener("ended", playend, false);

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
}

function play()
{
	var player = document.getElementById('audio_player');
	var songsList = document.getElementById('songs');
	if ( ! player.ended )
	{
		if ( lastid == songsList.selectedIndex )
		{
			return;
		}
	}
	lastid = songsList.selectedIndex;

	var id = songsList.options[ lastid ].value;
	var url = daap + id;
	player.src = url;
	player.play();
}

function playend()
{
	var songsList = document.getElementById('songs');
	if ( songsList.selectedIndex < songsList.length )
	{
		songsList.selectedIndex++;
	}
	else
	{
		songsList.selectedIndex = 0;
	}
	play();
}

function changeVolume(event, ui)
{
	var player = document.getElementById('audio_player');
	player.volume = ui.value / 100.0;
}

</script>

</body>
</html>
