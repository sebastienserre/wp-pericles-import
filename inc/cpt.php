<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'init', 'create_cpt', 990 );

// Register Custom Post Type
	function create_cpt() {
		if ( true === get_field( 'wppericles_cpt', 'option') ) {
			$labels = array(
				'name'                  => _x( 'Real estate properties', 'Post Type General Name', 'wp-pericles-import' ),
				'singular_name'         => _x( 'Real estate property', 'Post Type Singular Name', 'wp-pericles-import' ),
				'menu_name'             => __( 'Real Estate Properties', 'wp-pericles-import' ),
				'name_admin_bar'        => __( 'Real Estate Properties', 'wp-pericles-import' ),
				'archives'              => __( 'Real Estate Properties Archives', 'wp-pericles-import' ),
				'attributes'            => __( 'Real Estate Property Attributes', 'wp-pericles-import' ),
				'parent_item_colon'     => __( 'Parent Real Estate Property:', 'wp-pericles-import' ),
				'all_items'             => __( 'All Real Estate Properties', 'wp-pericles-import' ),
				'add_new_item'          => __( 'Add New Real Estate Property', 'wp-pericles-import' ),
				'add_new'               => __( 'Add New', 'wp-pericles-import' ),
				'new_item'              => __( 'New Real Estate Property', 'wp-pericles-import' ),
				'edit_item'             => __( 'Edit Real Estate Property', 'wp-pericles-import' ),
				'update_item'           => __( 'Update Real Estate Property', 'wp-pericles-import' ),
				'view_item'             => __( 'View Real Estate Property', 'wp-pericles-import' ),
				'view_items'            => __( 'View Real Estate Properties', 'wp-pericles-import' ),
				'search_items'          => __( 'Search Real Estate Property', 'wp-pericles-import' ),
				'not_found'             => __( 'Not found', 'wp-pericles-import' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'wp-pericles-import' ),
				'featured_image'        => __( 'Featured Image', 'wp-pericles-import' ),
				'set_featured_image'    => __( 'Set featured image', 'wp-pericles-import' ),
				'remove_featured_image' => __( 'Remove featured image', 'wp-pericles-import' ),
				'use_featured_image'    => __( 'Use as featured image', 'wp-pericles-import' ),
				'insert_into_item'      => __( 'Insert into Real Estate Property', 'wp-pericles-import' ),
				'uploaded_to_this_item' => __( 'Uploaded to this item', 'wp-pericles-import' ),
				'items_list'            => __( 'Real Estate Properties list', 'wp-pericles-import' ),
				'items_list_navigation' => __( 'Real Estate Properties list navigation', 'wp-pericles-import' ),
				'filter_items_list'     => __( 'Filter Real Estate Properties list', 'wp-pericles-import' ),
			);
			$args   = array(
				'label'               => __( 'Real estate property', 'wp-pericles-import' ),
				'description'         => __( 'Post Type Description', 'wp-pericles-import' ),
				'labels'              => $labels,
				'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions' ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 5,
				'menu_icon'           => 'dashicons-admin-home',
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'page',
				'show_in_rest'        => true,
			);
			register_post_type( 'real-estate-property', $args );

		}

	}

