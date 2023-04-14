<?php

// Umbra is a better-than-nothing security module to seal secrets on a system where no TPM or HSM is available
// This shouldn't be trusted with sensitive information when a better solution is available
class Umbra
{
	// Key Encryption Key
	private	$kek;

	function __construct()
	{
		$f3 = \Base::instance();

		$ciphertext = sodium_base642bin( $f3->get( 'user_config')['umbra']['wkek'], SODIUM_BASE64_VARIANT_ORIGINAL);
		$nonce = mb_substr($ciphertext, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
		$ciphertext = mb_substr($ciphertext, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

		// Internal Key Encryption Key, so that a 3rd party reading the wrapped KEK in the config can't read secrets.
		$ikek = sodium_hex2bin( 'af59a4cc51ac0f8b463bc23145469735ccf1d060d22beb8d0b497ebd12dfb526');
		$this->kek = sodium_crypto_secretbox_open($ciphertext, $nonce, $ikek);
		sodium_memzero( $ikek);

		if( $this->kek === false )
		{
			throw new \Exception('[__FILE__] Unable to unwrap Key Encryption Key.');
		}

		// Now we overwrite the nonce and secret key with null bytes in memory, to prevent any leakage of sensitive data.
		sodium_memzero($nonce);
		sodium_memzero($ciphertext);

		if( !$f3->exists( 'COOKIE.umbra_wdek') )
		{
			// Generate Data Encryption key
			$dek = sodium_crypto_secretbox_keygen();

			// Seal the DEK with the KEK
			$wdek = $this->seal( $dek, \UmbraKeys::KEK);
			sodium_memzero($dek);

			$f3->set( 'COOKIE.umbra_wdek', $wdek);
		}
	}

	function __destruct()
	{
		// Zeroize Key Encryption Key
		sodium_memzero($this->kek);
	}


	static function is_DEK_valid()
	{
		$f3 = \Base::instance();
		if( !$f3->exists( 'COOKIE.umbra_wdek') )
			return false;

		try
		{
			$sm = new \Umbra();
			$wdek = $f3->get( 'COOKIE.umbra_wdek');
			$used_key = $sm->unseal( $wdek, \UmbraKeys::KEK);
		}
		catch( \Exception $e )
		{
			if( isset($used_key) )
				sodium_memzero($used_key);
			if( isset($wdek) )
				sodium_memzero($wdek);
			return false;
		}
		sodium_memzero($used_key);
		sodium_memzero($wdek);

		return true;
	}

	// Generate a local Key Encryption Key, then seals it with the Internal KEK
	public function generate_wkek()
	{
		// Generate a new Nonce
		$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

		// Generate a new Key Encryption Key
		$kek = sodium_crypto_secretbox_keygen();

		// Load internal KEK
		$ikek = sodium_hex2bin('af59a4cc51ac0f8b463bc23145469735ccf1d060d22beb8d0b497ebd12dfb526');

		// Encrypt the KEK using the internal Key Encryption Key and Nonce.
		$ciphertext = sodium_crypto_secretbox($kek, $nonce, $ikek);

		// Zeroize keying material
		sodium_memzero($ikek);
		sodium_memzero($kek);

		// Convert the encrypted message with the nonce to base64 for safe transport or storage.
		$result = sodium_bin2base64($nonce . $ciphertext, SODIUM_BASE64_VARIANT_ORIGINAL);

		// Zeroize unnecessary values
		sodium_zero( $nonce);
		sodium_zero( $ciphertext);

		return $result;
	}

	// Seal a message with the KEK
	public function seal( $message, $key = \UmbraKeys::DEK)
	{
		$f3 = \Base::instance();

		// Select appropriate encryption key
		switch ($key)
		{
		case \UmbraKeys::DEK:
			$wdek = $f3->get( 'COOKIE.umbra_wdek');
			$used_key = $this->unseal( $wdek, \UmbraKeys::KEK);
			sodium_memzero($wdek);
			break;
		case \UmbraKeys::KEK:
			$used_key = $this->kek;
			break;
		default:
			throw new \Exception("[__FILE__] Unable to select the requested key.");
		}

		// Generate a random 24-byte Nonce
		$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

		// Encrypt the message using the internal Key Encryption Key and Nonce.
		$ciphertext = sodium_crypto_secretbox($message, $nonce, $used_key);

		// Convert the encrypted message with the nonce to base64 for safe transport or storage.
		$result = sodium_bin2base64($nonce . $ciphertext, SODIUM_BASE64_VARIANT_ORIGINAL);

		// Zeroize unnecessary values
		sodium_memzero($message);
		sodium_memzero($used_key);
		sodium_memzero($nonce);

		return $result;
	}

	// Unseal a message with the KEK
	public function unseal( $sealedValue, $key = \UmbraKeys::DEK)
	{
		$f3 = \Base::instance();

		// Select appropriate encryption key
		switch ($key)
		{
		case \UmbraKeys::DEK:
			$wdek = $f3->get( 'COOKIE.umbra_wdek');
			$used_key = $this->unseal( $wdek, \UmbraKeys::KEK);
			sodium_memzero($wdek);
			break;
		case \UmbraKeys::KEK:
			$used_key = $this->kek;
			break;
		default:
			throw new \Exception("[__FILE__] Unable to select the requested key.");
		}


		// Convert the base64 encoded message to binary using sodium_base642bin().
		$ciphertext = sodium_base642bin($sealedValue, SODIUM_BASE64_VARIANT_ORIGINAL);

		// Extract the nonce from the beginning of the message.
		$nonce = mb_substr($ciphertext, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');

		// The message is the rest of the ciphertext.
		$ciphertext = mb_substr($ciphertext, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

		// Now we can decrypt the message with the secret key and nonce.
		$plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $used_key);

		// If plaintext is false, the sealed value was corrupted.
		if ( $plaintext === false ) {
			throw new \Exception( '[__FILE__] Unable to unseal secret.');	
		}

		// Zeroize unnecessary values
		sodium_memzero($nonce);
		sodium_memzero($used_key);
		sodium_memzero($ciphertext);

		return $plaintext;
	}
}
