<?php

use WP_PERICLES\IMPORT\Import;

if (class_exists( 'WP_CLI' ) ){
	\WP_CLI::add_command( 'wp_pericles_import', 'wp_pericles_import' );
}

function wp_pericles_import(){
	if ( ! file_exists( WP_PERICLES_PLUGIN_PATH . 'pid.txt' ) ){
		error_log( 'Pericles Started successfully at '. date(' d M Y - G\hi'));
		wp_pericles_create_pid();
		$import = new Import();
		$import->extract_photo();
		wp_pericles_delete_pid();
		error_log( 'Pericles Imported successfully at '. date(' d M Y - G\hi'));
	}

}

/**
 * Create a file with the date to avoid launching cron twice
 */
function wp_pericles_create_pid() {

	$date = date( 'd F Y @ H\hi:s' );
	$file = fopen( WP_PERICLES_PLUGIN_PATH . 'pid.txt', 'w+' );
	fwrite( $file, $date );
	fclose( $file );
}

function wp_pericles_delete_pid() {
	if ( file_exists( WP_PERICLES_PLUGIN_PATH . 'pid.txt' ) ) {
		unlink( WP_PERICLES_PLUGIN_PATH . 'pid.txt' );
	}
}
