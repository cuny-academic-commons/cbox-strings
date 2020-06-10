<?php
/**
Plugin Name: CBOX CLI String Generator
Description: Generates dummy PHP files for bundled CBOX plugins to be used for GlotPress.
Version: 0.1
Author: CUNY Academic Commons
*/

add_action( 'plugins_loaded', function() {
	// WP-CLI integration
	if ( ! defined( 'WP_CLI' ) ) {
		return;
	}

	require_once __DIR__ . '/cbox-cli-strings.php';

	\WP_CLI::add_command( 'cbox strings', 'CBOX\\CLI\\Strings\\init' );
} );