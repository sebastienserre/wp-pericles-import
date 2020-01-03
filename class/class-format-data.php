<?php

namespace WP_PERICLES\IMPORT\FormatData;

use function get_field;
use function intval;
use function sanitize_text_field;
use function strval;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class Format_Data {
	public function __construct() {
	}

	public static function is_pericles_air() {
		$version = get_field( 'wppericles_version', 'option' );
		if ( 'air' === $version ) {
			return true;
		}

		return false;
	}

	public static function format_meta( $bien ) {
		if ( self::is_pericles_air() ) {
			$datas['adresse_ville_offre']            = strval( $bien->VILLE );
			$datas['bien_mandat']                    = strval( $bien->NO_MANDAT );
			$datas['interieur_nb_chambres']          = strval( $bien->NB_CHB );
			$datas['interieur_nb_sdb']               = strval( $bien->NB_SDB );
			$datas['exterieur_surface_terrain']      = strval( $bien->SURF_JAR );
			$datas['interieur_surface_habitable']    = strval( $bien->SURF_HAB );
			$datas['exterieur_terrasse']             = strval( $bien->NB_TERRASSE );
			$datas['interieur_nb_sde']               = strval( $bien->NB_SE );
			$datas['pratique_nature_chauffage']      = strval( $bien->NAT_CHAUFF );
			$datas['bien_annee_de_construction']     = strval( $bien->ANNEE_CONS );
			$datas['bien_prix']                      = strval( $bien->PV );
			$datas['adresse_code_postal_offre']      = strval( $bien->CP_WEB );
			$datas['adresse_ville_offre']            = strval( $bien->VILLE_WEB );
			$datas['bien_negociateur']               = strval( $bien->CONTACTS->CONTACT->LIBELLE );
			$datas['divers_score_dpe']               = strval( $bien->VAL_DPE );
			$datas['divers_score_ges']               = strval( $bien->VAL_GES );
			$datas['agence_wp_pericles_asp']         = strval( $bien->NO_ASP );
			$datas['agence_code_societe']            = strval( $bien->CODE_SOCIETE );
			$datas['agence_code_site']               = strval( $bien->CODE_SITE );
			$datas['agence_dossier']                 = strval( $bien->NO_DOSSIER );
			$datas['bien_type_mandat']               = strval( $bien->TYPE_MANDAT );
			$datas['bien_honoraires']                = strval( $bien->HONO );
			$datas['bien_travaux']                   = strval( $bien->TRAVAUX );
			$datas['bien_charges']                   = strval( $bien->CHARGES_ANN );
			$datas['bien_depot_garantie']            = strval( $bien->HRAL );
			$datas['bien_taxe_habitation']           = strval( $bien->TAXE_HAB );
			$datas['bien_date_disponibilite']        = strval( $bien->DATE_DISP );
			$datas['bien_taxe_fonciere']             = strval( $bien->TAXE_FONC );
			$datas['internet_code_postal_internet']  = strval( $bien->CP_WEB );
			$datas['internet_ville_internet']        = strval( $bien->VILLE_WEB );
			$datas['pratique_quartier']              = strval( $bien->QUARTIER );
			$datas['pratique_residence']             = strval( $bien->RESIDENCE );
			$datas['pratique_transport']             = strval( $bien->TRANSPORT );
			$datas['pratique_proximite']             = strval( $bien->PROXIMITE );
			$datas['interieur_nb_pieces']            = strval( $bien->NB_PCE );
			$datas['interieur_surface_carrez']       = strval( $bien->SURF_CARR );
			$datas['interieur_surface_sejour']       = strval( $bien->SURF_SEJ );
			$datas['pratique_etage']                 = strval( $bien->ETAGE );
			$datas['interieur_nb_etages']            = strval( $bien->NB_ETAGE );
			$datas['interieur_cuisine']              = strval( $bien->TYPE_CUIS );
			$datas['interieur_nb_wc']                = strval( $bien->NB_WC );
			$datas['annexes_nb_parking_interieur']   = strval( $bien->NB_PARK_INT );
			$datas['annexes_nb_parking_exterieur']   = strval( $bien->NB_PARK_EXT );
			$datas['annexes_nb_garage_box']          = strval( $bien->NB_GAR );
			$datas['annexes_nb_caves']               = strval( $bien->NB_CAVE );
			$datas['pratique_type_chauffage']        = strval( $bien->TYPE_CHAUFF );
			$datas['agence_type_offre']              = strval( $bien->TYPE_MANDAT );
			$datas['pratique_ascenseur']             = strval( $bien->ASCE );
			$datas['exterieur_balcon']               = strval( $bien->NB_BALCON );
			$datas['exterieur_piscine']              = strval( $bien->PISCINE );
			$datas['pratique_acces_handicape']       = strval( $bien->HAND );
			$datas['divers_mur_mitoyens']            = strval( $bien->NB_MUR_MIT );
			$datas['pratique_immeuble_prestige']     = strval( $bien->STANDING );
			$datas['bien_dispo']                     = strval( $bien->DISPO );
			$datas['bien_loyer2']                    = strval( $bien->LOYER );
			$datas['exterieur_surface_jardin']       = strval( $bien->SURF_JAR );
			$datas['divers_interphone']              = strval( $bien->INTERPHONE );
			$datas['pratique_digicode']              = strval( $bien->DIGICODE );
			$datas['divers_gardiennage']             = strval( $bien->GARDIENNAGE );
			$datas['bien_asp_lot']                   = strval( $bien->NO_ASP );
			$datas['bien_viager']                    = strval( $bien->VIAGER );
			$datas['divers_non_assujetti_dpe']       = strval( $bien->NON_DPE );
			$datas['bien_copropriete']               = strval( $bien->COPROPRIETE );
			$datas['bien_nb_de_lot_copropriete']     = strval( $bien->NB_LOTS_COPRO );
			$datas['bien_montant_quote_part']        = strval( $bien->MONTANT_QUOTEPART );
			$datas['bien_procedure_syndicat']        = strval( $bien->PROCEDURE_SYND );
			$datas['prix_honoraires_etat_lieux_ttc'] = strval( $bien->HONO_ETAT_LIEU_LOC );
		} else {
			$datas['adresse_ville_offre']               = strval( $bien->VILLE_OFFRE );
			$datas['bien_mandat']                       = strval( $bien->NO_MANDAT );
			$datas['interieur_nb_chambres']             = strval( $bien->NB_CHAMBRES );
			$datas['interieur_nb_sdb']                  = strval( $bien->NB_SDB );
			$datas['exterieur_surface_terrain']         = strval( $bien->SURF_TERRAIN );
			$datas['interieur_surface_habitable']       = strval( $bien->SURF_HAB );
			$datas['exterieur_terrasse']                = strval( $bien->TERRASSE );
			$datas['interieur_nb_sde']                  = strval( $bien->NB_SE );
			$datas['pratique_nature_chauffage']         = strval( $bien->NATURE_CHAUFF );
			$datas['bien_annee_de_construction']        = strval( $bien->ANNEE_CONS );
			$datas['bien_prix']                         = strval( $bien->PRIX );
			$datas['adresse_code_postal_offre']         = strval( $bien->CP_OFFRE );
			$datas['adresse_ville_offre']               = strval( $bien->VILLE_OFFRE );
			$datas['adresse_adresse2_offre']            = strval( $bien->ADRESSE2_OFFRE );
			$datas['adresse_adresse1_offre']            = strval( $bien->ADRESSE1_OFFRE );
			$datas['bien_negociateur']                  = strval( $bien->NEGOCIATEUR );
			$datas['agence_raison_sociale']             = strval( $bien->RS_AGENCE );
			$datas['agence_adresse_1_agence']           = strval( $bien->ADRESSE1_AGENCE );
			$datas['agence_code_postal_agence']         = strval( $bien->CP_AGENCE );
			$datas['agence_ville_agence']               = strval( $bien->VILLE_AGENCE );
			$datas['agence_telephone_agence']           = strval( $bien->TEL_AGENCE );
			$datas['agence_site_web_agence']            = strval( $bien->WEB_AGENCE );
			$datas['divers_score_dpe']                  = strval( $bien->DPE_VAL1 );
			$datas['divers_score_ges']                  = strval( $bien->DPE_VAL2 );
			$datas['wppericles_agence_wp_pericles_asp'] = strval( $bien->NO_ASP );
			$datas['agence_code_societe']               = strval( $bien->CODE_SOCIETE );
			$datas['agence_code_site']                  = strval( $bien->CODE_SITE );
			$datas['agence_dossier']                    = strval( $bien->NO_DOSSIER );
			$datas['bien_type_mandat']                  = strval( $bien->TYPE_MANDAT );
			$datas['bien_honoraires']                   = strval( $bien->HONORAIRES );
			$datas['bien_travaux']                      = strval( $bien->TRAVAUX );
			$datas['bien_charges']                      = strval( $bien->CHARGES );
			$datas['bien_depot_garantie']               = strval( $bien->DEPOT_GARANTIE );
			$datas['bien_taxe_habitation']              = strval( $bien->TAXE_HABITATION );
			$datas['bien_date_disponibilite']           = strval( $bien->DATE_DISPO );
			$datas['bien_taxe_fonciere']                = strval( $bien->TAXE_FONCIERE );
			$datas['internet_code_postal_internet']     = strval( $bien->CP_INTERNET );
			$datas['internet_ville_internet']           = strval( $bien->VILLE_INTERNET );
			$datas['pratique_quartier']                 = strval( $bien->QUARTIER );
			$datas['pratique_residence']                = strval( $bien->RESIDENCE );
			$datas['pratique_transport']                = strval( $bien->TRANSPORT );
			$datas['pratique_proximite']                = strval( $bien->PROXIMITE );
			$datas['pratique_secteur']                  = strval( $bien->SECTEUR );
			$datas['interieur_nb_pieces']               = strval( $bien->NB_PIECES );
			$datas['interieur_surface_carrez']          = strval( $bien->SURF_CARREZ );
			$datas['interieur_surface_sejour']          = strval( $bien->SURF_SEJOUR );
			$datas['pratique_etage']                    = strval( $bien->ETAGE );
			$datas['pratique_code_etage']               = strval( $bien->CODE_ETAGE );
			$datas['interieur_nb_etages']               = strval( $bien->NB_ETAGE );
			$datas['interieur_cuisine']                 = strval( $bien->CUISINE );
			$datas['interieur_nb_wc']                   = strval( $bien->NB_WC );
			$datas['annexes_nb_parking_interieur']      = strval( $bien->NB_PARK_INT );
			$datas['annexes_nb_parking_exterieur']      = strval( $bien->NB_PARK_EXT );
			$datas['annexes_nb_garage_box']             = strval( $bien->GARAGE_BOX );
			$datas['annexes_sous-sol']                  = strval( $bien->SOUS_SOL );
			$datas['annexes_nb_caves']                  = strval( $bien->NB_CAVES );
			$datas['pratique_type_chauffage']           = strval( $bien->TYPE_CHAUFF );
			$datas['agence_code_client']                = strval( $bien->CODE_CLIENT );
			$datas['agence_type_offre']                 = strval( $bien->TYPE_OFFRE );
			$datas['pratique_ascenseur']                = strval( $bien->ASCENSEUR );
			$datas['exterieur_balcon']                  = strval( $bien->BALCON );
			$datas['exterieur_piscine']                 = strval( $bien->PISCINE );
			$datas['pratique_acces_handicape']          = strval( $bien->ACCES_HANDI );
			$datas['internet_texte_mailing']            = strval( $bien->TEXTE_MAILING );
			$datas['divers_mur_mitoyens']               = strval( $bien->MUR_MITOYENS );
			$datas['exterieur_facade_terrain']          = strval( $bien->FACADE_TERRAIN );
			$datas['agence_adresse_2_agence']           = strval( $bien->ADRESSE2_AGENCE );
			$datas['internet_url_visite']               = strval( $bien->URL_VISITE );
			$datas['pratique_immeuble_prestige']        = strval( $bien->PRESTIGE );
			$datas['exterieur_info_terrasse']           = strval( $bien->INFO_TERRASSE );
			$datas['exterieur_info_balcon']             = strval( $bien->INFO_BALCON );
			$datas['bien_dispo']                        = strval( $bien->DISPO );
			$datas['bien_loyer2']                       = strval( $bien->LOYER2 );
			$datas['bien_date_libre']                   = strval( $bien->DATE_LIBRE );
			$datas['bien_net_vendeur']                  = strval( $bien->NET_VENDEUR );
			$datas['bien_hono_acq']                     = strval( $bien->HONO_ACQ );
			$datas['exterieur_surface_jardin']          = strval( $bien->SURF_JARDIN );
			$datas['divers_cos']                        = strval( $bien->COS );
			$datas['divers_shon']                       = strval( $bien->SHON );
			$datas['adresse_info_kms']                  = strval( $bien->INFO_KM );
			$datas['pratique_info_contact']             = strval( $bien->INFO_CONTACT );
			$datas['pratique_contact']                  = strval( $bien->CONTACT );
			$datas['interieur_nb_niveau']               = strval( $bien->INFO_CONTACT );
			$datas['pro_cession_droit_au_bail']         = strval( $bien->CESSIONDROITAUBAIL );
			$datas['pro_longueur_vitrine']              = strval( $bien->LONGUEURVITRINE );
			$datas['divers_interphone']                 = strval( $bien->INTERPHONE );
			$datas['pro_montecharge']                   = strval( $bien->MONTECHARGE );
			$datas['pratique_immeuble_independant']     = strval( $bien->IMMEUBLEINDEPENDANT );
			$datas['pratique_immeuble_collectif']       = strval( $bien->IMMEUBLECOLLECTIF );
			$datas['pratique_immeuble_prestige']        = strval( $bien->IMMEUBLEPRESTIGE );
			$datas['pratique_digicode']                 = strval( $bien->DIGICODE );
			$datas['interieur_climatisation']           = strval( $bien->CLIMATISATION );
			$datas['divers_gardiennage']                = strval( $bien->GARDIENNAGE );
			$datas['pro_surface_professionnelle']       = strval( $bien->SURFACEPROFESSIONNELLE );
			$datas['pro_surface_annexe']                = strval( $bien->SURFACEANNEXE );
			$datas['pro_surface_logement']              = strval( $bien->SURFACELOGEMENT );
			$datas['bien_asp_lot']                      = strval( $bien->NO_ASP_LOT );
			$datas['bien_viager']                       = strval( $bien->VIAGER );
			$datas['divers_non_assujetti_dpe']          = strval( $bien->NON_ASSUJETTI_DPE );
			$datas['divers_dpe_vierge']                 = strval( $bien->DPE_VIERGE );
			$datas['bien_taux_honoraires']              = strval( $bien->TAUX_HONORAIRES );
			$datas['bien_copropriete']                  = strval( $bien->COPROPRIETE );
			$datas['bien_nb_de_lot_copropriete']        = strval( $bien->NB_LOTS_COPROPRIETE );
			$datas['bien_montant_quote_part']           = strval( $bien->MONTANT_QUOTE_PART );
			$datas['bien_procedure_syndicat']           = strval( $bien->PROCEDURE_SYNDICAT );
			$datas['bien_detail_procedure']             = strval( $bien->DETAIL_PROCEDURE );
			$datas['bien_coup_de_coeur']                = strval( $bien->COUP_DE_COEUR );
			$datas['bien_repartition_acq']              = strval( $bien->REPARTITION_ACQ );
			$datas['prix_nature_charges']               = strval( $bien->NATURE_CHARGES );
			$datas['prix_montant_charges']              = strval( $bien->MONTANT_CHARGES );
			$datas['prix_complement_loyer']             = strval( $bien->COMPLEMENT_LOYER );
			$datas['prix_honoraires_etat_lieux_ttc']    = strval( $bien->HONO_ETAT_LIEUX_TTC );

		}

		return $datas;
	}
}

new Format_Data();