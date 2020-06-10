<?php

namespace CBOX\CLI\Strings;

use Gettext\Translations;
use WP_CLI\I18n\PhpCodeExtractor;

CONST TEXTDOMAIN = 'commons-in-a-box';

/**
 * Generates our dummy translation files.
 *
 * ## EXAMPLES
 *
 *     $ wp cbox strings
 *     Dummy translation file generated for cbox-openlab-core at /wp-content/plugins/commons-in-a-box/languages/strings/cbox-openlab-core.php.
 *     Dummy translation file generated for openlab-theme at /wp-content/plugins/commons-in-a-box/languages/strings/openlab-theme.php.
 *
 * @see https://github.com/cuny-academic-commons/commons-in-a-box/issues/148#issuecomment-431959258
 */
function init() {
	$basedir = WP_CONTENT_DIR . '/plugins/commons-in-a-box/languages/strings/';
	if ( ! file_exists( $basedir ) ) {
		@wp_mkdir_p( $basedir );
	}

	foreach ( get_dirs() as $dir ) {
		// If our directory doesn't exist, skip!
		if ( ! file_exists( $dir ) ) {
			continue;
		}

		$textdomain = basename( $dir );

		// File header.
		$lines = [];
		$lines[] = '<?php';
		$lines[] = sprintf( '/**
 * %1$s
 *
 * This is a dummy PHP file meant to be picked up by GlotPress for
 * translation purposes on wordpress.org.
 *
 * Apart from that, this file is not used or loaded anywhere.
 */
', $textdomain );

		$translations = new Translations();

		$options = [
			'extensions' => [ 'php' ],
		];

		// Parse all WP gettext functions from the directory.
		PhpCodeExtractor::fromDirectory( $dir, $translations, $options );

		// Reformat translations to use our 'commons-in-a-box' textdomain.
		foreach ( $translations as $t ) {
			$line = '';
			if ( $t->hasExtractedComments() ) {
				$lines[] = '';
				$lines[] = sprintf( '/* %s */', $t->getExtractedComments()[0] );
			}

			if ( $t->hasContext() ) {
				if ( $t->hasPlural() ) {
					$line = sprintf( '_nx_noop( \'%1$s\', \'%2$s\', \'%3$s\', \'%4$s\' );', addcslashes( $t->getOriginal(), "'" ), addcslashes( $t->getPlural(), "'" ), addcslashes( $t->getContext(), "'" ), TEXTDOMAIN );
				} else {
					$line = sprintf( '_x( \'%1$s\', \'%2$s\', \'%3$s\' );', addcslashes( $t->getOriginal(), "'" ), addcslashes( $t->getContext(), "'" ), TEXTDOMAIN );
				}
			} elseif ( $t->hasPlural() ) {
				$line = sprintf( '_n_noop( \'%1$s\', \'%2$s\', \'%3$s\' );', addcslashes( $t->getOriginal(), "'" ), addcslashes( $t->getPlural(), "'" ), TEXTDOMAIN );
			} else {
				$line = sprintf( '__( \'%1$s\', \'%2$s\' );', addcslashes( $t->getOriginal(), "'" ), TEXTDOMAIN );
			}

			$lines[] = $line;

			if ( $t->hasExtractedComments() ) {
				$lines[] = '';
			}
		}

		// Delete older file.
		$file = $basedir . $textdomain . '.php';
		if ( file_exists( $file ) ) {
			wp_delete_file( $file );
		}

		// Output time!
		file_put_contents( $file, implode( "\n", $lines ) );

		unset( $lines );

		// Let WP-CLI know.
		\WP_CLI::line( sprintf( 'Dummy translation file generated for %1$s at %2$s.', $textdomain, $file ) );
	}
}

/**
 * Returns list of plugin/theme filepaths meant for dummy translations.
 *
 * @return array
 */
function get_dirs() {
	$plugins = [
		'cbox-openlab-core',
	];

	$themes = [
		'openlab-theme',
	];

	$dirs = [];

	foreach ( $plugins as $plugin ) {
		$dirs[] = WP_CONTENT_DIR . '/plugins/' . $plugin . '/';
	}

	foreach ( $themes as $theme ) {
		$dirs[] = WP_CONTENT_DIR . '/themes/' . $theme . '/';
	}

	return $dirs;
}
