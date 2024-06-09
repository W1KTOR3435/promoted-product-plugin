<?php
/**
 * Contains the logic for displaying the promoted product.
 *
 * @package PromotedProduct
 */

namespace PromotedProduct;

/**
 * Class Frontend
 * Handles the frontend display of the promoted product.
 */
class Frontend {

	/**
	 * Initialize the plugin by setting up the necessary actions.
	 */
	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'display_promoted_product' ) );
	}

	/**
	 * Display the promoted product in the header.
	 */
	public static function display_promoted_product() {
		$promoted_product_id = get_option( 'promoted_product' );
		if ( ! $promoted_product_id ) {
			return;
		}

		$transient_key         = 'promoted_product_' . $promoted_product_id;
		$promoted_product_data = get_transient( $transient_key );

		if ( false === $promoted_product_data ) {
			$product = wc_get_product( $promoted_product_id );
			if ( ! $product ) {
				return;
			}

			$promoted_title = get_option( 'promoted_product_title', 'FLASH SALE:' );
			$bg_color       = get_option( 'promoted_product_bg_color', '#000000' );
			$text_color     = get_option( 'promoted_product_text_color', '#ffffff' );

			$custom_title = get_post_meta( $promoted_product_id, '_promoted_product_custom_title', true );
			$title        = $custom_title ? $custom_title : $product->get_name();
			$product_link = get_permalink( $promoted_product_id );

			$promoted_product_data = array(
				'title'          => $title,
				'product_link'   => $product_link,
				'promoted_title' => $promoted_title,
				'bg_color'       => $bg_color,
				'text_color'     => $text_color,
			);

			set_transient( $transient_key, $promoted_product_data, HOUR_IN_SECONDS );
		}

		?>
		<div style="width: 100%; background-color: <?php echo esc_attr( $promoted_product_data['bg_color'] ); ?>; text-align: center; padding: 10px;">
			<a href="<?php echo esc_url( $promoted_product_data['product_link'] ); ?>" style="color: <?php echo esc_attr( $promoted_product_data['text_color'] ); ?>;">
				<?php echo esc_html( $promoted_product_data['promoted_title'] ); ?> <?php echo esc_html( $promoted_product_data['title'] ); ?>
			</a>
		</div>
		<?php
	}
}
