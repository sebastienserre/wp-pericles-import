<?php

namespace WP_PERICLES\IMPORT\WPResidence;

use function add_action;
use function add_post_meta;
use function get_field;
use function get_post_meta;
use function get_term_by;
use function get_the_ID;
use function intval;
use function sanitize_email;
use function sanitize_text_field;
use function sanitize_title;
use function strtolower;
use function strval;
use function update_option;
use function wp_enqueue_style;
use function wp_insert_post;
use function wp_insert_term;
use function wp_set_post_terms;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**'
 * Class WPResidence
 *
 * @package wp-pericles-import
 * @author  Sébastien SERRE
 * @since   1.1.0
 */
class WPResidence {

	public static $fields;

	public function __construct() {
		add_action( 'after_setup_theme', array( 'WP_PERICLES\IMPORT\WPResidence\WPResidence', 'create_custom_field' ) );
		add_action( 'add_meta_boxes', array( $this, 'asp_box' ) );
		add_action( 'admin_print_styles', array( $this, 'admin_css' ) );
		add_action( 'wppericles_after_insert_property', array( $this, 'create_agency' ), 10, 2 );
		self::$fields = self::get_wp_residence_fields();
	}

	public static function get_wp_residence_fields(){
		return self::$fields =
			[
				'NO_MANDAT',
				'TERRASSE',
				'NB_SE',
				'NATURE_CHAUFF',
				'ANNEE_CONS',
				'DPE_VAL1',
				'DPE_VAL2',
				'NO_DOSSIER',
				'TYPE_MANDAT',
				'HONORAIRES',
				'TRAVAUX',
				'CHARGES',
				'DEPOT_GARANTIE',
				'TAXE_HABITATION',
				'DATE_DISPO',
				'TAXE_FONCIERE',
				'CP_INTERNET',
				'VILLE_INTERNET',
				'QUARTIER',
				'RESIDENCE',
				'TRANSPORT',
				'PROXIMITE',
				'SECTEUR',
				'NB_PIECES',
				'SURF_CARREZ',
				'SURF_SEJOUR',
				'ETAGE',
				'CODE_ETAGE',
				'NB_ETAGE',
				'CUISINE',
				'NB_WC',
				'NB_PARK_INT',
				'NB_PARK_EXT',
				'GARAGE_BOX',
				'SOUS_SOL',
				'NB_CAVES',
				'TYPE_CHAUFF',
				'CODE_CLIENT',
				'TYPE_OFFRE',
				'ASCENSEUR',
				'BALCON',
				'PISCINE',
				'ACCES_HANDI',
				'MUR_MITOYENS',
				'FACADE_TERRAIN',
				'PRESTIGE',
				'INFO_TERRASSE',
				'INFO_BALCON',
				'DISPO',
				'LOYER2',
				'DATE_LIBRE',
				'NET_VENDEUR',
				'HONO_ACQ',
				'SURF_JARDIN',
				'COS',
				'SHON',
				'INFO_KM',
				'INFO_CONTACT',
				'CONTACT',
				'INFO_CONTACT',
				'CESSIONDROITAUBAIL',
				'LONGUEURVITRINE',
				'INTERPHONE',
				'MONTECHARGE',
				'IMMEUBLEINDEPENDANT',
				'IMMEUBLECOLLECTIF',
				'IMMEUBLEPRESTIGE',
				'DIGICODE',
				'CLIMATISATION',
				'GARDIENNAGE',
				'VIAGER',
				'NON_ASSUJETTI_DPE',
				'DPE_VIERGE',
				'TAUX_HONORAIRES',
				'COPROPRIETE',
				'NB_LOTS_COPROPRIETE',
				'MONTANT_QUOTE_PART',
				'PROCEDURE_SYNDICAT',
				'DETAIL_PROCEDURE',
				'COUP_DE_COEUR',
				'REPARTITION_ACQ',
				'NATURE_CHARGES',
				'MONTANT_CHARGES',
				'COMPLEMENT_LOYER',
				'HONO_ETAT_LIEUX_TTC',
			];
	}

	public function asp_box() {
		add_meta_box( 'asp-meta-box-id', __( 'ASP number', 'wp-pericles-import' ), array( $this, 'asp_box_render'), 'estate_property', 'side', 'high' );
	}

