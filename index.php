<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width" />
<script src="http://code.jquery.com/jquery-1.8.3.js"></script>
<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css"/>

<style type="text/css">
select.listbox {
font-size: 12pt;
</style>

<script>
$(function() {
$( "#slider" ).slider({min:0, max:100, step:1, value:100, range:"min", change:changeVolume,});
});
</script>

<title>daap-play sample</title>
</head>
<body>

<div id="filterlist">
<select class="listbox" id="artists" size=12 style="width: 45%" onChange="changeArtist();">
<option value="0">initialize failed</option>
</select>
<select class="listbox" id="albums" size=12 style="width: 45%" onChange="changeAlbum();">
<option value="0">initialize failed</option>
</select>

<div id="playlist">
<select class="listbox" id="songs" size=12 style="width: 90%" onChange="play();">
<option value="0">initialize failed</option>
</select>
</div>

<div id="playerbox" style="margin: 4px">
<div id="player" style="float: left; margin: 4px;"><audio controls id="audio_player"></audio></div>
<div id="slider" style="float: left; width: 200px; margin: 8px;"></div>
</div>


<script type="text/javascript">
<?php
require_once('config.php');
require_once('daap.php');

$host = 'http://' . $server . ':' . $port;
$daap = new Daap();

$url = $daap->getSongUrl( $host );
print "var daap = \"$url/\";\n";

$list = $daap->getSongList( $host );
print( 'var item = ' . json_encode($list) . ";\n" );
?>

var lastArtist = -1;
var lastAlbum  = -1;
var lastSong   = -1;

setup();

function setup()
{
	var player = document.getElementById('audio_player');
	player.addEventListener("ended", playend, false);

	makeArtistList();
	makeAlbumList();
	makePlayList();
}

function makeArtistList()
{
	var selectList = document.getElementById('artists');
	var keyList = getKeys('asar');
	var listIndex = 0;
	selectList.length = keyList.length + 1;
	selectList.options[ listIndex ].value = "";
	selectList.options[ listIndex ].text  = "all";
	listIndex++;
	for ( var i = 0; i < keyList.length; i++ )
	{
		var name = keyList[i];
		selectList.options[ listIndex ].value = name;
		selectList.options[ listIndex ].text  = name;
		listIndex++;
	}
	selectList.selectedIndex = 0;
}

function makeAlbumList( artist )
{
	var selectList = document.getElementById('albums');
	var keyList;
	var listIndex = 0;
	if ( artist )
	{
		keyList = getFilterdKeys('asal', 'asar', artist );
		selectList.length = keyList.length;
	}
	else
	{
		keyList = getKeys('asal');
		selectList.length = keyList.length + 1;
		selectList.options[ listIndex ].value = "";
		selectList.options[ listIndex ].text  = "all";
		listIndex++;
	}

	for ( var i = 0; i < keyList.length; i++ )
	{
		var name = keyList[i];
		selectList.options[ listIndex ].value = name;
		selectList.options[ listIndex ].text  = name;
		listIndex++;
	}
	selectList.selectedIndex = 0;
}

function getKeys( keyName )
{
	var check = {};
	var unique = [];
	for ( var i = 0; i < item.length; i++ )
	{
		var value = item[i][keyName];
		if ( ! ( value in check ) )
		{
			check[value] = 1;
			unique.push(value);
		}
	}
	return unique;
}

function getFilterdKeys( keyName, keyFilter, valueFilter )
{
	var check = {};
	var unique = [];
	for ( var i = 0; i < item.length; i++ )
	{
		if ( item[i][keyFilter] == valueFilter )
		{
			var value = item[i][keyName];
			if ( ! ( value in check ) )
			{
				check[value] = 1;
				unique.push(value);
			}
		}
	}
	return unique;
}

function changeArtist()
{
	var selectList = document.getElementById( 'artists' );
	var artist = selectList.options[ selectList.selectedIndex ].value;

	makeAlbumList( artist );
	makePlayList();
}

function changeAlbum()
{
	makePlayList();
}

function makePlayList()
{
	var albumRegExp  = new RegExp( getFilter( 'albums' ) );

	var songsList = document.getElementById('songs');
	var selectedSongId = "";
	if ( songsList.selectedIndex >= 0 )
	{
		selectedSongId = songsList.options[ songsList.selectedIndex ].value;
	}

	songsList.length = 0;
	lastSong = -1;
	for ( var i = 0; i < item.length; i++ )
	{
		var al = albumRegExp.test( item[ i ].asal );
		if ( al )
		{
			var currentListIndex = songsList.length;
			var songIndex = item[ i ].miid + "." + item[ i ].asfm;
			var songName  = ( "0" + item[ i ].astn ).slice(-2) + " : " + item[ i ].minm;

			songsList.options[ currentListIndex ] = new Option( songName, songIndex );
		}
	}

	var albumList = document.getElementById( 'albums' );
	var album = albumList.options[ albumList.selectedIndex ].value;
	if ( album )
	{
		sortSelect( songsList );
	}

	for ( var i = 0; i < songsList.length; i++ )
	{
		if ( selectedSongId == songsList.options[i].value )
		{
			songsList.selectedIndex = i;
			lastSong = i;
		}
	}

}

function sortSelect(selElem)
{
	var tmpAry = new Array();
	for (var i=0;i<selElem.options.length;i++)
	{
		tmpAry[i] = new Array();
		tmpAry[i][0] = selElem.options[i].text;
		tmpAry[i][1] = selElem.options[i].value;
	}
	tmpAry.sort();
	while (selElem.options.length > 0)
	{
		selElem.options[0] = null;
	}
	for (var i=0;i<tmpAry.length;i++)
	{
		var op = new Option(tmpAry[i][0], tmpAry[i][1]);
		selElem.options[i] = op;
	}
	return;
}

function getFilter( elementId )
{
	var selectList = document.getElementById(elementId);
	var name = selectList.options[ selectList.selectedIndex ].value;
	if ( name == "" )
	{
		return ".*";
	}
	return name.replace(/\W/g,'\\$&');
}

function play()
{
	var player = document.getElementById('audio_player');
	var songsList = document.getElementById('songs');
	if ( songsList.selectedIndex >= 0 )
	{
		if ( ! player.ended )
		{
			if ( lastSong == songsList.selectedIndex )
			{
				return;
			}
		}
		lastSong = songsList.selectedIndex;

		var id = songsList.options[ lastSong ].value;
		var url = daap + id;
		player.src = url;
		player.play();
	}
}

function playend()
{
	var songsList = document.getElementById('songs');
	if ( songsList.selectedIndex < ( songsList.length - 1 ) )
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
