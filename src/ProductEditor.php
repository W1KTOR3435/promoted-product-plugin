<?php
/**
 * ProductEditor class for the Promoted Product Plugin.
 *
 * @package PromotedProduct
 */

namespace PromotedProduct;

/**
 * Class to handle product editor functionalities.
 */
class ProductEditor {

	/**
	 * Initialize product editor functionalities.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'woocommerce_product_options_general_product_data', array( __CLASS__, 'add_promoted_fields' ) );
		add_action( 'woocommerce_process_product_meta', array( __CLASS__, 'save_promoted_fields' ) );
		add_action( 'check_promoted_product_expiration', array( __CLASS__, 'check_promoted_product_expiration_handler' ) );
	}

	/**
	 * Add custom fields to the product editor.
	 *
	 * @return void
	 */
	public static function add_promoted_fields() {
		global $post;

		$promote_product    = get_post_meta( $post->ID, '_promote_product', true );
		$custom_title       = get_post_meta( $post->ID, '_promoted_product_custom_title', true );
		$expiration_checked = get_post_meta( $post->ID, '_promoted_product_expiration', true );
		$expiration_date    = get_post_meta( $post->ID, '_promoted_product_expiration_date', true );

		wp_nonce_field( 'save_promoted_fields', 'promoted_product_nonce' );

		woocommerce_wp_checkbox(
			array(
				'id'          => 'promote_product',
				'label'       => __( 'Promote this product', 'woocommerce' ),
				'description' => __( 'Check to promote this product', 'woocommerce' ),
				'value'       => $promote_product,
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => 'promoted_product_custom_title',
				'label'       => __( 'Promoted Product Title', 'woocommerce' ),
				'description' => __( 'Custom title to display instead of the product title', 'woocommerce' ),
				'desc_tip'    => 'true',
				'value'       => $custom_title,
			)
		);

		woocommerce_wp_checkbox(
			array(
				'id'          => 'promoted_product_expiration',
				'label'       => __( 'Set Expiration Date', 'woocommerce' ),
				'description' => __( 'Check to set an expiration date for the promotion', 'woocommerce' ),
				'value'       => $expiration_checked,
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => 'promoted_product_expiration_date',
				'label'       => __( 'Expiration Date', 'woocommerce' ),
				'description' => __( 'Set the expiration date and time', 'woocommerce' ),
				'type'        => 'datetime-local',
				'desc_tip'    => 'true',
				'value'       => $expiration_date,
			)
		);
	}

	/**
	 * Save custom fields.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public static function save_promoted_fields( $post_id ) {
		if ( ! isset( $_POST['promoted_product_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['promoted_product_nonce'] ) ), 'save_promoted_fields' ) ) {
			return;
		}

		$promote_product             = isset( $_POST['promote_product'] ) ? 'yes' : 'no';
		$custom_title                = isset( $_POST['promoted_product_custom_title'] ) ? sanitize_text_field( wp_unslash( $_POST['promoted_product_custom_title'] ) ) : '';
		$promoted_product_expiration = isset( $_POST['promoted_product_expiration'] ) ? 'yes' : 'no';
		$expiration_date             = isset( $_POST['promoted_product_expiration_date'] ) ? sanitize_text_field( wp_unslash( $_POST )['promoted_product_expiration_date'] ) : '';

		update_post_meta( $post_id, '_promote_product', $promote_product );
		update_post_meta( $post_id, '_promoted_product_custom_title', $custom_title );
		update_post_meta( $post_id, '_promoted_product_expiration', $promoted_product_expiration );
		update_post_meta( $post_id, '_promoted_product_expiration_date', $expiration_date );

		if ( 'yes' === $promoted_product_expiration && ! empty( $expiration_date ) ) {
			$timestamp = self::get_timestamp_from_date( $expiration_date );
			if ( $timestamp > time() ) {
				wp_clear_scheduled_hook( 'check_promoted_product_expiration', array( $post_id ) );
				wp_schedule_single_event( $timestamp, 'check_promoted_product_expiration', array( $post_id ) );
			}
		}

		if ( 'yes' === $promote_product ) {
			$current_promoted = get_option( 'promoted_product' );
			if ( $current_promoted && $current_promoted != $post_id ) { // phpcs:ignore
				update_post_meta( $current_promoted, '_promote_product', 'no' );
			}
			update_option( 'promoted_product', $post_id );
		} else {
			$current_promoted = get_option( 'promoted_product' );
			if ( $current_promoted == $post_id ) { // phpcs:ignore
				delete_option( 'promoted_product' );
			}
		}
	}

	/**
	 * Handle expiration of promoted product.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public static function check_promoted_product_expiration_handler( $post_id ) {
		$expiration_date = get_post_meta( $post_id, '_promoted_product_expiration_date', true );
		if ( self::get_timestamp_from_date( $expiration_date ) <= time() ) {
			update_post_meta( $post_id, '_promote_product', 'no' );
			$current_promoted = get_option( 'promoted_product' );
			if ( $current_promoted == $post_id ) { // phpcs:ignore
				delete_option( 'promoted_product' );
			}
		}
	}

	/**
	 * Convert date string to timestamp.
	 *
	 * @param string $date_string Date string.
	 * @return int Timestamp.
	 */
	private static function get_timestamp_from_date( $date_string ) {
		$timezone = new \DateTimeZone( wp_timezone_string() );
		$datetime = new \DateTime( $date_string, $timezone );
		return $datetime->getTimestamp();
	}
}
