<?php

namespace WP_PERICLES\IMPORT;

use WP_Error;
use WP_PERICLES\IMPORT\FormatData\Format_Data;
use WP_PERICLES\IMPORT\WPCasa\WPCasa;
use WP_PERICLES\IMPORT\WPResidence\WPResidence;
use function add_action;
use function apply_filters;
use function array_filter;
use function array_push;
use function basename;
use function copy;
use function date;
use function delete_post_meta;
use function do_action;
use function error_log;
use function explode;
use function file_exists;
use function get_field;
use function get_post_meta;
use function get_term_by;
use function get_the_ID;
use function has_post_thumbnail;
use function implode;
use function intval;
use function is_wp_error;
use function mkdir;
use function nl2br;
use function preg_replace;
use function range;
use function sanitize_text_field;
use function sanitize_title;
use SimpleXMLElement;
use function set_post_thumbnail;
use function strval;
use function update_field;
use function update_post_meta;
use function wp_check_filetype;
use function wp_delete_file;
use function wp_generate_attachment_metadata;
use function wp_get_attachment_url;
use function wp_get_upload_dir;
use function wp_insert_attachment;
use function wp_insert_post;
use function wp_insert_term;
use function wp_update_attachment_metadata;
use function wp_upload_dir;
use const WP_DEBUG;
use const WP_PERICLES_EXPORT_FOLDER;
use const WP_PERICLES_IMPORT;
use const WP_PERICLES_IMPORT_IMG;
use const WP_PERICLES_IMPORT_TMP;
use function wp_set_post_terms;
use XMLReader;
use ZipArchive;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Class Import
 *
 * @package WP_PERICLES\IMPORT
 */
class Import {

	protected $external;
	protected $zipname;
	protected $name;
	protected $cpt;
	protected $location;
	protected $type;
	protected $node;


	public function __construct() {
		add_action( 'wppericles_hourly_cron', array( $this, 'extract_photo' ) );

		$this->external = $this->get_external();
		$this->zipname  = $this->get_zipname();
		$this->name     = $this->get_name();
		$this->cpt      = $this->get_cpt();
		$this->location = $this->get_location();
		$this->type     = $this->get_property_type();
		$this->node     = $this->get_node_name();


		if ( ! empty( $_GET['test'] ) && 'ok' === $_GET['test'] ) {
			add_action( 'admin_init', array( $this, 'extract_photo' ) );
		}
	}


	/**
	 * @return string
	 */
	public function set_zipname() {
		$zipname = get_field( 'wppericles_nom_zip', 'option' );

		return $zipname;
	}

	/**
	 * @return string
	 */
	public function get_zipname() {
		return $this->set_zipname();
	}

	public function get_node_name( $element ) {
		if ( Format_Data::is_pericles_air() ) {
			$node = $element->ANNONCES->ANNONCE;
		} else {
			$node = $element->BIEN;
		}

		return $node;
	}

	/**
	 * @return string
	 */
	public function set_name() {
		$name = $this->zipname;
		if ( ! Format_Data::is_pericles_air() ) {
			$name = explode( '.', $this->zipname );
			$name = $name[0] . '.XML';
		}

		return $name;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->set_name();
	}

	public function get_cpt() {
		return $this->set_cpt();
	}

	public function get_location() {
		return $this->set_location();
	}

	public function get_property_type() {
		return $this->set_property_type();
	}

	public function get_external() {
		return $this->set_external();
	}

	public function set_external() {
		$external = get_field( 'wppericles_list_external', 'option' );

		return $external;
	}

	public function set_location() {
		if ( ! empty( $this->cpt ) ) {
			switch ( $this->cpt ) {
				case 'estate_property' : // if WP Residence
					$taxo = 'property_city';
					break;
				case 'listing' : // if WPCasa
					$taxo = 'location';
					break;
				case 'real-estate-property' :
				default:
					$taxo = 'location_tax';
					break;
			}
		}

		return apply_filters( 'wppericles_location', $taxo );
	}

	public function set_property_type() {
		if ( ! empty( $this->cpt ) ) {
			switch ( $this->cpt ) {
				case 'estate_property' : // if WP Residence
					$type = 'property_category';
					break;
				case 'listing' : // if WPCasa
					$type = 'property_types';
					break;
				case 'real-estate-property' :
				default:
					$type = 'listing-type';
					break;
			}
		}

		return apply_filters( 'wppericles_property_type', $type );
	}

	public function get_property_situation( $bien ) {
		if ( ! Format_Data::is_pericles_air() ) {
			$situation_name = sanitize_text_field( strval( $bien->VILLE_OFFRE ) );
		} else {
			$situation_name = sanitize_text_field( strval( $bien->VILLE ) );
		}
		$situation = get_term_by( 'name', $situation_name, $this->location );
		if ( ! $situation ) {
			$terms     = wp_insert_term( $situation_name, $this->location );
			$situation = get_term_by( 'name', $situation_name, $this->location );
		}

		return $situation;
	}

	public function get_property_term( $bien ) {
		if ( Format_Data::is_pericles_air() ) {
			$property_name = sanitize_text_field( strval( $bien->CAT ) );
		} else {
			$property_name = sanitize_text_field( strval( $bien->CATEGORIE ) );
		}
		$property_term = get_term_by( 'name', $property_name, $this->type );
		if ( ! $property_term ) {
			$insert        = wp_insert_term( $property_name, $this->type );
			$property_term = get_term_by( 'name', $property_name, $this->type );
		}

		return $property_term;
	}

