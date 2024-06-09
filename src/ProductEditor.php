<?php

namespace PromotedProduct;

class ProductEditor {
    public static function init() {
        add_action( 'woocommerce_product_options_general_product_data', [ __CLASS__, 'add_promoted_fields' ] );
        add_action( 'woocommerce_process_product_meta', [ __CLASS__, 'save_promoted_fields' ] );
        add_action('check_promoted_product_expiration', [__CLASS__, 'check_promoted_product_expiration_handler']);
    }

    public static function add_promoted_fields() {
        global $post;

        $promote_product = get_post_meta( $post->ID, '_promote_product', true );
        $custom_title = get_post_meta( $post->ID, '_promoted_product_custom_title', true );
        $expiration_checked = get_post_meta( $post->ID, '_promoted_product_expiration', true );
        $expiration_date = get_post_meta( $post->ID, '_promoted_product_expiration_date', true );

        woocommerce_wp_checkbox( [
            'id'            => 'promote_product',
            'label'         => __( 'Promote this product', 'woocommerce' ),
            'description'   => __( 'Check to promote this product', 'woocommerce' ),
            'value'         => $promote_product
        ] );

        woocommerce_wp_text_input( [
            'id'            => 'promoted_product_custom_title',
            'label'         => __( 'Promoted Product Title', 'woocommerce' ),
            'description'   => __( 'Custom title to display instead of the product title', 'woocommerce' ),
            'desc_tip'      => 'true',
            'value'         => $custom_title,
        ] );

        woocommerce_wp_checkbox( [
            'id'            => 'promoted_product_expiration',
            'label'         => __( 'Set Expiration Date', 'woocommerce' ),
            'description'   => __( 'Check to set an expiration date for the promotion', 'woocommerce' ),
            'value'         => $expiration_checked,
        ] );

        woocommerce_wp_text_input( [
            'id'            => 'promoted_product_expiration_date',
            'label'         => __( 'Expiration Date', 'woocommerce' ),
            'description'   => __( 'Set the expiration date and time', 'woocommerce' ),
            'type'          => 'datetime-local',
            'desc_tip'      => 'true',
            'value'         => $expiration_date,
        ] );
    }

    public static function save_promoted_fields( $post_id ) {
        $promote_product = isset( $_POST['promote_product'] ) ? 'yes' : 'no';
        $custom_title = isset( $_POST['promoted_product_custom_title'] ) ? sanitize_text_field( $_POST['promoted_product_custom_title'] ) : '';
        $promoted_product_expiration = isset( $_POST['promoted_product_expiration'] ) ? 'yes' : 'no';
        $expiration_date = isset( $_POST['promoted_product_expiration_date'] ) ? sanitize_text_field( $_POST['promoted_product_expiration_date'] ) : '';

        update_post_meta( $post_id, '_promote_product', $promote_product );
        update_post_meta( $post_id, '_promoted_product_custom_title', $custom_title );
        update_post_meta( $post_id, '_promoted_product_expiration', $promoted_product_expiration );
        update_post_meta( $post_id, '_promoted_product_expiration_date', $expiration_date );

        if ($promoted_product_expiration === 'yes' && !empty($expiration_date)) {
            $timestamp = self::get_timestamp_from_date($expiration_date);
            if ($timestamp > current_time('timestamp', true)) {
                wp_clear_scheduled_hook('check_promoted_product_expiration', [$post_id]);
                wp_schedule_single_event($timestamp, 'check_promoted_product_expiration', [$post_id]);
                error_log("Scheduled expiration event for product $post_id at $expiration_date ($timestamp)");
            }
        }

        if ( $promote_product === 'yes' ) {
            $current_promoted = get_option( 'promoted_product' );
            if ( $current_promoted && $current_promoted != $post_id ) {
                update_post_meta( $current_promoted, '_promote_product', 'no' );
            }
            update_option( 'promoted_product', $post_id );
        } else {
            $current_promoted = get_option( 'promoted_product' );
            if ( $current_promoted == $post_id ) {
                delete_option( 'promoted_product' );
            }
        }
    }

    public static function check_promoted_product_expiration_handler($post_id) {
        $expiration_date = get_post_meta($post_id, '_promoted_product_expiration_date', true);
        if (self::get_timestamp_from_date($expiration_date) <= current_time('timestamp', true)) {
            update_post_meta($post_id, '_promote_product', 'no');
            $current_promoted = get_option('promoted_product');
            if ($current_promoted == $post_id) {
                delete_option('promoted_product');
            }
        }
    }

    private static function get_timestamp_from_date($date_string) {
        $timezone = new \DateTimeZone(wp_timezone_string());
        $datetime = new \DateTime($date_string, $timezone);
        return $datetime->getTimestamp();
    }
}