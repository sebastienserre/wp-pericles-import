<?php

namespace WP_PERICLES\Options;

use function add_action;
use function get_post_type;
use function var_dump;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class Options {
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'add_option_page' ), 999 );
		add_filter( 'acf/load_field/name=wppericles_existing_cpt', [ $this, 'load_cpt_list']);
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
		}
	}

	public function load_cpt_list( $field ){
		$field['choices'] = get_post_types();
		return $field;
	}
}

new Options();
