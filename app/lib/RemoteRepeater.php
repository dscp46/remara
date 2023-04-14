<?php

class RemoteRepeater
{
	private $mqtt;

	private $callsign;

	private $config;

	function __construct( array $config, string $callsign)
	{
		$this->mqtt = new \MQTTClient();

		if( empty( $callsign) ) 
			throw new Exception('Repeater callsign must be supplied, and not an empty string');
		
		$this->callsign = $callsign;
		$this->config = $config;
	}

	protected function sendCommand( string $command)
	{
		$now = hrtime(true);
		$txTopic = "repeaters/{$this->callsign}/v1";
		$reply = $this->mqtt->sendCommand( $this->config, $txTopic, "{$now} {$command}", 2, "{$txTopic}/response", 3 );

		return $reply[0] ? ($reply[1] == $now) : false;
	}

	public function ping()
	{
		return $this->sendCommand('ping');
	}

	public function reboot()
	{
		return $this->sendCommand('reboot');
	}

	public function disconnect()
	{
		return $this->sendCommand('disc');
	}

	public function rfKill( bool $block)
	{
		return $this->sendCommand( $block ? 'txoff' : 'txon');
	}

	public function changeModule( string $newModule)
	{
		return $this->sendCommand("chmod {$newModule}");
	}

	public function update()
	{
		return $this->sendCommand('update');
	}

	public function provision( string $url)
	{
		return $this->sendCommand("provision {$url}");
	}
}


