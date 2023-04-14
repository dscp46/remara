<?php

// API Stub to interact with Mosquitto's Dynamic Security Plugin
// API Spec: https://github.com/eclipse/mosquitto/blob/master/plugins/dynamic-security/README.md#list-clients

class MQTTDynamicSecurity
{
	// MQTT Topic to send commands
	private $txTopic = "\$CONTROL/dynamic-security/v1";

	// MQTT Topic to receive responses
	private $rxTopic = "\$CONTROL/dynamic-security/v1/response";

	// MQTT Client
	private $mqtt;

	// System configuration
	private $config;

	function __construct( array $config)
	{
		$this->mqtt = new \MQTTClient();
		$this->config = $config;
	}

	function __destruct()
	{
	}

	public function getDefaultACLAccess()
	{
		$command = array( 'command' => 'getDefaultACLAccess');
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		if( $result[0] ) return json_decode($result[1], true)['responses'][0]['data'];
			return false;
	}

	public function setDefaultACLAccess( bool $allowPublishClientSend=false, bool $allowPublishClientReceive=true, bool $allowSubscribe=false, bool $allowUnsubscribe=true)
	{
		$command = array( 
			'command' => 'setDefaultAclAccess',
			'acl' => array(
				(object) array( 'acltype' => 'publishClientSend', 'allow' => $allowPublishClientSend),
				(object) array( 'acltype' => 'publishClientReceive', 'allow' => $allowPublishClientReceive),
				(object) array( 'acltype' => 'subscribe', 'allow' => $allowSubscribe),
				(object) array( 'acltype' => 'unsubscribe', 'allow' => $allowUnsubscribe)
			)
		);
		$data    = (object) array( 'commands' => array( (object) $command));
	
		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}

	public function createClient( string $username, string $password, ?int $id=null, ?string $textName=null, ?string $description=null)
	{
		$command = array( 'command' => 'createClient', 'username' => $username, 'password' => $password);
		if( $id !== null ) $command['clientid'] = $id;
		if( $textName !== null ) $command['textname'] = $textName;
		if( $description !== null ) $command['textdescription'] = $description;
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}
	
	public function deleteClient( string $username)
	{
		$command = array( 'command' => 'enableClient', 'username' => $username);
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}
	
	public function setClientPassword( string $username, string $password)
	{
		$command = array( 'command' => 'setClientPassword', 'username' => $username, 'password' => $password);
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}
	
	public function setClientId( string $username, ?int $id=null)
	{
		$command = array( 'command' => 'setClientId', 'username' => $username);
		if( $id !== null ) $command['clientid'] = $id;
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}
	
	public function addClientRole( string $username, string $roleName, int $priority=-1)
	{
		$command = array( 'command' => 'addClientRole', 'username' => $username, 'rolename' => $roleName);
		if( $priority != -1 ) $command['priority'] = $priority;
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}
	
	public function removeClientRole( string $username, string $role)
	{
		$command = array( 'command' => 'removeClientRole', 'username' => $username, 'rolename' => $roleName);
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}
	
	public function getClient( string $username)
	{
		$command = array( 'command' => 'getClient', 'username' => $username);
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		if( $result[0] ) return json_decode($result[1], true)['responses'][0]['data']['client'];
		return false;
	}
	
	public function listClients( int $count=-1, int $offset=0, ?bool $verbose=null)
	{
		$command = array( 'command' => 'listClients', 'count' => $count, 'offset' => $offset);
		if( $verbose !== null ) $command['verbose'] = $verbose;
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		if( $result[0] ) return json_decode($result[1], true)['responses'][0]['data']['clients'];
		return false;
	}
	
	public function enableClient( string $username)
	{
		$command = array( 'command' => 'enableClient', 'username' => $username);
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}
	
	public function disableClient( string $username)
	{
		$command = array( 'command' => 'disableClient', 'username' => $username);
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}
	
	public function createGroup( string $groupName)
	{
		$command = array( 'command' => 'createGroup', 'groupname' => $groupName);
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}
	
	public function deleteGroup( string $groupName)
	{
		$command = array( 'command' => 'deleteGroup', 'groupname' => $groupName);
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}
	
	public function addGroupRole( string $groupName, string $roleName, int $priority=-1)
	{
		$command = array( 'command' => 'addGroupRole', 'groupname' => $groupName, 'rolename' => $roleName);
		if( $priority != -1 ) $command['priority'] = $priority;
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}
	
	public function removeGroupRole( string $groupName, string $roleName)
	{
		$command = array( 'command' => 'removeGroupRole', 'groupname' => $groupName, 'rolename' => $roleName);
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}
	
	public function addGroupClient( string $groupName, string $username, int $priority=-1)
	{
		$command = array( 'command' => 'addGroupClient', 'groupname' => $groupName, 'username' => $username);
		if( $priority != -1 ) $command['priority'] = $priority;
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}
	
	public function removeGroupClient( string $groupName, string $username)
	{
		$command = array( 'command' => 'removeGroupClient', 'groupname' => $groupName, 'username' => $username);
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}
	
	public function getGroup( string $groupName)
	{
		$command = array( 'command' => 'getGroup', 'rolename' => $groupName);
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		if( $result[0] ) return json_decode($result[1], true)['responses'][0]['data']['group'];
		return false;
	}
	
	public function listGroups( int $count=null, int $offset=null, ?bool $verbose=null)
	{
		$command = array( 'command' => 'listGroups', 'count' => $count, 'offset' => $offset);
		if( $verbose !== null ) $command['verbose'] = $verbose;
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		if( $result[0] ) return json_decode($result[1], true)['responses'][0]['data']['groups'];
		return false;
	}
	
	public function createRole( string $roleName, string $description=null, string $textName=null)
	{
		$command = array( 'command' => 'createRole', 'rolename' => $roleName);
		if( !empty( $description) ) $command['textdescription'] = $description;
		if( !empty( $textName) ) $command['textName'] = $textName;
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}

	public function getRole( string $roleName)
	{
		$command = array( 'command' => 'getRole', 'rolename' => $roleName);
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		if( $result[0] ) return json_decode($result[1], true)['responses'][0]['data']['role'];
		return false;
	}

	public function deleteRole( string $roleName)
	{
		$command = array( 'command' => 'deleteRole', 'rolename' => $roleName);
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}

	public function addRoleACL( string $roleName, string $aclType, string $topicFilter, string $action, int $priority=-1)
	{
		$command = array( 
			'command' => 'addRoleACL', 'rolename' => $roleName, 'acltype' => (string)$aclType, 'topic' => $topicFilter,
			'allow' => ($action == "true")
		);
		if( $priority != -1 ) $command['priority'] = $priority;
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}

	public function removeRoleACL( string $roleName, string $aclType, string $topicFilter)
	{
		$command = array( 
			'command' => 'removeRoleACL', 'rolename' => $roleName, 'acltype' => (string)$aclType,'topic' => $topicFilter 
		);
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		return $result[0];
	}

	public function listRoles( int $count=-1, int $offset=0, ?bool $verbose=null)
	{
		$command = array( 'command' => 'listRoles', 'count' => $count, 'offset' => $offset);
		if( $verbose !== null ) $command['verbose'] = $verbose;
		$data    = (object) array( 'commands' => array( (object) $command));

		$result = $this->mqtt->sendCommand( $this->config, $this->txTopic, json_encode($data), 1, $this->rxTopic);

		if( $result[0] ) return json_decode($result[1], true)['responses'][0]['data']['roles'];
		return false;
	}
}

