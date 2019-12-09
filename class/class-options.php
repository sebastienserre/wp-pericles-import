<?php

namespace WP_PERICLES\Options;

use function acf_add_options_page;
use function add_action;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class Options {

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'add_option_page' ), 999 );
	}

	public function add_option_page() {

		if ( function_exists( 'acf_add_options_page' ) ) {

			acf_add_options_page(
				array(
					'page_title' => __( 'Pericles Import Settings', 'wp-pericles-import' ),
					'menu_slug'  => 'pericles-import-settings',
					'capability' => 'edit_posts',
					'redirect'   => false,
				)
			);
			acf_add_options_page(
				array(
					'page_title' => __( 'Listings Settings', 'wp-pericles-import' ),
					'menu_slug'  => 'listing-settings',
					'capability' => 'edit_posts',
					'redirect'   => false,
				)
			);
		}
	}
}

new Options();
