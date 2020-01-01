<?php

namespace WP_PERICLES\Templates;

use function add_action;
use function apply_filters;
use function array_filter;
use function esc_attr;
use function explode;
use function get_field;
use function get_fields;
use function ob_get_clean;
use function ob_start;
use function str_replace;
use function ucwords;
use function var_dump;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

function clean_key( $string ) {
	$string = explode( 'wppericles_', $string );
	$string = str_replace( '_', ' ', $string );
	$string = $string[1];

	return apply_filters( 'wppericles_clean_key', $string );
}

add_action( 'wppericles_single_property_summary', 'WP_PERICLES\Templates\display_title' );
function display_title() {
	$title = '<h2>' . get_the_title() . '</h2>';
	echo apply_filters( 'wppericles_single_title', $title );
}

add_action( 'wppericles_single_property_content', 'the_content' );

add_action( 'wppericles_single_agency_details', 'WP_PERICLES\Templates\display_agence_details' );
function display_agence_details() {
	$agence = get_field( 'wppericles_agence' );
	ob_start();
	?>
    <div class="property_agency_details">
        <h2><?php _e( 'Agency Details'); ?></h2>
		<?php
		foreach ( $agence as $key => $value ) {
			if ( ! empty( $value ) ) {
				$key = clean_key( $key );
				echo '<p class="property_agency_detail">' . esc_attr( ucwords( $key ) ) . ' : ' . esc_attr( $value ) . '</p>';
			}
		}
		?>
    </div>
	<?php
	$render = ob_get_clean();
	echo apply_filters( 'wppericles_agency_details', $render );
}

add_action( 'wppericles_single_property_details', 'WP_PERICLES\Templates\display_property_details' );
function display_property_details() {
	$fields = get_fields();
	foreach ( $fields as $key => $field ) {
	    $field = array_filter( $field );
		ob_start();
		if ( ! empty( $field ) && 'wppericles_agence' !== $key && 'wppericles_adresse' !== $key ) {
			?>
            <div class="property_details <?php echo $key ?>_details" >
				<?php
				foreach ( $field as $k => $value ) {
					if ( ! empty( $value ) ) {
						$label = clean_key( $k );
						echo '<p class="property_' . $k . '_detail">' . esc_attr( ucwords( $label ) ) . ' : ' .
						     esc_attr(
							     $value ) . '</p>';
					}
				}
				?>
            </div>
			<?php
		}
	}
	$render = ob_get_clean();
	echo apply_filters( 'wppericles_property_details', $render );
}