	public function set_cpt() {
		if ( get_field( 'wppericles_create_cpt', 'option' ) ) {
			$cpt = 'real-estate-property';
		} elseif ( ! empty( get_field( 'wppericles_list_external', 'option' ) ) ) {
			$cpt = get_field( 'wppericles_list_external', 'option' );
		} else {
			$cpt = get_field( 'wppericles_existing_cpt', 'option' );
		}

		return apply_filters( 'wppericles_cpt', $cpt );
	}
	
	public function extract_photo() {

		$date = date_i18n( 'Y-m-d-H-i' );

		if ( ! empty( $this->zipname ) ) {
			$file = WP_PERICLES_IMPORT . $this->zipname;
		}

		/**
		 * If no export file, we stop
		 **/
		if ( ! file_exists( $file ) ) {
			return;
		}

		if ( file_exists( WP_PERICLES_IMPORT . 'pid.txt' ) ) {
			return;
		}

		$this->create_pid();

		copy( WP_PERICLES_IMPORT . $this->zipname, WP_PERICLES_EXPORT_FOLDER . 'export-' . $date . '.zip' );

		error_log( "$this->zipname copied" );
		if ( ! Format_Data::is_pericles_air() ) {
			$zip = new ZipArchive();
			$res = $zip->open( $file );
			if ( $res === true ) {
				$zip->extractTo( WP_PERICLES_IMPORT_TMP );
				$zip->extractTo( WP_PERICLES_IMPORT_IMG );
				$zip->close();
			}
		}

		if ( Format_Data::is_pericles_air() ) {
			if ( ! file_exists( WP_PERICLES_IMPORT_TMP ) ) {
				mkdir( WP_PERICLES_IMPORT_TMP, 0777, true );
			}
			copy( WP_PERICLES_IMPORT . $this->zipname, WP_PERICLES_IMPORT_TMP . $this->zipname );

		}

		wp_delete_file( WP_PERICLES_IMPORT_IMG . $this->name );
		$this->check_listing();
	}

	public function check_listing() {
		error_log( 'start check listing' );
		/**
		 * Retrieve listing
		 */
		$args     = array(
			'post_type'      => $this->cpt,
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		);
		$listings = get_posts( $args );

		/**
		 * Search in listings if property exists in xml
		 * if not => draft
		 */

		$asp = [];
		if ( ! empty( $listings ) ) {
			foreach ( $listings as $listing ) {
				$listing_asp = get_post_meta( $listing->ID, 'wppericles_agence_wp_pericles_asp', true );
				$xml         = new XMLReader();
				$xml_file    = WP_PERICLES_IMPORT_TMP . $this->name;
				$open        = $xml->open( $xml_file );
				$read        = $xml->read();


				$element = new SimpleXMLElement( $xml->readOuterXml() );
				foreach ( $element->BIEN as $bien ) {

					$asp_bien = strval( $bien->NO_ASP );
					if ( $asp_bien === $listing_asp ) {
						array_push( $asp, $listing->ID );
					}
				}

			}
		}
		error_log( 'end check' );
		$this->read_xml();

	}

	public function format_date_post( $bien ) {
		if ( Format_Data::is_pericles_air() ) {
			if ( ! empty( $bien->DATE_CREATION ) ) {
				$date = $bien->DATE_CREATION . ' ' . '09:00:00';
			}
		} else {
			$post_date = explode( '/', $bien->DATE_OFFRE );
			$date      = $post_date[2] . '-' . $post_date[1] . '-' . $post_date[0] . ' ' . '09:00:00';
		}

		return $date;
	}

	public function format_date_modif( $bien ) {
		if ( Format_Data::is_pericles_air() ) {
			$post_modified = $bien->DATE_MODIF_PRIX . ' ' . '09:00:00';
		} else {
			$post_modified = explode( '/', $bien->DATE_MODIF );
			$post_modified = $post_modified[2] . '-' . $post_modified[1] . '-' . $post_modified[0] . ' ' . '09:00:00';
		}

		return $post_modified;
	}

