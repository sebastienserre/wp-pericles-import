<?php

namespace WP_PERICLES\IMPORT\Licence;

use function _e;
use function add_action;
use function class_exists;
use function define;
use function esc_url_raw;
use function get_field;
use function var_dump;
use function version_compare;
use function wp_remote_get;
use function wp_remote_retrieve_body;
use function wp_remote_retrieve_response_code;
use const PLUGIN_VERSION;
use const WP_MAIN_FILE_PLUGIN_PATH;
use const WP_PERICLES_PLUGIN_PATH;
use const WP_PLUGIN_ID;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class Licence {
	private $key;
	private  $product_id;
	private  $order_id;

	public function __construct() {
		$this->key = $this->setKey();

		add_action( 'acf/save_post', [ $this, 'launch_check' ], 15 );
		if ( ! $this->check_key_validity() ) {
			add_action( 'admin_notices', [ $this, 'invalid_key_notice' ] );
		}

		if ( empty( get_field( 'api_key', 'options' ) ) ) {
			add_action( 'admin_notices', [ $this, 'empty_key_notice' ] );
		}
		add_action( 'admin_init', [ $this, 'get_version' ] );

		add_action( 'pre_current_active_plugins', [ $this, 'render_update_notice' ] );
		add_action( 'admin_init', [ $this, 'render_update_notice' ] );
		define( 'PLUGIN_VERSION', $this->get_plugin_datas( 'Version' ) );
	}

	public function get_plugin_datas( $data = '' ) {
		$datas = \get_plugin_data( WP_MAIN_FILE_PLUGIN_PATH );

		return $datas[ $data ];
	}

	public function setKey() {
		$this->key = $this->getKey();
	}

	/**
	 * @return mixed
	 */
	public function getKey() {
		if ( ! class_exists( 'ACF' ) ) {
			return;
		}

		$this->key = get_field( 'api_key', 'options' );

		return $this->key;
	}

	public function launch_check( $post_id ) {
		if ( 'options' === $post_id ) {
			$this->check_key_validity();
		}
	}

	public function get_decoded_body( $url ) {
		$response = wp_remote_get( $url );
		if ( 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
			$body         = wp_remote_retrieve_body( $response );
			$decoded_body = json_decode( $body, true );
		}
		if ( ! empty( $decoded_body ) ) {
			return $decoded_body;
		}

		return false;
	}

	public function check_key_validity() {
		$key          = get_field( 'api_key', 'options' );
		$ck           = THFO_CONSUMER_KEY;
		$cs           = THFO_SECRET_KEY;
		$url          = "https://thivinfo.com/wp-json/lmfwc/v2/licenses/$key?consumer_key=$ck&consumer_secret=$cs";
		$decoded_body = $this->get_decoded_body( $url );
		$this->product_id = $decoded_body["data"]["productId"];
		$this->order_id = $decoded_body["data"]["orderId"];
		if ( $decoded_body['success'] && '0' !== $decoded_body['validFor'] ) {
			return true;
		}

		return false;
	}

	public function invalid_key_notice() {
		$plugin_name = $this->get_plugin_datas( 'Name' );
		?>
        <div class="notice notice-error">
            <h3><?php echo $plugin_name . ' ' . PLUGIN_VERSION; ?></h3>
            <p>
				<?php
				_e( 'The API key seems invalid, please check again', 'wp-pericles-import' );
				?>
            </p>
        </div>
		<?php
	}

	public function empty_key_notice() {
		$plugin_name = $this->get_plugin_datas( 'Name' );
		?>
        <div class="notice notice-error">
            <h3><?php echo $plugin_name . ' ' . PLUGIN_VERSION; ?></h3>
            <p>
				<?php

				_e( 'The API key field seems empty, please check again', 'wp-pericles-import' );
				?>
            </p>
        </div>
		<?php
	}

	public function get_product_data( $product_id =''){
		if ( empty( $product_id ) ) {
			$product_id = WP_PLUGIN_ID;
		}
		$url          = "https://thivinfo.com/wp-json/wc/v3/products/$product_id?consumer_key=ck_0e7d2eddb58ea1a2d56212e1042dbeb0511274c3&consumer_secret=cs_b0b9fb497e534026e45d2ce2335b8297fe90ead9";
		$decoded_body = $this->get_decoded_body( $url );
		return $decoded_body;
	}

	public function get_version() {
		$decoded_body = $this->get_product_data();
		$version      = $decoded_body["tags"][0]["name"];

		return $version;
	}

	public function prefix_plugin_update_message( $data, $response ) {
		?>
        <div class="update-message">test</div>
		<?php
	}

	public function check_update() {
		$old_version = PLUGIN_VERSION;
		$new_version = $this->get_version();
		$update      = version_compare( $old_version, $new_version, '<' );
		if ( $update ) {
			return true;
		}

		return false;
	}

	public function render_update_notice() {
		$upgrade = $this->check_update();
		if ( $upgrade ) {
			add_action( 'admin_notices', [ $this, 'display_upgrade_notice' ] );
		}
	}

	public function display_upgrade_notice() {
		$plugin_name = $this->get_plugin_datas( 'Name' );
		$link = "https://thivinfo.com/mon-compte/view-order/$this->order_id/"
		?>
        <div class="notice notice-info is-dismissible ">
            <h3><?php echo $plugin_name . ' ' . PLUGIN_VERSION; ?></h3>
            <p>
				<?php
				_e( 'An update is available', 'wp-pericles-import' );
	            ?>
	            <a href="<?php echo esc_url_raw( $link ) ?>"><?php
		            _e( 'Download', 'wp-pericles-import' );
		            ?></a>
            </p>
        </div>
		<?php
	}

	public function get_download(){
		$decoded_body = $this->get_product_data( $this->product_id );
		if ( !empty( $decoded_body ) ){
			$download = $decoded_body["downloads"][0]["file"];
			return $download;
		}
		return false;
	}

}

new Licence();
