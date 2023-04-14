<?php

class Main extends \DefaultCtlr
{
	function about( $f3) 
	{
		$f3->set( 'cur_module', '');
		$f3->set( 'content', 'about.tpl.php');
		echo View::instance()->render( 'layout.php');
	}

	function welcome( $f3) 
	{
		$f3->set( 'cur_module', '');
		$f3->set( 'content', 'hello.php');
		echo View::instance()->render( 'layout.php');
	}

	function get_logout( $f3) 
	{
		$f3->clear('SESSION');
		$f3->set( 'SESSION.csrf', $f3->CSRF);
		$f3->copy( 'mui_signin_logout_alert', 'alert_msg');
		$f3->set( 'cur_module', 'logout');
		$f3->set( 'custom_css', array('/assets/css/default.css', '/assets/css/login.css'));
		$f3->set( 'content', 'login.tpl.php');
		echo View::instance()->render( 'layout.php');
	}

	// Display the login form
	function get_login( $f3) 
	{
		$f3->set( 'cur_module', 'login');
		$f3->set( 'custom_css', array('/assets/css/default.css', '/assets/css/login.css'));
		$f3->set( 'content', 'login.tpl.php');
		echo View::instance()->render( 'layout.php');
	}

	// Process a login query
	function post_login( $f3) 
	{
		// Filter requests that don't pass the CSRF token check
		if( !$f3->exists('SESSION.csrf') || !$f3->exists('POST.token') || $f3->get( 'SESSION.csrf') != $f3->get('POST.token') )
			$f3->error( 403);
		
		// Fetch the user config
		$user_cfg = $f3->get('user_config') ?? array();

		try
		{
			// Check user credentials
			$mqtt = new MQTTClient();
			$retCode = $mqtt->testCredentials( $user_cfg, $f3->get( 'POST.callsign'), $f3->get( 'POST.password'));
			if( $retCode === false )
				$this->display_login( $f3, 'mui_signin_mqtt_unreachable_alert', 'alert-danger');

			if( $retCode != 0 )
			{
				sleep(3);
				$this->display_login( $f3, 'mui_signin_fail_alert', 'alert-danger');
			}

			unset( $mqtt);

			// Attempt to fetch from cache
			$cacheKey = 'AppAuth__userProfile_'.$f3->get('POST.callsign');

			if( $f3->get( 'hCache')->exists( $cacheKey) )
			{
				$mappedUser = $f3->get( 'hCache')->get( $cacheKey);
				$f3->set( 'SESSION.user', $mappedUser);
				$f3->copy( 'POST.callsign', 'SESSION.mqtt_uname');
				$sm = new \Umbra();
				$f3->set( 'SESSION.mqtt_passwd', $sm->seal( $f3->get( 'POST.password')));
				unset( $sm);
				$f3->copy( 'AGENT', 'SESSION.user-agent');
				$this->redirect_back( $f3);
			}


			// Fetch user profile
			$mqtt = new MQTTDynamicSecurity( $user_cfg);
			$client = $mqtt->getClient( $f3->get('POST.callsign'));

			// Get the list of groups they're a member of
			$memberOf = array_column( $client['groups'], 'groupname');

			// Attempt to map groups to the app's roles
			if( array_key_exists( 'autz', $user_cfg) )
			{
				foreach( $user_cfg['autz'] as $group => $mappedUser )
					if( array_search( $group, $memberOf) !== false )
					{
						$f3->get( 'hCache')->set( $cacheKey, $mappedUser, 3600);
						$f3->set( 'SESSION.user', $mappedUser);
						$f3->copy( 'POST.callsign', 'SESSION.mqtt_uname');
						$sm = new \Umbra();
						$f3->set( 'SESSION.mqtt_passwd', $sm->seal( $f3->get( 'POST.password')));
						unset( $sm);
						$f3->copy( 'AGENT', 'SESSION.user-agent');

						$this->redirect_back( $f3);
						// We exit this code path with the previous function
					}
			}

			// Authorization failure. Clear session, display the error
			$f3->clear('SESSION');
			$f3->set( 'SESSION.csrf', $f3->CSRF);
			$this->display_login( $f3, 'mui_signin_noautz', 'alert-danger');

			// display_login includes a die(), no further instruction will be processed
		}
		catch( e)
		{
			$this->set('mui_except_mqtt1_log', 'Error during connection: '.e.getMessage()); 
			$this->display_login( $f3, 'mui_except_mqtt1_log', 'alert-danger');
		}
		die();

	}

	// Redirect a user back to their initial query.
	protected function redirect_back( $f3)
	{
		if( $f3->exists('SESSION.last') )
		{
			if( $f3->exists( 'SESSION.last.get') )  $f3->copy( 'SESSION.last.get' , 'GET');
			if( $f3->exists( 'SESSION.last.post') ) $f3->copy( 'SESSION.last.post', 'POST');
			if( $f3->exists( 'SESSION.last.verb') ) $f3->copy( 'SESSION.last.verb', 'VERB');
			// Redirect user to their last query
			$f3->reroute( $f3->get( 'SESSION.last.uri') ?? '/' );
		}
		$f3->reroute('/');
	}

	/** display_login() - Display the login page with an error message.
	 * Calling this function will make the execution flow end, and thus never returns.
	 * @params
	 * 	$f3		\Base::instance	The Fat Free Framework instance
	 * 	$alert_msg 	string		Reference to a locale message
	 * 	$alert_severity string 		Bootstrap CSS class name to set the alert message's severity
	 *
	 */
	protected function display_login( $f3, string|null $alert_msg=null, string|null $alert_severity=null)
	{
		if( !empty( $alert_severity)) $f3->set( 'alert_severity', $alert_severity);
		if( !empty( $alert_msg)) $f3->copy( $alert_msg, 'alert_msg');
		$f3->set( 'cur_module', 'login');
		$f3->set( 'custom_css', array('/assets/css/default.css', '/assets/css/login.css'));
		$f3->set( 'content', 'login.tpl.php');
		echo View::instance()->render( 'layout.php');
		die();
	}

	function dump( $f3) 
	{
		if( !$f3->exists( 'DEBUG') )
			$f3->error( 403);

		$f3->set( 'content', 'debug.tpl.php');
		echo View::instance()->render( 'layout.php');
	}		

	function error_handler( $f3, array|null $params=null)
	{
		// Set the default error template
		$f3->set( 'content', 'error.tpl.php');

		$errcode = 200;
		if( $f3->exists('ERROR') )
			$errcode = $f3->get('ERROR.code');

		if( $params != null && !empty( $params['errcode']) )
			$errcode = $params['errcode'];

		$f3->set( 'errcode', $errcode);

		if( $errcode == 401)
		{
			if( \AppAuth::isStatelessQuery( $f3) )
			{
				header('HTTP/1.1 401 Unauthorized');
				header('WWW-Authenticate: Basic realm="REST API Access"');
				die();
			}

			// Authentication required, save context and redirect to the login form
			$f3->copy( 'GET', 'SESSION.last.get');
			$f3->copy( 'POST', 'SESSION.last.post');
			$f3->copy( 'URI', 'SESSION.last.uri');
			$f3->copy( 'VERB', 'SESSION.last.verb');
			$f3->reroute( '/login');
			return true;
		}
	
		$f3->set( 'content', 'errors.tpl.php');
		echo View::instance()->render( 'layout.php');
	}
}