	public function read_xml() {
		set_time_limit( 1800 );
		error_log( 'start read_xml' );
		$xml     = new XMLReader();
		$open    = $xml->open( WP_PERICLES_IMPORT_TMP . $this->name );
		$read    = $xml->read();
		$name    = $xml->name;
		$element = new SimpleXMLElement( $xml->readOuterXml() );
		$node    = $this->get_node_name( $element );

		foreach ( $node as $bien ) {
			$args    = array(
				'post_type'  => $this->cpt,
				'meta_query' => array(
					'relation' => 'OR',
					array(
						'key'     => 'wppericles_agence_wp_pericles_asp',
						'value'   => strval( $bien->NO_ASP ),
						'compare' => '=',
					),
				),
			);
			$listing = get_posts( $args );

			/**
			 * Retrieve XML datas
			 */
			if ( $listing ) {
				$listing_id = $listing[0]->ID;
			} else {
				$listing_id = '';
			}


			/**
			 * Get Authors ID
			 */
			$author = get_field( 'wppericles_utilisateur_importateur', 'option' );

			$situation = $this->get_property_situation( $bien );

			$property_term = $this->get_property_term( $bien );

			error_log( $listing_id . ' ' . $situation->term_id );

			$detail = [];
			$detail = $this->format_postmeta( $detail, $bien, $element );

			/**
			 * format date
			 */

			$post_date     = $this->format_date_post( $bien );
			$post_modified = $this->format_date_modif( $bien );

			$args_title = $this->create_title( $bien );
			$content    = $this->create_content( $bien );

			$title = implode( ' - ', array_filter( $args_title ) );
			$slug  = sanitize_title( $title );

			$postarr = array(
				'ID'             => $listing_id,
				'post_author'    => $author->ID,
				'post_content'   => $content,
				'post_title'     => $title,
				'post_status'    => 'publish',
				'post_type'      => $this->cpt,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_date'      => $post_date,
				'post_modified'  => $post_modified,
				'post_name'      => $slug,
				'meta_input'     => $detail,
			);

			$insert = wp_insert_post( $postarr, true );
			if ( is_wp_error( $insert ) ){
				error_log( "error on $i" );
				error_log( "modified: $post_modified & post: $post_date" );
				error_log( $insert->get_error_message() );
			}
				$this->format_postmeta( $detail, $bien, $element, $insert );

			/**
			 * Fires just after Property is inserted in db
			 * @args $insert ID of property
			 * @args $bien, data from Pericles
			 */
			do_action( 'wppericles_after_insert_property', $insert, $bien );

			$cat = wp_set_post_terms( $insert, $property_term->name, $this->type, true );
			$loc = wp_set_post_terms( $insert, $situation->term_id, $this->location, true );

			$this->prepare_gallery( $insert, $bien, $title );
			$i++;
		}
		/**
		 * On supprime le dossier temporaire puis on le recrée vide pour le prochain import.
		 */
		$this->delete_files( WP_PERICLES_IMPORT_TMP );

		if ( ! file_exists( WP_PERICLES_IMPORT_TMP ) ) {
			mkdir( WP_PERICLES_IMPORT_TMP, 0777, true );
		}
		/**
		 * Delete export.ZIP
		 */
		if ( file_exists( WP_PERICLES_IMPORT . $this->zipname ) && false === WP_DEBUG ) {
			wp_delete_file( WP_PERICLES_IMPORT . $this->zipname );
		}
		$this->delete_pid();
		error_log( 'stop read_xml' );
	}

	public function format_postmeta( $detail, $bien, $element = '', $id ='' ) {
		switch ( $this->cpt ) {
			case 'estate_property': // if WP Residence
				$detail = WPResidence::estate_property_meta( $detail, $bien );
				break;
			case 'listing': //if WP Casa
				$detail = WPCasa::listing_meta( $detail, $bien, $id );
				break;
			case 'real-estate-property':
			default:
				if ( ! Format_Data::is_pericles_air() ) {
					$detail = $this->real_estate_property_meta( $detail, $bien );
				} else {
					$detail                                                    = $this->air_real_estate_property_meta( $detail, $bien );
					$detail['wppericles_agence_wppericles_raison_sociale']     = sanitize_text_field( strval(
						$element->RAISON_SOCIALE ) );
					$detail['wppericles_agence_wppericles_adresse_1_agence']   = sanitize_text_field( strval(
						$element->ADRESSES ) );
					$detail['wppericles_agence_wppericles_code_postal_agence'] = sanitize_text_field( strval(
						$element->CP
					) );
					$detail['wppericles_agence_wppericles_ville_agence']       = sanitize_text_field( strval(
						$element->VILLE ) );
					$detail['wppericles_agence_wppericles_telephone_agence']   = sanitize_text_field( strval(
						$element->TEL ) );
					$detail['wppericles_agence_wppericles_site_web_agence']    = sanitize_text_field( strval(
						$element->WEB ) );

				}
				break;
		}

		/**
		 * Fire after data from XML has been retrieved and before property is saved.
		 * You can easily match your own post meta with this filter
		 */
		return apply_filters( 'wppericles_listings_meta', $detail, $bien );
	}

