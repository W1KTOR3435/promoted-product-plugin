<?php
/*
Plugin Name: Promoted Product Plugin
Description: A plugin to feature a promoted product on every page.
Version: 1.0
Author: Wiktor
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Autoload classes
require_once __DIR__ . '/vendor/autoload.php';

// Initialize the plugin
PromotedProduct\Plugin::init();