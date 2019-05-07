<?php
/*
	Plugin Name: WP Pericles Import
	Plugin URI: https://www.thivinfo.com
	Description:
	Author: Sébastien SERRE
	Author URI: https://thivinfo.com
	Version: 1.0.0
	Text Domain: wp-pericles-import
	Domain Path: /languages
	*/

namespace WPPERICLES;

use function add_action;
use function define;
use function plugin_basename;
use function plugin_dir_path;
use function plugin_dir_url;
use function untrailingslashit;
use function wp_get_upload_dir;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Class WPPericles
 * @package WPPERICLES
 */
class WPPericles {

	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'activation' ) );

		add_action( 'plugins_loaded', array( $this, 'define_constants' ) );
		add_action( 'plugins_loaded', array( $this, 'load_files' ) );

		add_filter( 'cron_schedules', array( $this, 'create_schedule' ) );
	}

	public function define_constants() {
		define( 'WP_PERICLES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		define( 'WP_PERICLES_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'WP_PERICLES_PLUGIN_DIR', untrailingslashit( 'WP_PERICLES' ) );
		define( 'WP_PERICLES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

		$path = wp_get_upload_dir();
		define( 'WP_PERICLES_IMPORT', $path['basedir'] . '/import/' );
		define( 'WP_PERICLES_IMPORT_TMP', $path['basedir'] . '/import/temp/' );
		define( 'WP_PERICLES_IMPORT_IMG', $path['basedir'] . '/import/img/' );
		define( 'WP_PERICLES_EXPORT_FOLDER', $path['basedir'] . '/import/export/' );
	}


	public function activation() {
		$this->create_folders();
		$this->create_cron();
	}


	public function load_files() {
		require plugin_dir_path( __FILE__ ) . '/class/class-import.php';
		require plugin_dir_path( __FILE__ ) . '/cron.php';
	}


	public function create_folders() {
		$path = wp_get_upload_dir();


		if ( ! file_exists( $path['basedir'] . '/import/export' ) ) {
			mkdir( $path['basedir'] . '/import/export', 0777, true );
		}
		if ( ! file_exists( $path['basedir'] . '/import/temp' ) ) {
			mkdir( $path['basedir'] . '/import/temp', 0777, true );
		}
		if ( ! file_exists( $path['basedir'] . '/import/img' ) ) {
			mkdir( $path['basedir'] . '/import/img', 0777, true );
		}

	}

	public function create_cron() {
		if ( ! wp_next_scheduled( 'wp_pericles_cron' ) ) {
			wp_schedule_event( time(), 'quarter_hour', 'wp_pericles_cron' );
		}
	}

	/**
	 * @param $schedules
	 *
	 * @return mixed
	 */
	public function create_schedule( $schedules ) {
		$schedules['quarter_hour'] = array(
			'interval' => 900,
			'display'  => __( 'Every 15 minutes', 'wp-pericles-import' ),
		);

		return $schedules;
	}
}

new WPPericles();
