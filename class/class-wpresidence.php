<?php

namespace WP_PERICLES\IMPORT\WPResidence;

use function get_field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class WPResidence {

}

if ( 'wpresidence' === get_field( 'wppericles_list_external' ) ) {
	new WPResidence();
}
