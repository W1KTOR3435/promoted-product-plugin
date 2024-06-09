<?php
/**
 * Settings class for the Promoted Product Plugin.
 *
 * @package PromotedProduct
 */

namespace PromotedProduct;

/**
 * Class to handle plugin settings.
 */
class Settings {

	/**
	 * Initialize settings.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'woocommerce_get_sections_products', array( __CLASS__, 'add_settings_section' ) );
		add_filter( 'woocommerce_get_settings_products', array( __CLASS__, 'add_settings_fields' ), 10, 2 );
		add_action( 'woocommerce_admin_field_custom_html', array( __CLASS__, 'output_custom_html' ) );
	}

	/**
	 * Add settings section.
	 *
	 * @param array $sections Existing sections.
	 * @return array Modified sections.
	 */
	public static function add_settings_section( $sections ) {
		$sections['promoted_product'] = __( 'Promoted Product', 'woocommerce' );
		return $sections;
	}

	/**
	 * Add settings fields.
	 *
	 * @param array  $settings Existing settings.
	 * @param string $current_section Current section ID.
	 * @return array Modified settings.
	 */
	public static function add_settings_fields( $settings, $current_section ) {
		if ( 'promoted_product' === $current_section ) {
			$promoted_product_id    = get_option( 'promoted_product' );
			$promoted_product_title = '';
			$edit_link              = '';

			if ( $promoted_product_id ) {
				$product = wc_get_product( $promoted_product_id );
				if ( $product ) {
					$promoted_product_title = $product->get_name();
					$edit_link              = get_edit_post_link( $promoted_product_id );
				}
			}

			$settings = array(
				array(
					'title' => __( 'Promoted Product Settings', 'woocommerce' ),
					'type'  => 'title',
					'id'    => 'promoted_product_settings',
				),
				array(
					'title' => __( 'Title', 'woocommerce' ),
					'type'  => 'text',
					'id'    => 'promoted_product_title',
				),
				array(
					'title' => __( 'Background Color', 'woocommerce' ),
					'type'  => 'color',
					'id'    => 'promoted_product_bg_color',
				),
				array(
					'title' => __( 'Text Color', 'woocommerce' ),
					'type'  => 'color',
					'id'    => 'promoted_product_text_color',
				),
				array(
					'title' => __( 'Active Promoted Product', 'woocommerce' ),
					'type'  => 'custom_html',
					'desc'  => $promoted_product_title ? $promoted_product_title . ' | <a href="' . $edit_link . '">' . __( 'Edit Product', 'woocommerce' ) . '</a>' : __( 'No promoted product selected', 'woocommerce' ),
					'id'    => 'active_promoted_product_display',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'promoted_product_settings',
				),
			);
		}
		return $settings;
	}

	/**
	 * Output custom HTML for settings field.
	 *
	 * @param array $value Field value.
	 * @return void
	 */
	public static function output_custom_html( $value ) {
		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ); ?></th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<?php echo wp_kses_post( $value['desc'] ); ?>
			</td>
		</tr>
		<?php
	}
}
