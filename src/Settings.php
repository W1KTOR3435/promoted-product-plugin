<?php

namespace PromotedProduct;

class Settings {
    public static function init() {
        add_filter( 'woocommerce_get_sections_products', [ __CLASS__, 'add_settings_section' ] );
        add_filter( 'woocommerce_get_settings_products', [ __CLASS__, 'add_settings_fields' ], 10, 2 );
        add_action( 'woocommerce_admin_field_custom_html', [ __CLASS__, 'output_custom_html' ] );
    }

    public static function add_settings_section( $sections ) {
        $sections['promoted_product'] = __( 'Promoted Product', 'woocommerce' );
        return $sections;
    }

    public static function add_settings_fields( $settings, $current_section ) {
        if ( $current_section == 'promoted_product' ) {
            $promoted_product_id = get_option( 'promoted_product' );
            $promoted_product_title = '';
            $edit_link = '';

            if ( $promoted_product_id ) {
                $product = wc_get_product( $promoted_product_id );
                if ( $product ) {
                    $promoted_product_title = $product->get_name();
                    $edit_link = get_edit_post_link( $promoted_product_id );
                }
            }

            $settings = [
                [
                    'title' => __( 'Promoted Product Settings', 'woocommerce' ),
                    'type'  => 'title',
                    'id'    => 'promoted_product_settings',
                ],
                [
                    'title' => __( 'Title', 'woocommerce' ),
                    'type'  => 'text',
                    'id'    => 'promoted_product_title',
                ],
                [
                    'title' => __( 'Background Color', 'woocommerce' ),
                    'type'  => 'color',
                    'id'    => 'promoted_product_bg_color',
                ],
                [
                    'title' => __( 'Text Color', 'woocommerce' ),
                    'type'  => 'color',
                    'id'    => 'promoted_product_text_color',
                ],
                [
                    'title' => __( 'Active Promoted Product', 'woocommerce' ),
                    'type'  => 'custom_html',
                    'desc'  => $promoted_product_title ? $promoted_product_title . ' | <a href="' . $edit_link . '">' .  __('Edit Product', 'woocommerce') . '</a>' : __( 'No promoted product selected', 'woocommerce' ),
                    'id'    => 'active_promoted_product_display',
                ],
                [
                    'type' => 'sectionend',
                    'id'   => 'promoted_product_settings',
                ],
            ];
        }
        return $settings;
    }

    public static function output_custom_html( $value ) {
        ?>
        <tr valign="top">
            <th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ); ?></th>
            <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ); ?>">
                <?php echo wp_kses_post( $value['desc'] ); ?>
            </td>
        </tr>
        <?php
    }
}