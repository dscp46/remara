<?php

class RepeaterDB
{
	private $db;

	function __construct( $config)
	{
		$this->db = new \PDO( 
			$config['db']['dsn'], 
			$config['db']['user'], 
			$config['db']['password']
		);
		$this->db->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}

	public function list()
	{
		$q = $this->db->query( "SELECT * FROM `member`;");
		return $q->fetchAll( \PDO::FETCH_ASSOC);
	}
	
	public function get( int $id)
	{
		$stmt = $this->db->prepare( "SELECT * FROM `member` WHERE `id` = :id;");
		$stmt->bindParam( 'id', $id, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch( \PDO::FETCH_ASSOC);
	}

	public function getIdFromQrz( string $qrz)
	{
		$stmt = $this->db->prepare( "SELECT `id` FROM `member` WHERE `qrz` = :qrz;");
		$stmt->bindParam( 'qrz', trim($qrz), \PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch( \PDO::FETCH_ASSOC);
		if( $result === false)
			return null;

		return $result['id'];
	}
	
	public function getFrequencies( int $id)
	{
		$stmt = $this->db->prepare( "SELECT * FROM `frequencies` WHERE `member` = :id;");
		$stmt->bindParam( 'id', $id, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll( \PDO::FETCH_ASSOC);
	}

	public function delete( int $id)
	{
		$stmt = $this->db->prepare( "DELETE FROM `member` WHERE `id` = :id;");
		$stmt->bindParam( 'id', $id, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->rowCount();
	}
	
	public function deleteFrequencies( int $id)
	{
		$stmt = $this->db->prepare( "DELETE FROM `frequencies` WHERE `member` = :id;");
		$stmt->bindParam( 'id', $id, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->rowCount();
	}

	public function getRooms()
	{
		$q = $this->db->query( "SELECT * FROM `conference`;");
		return $q->fetchAll( \PDO::FETCH_ASSOC);
	}

	public function add( array $properties, array $frequencies = array())
	{
		function pp_val( $val)
		{
			if( empty(trim($val)) )
				return null;
			return trim($val);
		}

		// Create member
		$stmt = $this->db->prepare( "INSERT INTO `member` (`qrz`, `svx_user`, `type`, `dep`, `lat`, `lon`, `asl`, `height`, `comment`) VALUES ( :qrz, :svx_user, :type, :dep, :lat, :lon, :asl, :height, :comment);");
		$stmt->bindParam( 'qrz'     ,       trim($properties['qrz']), \PDO::PARAM_STR);
		$stmt->bindParam( 'svx_user',  trim($properties['svx_user']), \PDO::PARAM_STR);
		$stmt->bindParam( 'type'    ,      trim($properties['type']), \PDO::PARAM_STR);
		$stmt->bindParam( 'dep'     ,     pp_val($properties['dep']), \PDO::PARAM_STR);
		$stmt->bindParam( 'lat'     ,     pp_val($properties['lat']), \PDO::PARAM_STR);
		$stmt->bindParam( 'lon'     ,     pp_val($properties['lon']), \PDO::PARAM_STR);
		$stmt->bindParam( 'asl'     ,     pp_val($properties['asl']), \PDO::PARAM_INT);
		$stmt->bindParam( 'height'  ,  pp_val($properties['height']), \PDO::PARAM_INT);
		$stmt->bindParam( 'comment' , pp_val($properties['comment']), \PDO::PARAM_STR);
		$stmt->execute();

		// Fetch ID from QRZ
		$id = $this->getIdFromQrz( $properties['qrz']);

		// Insert Frequencies
		foreach( $frequencies as $f )
		{
			$stmt = $this->db->prepare("INSERT INTO `frequencies` (`member`, `down`, `dup`, `ctcss`, `power`, `mode`) VALUES ( :member, :down, :dup, :ctcss, :power, :mode);");
			$stmt->bindParam( 'member'  ,                 $id, \PDO::PARAM_INT);
			$stmt->bindParam( 'down'    ,  pp_val($f['down']), \PDO::PARAM_STR);
			$stmt->bindParam( 'dup'     ,   pp_val($f['dup']), \PDO::PARAM_STR);
			$stmt->bindParam( 'ctcss'   , pp_val($f['ctcss']), \PDO::PARAM_STR);
			$stmt->bindParam( 'power'   , pp_val($f['power']), \PDO::PARAM_INT);
			$stmt->bindParam( 'mode'    ,  pp_val($f['mode']), \PDO::PARAM_STR);
			$stmt->execute();
		}

		return true;
	}

	public function update( array $properties, array|null $frequencies = null)
	{
		function pp_val( $val)
		{
			if( $val == null )
				return null;
			if( trim($val) == '' )
				return null;
			return trim($val);
		}

		// Create member
		$stmt = $this->db->prepare( "UPDATE `member` SET `qrz` = :qrz, `svx_user` = :svx_user, `type` = :type, `dep` = :dep, `lat` = :lat, `lon` = :lon, `asl` = :asl, `height` = :height, `comment` = :comment WHERE `id` = :id;");
		$stmt->bindParam( 'id'      ,              $properties['id'], \PDO::PARAM_INT);
		$stmt->bindParam( 'qrz'     ,       trim($properties['qrz']), \PDO::PARAM_STR);
		$stmt->bindParam( 'svx_user',  trim($properties['svx_user']), \PDO::PARAM_STR);
		$stmt->bindParam( 'type'    ,      trim($properties['type']), \PDO::PARAM_STR);
		$stmt->bindParam( 'dep'     ,     pp_val($properties['dep']), \PDO::PARAM_STR);
		$stmt->bindParam( 'lat'     ,     pp_val($properties['lat']), \PDO::PARAM_STR);
		$stmt->bindParam( 'lon'     ,     pp_val($properties['lon']), \PDO::PARAM_STR);
		$stmt->bindParam( 'asl'     ,     pp_val($properties['asl']), \PDO::PARAM_INT);
		$stmt->bindParam( 'height'  ,  pp_val($properties['height']), \PDO::PARAM_INT);
		$stmt->bindParam( 'comment' , pp_val($properties['comment']), \PDO::PARAM_STR);
		$stmt->execute();

		if( $frequencies === null )
			return true;

		// Delete Frequencies
		$this->deleteFrequencies( $properties['id']);

		// Insert Frequencies
		foreach( $frequencies as $f )
		{
			$stmt = $this->db->prepare("INSERT INTO `frequencies` (`member`, `down`, `dup`, `ctcss`, `power`, `mode`) VALUES ( :member, :down, :dup, :ctcss, :power, :mode);");
			$stmt->bindParam( 'member'  ,   $properties['id'], \PDO::PARAM_INT);
			$stmt->bindParam( 'down'    ,  pp_val($f['down']), \PDO::PARAM_STR);
			$stmt->bindParam( 'dup'     ,   pp_val($f['dup']), \PDO::PARAM_STR);
			$stmt->bindParam( 'ctcss'   , pp_val($f['ctcss']), \PDO::PARAM_STR);
			$stmt->bindParam( 'power'   , pp_val($f['power']), \PDO::PARAM_INT);
			$stmt->bindParam( 'mode'    ,  pp_val($f['mode']), \PDO::PARAM_STR);
			$stmt->execute();
		}

		return true;
	}
}

