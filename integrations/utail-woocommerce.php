<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

/* Affect total price */

function utail_woocommerce_get_discounted_price( $price, $values, $cart ) {
	$utail =& utail_instance();
	$user_id = utail_get_user_id();
	if ( $user_id ) {
		utail_log_debug('utail_woocommerce_cart_total: user_id is ' . $user_id);
		$price = $utail->apply_discount($user_id, $price);
	}
	return $price;
}
	
add_filter('woocommerce_get_discounted_price', 'utail_woocommerce_get_discounted_price', 10, 3);

/* Track purchase */

function utail_woocommerce_new_order( $order_id ) {
	$utail =& utail_instance();
	$user_id = utail_get_user_id();
	if ( $user_id ) {
		utail_log_debug('utail_woocommerce_new_order: user_id is ' . $user_id);
		$utail->track_purchase($user_id, WC()->cart->total);
	}
}
	
add_filter('woocommerce_new_order', 'utail_woocommerce_new_order', 10, 1);

/* Show message on cart & checkout pages */

function utail_woocommerce_cart_message() {
	$utail =& utail_instance();
	$user_id = utail_get_user_id();
	utail_log_debug('utail_woocommerce_cart_message user_id = ' . $user_id);
	$string = '0';
	if ( $user_id ) {
		$discount = $utail->get_discount($user_id);
		utail_log_dump($discount);
		if ( $discount ) {
			$string = $discount->type === 'percent'
				? $discount->discount . '%'
				: wc_price( $discount->discount );
		}
	}
?>
	<tr class="utail-discount">
		<th><?php _e( 'Social Discount' ); ?></th>
		<td><span class="amount"><?php echo $string; ?></span>
			<?= do_shortcode('[utail-button button_type=clean]'); ?></td>
	</tr>
	<script>
	jQuery(function($) {
		UTail && UTail.on('close', function() {
			window.location.reload();
		});
	});
	</script>
<?php
}

function utail_woocommerce_checkout_message() {
	$utail =& utail_instance();
	$user_id = utail_get_user_id();
	utail_log_debug('utail_woocommerce_cart_message user_id = ' . $user_id);
	if ( $user_id ) {
		$discount = $utail->get_discount($user_id);
		utail_log_dump($discount);
		if ( $discount ) {
			$string = $discount->type === 'percent'
				? $discount->discount . '%'
				: wc_price( $discount->discount );
?>
	<tr class="utail-discount">
		<th><?php _e( 'Social Discount' ); ?></th>
		<td><span class="amount"><?php echo $string; ?></span></td>
	</tr>
<?php
		}
	}
}

add_action('woocommerce_cart_totals_before_order_total', 'utail_woocommerce_cart_message', 99);
add_action('woocommerce_review_order_before_order_total', 'utail_woocommerce_checkout_message', 99);

/* Get discount button */

function utail_woocommerce_after_shop_loop_item() {
	echo '<br/>' . do_shortcode('[utail-button button_class=button]');
}

add_action('woocommerce_after_shop_loop_item', 'utail_woocommerce_after_shop_loop_item', 11);

function utail_woocommerce_after_add_to_cart_button() {
	echo do_shortcode('[utail-button button_class=button]');
}

add_action('woocommerce_after_add_to_cart_button', 'utail_woocommerce_after_add_to_cart_button');

/* Show message on order page */

/*
function utail_woocommerce_add_total( $total_rows, $order ) {
	$utail =& utail_instance();
	$user_id = utail_get_user_id();
	utail_log_debug('utail_woocommerce_cart_message user_id = ' . $user_id);
	if ( $user_id ) {
		$discount = $utail->get_discount($user_id);
		utail_log_dump($discount);
		if ( $discount ) {
			$string = $discount->type === 'percent'
				? $discount->discount . '%'
				: wc_price( $discount->discount );
			// Insert `utail_discount` just after `cart_subtotal`
			foreach ( $total_rows as $key => $value ) {
				$result[$key] = $value;
				if ( $key === 'cart_subtotal' ) {
					$result['utail_discount'] = array(
						'label' => _('Social Discount'),
						'value' => $string
					);
				}
			}
			$total_rows = $result;
		}
	}
	return $total_rows;
}

add_action('woocommerce_get_order_item_totals', 'utail_woocommerce_add_total', 99, 2);
*/
