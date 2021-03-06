<?php
/*
	Plugin Name: WP Pericles Import
	Plugin URI: https://www.thivinfo.com
	Description: Connect your Real Estate Agency to WordPress!
	Author: Sébastien SERRE
	Author URI: https://thivinfo.com
	Version: 1.4.2
	Text Domain: wp-pericles-import
	Domain Path: /languages
	*/

namespace WPPERICLES;

use function acf_update_setting;
use function add_action;
use function add_filter;
use function basename;
use function class_exists;
use function create_cpt;
use function deactivate_plugins;
use function define;
use function defined;
use function dirname;
use function flush_rewrite_rules;
use function plugin_basename;
use function plugin_dir_path;
use function plugin_dir_url;
use function register_activation_hook;
use function register_deactivation_hook;
use function time;
use function untrailingslashit;
use function wp_clear_scheduled_hook;
use function wp_enqueue_style;
use function wp_get_upload_dir;
use function wp_next_scheduled;
use const WP_PERICLES_ACF_PATH;
use function wp_schedule_event;
use const WP_PERICLES_PLUGIN_PATH;
use const WP_PERICLES_PLUGIN_URL;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Class WPPericles
 *
 * @package WPPERICLES
 */
class WPPericles {

	public function __construct() {

		add_action( 'plugins_loaded', array( $this, 'define_constants' ) );
		add_action( 'plugins_loaded', array( $this, 'load_acf' ), 10, 1 );
		add_action( 'plugins_loaded', array( $this, 'load_files' ), 9999, 1 );
		add_action( 'plugins_loaded', array( $this, 'get_plugin_datas' ) );
		add_action( 'acf/include_fields', [ $this, 'my_register_fields' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_style' ] );
		add_action( 'plugins_loaded', [ $this, 'load_textdomain'] , 999);
		add_action( 'acf/init', [ $this, 'acfe_php_paths'] );

		add_filter( 'acf/settings/save_json', [ $this, 'acf_save_point' ] );
		add_filter( 'acf/settings/l10n_textdomain', [ $this, 'acf_textdomain' ] );
		add_filter( 'template_include', [ $this, 'load_template'] );

		register_activation_hook( __FILE__, [ $this, 'activation' ] );
		register_deactivation_hook( __FILE__, [ $this, 'deactivation' ] );

	}

	public function define_constants() {
		define( 'THFO_PLUGIN_VERSION', '1.4.2' );
		define( 'WP_PERICLES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		define( 'WP_PERICLES_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'WP_PERICLES_PLUGIN_DIR', untrailingslashit( 'WP_PERICLES' ) );
		define( 'WP_PERICLES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'THFO_PLUGIN_NAME', dirname( plugin_basename( __FILE__ ) ) );
		define( 'THFO_OPENWP_PLUGIN_FILE', plugin_basename( __FILE__ ) );
		define( 'THFO_SLUG', basename( __FILE__ ) );

		// Update
		define( 'WP_MAIN_FILE_PLUGIN_PATH',  __FILE__ );
		define( 'WP_PLUGIN_ID', '2162070');

		$path = wp_get_upload_dir();
		define( 'WP_PERICLES_IMPORT', $path['basedir'] . '/import/' );
		define( 'WP_PERICLES_IMPORT_TMP', $path['basedir'] . '/import/temp/' );
		define( 'WP_PERICLES_IMPORT_IMG', $path['basedir'] . '/import/img/' );
		define( 'WP_PERICLES_EXPORT_FOLDER', $path['basedir'] . '/import/export/' );

		define( 'THFO_CONSUMER_KEY', 'ck_2272fdcb154d3b68a0123966d24d7d40efeb5728');
		define( 'THFO_CONSUMER_SECRET', 'cs_72ce7a4edaed47d8387cfbdc98ef9e486d272766');

		// Define path and URL to the ACF plugin.
		//https://www.advancedcustomfields.com/resources/including-acf-within-a-plugin-or-theme/
		define( 'WP_PERICLES_ACF_PATH', WP_PERICLES_PLUGIN_PATH . '/3rd-party/acf/' );
		define( 'WP_PERICLES_ACF_URL', WP_PERICLES_PLUGIN_URL . '/3rd-party/acf/' );

		// Customize the url setting to fix incorrect asset URLs.
		add_filter( 'acf/settings/url', array( $this, 'my_acf_settings_url' ) );

	}

	public function activation() {
		$this->create_folders();
		if ( ! wp_next_scheduled( 'wppericles_hourly_cron' ) ) {
			$cron = wp_schedule_event( time(), 'hourly', 'wppericles_hourly_cron' );
		}
		if ( ! defined('ACF_PRO') && class_exists( 'ACF' ) ){
			deactivate_plugins( 'advanced-custom-fields/acf.php' );
		}

	}

	public function deactivation() {
		wp_clear_scheduled_hook( 'wppericles_cron' );
	}

	public function create_cpt() {
		create_cpt();
		flush_rewrite_rules();
	}

	public function load_acf(){
		// Include the ACF plugin.
		if (  ! class_exists( 'ACF' )  ) {
			include_once WP_PERICLES_ACF_PATH . 'acf.php';
		}

		require plugin_dir_path( __FILE__ ) . '/class/class-options.php';
	}

	public function load_files() {

		require plugin_dir_path( __FILE__ ) . '/class/class-format-data.php';
		require plugin_dir_path( __FILE__ ) . '/class/class-wpresidence.php';
		require plugin_dir_path( __FILE__ ) . '/class/class-wpcasa.php';
		require plugin_dir_path( __FILE__ ) . '/cron.php';
		require plugin_dir_path( __FILE__ ) . '/inc/cpt.php';
		require plugin_dir_path( __FILE__ ) . '/inc/location_tax.php';
		require plugin_dir_path( __FILE__ ) . '/inc/property_type_tax.php';
		require plugin_dir_path( __FILE__ ) . '/class/class-import.php';
		require plugin_dir_path( __FILE__ ) . '/inc/templating.php';
		require plugin_dir_path( __FILE__ ) . '/3rd-party/acf-fields/acf-biens.php';
		require plugin_dir_path( __FILE__ ) . '/3rd-party/acf-fields/acf-options.php';
		require plugin_dir_path( __FILE__ ) . '/admin/class-settings.php';
	//	require plugin_dir_path( __FILE__ ) . '/licence/class-licence.php';

	}

	public function load_style(){
		wp_enqueue_style( 'wppericles', WP_PERICLES_PLUGIN_URL . '/assets/css/wppericles.css' );
	}

	public function my_register_fields() {

		include_once plugin_dir_path( __FILE__ ) . '/3rd-party/acf-post-type-selector/post-type-selector-v5.php';

	}

	public function my_acf_settings_url( $url ) {
		return WP_PERICLES_ACF_URL;
	}

	public function acfe_php_paths(){
		acf_update_setting( 'acfe/php_save', WP_PERICLES_PLUGIN_PATH . '/3rd-party/acf-fields' );
		acf_update_setting( 'acfe/php_load', [ WP_PERICLES_PLUGIN_PATH . '/3rd-party/acf-fields' ] );
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

	/**
	 * @param $path
	 *
	 * @return string
	 */
	public function acf_save_point( $path ) {

		// update path
		$path = WP_PERICLES_PLUGIN_PATH . '/3rd-party/acf-fields';

		// return
		return $path;

	}

	public function acf_textdomain( $td ) {
		return $td = 'wp-pericles-import';
	}

	public function load_template( $template ) {
		// Post ID
		$post_id = get_the_ID();
		// For all other CPT
		if ( get_post_type( $post_id ) != 'real-estate-property' ) {
			return $template;
		}

		// Else use custom template
		if ( is_single() ) {
			return $this->get_template_hierarchy( 'single' );
		}
		return $template;
	}

	public function get_template_hierarchy( $template ) {
		// Get the template slug
		$template_slug = rtrim( $template, '.php' );
		$template      = $template_slug . '.php';

		// Check if a custom template exists in the theme folder, if not, load the plugin template file
		if ( $theme_file = locate_template( array( 'wppericles_templates/' . $template ) ) ) {
			$file = $theme_file;
		} else {
			$file = WP_PERICLES_PLUGIN_PATH . 'templates/' . $template;
		}

		return apply_filters( 'wppericles_repl_template_' . $template, $file );
	}

	/**
	 * Load plugin textdomain.
	 */
	public function load_textdomain() {
		$lang = load_plugin_textdomain( 'wp-pericles-import', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

}

new WPPericles();
