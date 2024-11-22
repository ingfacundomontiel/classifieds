<?php
/**
 * This file contains a function to check if the current IP has reached the request limit.
 *
 * @package Classifieds
 */

/**
 * Checks if the current IP has reached the request limit.
 *
 * @return bool True if the IP has exceeded the limit, false otherwise.
 */
function is_rate_limited() {
	// Get the current user's IP address.
	$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

	// Generate a secure transient key using SHA-256 hash.
	$transient_key = 'rate_limit_' . hash( 'sha256', $ip_address );

	// Retrieve the current request count for this IP address.
	$request_count = get_transient( $transient_key );

	// If the transient does not exist, initialize the request count to 0.
	if ( false === $request_count ) {
		$request_count = 0;
	}

	// Define the request limit (e.g., 10 requests per hour).
	$request_limit = 10;

	// Check if the limit has already been reached before incrementing.
	if ( $request_count >= $request_limit ) {
		return true;
	}

	// Increment the request count.
	++$request_count;

	// Store the updated request count in a transient that expires in 1 hour (3600 seconds).
	set_transient( $transient_key, $request_count, HOUR_IN_SECONDS );

	return false;
}
