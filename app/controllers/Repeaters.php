<?php

class Repeaters extends \DefaultCtlr
{
	private $rdb;

	function __construct()
	{
		$f3=Base::instance();
		$f3->set( 'cur_module', 'repeaters');
		$this->rdb = new RepeaterDB( $f3->get('user_config'));
	}

	/* ***** Web Interface ***** */

	// GET /repeaters
	public function list( $f3)
	{
		$f3->set( 'repeaters', $this->rdb->list());
		$f3->set( 'content', 'repeaters-list.tpl.php');
		echo View::instance()->render( 'layout.php');
	}

	
	// GET /repeaters/@id
	public function show( $f3, $params)
	{
		if( !isset( $params['id']) || !ctype_digit( $params['id']))
			$f3->error( 400, 'Bad Request');
		
		$id = $params['id'];

		$rpt = $this->rdb->get( $id);
		if( $rpt === false )
			$f3->error( 404);

		$f3->set( 'repeater', $rpt);

		if( count( $f3->get('repeater')) == 0)
			$f3->error( 404);

		$f3->set( 'frequencies', $this->rdb->getFrequencies( $id));

		$f3->set( 'content', 'repeaters-edit.tpl.php');
		echo View::instance()->render( 'layout.php');
	}
	
	// POST /repeaters/@id
	public function update( $f3, $params)
	{
		// Filter requests that don't pass the CSRF token check
		if( !$f3->exists('SESSION.csrf') || !$f3->exists('POST.token') || $f3->get( 'SESSION.csrf') != $f3->get('POST.token') )
			$f3->error( 403);
	
		// Detect which frequencies have been submitted, using the '/down-(\d+)/' key
		$freq_Ns = array();
		$post_fields = array_keys( $f3->get('POST'));
		foreach( $post_fields as $field)
			if( preg_match( '/^down-(\d+)$/', $field, $matches))
				array_push( $freq_Ns, $matches[1]);

		$freqs = array();
		foreach( $freq_Ns as $f_N)
		{
			$freqs[] = array(
				'down' => $f3->get("POST.down-{$f_N}"),
				'dup' => $f3->get("POST.dup-{$f_N}"),
				'ctcss' => $f3->get("POST.ctcss-{$f_N}"),
				'power' => $f3->get("POST.power-{$f_N}"),
				'mode' => $f3->get("POST.mode-{$f_N}"),
			);
		}

		$this->rdb->update( $f3->get("POST"), $freqs);

		$f3->reroute('/repeaters');
	}
	
	// */	
	// GET /repeaters/new
	public function new( $f3)
	{
		$f3->set( 'content', 'repeaters-edit.tpl.php');
		echo View::instance()->render( 'layout.php');
	}
	
	// POST /repeaters/new
	public function add( $f3)
	{
		// Filter requests that don't pass the CSRF token check
		if( !$f3->exists('SESSION.csrf') || !$f3->exists('POST.token') || $f3->get( 'SESSION.csrf') != $f3->get('POST.token') )
			$f3->error( 403);
	
		// Detect which frequencies have been submitted, using the '/down-(\d+)/' key
		$freq_Ns = array();
		$post_fields = array_keys( $f3->get('POST'));
		foreach( $post_fields as $field)
			if( preg_match( '/^down-(\d+)$/', $field, $matches))
				array_push( $freq_Ns, $matches[1]);

		$freqs = array();
		foreach( $freq_Ns as $f_N)
		{
			$freqs[] = array(
				'down' => $f3->get("POST.down-{$f_N}"),
				'dup' => $f3->get("POST.dup-{$f_N}"),
				'ctcss' => $f3->get("POST.ctcss-{$f_N}"),
				'power' => $f3->get("POST.power-{$f_N}"),
				'mode' => $f3->get("POST.mode-{$f_N}"),
			);
		}

		$this->rdb->add( $f3->get("POST"), $freqs);

		// Create user on MQTT and enroll in gr_repeaters
		$mqtt_ds = new \MQTTDynamicSecurity( $f3->get('user_config'));
		$mqtt_ds->createClient( $f3->get('POST.qrz'), $f3->get('POST.password'));
		$mqtt_ds->addGroupClient( 'gr_repeaters', $f3->get('POST.qrz'));

		$f3->reroute('/repeaters');
	}
	