	public  function asp_box_render() {
		$asp = get_post_meta( get_the_ID(), 'wppericles_agence_wp_pericles_asp', true );
		if ( !empty( $asp ) ) {
			echo '<p>' . $asp . '</p>';
		}
	}
	/**
	 * Add WP Residence Meta
	 *
	 * @param $detail
	 * @param $bien
	 *
	 * @return mixed
	 * @author  Sébastien SERRE
	 * @package wp-pericles-import
	 * @since   1.1.0
	 */
	public static function estate_property_meta( $detail, $bien ) {
		$detail['property_bedrooms']                 = intval( strval( $bien->NB_CHAMBRES ) );
		$detail['property_bathrooms']                = intval( strval( $bien->NB_SDB ) );
		$detail['property_lot_size']                 = intval( strval( $bien->SURF_TERRAIN ) );
		$detail['property_size']                     = intval( strval( $bien->SURF_HAB ) );
		$detail['property_price']                    = intval( strval( $bien->PRIX ) );
		$detail['property_zip']                      = sanitize_text_field( strval( $bien->CP_OFFRE ) );
		$detail['property_address']                  = sanitize_text_field( strval( $bien->ADRESSE1_OFFRE ) );
		$detail['wppericles_agence_wp_pericles_asp'] = sanitize_text_field( strval( $bien->NO_ASP ) );

		$field_list = get_field( 'wppericles_wp_residence_fields', 'options' );
		foreach ( $field_list as $field) {
			$detail[ strtolower( $field) ] = strval( $bien->$field );
		}

		return $detail;
	}



	public static function create_custom_field() {

		$field_list = get_field( 'wppericles_wp_residence_fields', 'options' );
		foreach ( $field_list as $key => $value ) {
			$fields['wpestate_custom_fields_list']['add_field_name'][ $key ]  = strtolower( $value );
			$fields['wpestate_custom_fields_list']['add_field_label'][ $key ] = strtolower( $value );
			$fields['wpestate_custom_fields_list']['add_field_type'][ $key ]  = 'short text';
			$fields['wpestate_custom_fields_list']['add_field_order'][ $key ] = 'NaN';
			update_option( 'wpresidence_admin', $fields );
		}
	}

	public function admin_css() {
		wp_enqueue_style( 'wpresidence-admin-css', WP_PERICLES_PLUGIN_URL . '/assets/css/wpresidence-admin.css','');
	}

	public function create_agency( $id, $bien ) {
		$agency_name = sanitize_title( strval( $bien->RS_AGENCE ) );
		$author = get_field( 'wppericles_utilisateur_importateur', 'option' );
		$args        =
			[
				'post_type'      => array( 'estate_agency' ),
				'posts_per_page' => - 1,
				'name'           => $agency_name,
			];
		$agency      = get_posts( $args );
		if ( ! empty( $agency ) ){
			$agency_id = $agency[0]->ID;
		} else {
			$agency_id = '';
		}

		$agency_address = sanitize_text_field( strval( $bien->ADRESSE1_AGENCE ) ) . ' ' . sanitize_text_field( strval(
			$bien->CP_AGENCE ) ) . ' ' . sanitize_text_field( strval( $bien->VILLE_AGENCE ) );

		$meta = [
			'agency_address' => $agency_address,
			'agency_email'   => sanitize_email( strval( $bien->MAIL_AGENCE ) ),
			'agency_phone'   => sanitize_text_field( strval( $bien->TEL_AGENCE ) ),
			'agency_website' => esc_url_raw( strval( $bien->WEB_AGENCE ) ),
		];

		$postarr   = array(
			'ID'             => $agency_id,
			'post_author'    => $author->ID,
			'post_content'   => '',
			'post_title'     => sanitize_text_field( strval( $bien->RS_AGENCE ) ),
			'post_status'    => 'publish',
			'post_type'      => 'estate_agency',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_name'      => $agency_name,
			'meta_input'     => $meta,
		);
		$agency_id = wp_insert_post( $postarr );

		$agency_city_name = sanitize_title( strval( $bien->VILLE_AGENCE ) );
		$agency_city = get_term_by( 'slug', $agency_city_name, 'city_agency' );
		if ( empty( $agency_city ) ){
			$city_term_id = wp_insert_term( strval( $bien->VILLE_AGENCE ), 'city_agency' );
			$agency_city = get_term_by( 'slug', sanitize_title( strval( $bien->VILLE_AGENCE ) ), 'city_agency' );
		}

		wp_set_post_terms( $agency_id, $agency_city->term_id, 'city_agency');

		wp_update_post( [ 'ID' => $id, 'meta_input' => [ 'property_agent' => $agency_id ] ] );
		add_post_meta( $id, 'property_agent', $agency_id );
	}
}

if ( 'estate_property' === get_field( 'wppericles_list_external', 'option' ) ) {
	new WPResidence();
}
