<?php get_header();
/**
 * wppericles_before_main_content hook.
 *
 */
do_action( 'wppericles_before_main_content' );

while ( have_posts() ) : the_post(); ?>
    <div class="summary entry-summary">
		<?php
		/**
		 * Hook: wppericles_single_property_summary
		 *
		 * @hooked WP_PERICLES\Templates\display_title - 10
		 */
		?>
		<?php do_action( 'wppericles_single_property_summary' ) ?>
    </div>
    <div class="property-content">
		<?php do_action( 'wppericles_single_property_content' ) ?>
    </div>
    <div class="details entry-details">
            <?php do_action( 'wppericles_single_property_details' ) ?>
    </div>
    <div class="details agency-details">
		<?php
		/**
		 * Hook: wppericles_single_agency_details
         * @hooked WP_PERICLES\Templates\display_agency_details - 10
		 */
        do_action( 'wppericles_single_agency_details' ) ?>
    </div>
<?php
endwhile; // end of the loop.

/**
 * wppericles_after_main_content hook.
 *
 * @hooked
 */
do_action( 'wppericles_after_main_content' );

get_footer();
