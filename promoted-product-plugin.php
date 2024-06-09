<?php
/*
Plugin Name: Promoted Product Plugin
Description: A plugin to feature a promoted product on every page.
Version: 1.0
Author: Wiktor Kowalczyk
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function vm_is_woocommerce_active() {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    return is_plugin_active( 'woocommerce/woocommerce.php' );
}

function vm_admin_notice_wc_inactive() {
    ?>
    <div class="notice notice-error">
        <p><?php esc_html_e( 'The Promoted Product Plugin requires WooCommerce to be installed and activated.', 'promoted-product' ); ?></p>
    </div>
    <?php
}

if ( vm_is_woocommerce_active() ) {
    require_once __DIR__ . '/vendor/autoload.php';
    PromotedProduct\Plugin::init();
} else {
    add_action( 'admin_notices', 'vm_admin_notice_wc_inactive' );
}