	/* ***** API ***** */
/*	
	// GET /api/v1/repeaters
	public function api_list( $f3)
	{
	}
	
	// GET /api/v1/repeaters/@id
	public function api_add( $f3, $params)
	{
	}
	
	// PUT /api/v1/repeaters/@id
	public function api_update( $f3, $params)
	{
	}
	// */
		
	// DELETE /api/v1/repeaters/@id
	public function api_delete( $f3, $params)
	{
		if( !isset( $params['id']) || !ctype_digit( $params['id']))
			$f3->error( 400, 'Bad Request');
		
		$id = $params['id'];

		$rpt = $this->rdb->get( $id);

		if( $rpt === false)
			$f3->error( 404);

		$fz = $this->rdb->deleteFrequencies( $id);
		$rpt = $this->rdb->delete( $id);

		$mqtt_ds = new \MQTTDynamicSecurity( $f3->get( 'user_config'));
		$mqtt_ds->deleteClient( $rpt['qrz']);

		header( 'X-Must-Reload: true');
		echo( json_encode( array( 'status' => 'OK')));
	}
	
	/* ***** Commands ***** */
	
	// POST /api/v1/repeaters/@id/ping
	public function ping( $f3, $params)
	{
		if( !isset( $params['id']) || !ctype_digit( $params['id']))
			$f3->error( 400, 'Bad Request');
		
		$id = $params['id'];

		$rpt = $this->rdb->get( $id);
		if( $rpt === false)
			$f3->error( 404);

		$repeater = new RemoteRepeater( $f3->get('user_config'), $rpt['qrz']);

		$result = $repeater->ping();

		if( $result === false )
			$f3->error(504);

		header( 'X-Acked-Command: ping');
		header( "X-QRZ: {$rpt['qrz']}");
		echo( json_encode( array( 'status' => 'OK')));
	}

	// POST /api/v1/repeaters/@id/chmod
	public function change_module( $f3, $params)
	{
		if( !isset( $params['id']) || !ctype_digit( $params['id']))
			$f3->error( 400, 'Bad Request');
		
		$id = $params['id'];

		$rpt = $this->rdb->get( $id);

		if( $rpt === false)
			$f3->error( 404);

		if( !$f3->exists('POST.room') || empty($f3->get('POST.room')))
			$f3->error( 400, 'Bad Request');

		$repeater = new RemoteRepeater( $f3->get('user_config'), $rpt['qrz']);

		$result = $repeater->changeModule( $f3->get('POST.room'));

		if( $result === false )
			$f3->error(504);

		header( 'X-Acked-Command: chmod');
		header( "X-QRZ: {$rpt['qrz']}");
		echo( json_encode( array( 'status' => 'OK')));
	
	}

	// POST /api/v1/repeaters/@id/disconnect
	public function disconnect( $f3, $params)
	{
		if( !isset( $params['id']) || !ctype_digit( $params['id']))
			$f3->error( 400, 'Bad Request');
		
		$id = $params['id'];

		$rpt = $this->rdb->get( $id);

		if( $rpt === false )
			$f3->error( 404);

		$repeater = new RemoteRepeater( $f3->get('user_config'), $rpt['qrz']);

		$result = $repeater->disconnect();

		if( $result === false )
			$f3->error(504);
		header( 'X-Acked-Command: disc');
		header( "X-QRZ: {$rpt['qrz']}");
		echo( json_encode( array( 'status' => 'OK')));
	}
	
	// POST /api/v1/repeaters/@id/txon
	public function enable_tx( $f3, $params)
	{
		if( !isset( $params['id']) || !ctype_digit( $params['id']))
			$f3->error( 400, 'Bad Request');
		
		$id = $params['id'];

		$rpt = $this->rdb->get( $id);

		if( $rpt === false )
			$f3->error( 404);

		$repeater = new RemoteRepeater( $f3->get('user_config'), $rpt['qrz']);

		$result = $repeater->rfkill(false);

		if( $result === false )
			$f3->error(504);

		header( 'X-Acked-Command: txon');
		header( "X-QRZ: {$rpt['qrz']}");
		echo( json_encode( array( 'status' => 'OK')));
	}
	
	// POST /api/v1/repeaters/@id/txoff
	public function disable_tx( $f3, $params)
	{
		if( !isset( $params['id']) || !ctype_digit( $params['id']))
			$f3->error( 400, 'Bad Request');
		
		$id = $params['id'];

		$rpt = $this->rdb->get( $id);

		if( $rpt === false )
			$f3->error( 404);

		$repeater = new RemoteRepeater( $f3->get('user_config'), $rpt['qrz']);

		$result = $repeater->rfkill(true);

		if( $result === false )
			$f3->error(504);

		header( 'X-Acked-Command: txoff');
		header( "X-QRZ: {$rpt['qrz']}");
		echo( json_encode( array( 'status' => 'OK')));
	}

