<?php

namespace PromotedProduct;

class Frontend {
    public static function init() {
        add_action( 'wp_head', [ __CLASS__, 'display_promoted_product' ] );
    }

    public static function display_promoted_product() {
        $promoted_product_id = get_option( 'promoted_product' );
        if ( ! $promoted_product_id ) {
            return;
        }

        $product = wc_get_product( $promoted_product_id );
        if ( ! $product ) {
            return;
        }

        $promoted_title = get_option( 'promoted_product_title', 'FLASH SALE:' );
        $bg_color = get_option( 'promoted_product_bg_color', '#000000' );
        $text_color = get_option( 'promoted_product_text_color', '#ffffff' );

        $custom_title = get_post_meta( $promoted_product_id, '_promoted_product_custom_title', true );
        $title = $custom_title ? $custom_title : $product->get_name();
        $product_link = get_permalink($promoted_product_id);
        ?>
        <div style="width: 100%; background-color: <?php echo esc_attr($bg_color); ?>; text-align: center; padding: 10px;">
            <a href="<?php echo esc_url($product_link); ?>" style="color: <?php echo esc_attr($text_color); ?>;">
                <?php echo esc_html($promoted_title); ?> <?php echo esc_html($title); ?>
            </a>
        </div>
        <?php
    }
}