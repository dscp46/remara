<?php

class AppAuth
{
	// Checks if HTTP header X-Stateless is set to true.
	static function isStatelessQuery( $f3)
	{
		$x_stateless = 'HEADERS.X-Stateless';
		return ( $f3->exists( $x_stateless) && $f3->get( $x_stateless) == "true");
	}

	// Function used to get the mapped role at app startup
	// The 
	static function getUser( $f3)
	{
		$b_user = 'SERVER.PHP_AUTH_USER';
		$b_pw   = 'SERVER.PHP_AUTH_PW';

		// If the stateless header is set, check for Authenticate credentials
		if( self::isStatelessQuery( $f3))
		{
			if( $f3->exists($b_user) && $f3->exists($b_pw) && !empty( $f3->get($b_user)) && !empty( $f3->get($b_pw)))
			{
				$username = $f3->get( $b_user);
				$password = $f3->get( $b_pw);
				if( !empty($username) && !empty($password) && self::statelessAuthenticate( $f3, $username, $password))
				{
					// Perform Authorization checks and map roles
					
					// Attempt to fetch from cache
					$cacheKey = 'AppAuth__userProfile_'.$username;

					if( $f3->get( 'hCache')->exists( $cacheKey) )
					{
						$mappedUser = $f3->get( 'hCache')->get( $cacheKey);
						$f3->set( 'SESSION.user', $mappedUser);
						$f3->set( 'SESSION.mqtt_uname', $username);
						$sm = new \Umbra();
						$f3->set( 'SESSION.mqtt_passwd', $sm->seal( $password));
						unset( $sm);

						return $mappedUser;
					}

					// Fetch user profile
					$user_cfg = $f3->get('user_config') ?? array();
					$mqtt = new MQTTDynamicSecurity( $user_cfg);
					$client = $mqtt->getClient( $username);
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
							$f3->set( 'SESSION.mqtt_uname', $username);
							$sm = new \Umbra();
							$f3->set( 'SESSION.mqtt_passwd', $sm->seal( $password));
							unset( $sm);

							return $mappedUser;
						}
					}

					// If we reached this place, insufficient permission have been collected
					header('HTTP/1.1 403 Forbidden');
					die();
				}
			}
			else
			{
				// Stateless without Authorization header.
				return '';
			}

			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="REST API Access"');
			die();
		}

		if( $f3->exists( 'SESSION.user') && !empty( $f3->get('SESSION.user')) )
			return $f3->get( 'SESSION.user');

		return ''; // Returns that no user has been found.
	}

	static function statelessAuthenticate( $f3, string $username, string $password)
	{
		$user_cfg = $f3->get('user_config') ?? array();
		$mqtt = new MQTTClient();
		$retCode = $mqtt->testCredentials( $user_cfg, $username, $password);
		if( $retCode === false )
		{
			// MQTT Server unreachable
			header('HTTP/1.1 503 Service Unavailable');
			die();
		}

		if( $retCode == 0 )
			return true; // Successful authentication

		header('HTTP/1.1 401 Unauthorized');
		header('WWW-Authenticate: Basic realm="REST API Access"');
		die();
	}
}
