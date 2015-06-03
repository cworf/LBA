<?php
/*
Plugin Name: WooCommerce Store Credit
Plugin URI: http://www.woothemes.com/products/store-credit/
Description: Create "store credit" coupons for customers in your WooCommerce store which are redeemable at checkout. Also, generate and email store credit coupons to customers via the backend.
Version: 2.1.3
Author: WooThemes
Author URI: http://www.woothemes.com/
Requires at least: 3.3
Tested up to: 3.9

	Copyright: 2014 WooThemes.
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html

	Adapted from the original store credit extension created by Visser Labs (http://visser.com.au)
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once 'woo-includes/woo-functions.php';
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'c4bf3ecec4146cb69081e5b28b6cdac4', '18609' );

if ( is_woocommerce_active() && ! class_exists( 'WC_Store_Credit_Plus' ) ) {

	/**
	 * Localisation
	 */
	load_plugin_textdomain( 'woocommerce-store-credit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	/**
	 * WC_Store_Credit_Plus class
	 */
	class WC_Store_Credit_Plus {

		/**
		 * Constructor
		 */
		public function __construct() {
			define( 'WC_STORE_CREDIT_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			define( 'WC_STORE_CREDIT_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

			add_filter( 'woocommerce_coupon_is_valid', array( $this, 'coupon_is_valid' ), 10, 2 );
			add_filter( 'woocommerce_coupon_is_valid_for_cart', array( $this, 'coupon_is_valid_for_cart' ), 10, 2 );
			add_action( 'woocommerce_new_order', array( $this, 'update_credit_amount' ), 9 );
			add_action( 'woocommerce_before_my_account', array( $this, 'display_credit' ) );
			add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'coupon_get_discount_amount' ), 10, 5 );
			add_filter( 'woocommerce_cart_totals_coupon_label', array( $this, 'cart_totals_coupon_label' ), 10, 2 );

			// Workaround a 2.1.12 limitation where the coupon could not be made valid
			add_filter( 'woocommerce_coupon_loaded', array( $this, 'woocommerce_coupon_loaded' ) );

			// Admin
			if ( is_admin() ) {
				include_once( 'includes/class-wc-store-credit-plus-admin.php' );
			}
		}

		/**
		 * Display credit
		 */
		public function display_credit() {
			if ( $coupons = $this->get_customer_credit() ) {
				?>
					<h2><?php _e( 'Store Credit', 'woocommerce-store-credit' ); ?></h2>
					<ul class="store-credit">
						<?php
						foreach ( $coupons as $code ) {
							$coupon = new WC_Coupon( $code->post_title );
							if ( ( 'store_credit' === $coupon->type || ( isset( $coupon->is_store_credit ) && $coupon->is_store_credit ) ) ) {
								echo '<li><strong>' . $coupon->code . '</strong> &mdash;' . wc_price( $coupon->amount ) . '</li>';
							}
						}
						?>
					</ul>
				<?php
			}
		}

		/**
		 * Get credit for a customer
		 */
		public function get_customer_credit() {
			if ( 'no' === get_option( 'woocommerce_store_credit_show_my_account', 'yes' ) ) {
				return;
			}

			$user = wp_get_current_user();

			if ( '' === $user->user_email ) {
				return;
			}

			$args = array(
				'post_type'      => 'shop_coupon',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => 'customer_email',
						'value'   => $user->user_email,
						'compare' => 'LIKE'
					),
					array(
						'key'     => 'coupon_amount',
						'value'   => '0',
						'compare' => '>=',
						'type'    => 'NUMERIC'
					)
				)
			);

			return get_posts( $args );
		}

		/**
		 * Check if credit is valid
		 */
		public function coupon_is_valid( $valid, $coupon ) {
			if ( $valid && ( 'store_credit' === $coupon->type || $coupon->is_store_credit ) && $coupon->amount <= 0 ) {
				wc_add_notice( __( 'There is no credit remaining on this coupon.', 'woocommerce-store-credit' ), 'error' );
				return false;
			}
			return $valid;
		}

		/**
		 * Check if credit is valid
		 */
		public function coupon_is_valid_for_cart( $valid, $coupon ) {
			if ( ( 'store_credit' === $coupon->type || $coupon->is_store_credit ) ) {
				return true;
			}
			return $valid;
		}

		/**
		 * Update a coupon after purchase
		 */
		public function update_credit_amount() {
			if ( $coupons = WC()->cart->get_coupons() ) {
				foreach ( $coupons as $code => $coupon ) {
					if ( ( 'store_credit' === $coupon->type || $coupon->is_store_credit ) ) {
						$credit_remaining = max( 0, ( $coupon->amount - WC()->cart->coupon_discount_amounts[ $code ] ) );

						if ( $credit_remaining <= 0 && 'yes' === get_option( 'woocommerce_delete_store_credit_after_usage', 'yes' ) ) {
							wp_delete_post( $coupon->id );
						} else {
							update_post_meta( $coupon->id, 'coupon_amount', $credit_remaining );
						}
					}
				}
			}
		}

		/**
		 * Get coupon discount amount
		 * @param  float $discount
		 * @param  float $discounting_amount
		 * @param  object $cart_item
		 * @param  bool $single
		 * @param  WC_Coupon $coupon
		 * @return float
		 */
		public function coupon_get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
			if ( ( 'store_credit' === $coupon->type || $coupon->is_store_credit ) && ! is_null( $cart_item ) ) {
				/**
				 * This is the most complex discount - we need to divide the discount between rows based on their price in
				 * proportion to the subtotal. This is so rows with different tax rates get a fair discount, and so rows
				 * with no price (free) don't get discounted.
				 *
				 * Get item discount by dividing item cost by subtotal to get a %
				 */
				$discount_percent = 0;

				if ( WC()->cart->subtotal_ex_tax ) {
					$discount_percent = ( $cart_item['data']->get_price_excluding_tax() * $cart_item['quantity'] ) / WC()->cart->subtotal_ex_tax;
				}

				$discount = min( ( $coupon->amount * $discount_percent ) / $cart_item['quantity'], $discounting_amount );
			} elseif ( ( 'store_credit' === $coupon->type || $coupon->is_store_credit ) ) {
				$discount = min( $coupon->amount, $discounting_amount );
			}
			return $discount;
		}

		/**
		 * Change label in cart
		 * @param  string $label
		 * @param  WC_Coupon $coupon
		 * @return string
		 */
		public function cart_totals_coupon_label( $label, $coupon ) {
			if ( ( 'store_credit' === $coupon->type || $coupon->is_store_credit ) ) {
				$label = __( 'Store credit:', 'woocommerce-store-credit' );
			}
			return $label;
		}

		/**
		 * 2.1 workaround
		 */
		public function woocommerce_coupon_loaded( $coupon ) {
			if ( 'store_credit' === $coupon->type ) {
				if ( version_compare( WC_VERSION, '2.2', '<' ) ) {
					$coupon->type            = 'fixed_cart';
				}
				$coupon->is_store_credit = true;
			}
		}
	}

	new WC_Store_Credit_Plus();
}
