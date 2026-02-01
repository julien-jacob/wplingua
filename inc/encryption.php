<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get the encryption key for AES-256-GCM.
 *
 * Retrieves the encryption key from the database. If it doesn't exist,
 * generates a new 256-bit random key and stores it.
 *
 * @return string The raw binary encryption key.
 */
function wplng_encryption_get_key() {

	$option_name = 'wplng_encryption_key';

	// Try to retrieve the key from the database stored in an option
	$encryption_key = get_option( $option_name );

	// If the key doesn't exist, create one randomly and store it
	if ( empty( $encryption_key ) ) {
		$encryption_key = base64_encode( random_bytes( 32 ) ); // 256 bits
		update_option( $option_name, $encryption_key, false );
	}

	return base64_decode( $encryption_key );
}


/**
 * Encrypt a text using AES-256-GCM.
 *
 * Encrypts the given text using AES-256-GCM symmetric encryption.
 * The result includes the IV and authentication tag for decryption.
 *
 * @param string $text The plain text to encrypt.
 * @return string The encrypted text encoded in base64, or empty string on failure.
 */
function wplng_encryption_encrypt( $text ) {

	if ( empty( $text ) ) {
		return '';
	}

	$method = 'aes-256-gcm';
	$key    = wplng_encryption_get_key();

	// Generate a random IV (initialization vector)
	$iv_length = openssl_cipher_iv_length( $method );
	$iv        = random_bytes( $iv_length );

	// Encrypt the text with AES-256-GCM
	$tag       = '';
	$encrypted = openssl_encrypt(
		$text,
		$method,
		$key,
		OPENSSL_RAW_DATA,
		$iv,
		$tag,
		'',
		16 // Authentication tag length
	);

	if ( $encrypted === false ) {
		return '';
	}

	// Combine IV + tag + encrypted text and encode in base64
	$encrypted_text = base64_encode( $iv . $tag . $encrypted );

	return $encrypted_text;
}


/**
 * Decrypt a text encrypted with AES-256-GCM.
 *
 * Decrypts the given base64-encoded text that was encrypted
 * using the wplng_encryption_encrypt function.
 *
 * @param string $text The encrypted text encoded in base64.
 * @return string The decrypted plain text, or empty string on failure.
 */
function wplng_encryption_decrypt( $text ) {

	if ( empty( $text ) ) {
		return '';
	}

	$method = 'aes-256-gcm';
	$key    = wplng_encryption_get_key();

	// Decode the text from base64
	$data = base64_decode( $text );

	if ( $data === false ) {
		return '';
	}

	// Extract the IV, tag and encrypted text
	$iv_length = openssl_cipher_iv_length( $method );
	$iv        = substr( $data, 0, $iv_length );
	$tag       = substr( $data, $iv_length, 16 );
	$encrypted = substr( $data, $iv_length + 16 );

	// Decrypt the text
	$decrypted_text = openssl_decrypt(
		$encrypted,
		$method,
		$key,
		OPENSSL_RAW_DATA,
		$iv,
		$tag
	);

	if ( $decrypted_text === false ) {
		return '';
	}

	return $decrypted_text;
}
