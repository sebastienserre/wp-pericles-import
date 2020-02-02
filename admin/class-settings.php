<?php

namespace WP_PERICLES\Settings;

use function add_action;
use function add_query_arg;
use function add_settings_field;
use function add_settings_section;
use function add_submenu_page;
use function do_settings_sections;
use function esc_html;
use function esc_url;
use function is_openwp_pro;
use function openwp_add_import_content;
use function printf;
use function register_setting;
use function settings_fields;
use function submit_button;
use function var_dump;
use function wp_kses;
use function wp_nonce_url;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class Settings {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'settings_page'] );
		add_action( 'admin_init', [ $this, 'register_settings'] );

	}

	public function settings_page(){
		add_options_page( 'Pericles Settings', 'Pericles Settings', 'manage_options', 'wppi-settings', [
			$this, 'settings']);
	}

	public function settings(){
		?>
		<h2 class="nav-tab-wrapper">
			WP Pericles Import
		</h2>

		<form method="post" action="options.php">
			<?php
					settings_fields( 'wppi-settings' );
					do_settings_sections( 'wppi-settings' );
					submit_button( __( 'Save' ) );
			?>
		</form>
<?php
	}

	public function register_settings(){

		add_settings_section( 'wppi-settings', '', '', 'wppi-settings' );
		register_setting( 'wppi-settings', 'thfo_api_key' );
		add_settings_field( 'thfo_api_key', __( 'API WP Pericles Import', 'wp-pericles-import' ), [ $this, 'wppi_api' ],'wppi-settings', 'wppi-settings' );

	}

	public function wppi_api(){

		if ( '1' === get_option( 'thfo_key_validated' ) ){
			$text = __( 'Deactivate Key', 'wp-pericles-import' );
			$args = [
				'activate' => '2',
				'key'      => get_option( 'thfo_api_key' ),
			];

		} else {
			$text = __( 'Activate Key', 'wp-pericles-import' );
			$args = [
				'activate' => '1',
				'key'      => get_option( 'thfo_api_key' ),
			];
		}
		global $wp;
		$activation_url = wp_nonce_url(
			add_query_arg(
				$args,
				admin_url( 'options-general.php?page=wppi-settings' )
			),
			'validate',
			'_wpnonce'
		);
		?>
		<input type="text" name="thfo_api_key" value="<?php echo esc_html( get_option( 'thfo_api_key' ) ); ?>"/>
		<?php $url = esc_url( 'https://thivinfo.com' ); ?>
		<?php // translators: Add the OpenAGenda URL. ?>
		<p><?php printf( wp_kses( __( 'Find it in your account on <a href="%s" target="_blank">Thivinfo</a>. ', 'wp-pericles-import' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( $url ) ); ?></p>
		<a href="<?php echo esc_url( $activation_url ); ?>"><?php echo $text; ?></a>
		<?php
	}
}
new Settings();