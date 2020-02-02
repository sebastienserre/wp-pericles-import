<?php

namespace WP_PERICLES\Options;

use function acf_add_options_page;
use function add_action;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class Options {

	public function __construct() {
		add_action( 'acf/init', array( $this, 'add_option_page' ), 15 );
	}

	public function add_option_page() {

		if ( function_exists( 'acf_add_options_sub_page' ) ) {

			acf_add_options_sub_page(
				array(
					'page_title' => __( 'Pericles Import Options', 'wp-pericles-import' ),
					'menu_title'  => 'Pericles Import Options',
					'parent_slug' => 'options-general.php',
				)
			);
		}
	}
}

new Options();