	public function air_real_estate_property_meta( $detail, $bien ) {
		$detail['wppericles_adresse_wppericles_ville_offre']           = sanitize_text_field( strval( $bien->VILLE ) );
		$detail['wppericles_bien_wppericles_mandat']                   = intval( strval( $bien->NO_MANDAT ) );
		$detail['wppericles_interieur_wppericles_nb_chambres']         = intval( strval( $bien->NB_CHB ) );
		$detail['wppericles_interieur_wppericles_nb_sdb']              = intval( strval( $bien->NB_SDB ) );
		$detail['wppericles_exterieur_wppericles_surface_terrain']     = intval( strval( $bien->SURF_JAR ) );
		$detail['wppericles_interieur_wppericles_surface_habitable']   = intval( strval( $bien->SURF_HAB ) );
		$detail['wppericles_exterieur_wppericles_terrasse']            = intval( strval( $bien->NB_TERRASSE ) );
		$detail['wppericles_interieur_wppericles_nb_sde']              = intval( strval( $bien->NB_SE ) );
		$detail['wppericles_pratique_wppericles_nature_chauffage']     = sanitize_text_field( strval( $bien->NAT_CHAUFF ) );
		$detail['wppericles_bien_annee_de_construction']               = intval( strval( $bien->ANNEE_CONS ) );
		$detail['wppericles_bien_wppericles_prix']                     = intval( strval( $bien->PV ) );
		$detail['wppericles_adresse_wppericles_code_postal_offre']     = sanitize_text_field( strval( $bien->CP_WEB ) );
		$detail['wppericles_adresse_wppericles_ville_offre']           = sanitize_text_field( strval( $bien->VILLE_WEB ) );
		$detail['wppericles_bien_wppericles_negociateur']              = sanitize_text_field( strval(
			$bien->CONTACTS->CONTACT->LIBELLE ) );
		$detail['wppericles_divers_wppericles_score_dpe']                         = sanitize_text_field( intval( strval( $bien->VAL_DPE ) ) );
		$detail['wppericles_divers_wppericles_score_ges']                         = sanitize_text_field( strval( $bien->VAL_GES ) );
		$detail['wppericles_agence_wp_pericles_asp']                   = sanitize_text_field( strval( $bien->NO_ASP ) );
		$detail['wppericles_agence_wppericles_code_societe']           = sanitize_text_field( strval( $bien->CODE_SOCIETE ) );
		$detail['wppericles_agence_wppericles_code_site']              = sanitize_text_field( strval( $bien->CODE_SITE ) );
		$detail['wppericles_agence_wppericles_dossier']                = sanitize_text_field( strval( $bien->NO_DOSSIER ) );
		$detail['wppericles_bien_wppericles_type_mandat']              = sanitize_text_field( strval( $bien->TYPE_MANDAT ) );
		$detail['wppericles_bien_wppericles_honoraires']               = sanitize_text_field( strval( $bien->HONO ) );
		$detail['wppericles_bien_wppericles_travaux']                  = sanitize_text_field( strval( $bien->TRAVAUX ) );
		$detail['wppericles_bien_wppericles_charges']                  = sanitize_text_field( strval( $bien->CHARGES_ANN ) );
		$detail['wppericles_bien_wppericles_depot_garantie']           = sanitize_text_field( strval( $bien->HRAL ) );
		$detail['wppericles_bien_wppericles_taxe_habitation']          = sanitize_text_field( strval( $bien->TAXE_HAB ) );
		$detail['wppericles_bien_date_disponibilite']                  = sanitize_text_field( strval( $bien->DATE_DISP ) );
		$detail['wppericles_bien_wppericles_taxe_fonciere']            = sanitize_text_field( strval( $bien->TAXE_FONC ) );
		$detail['wppericles_internet_wppericles_code_postal_internet'] = sanitize_text_field( strval( $bien->CP_WEB ) );
		$detail['wppericles_internet_wppericles_ville_internet']       = sanitize_text_field( strval( $bien->VILLE_WEB
		) );
		$detail['wppericles_pratique_wppericles_quartier']             = sanitize_text_field( strval( $bien->QUARTIER ) );
		$detail['wppericles_pratique_wppericles_residence']            = sanitize_text_field( strval( $bien->RESIDENCE ) );
		$detail['wppericles_pratique_wppericles_transport']            = sanitize_text_field( strval( $bien->TRANSPORT ) );
		$detail['wppericles_pratique_wppericles_proximite']            = sanitize_text_field( strval( $bien->PROXIMITE ) );
		$detail['wppericles_interieur_wppericles_nb_pieces']           = sanitize_text_field( strval( $bien->NB_PCE ) );
		$detail['wppericles_interieur_wppericles_surface_carrez']      = sanitize_text_field( strval( $bien->SURF_CARR ) );
		$detail['wppericles_interieur_wppericles_surface_sejour']      = sanitize_text_field( strval( $bien->SURF_SEJ ) );
		$detail['wppericles_pratique_wppericles_etage']                = sanitize_text_field( strval( $bien->ETAGE ) );
		$detail['wppericles_interieur_wppericles_nb_etages']           = sanitize_text_field( strval( $bien->NB_ETAGE ) );
		$detail['wppericles_interieur_wppericles_cuisine']             = sanitize_text_field( strval( $bien->TYPE_CUIS ) );
		$detail['wppericles_interieur_wppericles_nb_wc']               = sanitize_text_field( strval( $bien->NB_WC ) );
		$detail['wppericles_annexes_wppericles_nb_parking_interieur']  = sanitize_text_field( strval( $bien->NB_PARK_INT ) );
		$detail['wppericles_annexes_wppericles_nb_parking_exterieur']  = sanitize_text_field( strval( $bien->NB_PARK_EXT ) );
		$detail['wppericles_annexes_wppericles_nb_garage_box']         = sanitize_text_field( strval(
			$bien->NB_GAR ) );
		$detail['wppericles_annexes_wppericles_nb_caves']              = sanitize_text_field( strval( $bien->NB_CAVE ) );
		$detail['wppericles_pratique_wppericles_type_chauffage']       = sanitize_text_field( strval( $bien->TYPE_CHAUFF ) );
		$detail['wppericles_agence_wppericles_type_offre']             = sanitize_text_field( strval( $bien->TYPE_MANDAT ) );
		$detail['wppericles_pratique_wppericles_ascenseur']            = sanitize_text_field( strval( $bien->ASCE ) );
		$detail['wppericles_exterieur_wppericles_balcon']              = sanitize_text_field( strval( $bien->NB_BALCON
		) );
		$detail['wppericles_exterieur_wppericles_piscine']             = sanitize_text_field( strval( $bien->PISCINE ) );
		$detail['wppericles_pratique_wppericles_acces_handicape']      = sanitize_text_field( strval( $bien->HAND ) );
		$detail['wppericles_divers_wppericles_mur_mitoyens']                      = sanitize_text_field( strval( $bien->NB_MUR_MIT ) );
		$detail['wppericles_pratique_wppericles_immeuble_prestige']    = sanitize_text_field( strval( $bien->STANDING ) );
		$detail['wppericles_bien_wppericles_dispo']                    = sanitize_text_field( strval( $bien->DISPO ) );
		$detail['wppericles_bien_wppericles_loyer2']                   = sanitize_text_field( strval( $bien->LOYER ) );
		$detail['wppericles_exterieur_wppericles_surface_jardin']      = sanitize_text_field( strval( $bien->SURF_JAR ) );
		$detail['wppericles_divers_wppericles_interphone']                        = sanitize_text_field( strval( $bien->INTERPHONE ) );
		$detail['wppericles_pratique_wppericles_digicode']             = sanitize_text_field( strval( $bien->DIGICODE ) );
		$detail['wppericles_divers_wppericles_gardiennage']                       = sanitize_text_field( strval( $bien->GARDIENNAGE ) );
		$detail['wppericles_bien_wppericles_asp_lot']                  = sanitize_text_field( strval( $bien->NO_ASP ) );
		$detail['wppericles_bien_wppericles_viager']                   = sanitize_text_field( strval( $bien->VIAGER ) );
		$detail['wppericles_divers_wppericles_non_assujetti_dpe']                 = sanitize_text_field( strval( $bien->NON_DPE ) );
		$detail['wppericles_bien_wppericles_copropriete']              = sanitize_text_field( strval( $bien->COPROPRIETE ) );
		$detail['wppericles_bien_wppericles_nb_de_lot_copropriete']    = sanitize_text_field( strval( $bien->NB_LOTS_COPRO ) );
		$detail['wppericles_bien_wppericles_montant_quote_part']       = sanitize_text_field( strval( $bien->MONTANT_QUOTEPART ) );
		$detail['wppericles_bien_wppericles_procedure_syndicat']       = sanitize_text_field( strval(
			$bien->PROCEDURE_SYND ) );
		$detail['wppericles_prix_honoraires_etat_lieux_ttc']           = sanitize_text_field( strval(
			$bien->HONO_ETAT_LIEU_LOC ) );

		return $detail;
	}

