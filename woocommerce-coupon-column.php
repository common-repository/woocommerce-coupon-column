<?php
/*
Plugin Name: WooCommerce - Coupon Column
Plugin URI:
Description: Simple plugin that adds a new column into WooCommerce Orders page to show coupons used.
Author: Pross
Author URI: https://www.wpbeaverbuider.com
Version: 0.4
Text Domain: woocommerce-coupon-column
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

class WC_Coupon_Column {

	function __construct() {
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_coupon_column' ), 11, 1 );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'coupon_columns' ), 2 );
	}

	function add_coupon_column( $columns ) {

		return array_slice( $columns, 0, 3, true ) + array( 'coupon_code' => __( 'Coupons', 'woocommerce-coupon-column' ) ) + array_slice( $columns, 3, count( $columns ) - 3, true );
	}

	function coupon_columns( $column ) {

		switch ( $column ) {
			case 'coupon_code':
				global $post;
				$order = new WC_Order( $post->ID );
				if ( $order->get_coupon_codes() ) {
					$coupons = array_map( array( $this, 'filter' ), $order->get_coupon_codes() );
					$coupons = implode( '', $coupons );
					echo '<style>#coupon_code{width:15%}</style>';
					echo $coupons;
				}
				break;
		}
	}

	function filter( $tag ) {
		$id = $this->get_coupon_id( $tag );
		if ( is_numeric( $id ) ) {
			$url   = admin_url( 'post.php?action=edit&post=' . $id );
			$style = '';
			$del   = '';
		} else {
			$url   = '#';
			$style = ' style="color:red;"';
			$del   = __( ' (deleted)', 'woocommerce-coupon-column' );
		}
		return sprintf(
			'<span class="code"><a %s href="%s">%s%s</a></span>',
			$style,
			$url,
			$tag,
			$del
		);
	}

	function get_coupon_id( $title ) {
		$post = get_page_by_title( $title, OBJECT, 'shop_coupon' );
		return ( is_object( $post ) ) ? $post->ID : false;
	}
}
new WC_Coupon_Column;
