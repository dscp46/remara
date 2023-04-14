<?php
// Load config
$cfg_file = 'data/config.ini';
$config = file_exists( $cfg_file) ? parse_ini_file( $cfg_file, true) : array();

// Load F3 and app configuration
$f3 = include('lib/base.php');
$f3->config( array( 'app/config.ini', 'data/cache.ini'));
$f3->set( 'user_config', $config);

// Instanciate application cache
$f3->set( 'hCache', \Cache::instance());

// Start session with F3
//new Session( NULL, 'CSRF');
new Session( '\DefaultCtlr::onSuspectSession', 'CSRF');

// Run access control
$access = \Access::instance();
$access->authorize( \AppAuth::getUser( $f3));

$f3->run();

?>