	public function real_estate_property_meta( $detail, $bien ) {
		$detail['wppericles_adresse_wppericles_ville_offre']           = sanitize_text_field( strval( $bien->VILLE_OFFRE ) );
		$detail['wppericles_bien_wppericles_mandat']                   = intval( strval( $bien->NO_MANDAT ) );
		$detail['wppericles_interieur_wppericles_nb_chambres']         = intval( strval( $bien->NB_CHAMBRES ) );
		$detail['wppericles_interieur_wppericles_nb_sdb']              = intval( strval( $bien->NB_SDB ) );
		$detail['wppericles_exterieur_wppericles_surface_terrain']     = intval( strval( $bien->SURF_TERRAIN ) );
		$detail['wppericles_interieur_wppericles_surface_habitable']   = intval( strval( $bien->SURF_HAB ) );
		$detail['wppericles_exterieur_wppericles_terrasse']            = intval( strval( $bien->TERRASSE ) );
		$detail['wppericles_interieur_wppericles_nb_sde']              = intval( strval( $bien->NB_SE ) );
		$detail['wppericles_pratique_wppericles_nature_chauffage']     = sanitize_text_field( strval( $bien->NATURE_CHAUFF ) );
		$detail['wppericles_bien_annee_de_construction']               = intval( strval( $bien->ANNEE_CONS ) );
		$detail['wppericles_bien_wppericles_prix']                     = intval( strval( $bien->PRIX ) );
		$detail['wppericles_adresse_wppericles_code_postal_offre']     = sanitize_text_field( strval( $bien->CP_OFFRE ) );
		$detail['wppericles_adresse_wppericles_ville_offre']           = sanitize_text_field( strval( $bien->VILLE_OFFRE ) );
		$detail['wppericles_adresse_wppericles_adresse2_offre']        = sanitize_text_field( strval( $bien->ADRESSE2_OFFRE ) );
		$detail['wppericles_adresse_wppericles_adresse1_offre']        = sanitize_text_field( strval( $bien->ADRESSE1_OFFRE ) );
		$detail['wppericles_bien_wppericles_negociateur']              = sanitize_text_field( strval( $bien->NEGOCIATEUR ) );
		$detail['wppericles_agence_wppericles_raison_sociale']         = sanitize_text_field( strval( $bien->RS_AGENCE ) );
		$detail['wppericles_agence_wppericles_adresse_1_agence']       = sanitize_text_field( strval( $bien->ADRESSE1_AGENCE ) );
		$detail['wppericles_agence_wppericles_code_postal_agence']     = sanitize_text_field( strval( $bien->CP_AGENCE ) );
		$detail['wppericles_agence_wppericles_ville_agence']           = sanitize_text_field( strval( $bien->VILLE_AGENCE ) );
		$detail['wppericles_agence_wppericles_telephone_agence']       = sanitize_text_field( strval( $bien->TEL_AGENCE ) );
		$detail['wppericles_agence_wppericles_site_web_agence']        = sanitize_text_field( strval( $bien->WEB_AGENCE ) );
		$detail['wppericles_divers_wppericles_score_dpe']                         = sanitize_text_field( intval( strval( $bien->DPE_VAL1 ) ) );
		$detail['wppericles_divers_wppericles_score_ges']                         = sanitize_text_field( strval( $bien->DPE_VAL2 ) );
		$detail['wppericles_agence_wp_pericles_asp']                   = sanitize_text_field( strval( $bien->NO_ASP ) );
		$detail['wppericles_agence_wppericles_code_societe']           = sanitize_text_field( strval( $bien->CODE_SOCIETE ) );
		$detail['wppericles_agence_wppericles_code_site']              = sanitize_text_field( strval( $bien->CODE_SITE ) );
		$detail['wppericles_agence_wppericles_dossier']                = sanitize_text_field( strval( $bien->NO_DOSSIER ) );
		$detail['wppericles_bien_wppericles_type_mandat']              = sanitize_text_field( strval( $bien->TYPE_MANDAT ) );
		$detail['wppericles_bien_wppericles_honoraires']               = sanitize_text_field( strval( $bien->HONORAIRES ) );
		$detail['wppericles_bien_wppericles_travaux']                  = sanitize_text_field( strval( $bien->TRAVAUX ) );
		$detail['wppericles_bien_wppericles_charges']                  = sanitize_text_field( strval( $bien->CHARGES ) );
		$detail['wppericles_bien_wppericles_depot_garantie']           = sanitize_text_field( strval( $bien->DEPOT_GARANTIE ) );
		$detail['wppericles_bien_wppericles_taxe_habitation']          = sanitize_text_field( strval( $bien->TAXE_HABITATION ) );
		$detail['wppericles_bien_date_disponibilite']                  = sanitize_text_field( strval( $bien->DATE_DISPO ) );
		$detail['wppericles_bien_wppericles_taxe_fonciere']            = sanitize_text_field( strval( $bien->TAXE_FONCIERE ) );
		$detail['wppericles_internet_wppericles_code_postal_internet'] = sanitize_text_field( strval( $bien->CP_INTERNET ) );
		$detail['wppericles_internet_wppericles_ville_internet']       = sanitize_text_field( strval( $bien->VILLE_INTERNET ) );
		$detail['wppericles_pratique_wppericles_quartier']             = sanitize_text_field( strval( $bien->QUARTIER ) );
		$detail['wppericles_pratique_wppericles_residence']            = sanitize_text_field( strval( $bien->RESIDENCE ) );
		$detail['wppericles_pratique_wppericles_transport']            = sanitize_text_field( strval( $bien->TRANSPORT ) );
		$detail['wppericles_pratique_wppericles_proximite']            = sanitize_text_field( strval( $bien->PROXIMITE ) );
		$detail['wppericles_pratique_wppericles_secteur']              = sanitize_text_field( strval( $bien->SECTEUR ) );
		$detail['wppericles_interieur_wppericles_nb_pieces']           = sanitize_text_field( strval( $bien->NB_PIECES ) );
		$detail['wppericles_interieur_wppericles_surface_carrez']      = sanitize_text_field( strval( $bien->SURF_CARREZ ) );
		$detail['wppericles_interieur_wppericles_surface_sejour']      = sanitize_text_field( strval( $bien->SURF_SEJOUR ) );
		$detail['wppericles_pratique_wppericles_etage']                = sanitize_text_field( strval( $bien->ETAGE ) );
		$detail['wppericles_pratique_wppericles_code_etage']           = sanitize_text_field( strval( $bien->CODE_ETAGE ) );
		$detail['wppericles_interieur_wppericles_nb_etages']           = sanitize_text_field( strval( $bien->NB_ETAGE ) );
		$detail['wppericles_interieur_wppericles_cuisine']             = sanitize_text_field( strval( $bien->CUISINE ) );
		$detail['wppericles_interieur_wppericles_nb_wc']               = sanitize_text_field( strval( $bien->NB_WC ) );
		$detail['wppericles_annexes_wppericles_nb_parking_interieur']  = sanitize_text_field( strval( $bien->NB_PARK_INT ) );
		$detail['wppericles_annexes_wppericles_nb_parking_exterieur']  = sanitize_text_field( strval( $bien->NB_PARK_EXT ) );
		$detail['wppericles_annexes_wppericles_nb_garage_box']         = sanitize_text_field( strval( $bien->GARAGE_BOX ) );
		$detail['wppericles_annexes_wppericles_sous-sol']              = sanitize_text_field( strval( $bien->SOUS_SOL ) );
		$detail['wppericles_annexes_wppericles_nb_caves']              = sanitize_text_field( strval( $bien->NB_CAVES ) );
		$detail['wppericles_pratique_wppericles_type_chauffage']       = sanitize_text_field( strval( $bien->TYPE_CHAUFF ) );
		$detail['wppericles_agence_wppericles_code_client']            = sanitize_text_field( strval( $bien->CODE_CLIENT ) );
		$detail['wppericles_agence_wppericles_type_offre']             = sanitize_text_field( strval( $bien->TYPE_OFFRE ) );
		$detail['wppericles_pratique_wppericles_ascenseur']            = sanitize_text_field( strval( $bien->ASCENSEUR ) );
		$detail['wppericles_exterieur_wppericles_balcon']              = sanitize_text_field( strval( $bien->BALCON ) );
		$detail['wppericles_exterieur_wppericles_piscine']             = sanitize_text_field( strval( $bien->PISCINE ) );
		$detail['wppericles_pratique_wppericles_acces_handicape']      = sanitize_text_field( strval( $bien->ACCES_HANDI ) );
		$detail['wppericles_internet_wppericles_texte_mailing']        = sanitize_text_field( strval( $bien->TEXTE_MAILING ) );
		$detail['wppericles_divers_wppericles_mur_mitoyens']                      = sanitize_text_field( strval( $bien->MUR_MITOYENS ) );
		$detail['wppericles_exterieur_wppericles_facade_terrain']      = sanitize_text_field( strval( $bien->FACADE_TERRAIN ) );
		$detail['wppericles_agence_wppericles_adresse_2_agence']       = sanitize_text_field( strval( $bien->ADRESSE2_AGENCE ) );
		$detail['wppericles_internet_wppericles_url_visite']           = sanitize_text_field( strval( $bien->URL_VISITE ) );
		$detail['wppericles_pratique_wppericles_immeuble_prestige']    = sanitize_text_field( strval( $bien->PRESTIGE ) );
		$detail['wppericles_exterieur_wppericles_info_terrasse']       = sanitize_text_field( strval( $bien->INFO_TERRASSE ) );
		$detail['wppericles_exterieur_wppericles_info_balcon']         = sanitize_text_field( strval( $bien->INFO_BALCON ) );
		$detail['wppericles_bien_wppericles_dispo']                    = sanitize_text_field( strval( $bien->DISPO ) );
		$detail['wppericles_bien_wppericles_loyer2']                   = sanitize_text_field( strval( $bien->LOYER2 ) );
		$detail['wppericles_bien_wppericles_date_libre']               = sanitize_text_field( strval( $bien->DATE_LIBRE ) );
		$detail['wppericles_bien_wppericles_net_vendeur']              = sanitize_text_field( strval( $bien->NET_VENDEUR ) );
		$detail['wppericles_bien_wppericles_hono_acq']                 = sanitize_text_field( strval( $bien->HONO_ACQ ) );
		$detail['wppericles_exterieur_wppericles_surface_jardin']      = sanitize_text_field( strval( $bien->SURF_JARDIN ) );
		$detail['wppericles_divers_wppericles_cos']                               = sanitize_text_field( strval( $bien->COS ) );
		$detail['wppericles_divers_wppericles_shon']                              = sanitize_text_field( strval( $bien->SHON ) );
		$detail['wppericles_adresse_info_kms']                         = sanitize_text_field( strval( $bien->INFO_KM ) );
		$detail['wppericles_pratique_wppericles_info_contact']         = sanitize_text_field( strval( $bien->INFO_CONTACT ) );
		$detail['wppericles_pratique_wppericles_contact']              = sanitize_text_field( strval( $bien->CONTACT ) );
		$detail['wppericles_interieur_wppericles_nb_niveau']           = sanitize_text_field( strval( $bien->INFO_CONTACT ) );
		$detail['wppericles_pro_wppericles_cession_droit_au_bail']     = sanitize_text_field( strval( $bien->CESSIONDROITAUBAIL ) );
		$detail['wppericles_pro_wppericles_longueur_vitrine']          = sanitize_text_field( strval( $bien->LONGUEURVITRINE ) );
		$detail['wppericles_divers_wppericles_interphone']                        = sanitize_text_field( strval( $bien->INTERPHONE ) );
		$detail['wppericles_pro_wppericles_montecharge']               = sanitize_text_field( strval( $bien->MONTECHARGE ) );
		$detail['wppericles_pratique_wppericles_immeuble_independant'] = sanitize_text_field( strval( $bien->IMMEUBLEINDEPENDANT ) );
		$detail['wppericles_pratique_wppericles_immeuble_collectif']   = sanitize_text_field( strval( $bien->IMMEUBLECOLLECTIF ) );
		$detail['wppericles_pratique_wppericles_immeuble_prestige']    = sanitize_text_field( strval( $bien->IMMEUBLEPRESTIGE ) );
		$detail['wppericles_pratique_wppericles_digicode']             = sanitize_text_field( strval( $bien->DIGICODE ) );
		$detail['wppericles_interieur_wppericles_climatisation']       = sanitize_text_field( strval( $bien->CLIMATISATION ) );
		$detail['wppericles_divers_wppericles_gardiennage']                       = sanitize_text_field( strval( $bien->GARDIENNAGE ) );
		$detail['wppericles_pro_wppericles_surface_professionnelle']   = sanitize_text_field( strval( $bien->SURFACEPROFESSIONNELLE ) );
		$detail['wppericles_pro_wppericles_surface_annexe']            = sanitize_text_field( strval( $bien->SURFACEANNEXE ) );
		$detail['wppericles_pro_wppericles_surface_logement']          = sanitize_text_field( strval( $bien->SURFACELOGEMENT ) );
		$detail['wppericles_bien_wppericles_asp_lot']                  = sanitize_text_field( strval( $bien->NO_ASP_LOT ) );
		$detail['wppericles_bien_wppericles_viager']                   = sanitize_text_field( strval( $bien->VIAGER ) );
		$detail['wppericles_divers_wppericles_non_assujetti_dpe']                 = sanitize_text_field( strval( $bien->NON_ASSUJETTI_DPE ) );
		$detail['wppericles_divers_wppericles_dpe_vierge']                        = sanitize_text_field( strval( $bien->DPE_VIERGE ) );
		$detail['wppericles_bien_wppericles_taux_honoraires']          = sanitize_text_field( strval( $bien->TAUX_HONORAIRES ) );
		$detail['wppericles_bien_wppericles_copropriete']              = sanitize_text_field( strval( $bien->COPROPRIETE ) );
		$detail['wppericles_bien_wppericles_nb_de_lot_copropriete']    = sanitize_text_field( strval( $bien->NB_LOTS_COPROPRIETE ) );
		$detail['wppericles_bien_wppericles_montant_quote_part']       = sanitize_text_field( strval( $bien->MONTANT_QUOTE_PART ) );
		$detail['wppericles_bien_wppericles_procedure_syndicat']       = sanitize_text_field( strval( $bien->PROCEDURE_SYNDICAT ) );
		$detail['wppericles_bien_wppericles_detail_procedure']         = sanitize_text_field( strval( $bien->DETAIL_PROCEDURE ) );
		$detail['wppericles_bien_wppericles_coup_de_coeur']            = sanitize_text_field( strval( $bien->COUP_DE_COEUR ) );
		$detail['wppericles_bien_wppericles_repartition_acq']          = sanitize_text_field( strval( $bien->REPARTITION_ACQ ) );
		$detail['wppericles_prix_nature_charges']                      = sanitize_text_field( strval( $bien->NATURE_CHARGES ) );
		$detail['wppericles_prix_montant_charges']                     = sanitize_text_field( strval( $bien->MONTANT_CHARGES ) );
		$detail['wppericles_prix_complement_loyer']                    = sanitize_text_field( strval( $bien->COMPLEMENT_LOYER ) );
		$detail['wppericles_prix_honoraires_etat_lieux_ttc']           = sanitize_text_field( strval( $bien->HONO_ETAT_LIEUX_TTC ) );

		return $detail;
	}

