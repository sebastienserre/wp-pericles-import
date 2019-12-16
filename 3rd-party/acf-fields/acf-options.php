<?php

use WP_PERICLES\IMPORT\WPResidence\WPResidence;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( function_exists( 'acf_add_local_field_group' ) ):

	acf_add_local_field_group( array(
		'key'                   => 'group_5cd5792ac8a83',
		'title'                 => 'Options',
		'fields'                => array(
			array(
				'key'               => 'field_5cd5793e1309c',
				'label'             => 'Général',
				'name'              => '',
				'type'              => 'tab',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'placement'         => 'top',
				'endpoint'          => 0,
			),
			array(
				'key'               => 'field_5cd5794d1309d',
				'label'             => 'Nom du zip',
				'name'              => 'wppericles_nom_zip',
				'type'              => 'text',
				'instructions'      => 'Entre le nom du fichier ZIP exporté par Péricles dans "wp-content/uploads/import/". (attention à la casse)
Example: export.ZIP',
				'required'          => 1,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => 'export.ZIP',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			),
			array(
				'key'               => 'field_5cd6a074abb59',
				'label'             => 'Utilisateur Importateur',
				'name'              => 'wppericles_utilisateur_importateur',
				'type'              => 'user',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'role'              => '',
				'allow_null'        => 0,
				'multiple'          => 0,
				'return_format'     => 'object',
			),
			array(
				'key'               => 'field_5df0911ea92a4',
				'label'             => 'Custom Post Type',
				'name'              => '',
				'type'              => 'tab',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'placement'         => 'top',
				'endpoint'          => 0,
			),
			array(
				'key'               => 'field_5d4edf4f8b75b',
				'label'             => 'Créer un Type de contenu dédié?',
				'name'              => 'wppericles_create_cpt',
				'type'              => 'true_false',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'message'           => '',
				'default_value'     => 0,
				'ui'                => 1,
				'ui_on_text'        => '',
				'ui_off_text'       => '',
			),
			array(
				'key'               => 'field_5df092c933060',
				'label'             => 'Use an existing CPT ?',
				'name'              => 'wppericles_use_existing',
				'type'              => 'true_false',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'message'           => '',
				'default_value'     => 0,
				'ui'                => 1,
				'ui_on_text'        => '',
				'ui_off_text'       => '',
			),
			array(
				'key'               => 'field_5df091ff974cc',
				'label'             => 'Use a Real Estate Plugin or Theme ?',
				'name'              => 'wppericles_use_external',
				'type'              => 'true_false',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'message'           => '',
				'default_value'     => 0,
				'ui'                => 1,
				'ui_on_text'        => '',
				'ui_off_text'       => '',
			),
			array(
				'key'               => 'field_5d4ee0439382c',
				'label'             => 'Utilisez un type de contenu existant',
				'name'              => 'wppericles_existing_cpt',
				'type'              => 'post_type_selector',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_5d4edf4f8b75b',
							'operator' => '!=',
							'value'    => '1',
						),
						array(
							'field'    => 'field_5df092c933060',
							'operator' => '==',
							'value'    => '1',
						),
						array(
							'field'    => 'field_5df091ff974cc',
							'operator' => '!=',
							'value'    => '1',
						),
					),
				),
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'select_type'       => 1,
			),
			array(
				'key'               => 'field_5df0caffcf03b',
				'label'             => 'Which Plugin or Theme do you use?',
				'name'              => 'wppericles_list_external',
				'type'              => 'select',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_5d4edf4f8b75b',
							'operator' => '!=',
							'value'    => '1',
						),
						array(
							'field'    => 'field_5df092c933060',
							'operator' => '!=',
							'value'    => '1',
						),
						array(
							'field'    => 'field_5df091ff974cc',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'choices'           => array(
					'estate_property' => 'WP Residence',
				),
				'default_value'     => array(),
				'allow_null'        => 0,
				'multiple'          => 0,
				'ui'                => 0,
				'return_format'     => 'value',
				'ajax'              => 0,
				'placeholder'       => '',
			),
			array(
				'key'               => 'field_5df390d90f1c6',
				'label'             => 'WP Residence',
				'name'              => '',
				'type'              => 'tab',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_5df0caffcf03b',
							'operator' => '==',
							'value'    => 'estate_property',
						),
					),
				),
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'placement'         => 'top',
				'endpoint'          => 0,
			),
			array(
				'key'               => 'field_5df392b38746d',
				'label'             => 'Fields',
				'name'              => 'wppericles_wp_residence_fields',
				'type'              => 'checkbox',
				'instructions'      => 'Check fields you want to create',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'choices'           => WPResidence::$fields,
				'allow_custom'      => 0,
				'default_value'     => array(),
				'layout'            => 'horizontal',
				'toggle'            => 1,
				'return_format'     => 'label',
				'save_custom'       => 0,
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'pericles-import-settings',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
	) );

endif;