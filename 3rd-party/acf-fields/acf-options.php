<?php

use WP_PERICLES\IMPORT\WPResidence\WPResidence;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( function_exists( 'acf_add_local_field_group' ) ):

	acf_add_local_field_group( array(
		'key'                   => 'group_5cd5792ac8a83',
		'title'                 => __( 'Options', 'wp-pericles-import' ),
		'fields'                => array(
			array(
				'key'               => 'field_5cd5793e1309c',
				'label'             => __( 'General', 'wp-pericles-import' ),
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
				'label'             => __( 'File Name', 'wp-pericles-import' ),
				'name'              => 'wppericles_nom_zip',
				'type'              => 'text',
				'instructions'      => __( 'Please fill in the exported filename saved in "wp-content/uploads/import/". (case sensitive)
Example: export.ZIP', 'wp-pericles-import' ),
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
				'label'             => __( 'Importer User', 'wp-pericles-import' ),
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
				'key'               => 'field_5e09bad4d0fc0',
				'label'             => __( 'Pericles Version', 'wp-pericles-import' ),
				'name'              => 'wppericles_version',
				'type'              => 'radio',
				'instructions'      => '',
				'required'          => 1,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'choices'           => array(
					5     => __( 'Pericles 5 ( Desktop version)', 'wp-pericles-import' ),
					'air' => __( 'Pericles AIR', 'wp-pericles-import' ),
				),
				'allow_null'        => 1,
				'other_choice'      => 0,
				'default_value'     => 5,
				'layout'            => 'vertical',
				'return_format'     => 'value',
				'save_other_choice' => 0,
			),
			array(
				'key'               => 'field_5df0911ea92a4',
				'label'             => __( 'Custom Post Type', 'wp-pericles-import' ),
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
				'label'             => __( 'Create a dedicated CPT ?', 'wp-pericles-import' ),
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
				'default_value'     => 1,
				'ui'                => 1,
				'ui_on_text'        => '',
				'ui_off_text'       => '',
			),
			array(
				'key'               => 'field_5df091ff974cc',
				'label'             => __( 'Use a Real Estate Plugin or Theme ?', 'wp-pericles-import' ),
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
				'default_value'     => 1,
				'ui'                => 1,
				'ui_on_text'        => '',
				'ui_off_text'       => '',
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
					'listing'         => 'WP Casa',
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
				'label'             => __( 'WP Residence', 'wp-pericles-import' ),
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
				'label'             => __( 'Fields', 'wp-pericles-import' ),
				'name'              => 'wppericles_wp_residence_fields',
				'type'              => 'checkbox',
				'instructions'      => __( 'Check fields you want to create', 'wp-pericles-import' ),
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
			array(
				'key'               => 'field_5e0cad85a5fa9',
				'label'             => __( 'Help', 'wp-pericles-import' ),
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
				'key'               => 'field_5e0cad95a5faa',
				'label'             => __( 'Finding Help', 'wp-pericles-import' ),
				'name'              => '',
				'type'              => 'message',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'message'           => __( '<a href="https://docs.thivinfo.com/collection/58-wp-pericles-import" target="_blank"> Lien vers l\'aide</a>', 'wp-pericles-import' ),
				'new_lines'         => 'wpautop',
				'esc_html'          => 0,
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
