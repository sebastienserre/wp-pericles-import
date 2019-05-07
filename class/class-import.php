<?php

namespace WP_PERICLES\IMPORT;

use function __return_false;
use function add_action;
use function array_push;
use function basename;
use function date;
use function delete_post_meta;
use function error_log;
use function file_exists;
use function get_post_meta;
use function get_term_by;
use function has_post_thumbnail;
use function intval;
use function sanitize_text_field;
use SimpleXMLElement;
use function strtotime;
use function strval;
use function update_post_meta;
use function var_dump;
use function wp_check_filetype;
use function wp_delete_file;
use function wp_insert_attachment;
use function wp_insert_post;
use function wp_set_post_terms;
use function wp_update_post;
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

	public function __construct() {
		add_action( 'wp_pericles_cron', array( $this, 'extract_photo' ) );
		if ( ! empty( $_GET['test'] ) && 'ok' === $_GET['test'] ) {
			add_action( 'admin_init', array( $this, 'extract_photo' ) );
		}

	}

	public function extract_photo() {

		$date = date_i18n( 'Y-m-d-H-i' );
		$file = WP_PERICLES_IMPORT . 'export.ZIP';

		/**
		 * Si on a pas de fichier d'export, on arrete tout
		 **/
		if ( ! file_exists( $file ) ) {
			return;
		}

		if ( file_exists( WP_PERICLES_PLUGIN_PATH . 'pid.txt' ) ) {
			return;
		}

		$this->create_pid();

		copy( WP_PERICLES_IMPORT . 'export.ZIP', WP_PERICLES_EXPORT_FOLDER . 'export-' . $date . '.zip' );

		error_log( 'export.zip copié' );
		$zip = new ZipArchive();
		$res = $zip->open( $file );
		if ( $res === true ) {
			$zip->extractTo( WP_PERICLES_IMPORT_TMP );
			$zip->extractTo( WP_PERICLES_IMPORT_IMG );
			$zip->close();
		}
		wp_delete_file( WP_PERICLES_IMPORT_IMG . 'export.XML' );
		$this->check_listing();
	}

	public function check_listing() {
		error_log( 'start check listing' );
		/**
		 * Retrieve listing
		 */
		$args     = array(
			'post_type'      => 'listing',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		);
		$listings = get_posts( $args );

		/**
		 * Search in listings if property exists in xml
		 * if not => draft
		 */
		foreach ( $listings as $listing ) {
			$listing_ID = get_post_meta( $listing->ID, '_listing_id' );
			$xml        = new XMLReader();
			$xml->open( WP_PERICLES_IMPORT_TMP . 'export.XML' );
			$xml->read();
			$mandat = array();

			$element = new SimpleXMLElement( $xml->readOuterXml() );
			foreach ( $element->BIEN as $bien ) {

				if ( strval( $bien->NO_MANDAT ) === $listing_ID[0] ) {
					array_push( $mandat, $listing->ID );
				}
			}

			if ( empty( $mandat ) ) {
				$postarr = array(
					'ID'          => $listing->ID,
					'post_status' => 'draft',
				);
				wp_update_post( $postarr );
			}
		}
		$this->read_xml();
		error_log( 'end check' );
	}

	/**
	 *
	 */
	public function read_xml() {
		set_time_limit( 1800 );
		error_log( 'start read_xml' );
		$xml           = new XMLReader();
		$open          = $xml->open( WP_PERICLES_IMPORT_TMP . 'export.XML' );
		$read          = $xml->read();
		$name          = $xml->name;
		$element       = new SimpleXMLElement( $xml->readOuterXml() );
		$wp_upload_dir = wp_upload_dir();
		foreach ( $element->BIEN as $bien ) {
			$args    = array(
				'post_type'  => 'listing',
				'meta_query' => array(
					'relation' => 'OR',
					array(
						'key'     => '_listing_id',
						'value'   => strval( $bien->NO_MANDAT ),
						'compare' => '=',
					),
				),
			);
			$listing = get_posts( $args );

			/**
			 * On recupere les datas du xml
			 */
			if ( $listing ) {
				$listing_id = $listing[0]->ID;
			} else {
				$listing_id = '';
			}

			/**
			 * format date
			 */

			$post_date = explode( '/', $bien->DATE_OFFRE );
			$post_date = $post_date[2] . '-' . $post_date[1] . '-' . $post_date[0] . ' ' . '09:00:00';


			$post_modified = explode( '/', $bien->DATE_MODIF );
			$post_modified = $post_modified[2] . '-' . $post_modified[1] . '-' . $post_modified[0] . ' ' . '09:00:00';

			$title        = sanitize_text_field( strval( $bien->CATEGORIE ) . ' - ' . strval( $bien->VILLE_OFFRE ) . ' - ' . strval( $bien->NO_MANDAT ) );
			$content      = sanitize_text_field( strval( $bien->TEXTE_FR ) );
			$ville        = sanitize_text_field( strval( $bien->VILLE_OFFRE ) );
			$mandat       = intval( strval( $bien->NO_MANDAT ) );
			$chambres     = intval( strval( $bien->NB_CHAMBRES ) );
			$sdb          = intval( strval( $bien->NB_SDB ) );
			$terrain      = intval( strval( $bien->SURF_TERRAIN ) );
			$habitable    = intval( strval( $bien->SURF_HAB ) );
			$terrasse     = intval( strval( $bien->TERRASSE ) );
			$sde          = intval( strval( $bien->NB_SE ) );
			$chauffage    = sanitize_text_field( strval( $bien->NATURE_CHAUFF ) );
			$annee_const  = intval( strval( $bien->ANNEE_CONS ) );
			$price        = intval( strval( $bien->PRIX ) );
			$public_note  = sanitize_text_field( strval( $bien->CP_OFFRE ) . ' ' . strval( $bien->VILLE_OFFRE ) . ', France' );
			$secret_note  = sanitize_text_field( strval( $bien->ADRESSE2_OFFRE ) . ' ' . strval( $bien->ADRESSE1_OFFRE ) . ' ' . strval( $bien->CP_OFFRE ) . ' ' . strval( $bien->VILLE_OFFRE ) . ', France' );
			$nego         = sanitize_text_field( strval( $bien->NEGOCIATEUR ) );
			$company      = sanitize_text_field( strval( $bien->RS_AGENCE ) . ', ' . strval( $bien->ADRESSE1_AGENCE ) . ', ' . strval( $bien->CP_AGENCE ) . ' ' . strval( $bien->VILLE_AGENCE ) );
			$phone        = sanitize_text_field( strval( $bien->TEL_AGENCE ) );
			$web          = sanitize_text_field( strval( $bien->WEB_AGENCE ) );
			$facebook     = 'https://www.facebook.com/MadaniImmobilier/';
			$dpe          = intval( strval( $bien->DPE_VAL1 ) );
			$ges          = sanitize_text_field( strval( $bien->DPE_VAL2 ) );
			$asp          = sanitize_text_field( strval( $bien->NO_ASP ) );
			$type_de_bien = sanitize_text_field( strval( $bien->CATEGORIE ) );
			$slug         = sanitize_text_field( strval( $bien->CATEGORIE ) . '-' . strval( $bien->VILLE_OFFRE ) . '-' . strval( $bien->NO_MANDAT ) );

			/**
			 * Get Authors ID
			 */
			$author  = 'Madani';
			$authors = get_users( array( 'search' => $author ) );

			/**
			 * Get Situation Term ID
			 */
			$situation = sanitize_text_field( strval( $bien->VILLE_OFFRE ) );
			$situation = get_term_by( 'name', $situation, 'location' );

			error_log( $listing_id . ' ' . $situation->term_id );
			/**
			 * @TODO Trouver une solution si plusieurs auteur... pas sur que ca arrive!
			 */
			foreach ( $authors as $author ) {
				$author_id[] = $author->ID;
			}

			$postarr = array(
				'ID'             => $listing_id,
				'post_author'    => $author_id[0],
				'post_content'   => $content,
				'post_title'     => $title,
				'post_status'    => 'publish',
				'post_type'      => 'listing',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_date'      => $post_date,
				'post_modified'  => $post_modified,
				'post_name'      => $slug,
				'meta_input'     => array(
					'madani_asp'      => $asp,
					'madani_dpe'      => $dpe,
					'madani_ges'      => $ges,
					'_price'          => $price,
					'_price_offer'    => 'sale',
					'_listing_id'     => $mandat,
					'_details_1'      => $chambres,
					'_details_2'      => $sdb,
					'_details_3'      => $terrain,
					'_details_4'      => $habitable,
					'_details_5'      => $terrasse,
					'_details_6'      => $sde,
					'_details_7'      => $chauffage,
					'_details_8'      => $annee_const,
					'_map_address'    => $ville,
					'_map_note'       => $public_note,
					'_map_secret'     => $secret_note,
					'_agent_name'     => $nego,
					'_agent_company'  => $company,
					'_agent_phone'    => $phone,
					'_agent_website'  => $web,
					'_agent_facebook' => $facebook,
				),
			);
			$insert  = wp_insert_post( $postarr );
			wp_set_post_terms( $insert, $type_de_bien, 'listing-type', true );
			wp_set_post_terms( $insert, $situation->term_id, 'location', true );

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
			delete_post_meta( $insert, '_gallery' );
			foreach ( $alphabet as $letter ) {
				$societe = strval( $bien->CODE_SOCIETE );
				$site    = strval( $bien->CODE_SITE );
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

					/**
					 * Ajout des images dans la galerie
					 */
					if ( 'a' !== $letter ) {
						// Add current images to array
						$image_url                      = wp_get_attachment_url( $attachment_id );
						$images_array[ $attachment_id ] = $image_url;
						update_post_meta( $insert, '_gallery', $images_array );
					}

				}
			}
			unset( $images_array );
		}
		/**
		 * On supprime le dossier temporaire puis on le recrée vide pour le prochain import.
		 */
		global $wp_filesystem;
		$this->delete_files( WP_PERICLES_IMPORT_TMP );

		if ( ! file_exists( $wp_upload_dir['basedir'] . '/import/temp' ) ) {
			mkdir( $wp_upload_dir['basedir'] . '/import/temp', 0777, true );
		}
		/**
		 * On supprime l'export.ZIP
		 */
		if ( file_exists( WP_PERICLES_IMPORT . 'export.ZIP' ) ) {
			wp_delete_file( WP_PERICLES_IMPORT . 'export.ZIP' );
		}
		$this->delete_pid();
		error_log( 'stop read_xml' );
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
		$file = fopen( WP_PERICLES_PLUGIN_PATH . 'pid.txt', 'w+' );
		fwrite( $file, $date );
		fclose( $file );
	}

	public function delete_pid() {
		if ( file_exists( WP_PERICLES_PLUGIN_PATH . 'pid.txt' ) ) {
			unlink( WP_PERICLES_PLUGIN_PATH . 'pid.txt' );
		}
	}


}

new Import();