	/**
	 * php delete function that deals with directories recursively
	 * https://paulund.co.uk/php-delete-directory-and-files-in-directory
	 */
	public function delete_files( $target ) {
		if ( is_dir( $target ) ) {
			$files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

			foreach ( $files as $file ) {
				$this->delete_files( $file );
			}

			rmdir( $target );
		} elseif ( is_file( $target ) ) {
			unlink( $target );
		}
	}

	public function create_pid() {
		$date = date( 'd F Y @ H\hi:s' );
		$file = fopen( WP_PERICLES_IMPORT . 'pid.txt', 'w+' );
		fwrite( $file, $date );
		fclose( $file );
	}

	public function delete_pid() {
		if ( file_exists( WP_PERICLES_IMPORT . 'pid.txt' ) ) {
			unlink( WP_PERICLES_IMPORT . 'pid.txt' );
		}
	}

	public function create_title( $bien ) {
		if ( Format_Data::is_pericles_air() ) {
			$args_title = [
				sanitize_text_field( strval( $bien->CAT ) ),
				sanitize_text_field( strval( $bien->VILLE_WEB ) ),
				sanitize_text_field( strval( $bien->NO_MANDAT ) ),

			];
		} else {
			$args_title = [
				sanitize_text_field( strval( $bien->CATEGORIE ) ),
				sanitize_text_field( strval( $bien->VILLE_OFFRE ) ),
				sanitize_text_field( strval( $bien->NO_MANDAT ) ),

			];
		}

		return $args_title;
	}

