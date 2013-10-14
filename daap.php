<?php

// Crossfire 0.6b (http://sourceforge.net/p/ffmcrossfire/news/)
require_once('php_dmap.php');

class Daap
{
	private $dmap;

	function __construct()
	{
		$this->dmap = new dmap_document();
	}

	function __destruct()
	{
		unset( $this->dmap );
	}

	//-----------------------------------------------------------------------------
	// http://localhost:3689/databases/1/
	//-----------------------------------------------------------------------------
	private function getBaseUrl( $server = 'http://localhost:3689', $database = 1 )
	{
		// serverinfoからデータベース数
		$data = file_get_contents( $server . '/server-info');
		$this->dmap->parse_binairydata($data);
		$maxDatabase = $this->getInfo($this->dmap->elements, 'msdc');

		if ( $database < 1 ) $database = 1;
		if ( $database > $maxDatabase ) $database = $maxDatabase;

		return "$server/databases/$database";
	}

	//-----------------------------------------------------------------------------
	// http://localhost:3689/databases/1/containers/1/items
	//-----------------------------------------------------------------------------
	private function getListUrl( $server = 'http://localhost:3689', $database = 1, $container = 1 )
	{
		// データベースまで
		$base = $this->getBaseUrl( $server, $database );

		// データベースからコンテナ数
		$data = file_get_contents( $base . '/containers');
		$this->dmap->parse_binairydata($data);
		$maxContainer = $this->getInfo($this->dmap->elements, 'mrco'); // mtco かもしれない

		if ( $container < 1 ) $container = 1;
		if ( $container > $maxContainer ) $container = $maxContainer;

		unset($dmap_doc);

		return "$base/containers/$container/items";
	}

	//=========================================================================
	// http://localhost:3689/databases/1/items
	//=========================================================================
	public function getSongUrl( $server = 'http://localhost:3689', $database = 1 )
	{
		// データベースまで
		$base = $this->getBaseUrl( $server, $database );

		return "$base/items";
	}

	//=========================================================================
	//=========================================================================
	public function getSongList( $server = 'http://localhost:3689', $database = 1, $container = 1  )
	{
		// コンテナ内からアイテム情報
		$url = $this->getListUrl($server, $database, $container);
		$options = '?meta=daap.songartist,daap.songalbum,daap.songtracknumber,dmap.itemid,dmap.itemname,daap.songformat,daap.songtime';
		$data = file_get_contents( $url . $options);
		$this->dmap->parse_binairydata($data);
		$list = $this->getSongs($this->dmap->elements);

		// ソート
		$asar = array();
		$asal = array();
		$astn = array();
		$minm = array();
		foreach( $list as $info )
		{
			$asar[] = $info['asar'];
			$asal[] = $info['asal'];
			$astn[] = $info['astn'];
			$minm[] = $info['minm'];
		}
		array_multisort(
			$asar, SORT_ASC, SORT_STRING,
			$asal, SORT_ASC, SORT_STRING,
			$astn, SORT_ASC, SORT_NUMERIC,
			$minm, SORT_ASC, SORT_STRING,
			$list );

		return $list;
	}

	//-----------------------------------------------------------------------------
	//-----------------------------------------------------------------------------
	private function getInfo( $objects, $key )
	{
		$result = array();
		foreach( $objects as $object )
		{
			if ( $object->contentcodesnumber == $key )
			{
				return $object->value;
			}
			$ret = $this->getInfo( $object->children, $key );
			if ( ! empty( $ret ) )
			{
				return $ret;
			}
		}
		return NULL;
	}

	//-----------------------------------------------------------------------------
	//-----------------------------------------------------------------------------
	private function getSongs( $objects )
	{
		foreach( $objects as $object )
		{
			switch( $object->contentcodesnumber )
			{
				case 'asar': $asar = $object->value; break; // アーティスト
				case 'asal': $asal = $object->value; break; // アルバム名
				case 'astn': $astn = $object->value; break; // トラック番号
				case 'astm': $astm = $object->value; break; // 時間
				case 'asfm': $asfm = $object->value; break; // ファイルフォーマット
				case 'miid': $miid = $object->value; break; // データベースインデックス
				case 'minm': $minm = $object->value; break; // 曲名
			}
			$ret = $this->getSongs( $object->children );
			if ( ! empty( $ret ) )
			{
				$result[] = $ret;
			}
		}

		if ( isset($miid) && isset($minm) && isset( $asfm ) )
		{
			return array(
				'asar' => isset( $asar ) ? $asar : 'no artist',
				'asal' => isset( $asal ) ? $asal : 'no album',
				'astn' => isset( $astn ) ? $astn : 0,
				'astm' => isset( $astm ) ? $astm : 0,
				'asfm' => $asfm,
				'miid' => $miid,
				'minm' => $minm );
		}

		if ( isset( $result ) )
		{
			if ( 1 == count( $result ) )
			{
				return reset($result);
			}
			return $result;
		}

		return NULL;
	}
}
