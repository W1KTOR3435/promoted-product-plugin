<?php
/**
 * Main plugin class for the Promoted Product Plugin.
 *
 * @package PromotedProduct
 */

namespace PromotedProduct;

/**
 * Main plugin class.
 */
class Plugin {

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public static function init() {
		Settings::init();
		ProductEditor::init();
		Frontend::init();
	}
}