	public function create_content( $bien ) {
		if ( Format_Data::is_pericles_air() ) {
			$content = nl2br( strval( $bien->TXT_INTERNET ) );
		} else {
			$content = nl2br( strval( $bien->TEXTE_FR ) );
		}

		return $content;
	}

	public function prepare_gallery( $insert, $bien, $title ) {
		$wp_upload_dir = wp_upload_dir();
		/**
		 * MAJ Gallery Image
		 */
		$alphabet = array();
		foreach ( range( 'a', 'z' ) as $i ) {
			array_push( $alphabet, $i );
		}

		/**
		 * Créer la galerie
		 */
		delete_post_meta( $insert, 'wppericles_image' );
		$imgs = [];
		foreach ( $alphabet as $letter ) {
			$societe = strval( $bien->CODE_SOCIETE );
			$site    = strval( $bien->CODE_SITE );
			$asp     = get_post_meta( $insert, 'wppericles_agence_wp_pericles_asp', true );
			$img     = WP_PERICLES_IMPORT_IMG . $societe . '-' . $site . '-' . $asp . '-' . $letter . '.jpg';
			if ( file_exists( $img ) ) {


				$filetype      = wp_check_filetype( $img, null );
				$attachment    = array(
					'guid'           => $wp_upload_dir['url'] . '/' . basename( $img ),
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $img ) ),
					'post_content'   => '',
					'post_status'    => 'inherit',
				);
				$attachment_id = wp_insert_attachment( $attachment, $img, $insert );
				update_post_meta( $attachment_id, '_wp_attachment_image_alt', $title );

				// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
				require_once( ABSPATH . 'wp-admin/includes/image.php' );

				// Generate the metadata for the attachment, and update the database record.
				$attach_data = wp_generate_attachment_metadata( $attachment_id, $img );
				wp_update_attachment_metadata( $attachment_id, $attach_data );

				/**
				 * Attribution du post thumbnail pour la 1ere image.
				 */
				if ( ! has_post_thumbnail( $insert ) ) {
					set_post_thumbnail( $insert, $attachment_id );
				}

				if ( 'a' !== $letter ) {
					array_push( $imgs, $attachment_id );
				}

			}

			/**
			 * Ajout des images dans la galerie
			 */
			if ( 'a' !== $letter ) {
				// Add current images to array
				$image_url                      = wp_get_attachment_url( $attachment_id );
				$images_array[ $attachment_id ] = $image_url;
				update_field( 'wppericles_image', $imgs, $insert );
			}
			unset( $images_array );
		}
	}

}

new Import();
