<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array(
		'key' => 'group_5cd5792ac8a83',
		'title' => 'Options',
		'fields' => array(
			array(
				'key' => 'field_5cd5793e1309c',
				'label' => 'Général',
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			),
			array(
				'key' => 'field_5cd5794d1309d',
				'label' => 'Nom du zip',
				'name' => 'wppericles_nom_zip',
				'type' => 'text',
				'instructions' => 'Entre le nom du fichier ZIP exporté par Péricles. (attention à la casse)
Example: export.ZIP',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => 'export.ZIP',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_5cd6a074abb59',
				'label' => 'Utilisateur Importateur',
				'name' => 'wppericles_utilisateur_importateur',
				'type' => 'user',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'role' => '',
				'allow_null' => 0,
				'multiple' => 0,
				'return_format' => 'object',
			),
			array(
				'key' => 'field_5d4edf4f8b75b',
				'label' => 'Créer un Type de contenu dédié?',
				'name' => 'wppericles_create_cpt',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 1,
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_5cd857a64e3b9',
				'label' => 'Slug',
				'name' => 'wppericles_cpt_slug',
				'type' => 'text',
				'instructions' => 'Slug du type de contenu Immobilier à créer.
Veuillez visiter la page des Permaliens pour activer la réécriture.
https://example.com/wp-admin/options-permalink.php',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5d4edf4f8b75b',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'real-estate-property',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_5d4ee0439382c',
				'label' => 'Utilisez un type de contenu existant',
				'name' => 'wppericles_existing_cpt',
				'type' => 'post_type_selector',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5d4edf4f8b75b',
							'operator' => '!=',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'select_type' => 1,
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'pericles-import-settings',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
		'modified' => 1565457890,
	));

endif;