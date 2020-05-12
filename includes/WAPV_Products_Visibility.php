<?php
defined( 'ABSPATH' ) || exit;

class WAPV_Products_Visibility {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}


	public function visibility_column( $columns ) {
		$columns['wapv_visibility'] = __( 'Visibility', 'wapv' );

		return $columns;
	}

	public function visibility_column_content( $column, $product_id ) {
		if ( $column === 'wapv_visibility' ) {
			?>
            <div class="wapv-visibility">
                <input type="checkbox" class="wapv-visibility-checkbox"
                       id="wapv-visibility-checkbox-<?php echo esc_attr( $product_id ) ?>"
                       data-nonce="<?php echo wp_create_nonce( 'wapv_nonce' ) ?>"
                       data-id="<?php echo esc_attr( $product_id ) ?>" <?php checked( get_post_status( $product_id ) == 'publish', true, true ) ?>>
            </div>
			<?php
		}
	}

	public function handle() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'wapv_nonce' ) or ! isset( $_POST['nonce'] ) ) {
			wp_send_json_error( array(
				'code' => 1,
				'msg'  => esc_html__( 'Cheating!', 'wapv' )
			) );
		}
		if ( ! current_user_can( 'edit_others_posts' ) ) {
			wp_send_json_error( array(
				'code' => 2,
				'msg'  => esc_html__( 'You don\'t have sufficient permissions to do this action', 'wapv' )
			) );
		}
		if ( ! isset( $_POST['product_id'] ) ) {
			wp_send_json_error( array(
				'code' => 3,
				'msg'  => esc_html__( 'You must provide product ID', 'wapv' )
			) );
		}

		if ( ! class_exists( 'woocommerce' ) ) {
			wp_send_json_error( array(
				'code' => 4,
				'msg'  => esc_html__( 'You must install woocommerce', 'wapv' )
			) );
		}
		$product_id     = sanitize_text_field( $_POST['product_id'] );
		$product        = new WC_Product( $product_id );
		$product_status = $product->get_status();

		if ( $product_status == 'publish' ) { //flipping product status between publish and draft
			$new_status = 'draft';
		} else {
			$new_status = 'publish';
		}

		$product->set_status( $new_status );
		$product->save();
		wp_send_json_success( array(
			'code' => 1,
			'msg'  => esc_html__( 'Saved!', 'wapv' )
		) );

	}


}