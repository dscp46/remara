<?php

use Mosquitto\Client;

class MQTTClient
{
	// Message ID, used by MQTT client
	private $mid = 0;

	// Mosquitto Client Instance
	private $c;

	// Timer used to check if subscription is authorized
	private $timer;

	// The message to send
	private $message;

	// Response received. False if insufficient permissions 
	private $validResult;

	// The topic the message will be sent to
	private $txTopic;

	// The payload we're sending
	private $txPayload;

	// The topic we'll subscribe for the return channel
	private $rxTopic;

	// The response we've received
	private $rxPayload;

	// The chosen MQTT QoS level
	private $qos;

	// Subscribe to a return channel in order to get a response
	private $expectResponse;

	// Produce a verbose output
	private $debug;

	// Used to grab the connection return code for credentials validation
	private $connectRetCode;

	function __construct()
	{
		$this->connectRetCode = false;
		$this->c = new Mosquitto\Client("PHP");
		$this->timer = time();
		$this->mid = 0;
		$this->c->onConnect( 'MQTTClient::onMqttConnect');
		$this->c->onSubscribe( 'MQTTClient::onMqttSubscribe');
		$this->c->onMessage( 'MQTTClient::onMqttMessage');
	}

	function __destruct()
	{
	}

	public static function logdisp(...$arg)
	{
		$args = func_get_args();
		echo("<p>{$args[1]}</p>");
		flush();
		ob_flush();
	}

	public static function lognop(...$arg)
	{
	}

	public static function dbgdisp($arg)
	{
		echo('<pre>');
		var_dump($arg);
		echo('</pre>');
		flush();
		ob_flush();
	}

	protected function onMqttConnect( $retCode, $retMessage)
	{
		// Raise an exception if we encounter a connection error
		if( $retCode != 0)
			throw new \Exception( "$retcode: $retMessage");

		if( $this->expectResponse )
			$this->mid = $this->c->subscribe( $this->rxTopic, $this->qos);
		else
		{
			$this->mid = $this->c->publish( $this->txTopic, $this->txPayload, $this->qos);

			// Technically, we don't have a result, but a QoS 0 message is implied as successfully sent.
			$this->validResult = true;
			$this->timer = time()-3600;
		}
	}	

	protected function onMqttSubscribe( $mid, $qosCount)
	{
		$this->mid = $this->c->publish( $this->txTopic, $this->txPayload, $this->qos);
		$this->timer = time();
	}

	protected function onMqttMessage( $mesg)
	{
		// Debug: display received data
		if( $this->debug )
			self::dbgdisp( array( "topic" => $mesg->topic, "payload" => $mesg->payload, "qos"=>$mesg->qos));

		if( $mesg->topic == $this->rxTopic )
		{
			// Copy the message payload in the field used in sendCommand to return the result
			$this->rxPayload = $mesg->payload;

			// Indicate we've received a valid result
			$this->validResult = true;

			// Break the event loop
			$this->timer = time()-3600;
		}
	}


	public function sendCommand( ...$arg)
	{
		// Reset valid result flag. This is used to check for insufficient permissions.
		$this->validResult = false;

		// Unset Response
		$this->rxPayload = null;

		// Extract arguments
		$args = func_get_args();

		// Map arguments
		$config               = $args[0];
		$this->txTopic        = $args[1] ?? '';
		$this->txPayload      = $args[2] ?? null;
		$this->qos            = $args[3] ?? 0;
		$this->rxTopic        = $args[4] ?? null;
		$this->expectResponse = !empty($this->rxTopic);
		$timeout              = $args[5] ?? 3;
		$this->debug          = $args[6] ?? false;
		$logFunc              = $args[7] ?? 'MQTTClient::logdisp';
		$username             = $args[8] ?? null;
		$password             = $args[9] ?? null;

		// Ignore if message is empty
		if( empty($this->txTopic)) return false;

		// Reset timer
		$this->timer = time();

		// No output if debugging is disabled
		$this->c->onLog( $this->debug ? $logFunc : 'MQTTClient::lognop');

		// Set credentials if they exist
		if( !empty( $username) && !empty( $password))
			$this->c->setCredentials( $username, $password);
		else if( !empty( $config['mqtt']['username']) && !empty( $config['mqtt']['password']) )
			$this->c->setCredentials( $config['mqtt']['username'], $config['mqtt']['password']);

		// Attempt to connect
		$this->c->connect( $config['mqtt']['host'], $config['mqtt']['port'] ?? 1883);

		while( $this->timer > time()-$timeout )
			$this->c->loop(1);

		$this->c->disconnect();

		return array( $this->validResult, $this->rxPayload); 
	}

	protected function onMqttConnectCredValidate( $retCode, $retMessage)
	{
		// Copy the onconnect return code
		$this->connectRetCode = $retCode;

		// Break the event loop
		$this->timer = time()-3600;
	}


	public function testCredentials( array $config, string $username, string $password)
	{
		$f3 = \Base::instance();
		// Cache entry name
		$cacheKey = 'AppAuth__userPassHash_'.$username;

		// Attempt to get hash from cache
		if( $f3->get( 'hCache')->exists( $cacheKey) )
		{
			$salt = $f3->get( 'hCache')->get( $cacheKey.'_salt');
			$passhash = $f3->get( 'hCache')->get( $cacheKey);

			// Authenticate against cached hash
			if( hash( 'sha256', $salt.$password) == $passhash )
				return 0;

			return -1;
		}

		// Set up the test environment
		$this->connectRetCode = false;
		$this->c->onConnect( 'MQTTClient::onMqttConnectCredValidate');
		$this->c->onLog( 'MQTTClient::lognop');
		$this->c->setCredentials( $username, $password);
		$this->c->connect( $config['mqtt']['host'], $config['mqtt']['port'] ?? 1883);

		// Reset loop timer
		$this->timer = time();

		// Keep a reasonable loop life time, so the connection can proceed
		$timeout = 3; // seconds
	
		while( $this->timer > time()-$timeout )
		{
			try
			{
				$this->c->loop(1);
			}
			catch(Exception $e)
			{
				restore_error_handler();
			}
		}
		
		try
		{
			$this->c->disconnect();
		}
		catch(Exception $e)
		{
			restore_error_handler();
		}

		// Set back up the onConnect event
		$this->c->onConnect( 'MQTTClient::onMqttConnect');

		// Successful authentication: save the password hash in cache
		$salt = base64_encode(random_bytes(32));
		$f3->get( 'hCache')->set( $cacheKey.'_salt', $salt, 3601);
		$f3->get( 'hCache')->set( $cacheKey, hash('sha256', $salt.$password), 3600);

		// Return the connection return code
		return $this->connectRetCode;
	}
}