	// POST /api/v1/repeaters/@id/reboot
	public function reboot( $f3, $params)
	{
		if( !isset( $params['id']) || !ctype_digit( $params['id']))
			$f3->error( 400, 'Bad Request');
		
		$id = $params['id'];

		$rpt = $this->rdb->get( $id);

		if( $rpt === false )
			$f3->error( 404);

		$repeater = new RemoteRepeater( $f3->get('user_config'), $rpt['qrz']);

		$result = $repeater->reboot();

		if( $result === false )
			$f3->error(504);

		header( 'X-Acked-Command: reboot');
		header( "X-QRZ: {$rpt['qrz']}");
		echo( json_encode( array( 'status' => 'OK')));
	}

	// POST /api/v1/repeaters/@id/passwd
	public function update_password( $f3, $params)
	{
		if( !isset( $params['id']) || !ctype_digit( $params['id']) || $f3->exists('POST.password') == false )
			$f3->error( 400, 'Bad Request');
		
		$id = $params['id'];

		$rpt = $this->rdb->get( $id);

		if( $rpt === false )
			$f3->error( 404);

		$newpass = $f3->get('POST.password');

		// TODO: additional check that current user is granted to update the repeater's password. 403 otherwise
		// (sysop && granted) || admin || superadmin
		
		// Clear local hash password
		$cacheKey = 'AppAuth__userPassHash_'.$rpt['qrz'];
		$f3->get( 'hCache')->clear( $cacheKey.'_salt');
		$f3->get( 'hCache')->clear( $cacheKey);

		// Instanciate Dynsec and update password
		$mqtt_ds = new \MQTTDynamicSecurity( $f3->get('user_config'));
		if( !$mqtt_ds->setClientPassword( $rpt['qrz'], $newpass) )
			$f3->error( 403);

		header( "X-QRZ: {$rpt['qrz']}");
		echo( json_encode( array( 'status'=>'OK')));
	}

	// GET /api/v1/repeaters/export
	public function export_userdb( $f3)
	{
		$cache_key = 'repeaters_export_userdb';
		if( $f3->get( 'hCache')->exists( $cache_key) )
		{
			header( 'Content-Type: application/json; charset=utf-8');
			echo($f3->get( 'hCache')->get( $cache_key));
			die();
		}
		$repeaters = $this->rdb->list();

		$result = array();

		foreach( $repeaters as $rpt)
		{
			$result[ $rpt['svx_user'] ] = array(
				'dpt' => $rpt['dep'], 'type' => $rpt['type'], 'comment' => $rpt['comment'] ?? null,
				'lat' => $rpt['lat'] ?? null, 'lon' => $rpt['lon'] ?? null, 'asl' => $rpt['asl'] ?? null
			);
			if( array_key_exists( 'height', $rpt) )
				$result[ $rpt['svx_user']]['height'] = $rpt['height'];
			$result[ $rpt['svx_user']]['freqs'] = array();
			$max_pow = null;
			foreach( $this->rdb->getFrequencies( $rpt['id']) as $f )
			{
				if( array_key_exists( 'power', $f) )
				{
					if( $max_pow === null || $max_pow < $f['power'])
						$max_pow = $f['power'];
				}
				$result[ $rpt['svx_user']]['freqs'][] = array(
					'down' => $f['down'],
					'dup' => $f['dup'] ?? null,
					'ctcss' => $f['ctcss'] ?? null
				);
			}
		}

		header( 'Content-Type: application/json; charset=utf-8');
		$f3->get( 'hCache')->set( $cache_key, json_encode($result), 600);
		echo($f3->get( 'hCache')->get( $cache_key));
	}

	// GET /js/rooms
	public function export_js_rooms( $f3)
	{
		header('Content-Type: text/javascript; charset=utf-8');
		$rooms = $this->rdb->getRooms();
		$result = array();
		foreach( $rooms as $r)
			$result[$r['abbr']] = $r['name'];

		$result = json_encode( $result);
		$result = str_replace( '\'', "\\'", $result);
		echo( "var rooms = JSON.parse('{$result}');");
	}
}
