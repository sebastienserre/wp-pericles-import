<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'init', 'location_tax', 0 );
	function location_tax() {
		if ( true === get_field( 'wppericles_cpt', 'option') ) {
			$labels = array(
				'name'                       => _x( 'Locations', 'Taxonomy General Name', 'wp-pericles-import' ),
				'singular_name'              => _x( 'Location', 'Taxonomy Singular Name', 'wp-pericles-import' ),
				'menu_name'                  => __( 'Location', 'wp-pericles-import' ),
				'all_items'                  => __( 'Locations', 'wp-pericles-import' ),
				'parent_item'                => __( 'Parent Location', 'wp-pericles-import' ),
				'parent_item_colon'          => __( 'Parent Location:', 'wp-pericles-import' ),
				'new_item_name'              => __( 'New Location Name', 'wp-pericles-import' ),
				'add_new_item'               => __( 'Add New Location', 'wp-pericles-import' ),
				'edit_item'                  => __( 'Edit Location', 'wp-pericles-import' ),
				'update_item'                => __( 'Update Location', 'wp-pericles-import' ),
				'view_item'                  => __( 'View Location', 'wp-pericles-import' ),
				'separate_items_with_commas' => __( 'Separate Locations with commas', 'wp-pericles-import' ),
				'add_or_remove_items'        => __( 'Add or remove Locations', 'wp-pericles-import' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'wp-pericles-import' ),
				'popular_items'              => __( 'Popular Locations', 'wp-pericles-import' ),
				'search_items'               => __( 'Search Locations', 'wp-pericles-import' ),
				'not_found'                  => __( 'Not Found', 'wp-pericles-import' ),
				'no_terms'                   => __( 'No Locations', 'wp-pericles-import' ),
				'items_list'                 => __( 'Locations list', 'wp-pericles-import' ),
				'items_list_navigation'      => __( 'Locations list navigation', 'wp-pericles-import' ),
			);
			$args   = array(
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'show_tagcloud'     => true,
				'show_in_rest'      => true,
			);
			register_taxonomy( 'location_tax', array( 'real-estate-property' ), $args );
		}

	}


