<?php

class DefaultCtlr
{

	public static function onSuspectSession( \Session $session)
	{
		$f3 = \Base::instance();
		if( $session->agent() == $f3->get( 'AGENT') && \Umbra::is_DEK_valid())
		{
			// Don't scratch the session if the user agent stayed the same.
			return true;
		}
		return false;
	}

	function __destruct()
	{
		$f3 = \Base::instance();
		if( \AppAuth::isStatelessQuery( $f3) )
			$f3->clear('SESSION');	
	}
}
