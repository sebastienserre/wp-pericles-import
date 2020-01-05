<?php

namespace WP_PERICLES\IMPORT\WPCasa;

use WP_PERICLES\IMPORT\FormatData\Format_Data;
use WP_PERICLES\IMPORT\WPResidence\WPResidence;
use function class_exists;
use function get_field;
use function intval;
use function sanitize_text_field;
use function strval;
use function update_post_meta;
use function wp_die;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Class WPCasa
 *
 * @package wp-pericles-import
 * @author  Sébastien SERRE
 * @since   1.4.0
 */
class WPCasa {

	public function __construct() {
	}

	public static function listing_meta( $detail, $bien, $id ) {
		$datas                                       = Format_Data::format_meta( $bien );
		$detail['_price']                            = intval( $datas['bien_prix'] );
		$detail['_listing_id']                       = sanitize_text_field( $datas['bien_mandat'] );
		$detail['_map_address']                      = sanitize_text_field( $datas['adresse_ville_offre'] );
		$detail['_agent_name']                       = sanitize_text_field( $datas['bien_negociateur'] );
		$detail['_details_1']                        = intval( $datas['interieur_nb_chambres'] );
		$detail['_details_2']                        = intval( $datas['interieur_nb_sdb'] );
		$detail['_details_3']                        = sanitize_text_field( $datas['exterieur_surface_terrain'] );
		$detail['_details_4']                        = sanitize_text_field( $datas['interieur_surface_sejour'] );
		$detail['_details_5']                        = sanitize_text_field( $datas['exterieur_terrasse'] );
		$detail['_details_6']                        = sanitize_text_field( $datas['annexes_nb_parking_interieur'] );
		$detail['_details_7']                        = sanitize_text_field( $datas['pratique_type_chauffage'] );
		$detail['wppericles_agence_wp_pericles_asp'] = sanitize_text_field( $datas['agence_wp_pericles_asp'] );

		$chb = intval( $datas['interieur_nb_chambres'] );
		$update_id = update_post_meta( $id, 'details_1', $chb );

		return $detail;
	}
}

if ( 'listing' === get_field( 'wppericles_list_external', 'option' ) && class_exists( 'WPSight_Framework') ) {
	new WPCasa();
}