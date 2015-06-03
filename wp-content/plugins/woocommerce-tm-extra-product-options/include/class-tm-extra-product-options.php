<?php
// Direct access security
if ( !defined( 'TM_EPO_PLUGIN_SECURITY' ) ) {
	die();
}

/**
 * TM_Extra_Product_Options Class
 *
 * This class is responsible for displaying the Extra Product Options on the frontend.
 */

final class TM_Extra_Product_Options {

	var $version        = TM_EPO_VERSION;
	var $_namespace     = 'tm-extra-product-options';
	var $plugin_path;
	var $template_path;
	var $plugin_url;
	var $postid_pre=false;

	/* Product custom settings */
	var $tm_meta_cpf = array();
	/* Product custom settings options */
	var $meta_fields = array(
			'exclude' 					=> '',
			'override_display' 			=> '',
			'override_final_total_box' 	=> ''
		);

	/* Cache for all the extra options */
	var $cpf=false;

	private $upload_dir="/extra_product_options/";

	/* Replacement name for Subscription fee fields */
	var $fee_name="tmfee_";
	var $fee_name_class="tmcp-sub-fee-field";

	/* Replacement name for cart fee fields */
	var $cart_fee_name="tmcartfee_";
	var $cart_fee_class="tmcp-fee-field";

	/* Holds the total fee added by Subscription fee fields */
	public $tmfee=0;

	/* Array of element types that get posted */
	var $element_post_types=array();

	/* Holds builder element attributes */
	private $tm_original_builder_elements=array();

	/* Holds modified builder element attributes */
	public $tm_builder_elements=array();

	/* Inline styles */
	public $inline_styles;

	/* edit option in cart helper */
	var $new_add_to_cart_key = false;

	/* Plugin settings */
	var $tm_plugin_settings=array(
			"tm_epo_roles_enabled" 						=> "@everyone",
			"tm_epo_final_total_box" 					=> "normal",
			"tm_epo_enable_final_total_box_all" 		=> "no",
			"tm_epo_strip_html_from_emails" 			=> "yes",
			"tm_epo_no_lazy_load" 						=> "yes",
			"tm_epo_enable_shortcodes" 					=> "no",

			"tm_epo_display" 							=> "normal",
			"tm_epo_options_placement" 					=> "woocommerce_before_add_to_cart_button",
			"tm_epo_options_placement_custom_hook" 		=> "",
			"tm_epo_totals_box_placement" 				=> "woocommerce_before_add_to_cart_button",
			"tm_epo_totals_box_placement_custom_hook" 	=> "",
			"tm_epo_floating_totals_box" 				=> "disable",
			"tm_epo_force_select_options" 				=> "normal",
			"tm_epo_remove_free_price_label" 			=> "no",
			"tm_epo_hide_upload_file_path" 				=> "yes",
			"tm_epo_show_only_active_quantities"		=> "",

			"tm_epo_clear_cart_button" 					=> "normal",
			"tm_epo_cart_field_display" 				=> "normal",
			"tm_epo_hide_options_in_cart" 				=> "normal",
			"tm_epo_hide_options_prices_in_cart" 		=> "normal",
			"tm_epo_no_negative_priced_products" 		=> "no",

			"tm_epo_final_total_text" 					=> "",
			"tm_epo_options_total_text" 				=> "",
			"tm_epo_subscription_fee_text" 				=> "",
			"tm_epo_replacement_free_price_text" 		=> "",
			"tm_epo_reset_variation_text" 				=> "",

			"tm_epo_css_styles" 						=> "",
			"tm_epo_css_styles_style" 					=> "round",
			"tm_epo_css_selected_border" 				=> "",

			"tm_epo_dpd_enable" 						=> "no",
		);

	/* Prevents options duplication for bad coded themes */
	var $tm_options_have_been_displayed=false;
	var $tm_options_single_have_been_displayed=false;
	var $tm_options_totals_have_been_displayed=false;

	protected static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function init(){
        return;
    }

	public function __construct() {
		$this->plugin_path      						= TM_PLUGIN_PATH;
		$this->template_path    						= TM_TEMPLATE_PATH;
		$this->plugin_url       						= TM_PLUGIN_URL;
		$this->inline_styles 							= '';
		$this->is_bto 									= false;
		$this->noactiondisplay 							= false;
		
		$this->get_plugin_settings();
		$this->get_override_settings();
		$this->add_plugin_actions();
		$this->add_compatibility_actions();

		add_action( 'plugins_loaded', array($this,'tm_epo_add_elements') );  
	}

	public final function tm_epo_add_elements(){

		do_action('tm_epo_register_addons');

		$this->tm_original_builder_elements=TM_EPO_BUILDER()->get_elements();

		foreach ($this->tm_original_builder_elements as $key => $value) {
			
			if ($value["is_post"]=="post"){
				$this->element_post_types[] = $value["post_name_prefix"];
			}

			if ($value["is_post"]=="post" || $value["is_post"]=="display"){
				$this->tm_builder_elements[$value["post_name_prefix"]] = $value;
			}
			
		}

	}

	/* Gets all of the plugin settings */
	public function get_plugin_settings(){

		foreach ($this->tm_plugin_settings as $key => $value) {
			$this->$key = get_option( $key );
			if (!$this->$key){
				$this->$key = $value;
			}
		}

		if ($this->tm_epo_options_placement=="custom"){
			$this->tm_epo_options_placement = $this->tm_epo_options_placement_custom_hook;
		}
		
		if ($this->tm_epo_totals_box_placement=="custom"){
			$this->tm_epo_totals_box_placement = $this->tm_epo_totals_box_placement_custom_hook;
		}
		
	}

	/* Gets custom settings for the current product */
	public function get_override_settings(){
		foreach ( $this->meta_fields as $key=>$value ) {
			$this->tm_meta_cpf[$key] = $value;
		}
	}

	public function add_plugin_actions(){

		/**
		 * Initialize settings
		 */
		if ( $this->is_enabled_shortcodes() && !$this->is_quick_view() ){
			add_action( 'init', array( $this, 'init_settings_pre' ) );
		}else{
			if ( $this->is_quick_view() ){
				add_action( 'init', array( $this, 'init_settings' ) );
			}else{
				add_action( 'wp', array( $this, 'init_settings' ) );	
			}					
		}

		/**
		 * Load js,css files
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'woocommerce_tm_custom_price_fields_enqueue_scripts', array( $this, 'custom_frontend_scripts' ) );
		add_action( 'woocommerce_tm_epo_enqueue_scripts', array( $this, 'custom_frontend_scripts' ) );

		/**
		 * Display in frontend
		 */
		add_action( 'woocommerce_tm_epo', array( $this, 'frontend_display' ) );
		add_action( 'woocommerce_tm_epo_fields', array( $this, 'tm_epo_fields' ) );
		add_action( 'woocommerce_tm_epo_totals', array( $this, 'tm_epo_totals' ) );

		/* compatibility for older plugin versions */
		add_action( 'woocommerce_tm_custom_price_fields', array( $this, 'frontend_display' ) );
		add_action( 'woocommerce_tm_custom_price_fields_only', array( $this, 'tm_epo_fields' ) );
		add_action( 'woocommerce_tm_custom_price_fields_totals', array( $this, 'tm_epo_totals' ) );

		/**
		 * Cart manipulation
		 */
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 50, 1 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 50, 2 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 50, 2 );
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 50, 2 );
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 50, 2 );
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 50, 6 );
		add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'order_again_cart_item_data' ), 50, 3 );

		add_filter( 'woocommerce_cart_item_name', array( $this, 'tm_woocommerce_cart_item_name' ), 50, 3 );

		add_filter( 'wp_footer', array( $this, 'tm_add_inline_style' ), 99999 );

		/**
		 * Empty cart button
		 */
		if ($this->tm_epo_clear_cart_button=="show"){
			add_action( 'woocommerce_cart_actions', array( $this, 'add_empty_cart_button' ) );
			// check for empty-cart get param to clear the cart
			add_action( 'init', array( $this, 'clear_cart' ) );
		}

		/**
		 * Force Select Options
		 */
		add_filter( 'woocommerce_add_to_cart_url', array( $this, 'add_to_cart_url' ), 50, 1 );
		add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_to_cart_url' ), 50, 1 );
		add_action( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text' ), 10, 1 );		

		/* enable shortcodes for labels */	
		add_filter('woocommerce_tm_epo_option_name', array( $this, 'tm_epo_option_name' ), 10, 1);
		
		/* For hiding uploaded file path */
		add_filter('woocommerce_order_item_display_meta_value', array( $this, 'tm_order_item_display_meta_value' ), 10, 1);

		/* Support for fee price types */
		add_action( 'woocommerce_cart_calculate_fees', array( $this,'tm_calculate_cart_fee' ) );

		/* Cart advanced template system */		 
		if(apply_filters( 'tm_get_template',true)){
			add_filter( 'wc_get_template', array( $this,'tm_wc_get_template'), 10, 5 );
		}
		add_filter( 'woocommerce_order_get_items', array( $this,'tm_woocommerce_order_get_items'), 10, 2 );
		 
		add_action( 'tm_woocommerce_cart_after_row', array( $this,'tm_woocommerce_cart_after_row' ),10,4 );
		add_action( 'tm_woocommerce_checkout_after_row', array( $this,'tm_woocommerce_checkout_after_row' ),10,4 );

		/* Display fields on admin Order */
		add_action( 'woocommerce_order_item_' . 'line_item' . '_html', array( $this,'tm_woocommerce_order_item_line_item_html'), 10, 2);

		/* Edit cart item */
		add_action( 'woocommerce_add_to_cart', array( $this,'tm_woocommerce_add_to_cart'), 10, 6);		
		add_filter( 'woocommerce_add_to_cart_redirect', array( $this,'tm_woocommerce_add_to_cart_redirect'), 9999, 1 );
		add_filter( 'woocommerce_quantity_input_args', array( $this,'tm_woocommerce_quantity_input_args'), 9999, 2 );

		/* Add custom class to product div */
		add_filter( 'post_class', array( $this,'tm_post_class') );
	}

	public function tm_post_class($classes ){
		global $post;
		if ( $post && $post->post_type == 'product' ) {
			$has_options = get_post_meta($post->ID, 'tm_meta', true); 
			if ( $has_options ) {
				$classes[] = 'tm-has-options';
			}
		}
		return $classes;
	}

	public function tm_woocommerce_order_get_items($items, $order){
		foreach ( $items as $item_id => $item ){
			$has_epo = isset($item['item_meta']) && isset($item['item_meta']['_tmcartepo_data']) && isset($item['item_meta']['_tmcartepo_data'][0]);
			if ($has_epo){
				$epos = maybe_unserialize($item['item_meta']['_tmcartepo_data'][0]);

				$current_product_id=$item['product_id'];
				$original_product_id = floatval(TM_EPO_WPML()->get_original_id( $current_product_id,'product' ));
				if (TM_EPO_WPML()->get_lang()==TM_EPO_WPML()->get_default_lang() && $original_product_id!=$current_product_id){
					$current_product_id = $original_product_id;
				}
				$wpml_translation_by_id=$this->get_wpml_translation_by_id( $current_product_id );
				foreach ($epos as $key => $epo) {
					if ($epo){
						if(!isset($epo['quantity'])){
							$epo['quantity'] = 1;
						}
						if ($epo['quantity']<1){
							$epo['quantity'] = 1;
						}
						if(isset($wpml_translation_by_id[$epo['section']])){
							$epo['name'] = $wpml_translation_by_id[$epo['section']];
						}
						if(!empty($epo['multiple']) && !empty($epo['key'])){
							$pos = strrpos($epo['key'], '_');
							if($pos!==false) {
								$av=array_values( $wpml_translation_by_id["options_".$epo['section']] );
								if (isset($av[substr($epo['key'], $pos+1)])){
									$epo['value'] = $av[substr($epo['key'], $pos+1)];
								}
							}
						}
						$epo['value'] = $this->tm_order_item_display_meta_value($epo['value']);
						$epovalue = '';
						if ($this->tm_epo_hide_options_prices_in_cart=="normal" && !empty($epo['price'])){
							$epovalue .= ' '.((!empty($item['item_meta']['tm_has_dpd']))?'':(wc_price(  (float) $epo['price']/(float) $epo['quantity']  )));
						}
						if ($epo['quantity']>1){
							$epovalue .= ' x '. $epo['quantity'];	
						}
						if ($epovalue!==''){
							$epo['value'] .= '<small>'.$epovalue.'</small>';
						}
						$epo['value'] = make_clickable($epo['value']);
						if ($this->tm_epo_strip_html_from_emails=="yes"){
							$epo['value']=strip_tags($epo['value']);
						}
						$items[$item_id]['item_meta'][$epo['name']][] = make_clickable($epo['value']);
					}
				}
			}
		}
		return $items;
	}

	public function add_compatibility_actions() {

		/* WPML support */
		if (TM_EPO_WPML()->is_active()){
			add_filter( 'tm_cart_contents', array( $this,'tm_cart_contents'), 10, 2 );
		}

		/* Subscriptions support */
		add_filter('woocommerce_subscriptions_product_sign_up_fee', array( $this, 'tm_subscriptions_product_sign_up_fee' ), 10, 2);

		/* WooCommerce Currency Switcher support */
		add_filter('woocommerce_tm_epo_price', array( $this, 'tm_epo_price' ), 10, 2);
		add_filter('woocommerce_tm_epo_price2', array( $this, 'tm_epo_price2' ), 10, 2);
		add_filter('woocommerce_tm_epo_price2_remove', array( $this, 'tm_epo_price2_remove' ), 10, 2);	

		/* Composite Products support */
		add_action( 'woocommerce_composite_product_add_to_cart', array( $this, 'tm_bto_display_support' ), 11, 2 );
		add_filter( 'woocommerce_composite_button_behaviour', array( &$this, 'tm_woocommerce_composite_button_behaviour' ), 50, 2 );

        /* WooCommerce Dynamic Pricing & Discounts support */
		add_filter( 'woocommerce_cart_item_price', array($this, 'cart_item_price'), 101, 3 );	
		add_filter( 'woocommerce_cart_item_subtotal', array($this, 'tm_woocommerce_cart_item_subtotal'), 101, 3 );	
        if ($this->tm_epo_dpd_enable=="no"){
			add_action( 'woocommerce_cart_loaded_from_session', array($this, 'cart_loaded_from_session_2'), 2 );
			add_action( 'woocommerce_cart_loaded_from_session', array($this, 'cart_loaded_from_session_99999'), 99999 );
		}
		add_action( 'woocommerce_cart_loaded_from_session', array($this, 'cart_loaded_from_session_1'), 1 );

	}

	public function tm_woocommerce_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ){
		if(isset($_GET['tm_cart_item_key'])){
			$this->new_add_to_cart_key = $cart_item_key;
		}
	}

	/* change quantity value when editing a cart item */
	public function tm_woocommerce_quantity_input_args($args, $product){
		if(isset($_GET['tm_cart_item_key'])){
			$cart_item_key = $_GET['tm_cart_item_key'];
			$cart_item = WC()->cart->get_cart_item( $cart_item_key );

			if (isset($cart_item["quantity"])){
				$args["input_value"] = $cart_item["quantity"];
			}
		}
		
		return $args;
	}

	/* redirect to cart when updating information for a cart item */
	public function tm_woocommerce_add_to_cart_redirect($url){
		if(isset($_GET['tm_cart_item_key'])){
			$cart_item_key = $_GET['tm_cart_item_key'];
			//$cart_item     = WC()->cart->get_cart_item( $cart_item_key );
			//$product       = wc_get_product( $cart_item['product_id'] );
			if (isset($this->new_add_to_cart_key)){
				if ($this->new_add_to_cart_key == $cart_item_key){
					WC()->cart->set_quantity( $this->new_add_to_cart_key, $_POST['quantity'], true );
				}else{
					WC()->cart->remove_cart_item( $cart_item_key );
					unset(WC()->cart->removed_cart_contents[ $cart_item_key ]);
				}
			}else{

			}
			
			$url = WC()->cart->get_cart_url();
		}
		return $url;
	}

	/* returns translated options values */
	public function get_wpml_translation_by_id($current_product_id=0){
		$this_land_epos=$this->get_product_tm_epos( $current_product_id );
		$wpml_translation_by_id=array();
		if (isset($this_land_epos['global'])){
			foreach ( $this_land_epos['global'] as $priority=>$priorities ) {
				foreach ( $priorities as $pid=>$field ) {
					if (isset($field['sections']) && is_array($field['sections'])){
						foreach ( $field['sections'] as $section_id=>$section ) {
							foreach ( $section['elements'] as $element ) {
								$wpml_translation_by_id[$element['uniqid']]=$element['label'];
								$wpml_translation_by_id["options_".$element['uniqid']]=$element['options'];
							}
						}
					}
				}
			}
		}
		return $wpml_translation_by_id;
	}

	public function tm_woocommerce_order_item_line_item_html($item_id, $item){
		
		$html = '';

		$has_epo = isset($item['item_meta']) 
				&& isset($item['item_meta']['_tmcartepo_data']) 
				&& isset($item['item_meta']['_tmcartepo_data'][0])
				&& isset($item['item_meta']['_tm_epo']);
		
		$has_fee = isset($item['item_meta']) 
				&& isset($item['item_meta']['_tmcartfee_data']) 
				&& isset($item['item_meta']['_tmcartfee_data'][0]);

		if ($has_epo || $has_fee){
			$current_product_id=$item['product_id'];
			$original_product_id = floatval(TM_EPO_WPML()->get_original_id( $current_product_id,'product' ));
			if (TM_EPO_WPML()->get_lang()==TM_EPO_WPML()->get_default_lang() && $original_product_id!=$current_product_id){
				$current_product_id = $original_product_id;
			}
			$wpml_translation_by_id=$this->get_wpml_translation_by_id( $current_product_id );
		}

		if ($has_epo){
			$epos = maybe_unserialize($item['item_meta']['_tmcartepo_data'][0]);

			if ($epos && is_array($epos)){
				$html .= '<tr class="tm-order-line item '.apply_filters( 'woocommerce_admin_html_order_item_class', ( ! empty( $class ) ? $class : '' ), $item ) .'">'
						.'<td class="check-column">&nbsp;</td>'
						.'<td class="thumb">&nbsp;</td>';
				$html .= '<td class="tm-c name"><div class="view tm-order-header">'.__('Extra Product Options',TM_EPO_TRANSLATION).'</div><div class="view tm-header"><div class="tm-50">'.__('Option',TM_EPO_TRANSLATION).'</div><div class="tm-50">'.__('Value',TM_EPO_TRANSLATION).'</div></div></td>';
				if(empty($item['item_meta']['tm_has_dpd'])){
					$html .= '<td class="tm-c item_cost" width="1%"><div class="view"><div class="view tm-order-header">&nbsp;</div>'.__('Price',TM_EPO_TRANSLATION).'</div></td>';
					$html .= '<td class="tm-c tm_quantity" width="1%"><div class="view"><div class="view tm-order-header">&nbsp;</div>'.__('Qty',TM_EPO_TRANSLATION).'</div></td>';
					$html .= '<td class="tm-c line_cost" width="1%"><div class="view"><div class="view tm-order-header">&nbsp;</div>'.__('Total',TM_EPO_TRANSLATION).'</div></td>';
				}else{
					$html .= '<td class="tm-c item_cost" width="1%"><div class="view"><div class="view tm-order-header">&nbsp;</div>&nbsp;</div></td>';
					$html .= '<td class="tm-c tm_quantity" width="1%"><div class="view"><div class="view tm-order-header">&nbsp;</div>'.__('Qty',TM_EPO_TRANSLATION).'</div></td>';
					$html .= '<td class="tm-c line_cost" width="1%"><div class="view"><div class="view tm-order-header">&nbsp;</div>&nbsp;</div></td>';
				}
				$html .= '<td class="tm-c wc-order-edit-line-item">&nbsp;</td>';
			
				foreach ($epos as $key => $epo) {
					if ($epo){
						if(!isset($epo['quantity'])){
							$epo['quantity'] = 1;
						}
						if(isset($wpml_translation_by_id[$epo['section']])){
							$epo['name'] = $wpml_translation_by_id[$epo['section']];
						}
						if(!empty($epo['multiple']) && !empty($epo['key'])){
							$pos = strrpos($epo['key'], '_');
							if($pos!==false) {
								$av=array_values( $wpml_translation_by_id["options_".$epo['section']] );
								if (isset($av[substr($epo['key'], $pos+1)])){
									$epo['value'] = $av[substr($epo['key'], $pos+1)];
									if (!empty($epo['use_images']) && !empty($epo['images']) && $epo['use_images']=="images"){
										$epo['value'] ='<div class="cpf-img-on-cart"><img alt="" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="'.$epo['images'].'" /></div>'.$epo['value'];
									}
								}
							}
						}
						$html .= '<tr class="tm-order-line-option item">';
						$html .= '<td class="check-column">&nbsp;</td>';
						$html .= '<td class="thumb">&nbsp;</td>';
						$html .= '<td class="tm-c name"><div class="view"><div class="tm-50">'.$epo['name'].'</div><div class="tm-50">'.$epo['value'].'</div></div></td>';
						if(empty($item['item_meta']['tm_has_dpd'])){
							$html .= '<td class="tm-c item_cost" width="1%"><div class="view">'.wc_price( $epo['price']/$epo['quantity'] ).'</div></td>';
							$html .= '<td class="tm-c tm_quantity" width="1%"><div class="view">'.( $epo['quantity'] * (float) $item['item_meta']['_qty'][0] ).'</div></td>';
							$html .= '<td class="tm-c line_cost" width="1%"><div class="view"><span class="amount">'.wc_price(  (float) $epo['price'] * (float) $item['item_meta']['_qty'][0] ).'</span></div></td>';
						}else{
							$html .= '<td class="tm-c item_cost" width="1%"><div class="view">&nbsp;</div></td>';
							$html .= '<td class="tm-c tm_quantity" width="1%"><div class="view">'.( $epo['quantity'] * (float) $item['item_meta']['_qty'][0] ).'</div></td>';
							$html .= '<td class="tm-c line_cost" width="1%"><div class="view">&nbsp;</div></td>';
						}
						$html .= '<td class="tm-c wc-order-edit-line-item">&nbsp;</td>';
						$html .= '</tr>';
					}
				}
			}
		}

		if ($has_fee){
			$epos = maybe_unserialize($item['item_meta']['_tmcartfee_data'][0]);
			if (isset($epos[0])){
				$epos = $epos[0];				
			}else{
				$epos = false;
			}


			if ($epos && is_array($epos) && !empty($epos[0])){
				$html .= '<tr class="tm-order-line item '.apply_filters( 'woocommerce_admin_html_order_item_class', ( ! empty( $class ) ? $class : '' ), $item ) .'">'
						.'<td class="check-column">&nbsp;</td>'
						.'<td class="thumb">&nbsp;</td>';
				$html .= '<td class="tm-c name"><div class="view tm-order-header">'.__('Extra Product Options Fees',TM_EPO_TRANSLATION).'</div><div class="view tm-header"><div class="tm-50">'.__('Option',TM_EPO_TRANSLATION).'</div><div class="tm-50">'.__('Value',TM_EPO_TRANSLATION).'</div></div></td>';
				$html .= '<td class="tm-c tm_quantity" width="1%"><div class="view"><div class="view tm-order-header">&nbsp;</div>'.__('Qty',TM_EPO_TRANSLATION).'</div></td>';

				$html .= '<td class="tm-c wc-order-edit-line-item">&nbsp;</td>';
				
				foreach ($epos as $key => $epo) {
					if ($epo){
						if(!isset($epo['quantity'])){
							$epo['quantity'] = 1;
						}
						if(isset($wpml_translation_by_id[$epo['section']])){
							$epo['name'] = $wpml_translation_by_id[$epo['section']];
						}
						if(!empty($epo['multiple']) && !empty($epo['key'])){
							$pos = strrpos($epo['key'], '_');
							if($pos!==false) {
								$av=array_values( $wpml_translation_by_id["options_".$epo['section']] );
								if (isset($av[substr($epo['key'], $pos+1)])){
									$epo['value'] = $av[substr($epo['key'], $pos+1)];
									if (!empty($epo['use_images']) && !empty($epo['images']) && $epo['use_images']=="images"){
										$epo['value'] ='<div class="cpf-img-on-cart"><img alt="" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="'.$epo['images'].'" /></div>'.$epo['value'];
									}
								}
							}
						}
						$html .= '<tr class="tm-order-line-option item">';
						$html .= '<td class="check-column">&nbsp;</td>';
						$html .= '<td class="thumb">&nbsp;</td>';
						$html .= '<td class="tm-c name"><div class="view"><div class="tm-50">'.$epo['name'].'</div><div class="tm-50">'.$epo['value'].'</div></div></td>';
						$html .= '<td class="tm-c tm_quantity" width="1%"><div class="view">'.( $epo['quantity'] * (float) $item['item_meta']['_qty'][0] ).'</div></td>';
						$html .= '<td class="tm-c wc-order-edit-line-item">&nbsp;</td>';
						$html .= '</tr>';
					}
				}
			}
		}

		echo $html;
	}

	public function tm_cart_contents($cart, $values ){
		if (!TM_EPO_WPML()->is_active()){
			return $cart;
		}

		if (isset($cart['tmcartepo']) && is_array($cart['tmcartepo'])){
			$current_product_id=$cart["product_id"];
			$wpml_translation_by_id=$this->get_wpml_translation_by_id( $current_product_id );

			foreach ($cart['tmcartepo'] as $k => $epo) {
				if(isset($epo['mode']) && $epo['mode']=='local'){
					if(isset($epo['is_taxonomy'])){
						if($epo['is_taxonomy']=="1"){							
							$term=get_term_by("name",$epo["key"],$epo['section']);							
							$wpml_term_id = icl_object_id($term->term_id,  $epo['section'], false);							
							if ($wpml_term_id){
								$wpml_term = get_term( $wpml_term_id, $epo['section'] );
							}else{
								$wpml_term = $term;
							}
							$cart['tmcartepo'][$k]['section_label'] = esc_html( urldecode( wc_attribute_label($epo['section']) ) );
							$cart['tmcartepo'][$k]['value'] = esc_html( wc_attribute_label($wpml_term->name) );
						}elseif($epo['is_taxonomy']=="0"){
							
							$attributes = maybe_unserialize( get_post_meta( floatval(TM_EPO_WPML()->get_original_id( $cart['product_id'] )), '_product_attributes', true ) );
							$wpml_attributes = maybe_unserialize( get_post_meta( $cart['product_id'], '_product_attributes', true ) );
							
							$options = array_map( 'trim', explode( WC_DELIMITER, $attributes[$epo['section']]['value'] ) );
							$wpml_options = array_map( 'trim', explode( WC_DELIMITER, $wpml_attributes[$epo['section']]['value'] ) );

							$cart['tmcartepo'][$k]['section_label'] = esc_html( urldecode( wc_attribute_label($epo['section']) ) );
							$cart['tmcartepo'][$k]['value'] = 
							esc_html( 
								wc_attribute_label(
									isset(
										$wpml_options[array_search($epo['key'], $options)]
									)
									?$wpml_options[array_search($epo['key'], $options)]
									:$epo['key'] 
								) 
							);
						}
					}
				}elseif(isset($epo['mode']) && $epo['mode']=='builder'){
					if(isset($wpml_translation_by_id[$epo['section']])){
						$cart['tmcartepo'][$k]['section_label'] = $wpml_translation_by_id[$epo['section']];
						if(!empty($epo['multiple']) && !empty($epo['key'])){
							$pos = strrpos($epo['key'], '_');
							if($pos!==false) {
								$av=array_values( $wpml_translation_by_id["options_".$epo['section']] );
								if (isset($av[substr($epo['key'], $pos+1)])){
									$cart['tmcartepo'][$k]['value'] = $av[substr($epo['key'], $pos+1)];
								}
							}
						}
					}
				}
			}
		}
		return $cart;
	}

	public function tm_woocommerce_cart_item_name($title, $cart_item, $cart_item_key ){
		if (!is_cart() || !isset($cart_item['tmhasepo']) || isset($cart_item['composite_item']) || isset($cart_item['composite_data']) ){
			return $title;
		}
		$product=$cart_item['data'];
		$link=$product->get_permalink( $cart_item );
		$link = add_query_arg( 
			array(
				'tm_cart_item_key' => $cart_item_key,
				)
			, $link );
		$link=wp_nonce_url($link,'tm-edit');
		$title .='<a href="'.$link.'" class="tm-cart-edit-options">'.__('Edit options',TM_EPO_TRANSLATION).'</a>';
		return $title;
	}

	public function tm_woocommerce_checkout_after_row($cart_item_key, $cart_item, $_product, $product_id){
		$out = '';
		$other_data = array();
		if ($this->tm_epo_hide_options_in_cart=="normal"){
			$other_data = $this->get_item_data_array( array(), $cart_item );
		}
		$odd=1;
		foreach ($other_data as $key => $value) {
			$zebra_class="odd ";
			if (!$odd){
				$zebra_class="even ";
				$odd=2;
			}
			$out .= '<tr class="tm-epo-cart-row '.$zebra_class.esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ).'">';
			
			$name = '<div class="tm-epo-cart-option-value tm-epo-cart-no-label">'.$value['tm_value'].' <strong class="product-quantity">' . sprintf( '&times; %s', $value['tm_quantity']*$cart_item['quantity'] ) . '</strong>'.'</div>';
			if (!empty($value['tm_label'])){
				$name = '<div class="tm-epo-cart-option-label">'.$value['tm_label'].' <strong class="product-quantity">' . sprintf( '&times; %s', $value['tm_quantity']*$cart_item['quantity'] ) . '</strong>'.'</div>'.'<div class="tm-epo-cart-option-value">'.$value['tm_value'].'</div>';
			}
			$out .= '<td class="product-name">'.$name.'</td>';			
			
			$out .= '<td class="product-subtotal">'.$value['tm_total_price'].'</td>';
			$out .= '</tr>';
			$odd--;
		}
		
		echo $out;
	}

	public function tm_woocommerce_cart_after_row($cart_item_key, $cart_item, $_product, $product_id){
		$out = '';
		$other_data = array();
		if ($this->tm_epo_hide_options_in_cart=="normal"){
			$other_data = $this->get_item_data_array( array(), $cart_item );
		}
		$odd=1;
		foreach ($other_data as $key => $value) {
			$zebra_class="odd ";
			if (!$odd){
				$zebra_class="even ";
				$odd=2;
			}
			$out .= '<tr class="tm-epo-cart-row '.$zebra_class.esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ).'">';
			$out .= '<td class="product-remove">&nbsp;</td>';
			$thumbnail='&nbsp;';
			/*if (!empty($value['tm_image'])){
				$size = 'shop_thumbnail';
				$dimensions = wc_get_image_size( $size );

				$thumbnail = apply_filters('woocommerce_placeholder_img', '<img src="' . $value['tm_image'] . '" alt="' . __( 'Placeholder', 'woocommerce' ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="attachment-shop_thumbnail wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />', $size, $dimensions );

				$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $thumbnail, $cart_item, $cart_item_key );
				if ( ! $_product->is_visible() ){
					
				}else{
					$thumbnail = sprintf( '<a href="%s">%s</a>', $_product->get_permalink( $cart_item ), $thumbnail );
				}
			}*/
			$out .= '<td class="product-thumbnail">'.$thumbnail.'</td>';
			$name = '<div class="tm-epo-cart-option-value tm-epo-cart-no-label">'.$value['tm_value'].'</div>';
			if (!empty($value['tm_label'])){
				$name = '<div class="tm-epo-cart-option-label">'.$value['tm_label'].'</div>'.'<div class="tm-epo-cart-option-value">'.$value['tm_value'].'</div>';
			}
			$out .= '<td class="product-name">'.$name.'</td>';
			$out .= '<td class="product-price">'.$value['tm_price'].'</td>';
			$out .= '<td class="product-quantity">'.$value['tm_quantity']*$cart_item['quantity'].'</td>';
			$out .= '<td class="product-subtotal">'.$value['tm_total_price'].'</td>';
			$out .= '</tr>';
			$odd--;
		}
		if (is_array($other_data) && count($other_data)>0){
			$out .= '<tr class="tm-epo-cart-row tm-epo-cart-row-total '.esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ).'">';
			$out .= '<td class="product-remove">&nbsp;</td>';
			$out .= '<td class="product-thumbnail">&nbsp;</td>';
			$out .= '<td class="product-name">&nbsp;</td>';
			$out .= '<td class="product-price">&nbsp;</td>';
			if ( $_product->is_sold_individually() ) {
				$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
			} else {
				$product_quantity = woocommerce_quantity_input( array(
					'input_name'  => "cart[{$cart_item_key}][qty]",
					'input_value' => $cart_item['quantity'],
					'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
					'min_value'   => '0'
				), $_product, false );
			}			
			$out .= '<td class="product-quantity">'.apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key ).'</td>';
			$out .= '<td class="product-subtotal">'.apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ).'</td>';
			$out .= '</tr>';			
		}
		
		echo $out;
	}

	public function tm_wc_get_template($located, $template_name, $args, $template_path, $default_path) {

		$templates = array();
		if ($this->tm_epo_cart_field_display=="advanced"){
			$templates = array_merge($templates,array('cart/cart.php','checkout/review-order.php')); 
		}
		if (in_array($template_name, $templates)){
			$_located = wc_locate_template( $template_name, $this->template_path, $this->template_path );
			if ( file_exists( $_located ) ) {
				$located = $_located;
			}
		}
		 		 
		return $located;
	}

	// WooCommerce Dynamic Pricing & Discounts
	public function cart_loaded_from_session_1(){
		global $woocommerce;

		$cart_contents = $woocommerce->cart->cart_contents;

		foreach ($cart_contents as $cart_item_key => $cart_item) {
			$woocommerce->cart->cart_contents[$cart_item_key]['tm_cart_item_key'] = $cart_item_key;
		}

	}

	// WooCommerce Dynamic Pricing & Discounts
	public function cart_loaded_from_session_2(){
		if(!class_exists('RP_WCDPD')){
			return;
		}
		global $woocommerce;

		$cart_contents = $woocommerce->cart->cart_contents;

		foreach ($cart_contents as $cart_item_key => $cart_item) {
			if (isset($cart_item['tm_epo_product_original_price'])){
				$woocommerce->cart->cart_contents[$cart_item_key]['data']->price = $cart_item['tm_epo_product_original_price'];
				$woocommerce->cart->cart_contents[$cart_item_key]['tm_epo_doing_adjustment'] = true;
			}
		}

	}

	// WooCommerce Dynamic Pricing & Discounts
	public function cart_loaded_from_session_99999(){
		if(!class_exists('RP_WCDPD')){
			return;
		}
		global $woocommerce;

		$cart_contents = $woocommerce->cart->cart_contents;

		foreach ($cart_contents as $cart_item_key => $cart_item) {
			$current_product_price=$woocommerce->cart->cart_contents[$cart_item_key]['data']->price;

			if (isset($cart_item['tm_epo_options_prices']) && !empty($cart_item['tm_epo_doing_adjustment'])){
				$woocommerce->cart->cart_contents[$cart_item_key]['tm_epo_product_after_adjustment']=$current_product_price;
				$woocommerce->cart->cart_contents[$cart_item_key]['data']->adjust_price($cart_item['tm_epo_options_prices']);
				unset($woocommerce->cart->cart_contents[$cart_item_key]['tm_epo_doing_adjustment']);
			}
		}

	}

	public function tm_woocommerce_cart_item_subtotal($price, $cart_item, $cart_item_key){
		return $price;
	}

	/**
	 * Replace cart html prices for WooCommerce Dynamic Pricing & Discounts
	 * 
	 * @access public
	 * @param string $item_price
	 * @param array $cart_item
	 * @param string $cart_item_key
	 * @return string
	 */
	 public function cart_item_price($item_price, $cart_item, $cart_item_key){
		if(!class_exists('RP_WCDPD')){
			return $item_price;
		}
		if (!isset($cart_item['tmcartepo'])) {
			return $item_price;
		}
		if (!isset($cart_item['rp_wcdpd'])) {
			return $item_price;
		}

        // Get price to display
		$price = $this->get_price_for_cart(false,$cart_item,"");

        // Format price to display
		$price_to_display = $price;
		if ($this->tm_epo_cart_field_display=="advanced"){
			$original_price_to_display = $this->get_price_for_cart($cart_item['tm_epo_product_original_price'],$cart_item,"");
			if ($this->tm_epo_dpd_enable=="yes"){
				$price=$this->get_RP_WCDPD(wc_get_product($cart_item['data']->id),$cart_item['tm_epo_product_original_price'], $cart_item_key);
				$price_to_display = $this->get_price_for_cart($price,$cart_item,"");
			}else{
				$price=$cart_item['data']->price;
				$price=$price-$cart_item['tm_epo_options_prices'];
				$price_to_display = $this->get_price_for_cart($price,$cart_item,"");
			}
		}else{
			$original_price_to_display = $this->get_price_for_cart($cart_item['tm_epo_product_price_with_options'],$cart_item,"");
		}

		$item_price = '<span class="rp_wcdpd_cart_price"><del>' . $original_price_to_display . '</del> <ins>' . $price_to_display . '</ins></span>';

		return $item_price;
	}

	public function tm_calculate_cart_fee( $cart_object ) {
		$tax=get_option('woocommerce_calc_taxes');
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		
		if ($tax=="no"){
			$tax=false;
		}else{
			if ( $tax_display_mode == 'excl' ) {
				$tax=true;
			}else{
				$tax=false;
			}
		}
	    foreach ( $cart_object->cart_contents as $key => $value ) {
	    	$tmcartfee=isset($value['tmcartfee'])?$value['tmcartfee']:false;
	    	if ($tmcartfee && is_array($tmcartfee)){
	    		foreach ( $tmcartfee as $cartfee ) {
					$new_price = $cartfee["price"];
					$new_name = $cartfee["name"];
					if (empty($new_name)){
						$new_name=__("Extra fee",TM_EPO_TRANSLATION);
					}
					$canbadded=true;
			            
					foreach ( $cart_object->fees as $fee ) {
						if ( $fee->id == sanitize_title($new_name) ) {
							$fee->amount=$fee->amount+(float) esc_attr( $new_price );
							$canbadded=false;
							break;
						}
					}
					if($canbadded){
						$cart_object->add_fee( $new_name, $new_price,$tax );	
					}
				}
			}
		}
	}

	public function check_enable(){
		$enable=false;
		$enabled_roles=$this->tm_epo_roles_enabled;
		if (!is_array($enabled_roles)){
			$enabled_roles=array($this->tm_epo_roles_enabled);
		}
		/* Check if plugin is enabled for everyone */
		foreach ($enabled_roles as $key => $value) {
			if($value=="@everyone"){
				return true;
			}
			if($value=="@loggedin" && is_user_logged_in()){
				return true;
			}
		}

		/* Get all roles */
		$current_user = wp_get_current_user();
		if ( $current_user instanceof WP_User ){		
			$roles = $current_user->roles;			
			/* Check if plugin is enabled for current user */			
			foreach ($roles as $key => $value) {
				if (in_array($value, $enabled_roles)){
					$enable=true;
					break;
				}
			}
		}

		return $enable;
	}

	public function is_quick_view(){
		$qv=false;
		$woothemes_quick_view=( isset($_GET['wc-api']) && $_GET['wc-api']=='WC_Quick_View' );
		$theme_flatsome_quick_view=( isset($_POST['action']) && ($_POST['action']=='jck_quickview') );
		if ( $woothemes_quick_view || $theme_flatsome_quick_view ){
			$qv=true;
		}
		return apply_filters( 'woocommerce_tm_quick_view',$qv);
	}

	public function is_enabled_shortcodes(){
		return ($this->tm_epo_enable_shortcodes=="yes");
	}

	/* WooCommerce Currency Switcher compatibility  */
	public function tm_epo_price($price,$type){
		if (class_exists('WOOCS')){
			global $WOOCS;
			$currencies = $WOOCS->get_currencies();
			$current_currency=$WOOCS->current_currency;
			
			$price=(double)$price* $currencies[$current_currency]['rate'];
		}
		return $price;
	}

	/* WooCommerce Currency Switcher compatibility  */
	public function tm_epo_price2($price,$type){
		if (class_exists('WOOCS') && (empty($type) || $type=="char" || $type=="step" || $type=="currentstep")){
			global $WOOCS;
			$currencies = $WOOCS->get_currencies();
			$current_currency=$WOOCS->current_currency;
			
			$price=(double)$price* $currencies[$current_currency]['rate'];
		}
		return $price;
	}

	/* WooCommerce Currency Switcher compatibility  */
	public function tm_epo_price2_remove($price,$type){
		if (class_exists('WOOCS')){
			global $WOOCS;
			$currencies = $WOOCS->get_currencies();
			$current_currency=$WOOCS->current_currency;
			if (!empty($currencies[$current_currency]['rate'])){
				$price=(double)$price/ $currencies[$current_currency]['rate'];	
			}			
		}
		return $price;
	}

	public function tm_epo_price_filtered($price,$type){
		return apply_filters( 'woocommerce_tm_epo_price2',$price,$type);
	}

	/* For hiding uploaded file path */
	public function tm_order_item_display_meta_value($value,$override=0){
		$original_value=$value;
		
		$found=(strpos($value, $this->upload_dir) !== FALSE);
		if (($found && empty($override) ) || !empty($override)){
			if($this->tm_epo_hide_upload_file_path != 'no' && filter_var($value , FILTER_VALIDATE_URL)){
				$value=basename($value);
			}
		}
		if (!empty($override)){
			$value='<a href="'.$original_value.'">'.$value.'</a>';
		}
		return $value;
	}
	
	/**
	 * Caclulates the extra subscription fee
	 */
	public function tm_subscriptions_product_sign_up_fee( $subscription_sign_up_fee, $product ) {
		global $woocommerce;
		$options_fee=0;
		if (!is_product() && $woocommerce->cart){
			$cart_contents = $woocommerce->cart->get_cart();
			foreach ($cart_contents as $cart_key => $cart_item) {
				foreach ($cart_item as $key => $data) {
					if ($key=="tmsubscriptionfee"){
						$options_fee=$data;
					}
				}
			}
			$subscription_sign_up_fee += $options_fee;
		}
		return $subscription_sign_up_fee;
	}

	/* enable shortcodes for labels */	
	public function tm_epo_option_name( $label ) {
		return do_shortcode($label);
	}

	/* Alters the Free label html */
	public function get_price_html( $price, $product ) {
		if ($product && is_object($product) && method_exists($product, "get_price") ){
			if((float)$product->get_price()>0){
				return $price;
			}else{
				return sprintf( $this->tm_epo_replacement_free_price_text, $price );
			}
		}else{
			return sprintf( $this->tm_epo_replacement_free_price_text, $price );	
		}		
	}

	public function related_get_price_html( $price, $product ) {
		if ($product && is_object($product) && method_exists($product, "get_price") ){
			if((float)$product->get_price()>0){
				return $price;
			}else{
				if ($this->tm_epo_replacement_free_price_text){
					return sprintf( $this->tm_epo_replacement_free_price_text, $price );
				}else{
					$price='';
				}
			}
		}else{
			if ($this->tm_epo_replacement_free_price_text){
				return sprintf( $this->tm_epo_replacement_free_price_text, $price );
			}else{
				$price='';
			}
		}		

		return $price;
	}

	public function get_price_html_shop( $price, $product ) {
		if ($product && 
			is_object($product) && method_exists($product, "get_price") 
			&& !(float)$product->get_price()>0 ){
			
			if ($this->tm_epo_replacement_free_price_text){
				$price=sprintf( $this->tm_epo_replacement_free_price_text, $price );
			}else{
				$price='';
			}
		}
		return $price;
	}

	public function add_to_cart_text( $text ) {
		global $product;

		if ( $this->tm_epo_force_select_options=="display" ) {
			$cpf=$this->get_product_tm_epos($product->id);
			if (is_array($cpf) && (!empty($cpf['global']) || !empty($cpf['local']))) {
				$text = __( 'Select options', TM_EPO_TRANSLATION );
			}
		}

		return $text;
	}

	public function add_to_cart_url( $url ) {
		global $product;

		if ( (is_shop() || is_product_category() || is_product_tag()) && $this->tm_epo_force_select_options=="display" ) {
			if ($this->cpf){
				$cpf=$this->cpf;
			}else{
				$cpf=$this->get_product_tm_epos($product->id);
			}
			if (is_array($cpf) && (!empty($cpf['global']) || !empty($cpf['local']))) {
				$url = get_permalink( $product->id );
			}
		}

		return $url;
	}

	public function tm_empty_cart() {
		if ( ! isset( WC()->cart ) || WC()->cart == '' ) {
			WC()->cart = new WC_Cart();
		}
		WC()->cart->empty_cart( true );
	}
	public function clear_cart() {
		if ( isset( $_POST['tm_empty_cart'] ) ) {
			$this->tm_empty_cart();
		}
	}

	public function add_empty_cart_button(){
		echo '<input type="submit" class="tm-clear-cart-button checkout-button button wc-forward" name="tm_empty_cart" value="'.__( 'Empty cart', TM_EPO_TRANSLATION ).'" />';
	}

	private function set_tm_meta($override_id=0){
		if (empty($override_id)){
			global $post;
			if (!is_null($post) && property_exists($post,'ID')){
				$override_id=$post->ID;
			}
			if (isset($_REQUEST['add-to-cart'])){
				$override_id=$_REQUEST['add-to-cart'];
			}
		}
		if (empty($override_id)){
			return;
		}
		$this->tm_meta_cpf = get_post_meta( $override_id, 'tm_meta_cpf', true );		
		
		foreach ( $this->meta_fields as $key=>$value ) {
			$this->tm_meta_cpf[$key] = isset( $this->tm_meta_cpf[$key] ) ? $this->tm_meta_cpf[$key] : $value;
		}
		$this->tm_meta_cpf['metainit']=1;
	}

	public function init_settings_pre(){
		$url = 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$postid = TM_EPO_HELPER()->get_url_to_postid($url);
		$this->postid_pre=$postid;
		$product=wc_get_product($postid);
		if (($product && is_object($product) && property_exists($product,'post') && property_exists($product->post,'post_type') && (in_array( $product->post->post_type, array( 'product', 'product_variation' ) ) )) || $postid==0){
			add_action( 'wp', array( $this, 'init_settings' ) );
		}else{
			$this->init_settings();
		}
	}

	/**
	 * Initialize custom product settings
	 */
	public function init_settings(){
		
		if (is_admin() && !$this->is_quick_view()){
			return;
		}
		
		if (class_exists('WOOCS')){
			global $WOOCS;
			remove_filter('woocommerce_order_amount_total', array( $WOOCS, 'woocommerce_order_amount_total' ), 999);
		}
		/* post_max_size debug */
		if(empty($_FILES) 
			&& empty($_POST) 
			&& isset($_SERVER['REQUEST_METHOD']) 
			&& strtolower($_SERVER['REQUEST_METHOD']) == 'post'){

        	$postMax = ini_get('post_max_size');
			wc_add_notice( sprintf( __( 'Trying to upload files larger than %s is not allowed!', TM_EPO_TRANSLATION ), $postMax ) , 'error' );
		}

		global $post,$product;
		$this->set_tm_meta();

		/* Check if the plugin is active for the user */
		if ($this->check_enable()){
			if (( $this->is_enabled_shortcodes() || is_product() || $this->is_quick_view() ) 
				&& ($this->tm_epo_display=='normal' || $this->tm_meta_cpf['override_display']=='normal') 
				&& $this->tm_meta_cpf['override_display']!='action'){
				$this->noactiondisplay=true;
				add_action( $this->tm_epo_options_placement, array( $this, 'tm_epo_fields' ), 50 );
				add_action( $this->tm_epo_totals_box_placement, array( $this, 'tm_epo_totals' ), 50 );				
			}
		}

		if ($this->tm_epo_remove_free_price_label=='yes'){
			if ($post || $this->postid_pre){
				
				if ($post){
					$this->cpf=$this->get_product_tm_epos($post->ID);
				}
				
				if (is_product() && is_array($this->cpf) && (!empty($this->cpf['global']) || !empty($this->cpf['local']))) {
					if ($product && 
						(is_object($product) && !method_exists($product, "get_price")) ||
						(!is_object($product) ) 
						){
						$product=wc_get_product($post->ID);
					}
					if ($product && 
						is_object($product) && method_exists($product, "get_price") 
						){

						if (!(float)$product->get_price()>0){
							if ($this->tm_epo_replacement_free_price_text){
								add_filter('woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2);
							}else{
								remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' ,10);					
							}							
						}
						
						add_filter('woocommerce_get_price_html', array( $this, 'related_get_price_html' ), 10, 2);
						
					}
				}else{
					if ( is_shop() || is_product_category() || is_product_tag() ){
						add_filter('woocommerce_get_price_html', array( $this, 'get_price_html_shop' ), 10, 2);	
					}elseif( !is_product() && $this->postid_pre && $this->is_enabled_shortcodes() ){
						if ($this->tm_epo_replacement_free_price_text){
							add_filter('woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2);
						}else{
							remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' ,10);					
						}
					}				
				}
			}else{
				if ($this->is_quick_view()){
					if ($this->tm_epo_replacement_free_price_text){
						add_filter('woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2);
					}else{
						add_filter('woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2);
						remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' ,10);
					}
				}
			}
		}

	}

	public function tm_woocommerce_composite_button_behaviour($type,$product){
		if ( isset($_POST) && isset($_POST['cpf_bto_price']) && isset($_POST['add-product-to-cart']) && isset($_POST['item_quantity']) ){
			 $type = 'posted';
		}
		return $type;
	}

	public function tm_bto_display_support( $product_id, $item_id ) {
		global $product;

		if (!$product){
			$product = wc_get_product( $product_id );
		}
	    if (!$product){
	        // something went wrong. wrond product id??
	        // if you get here the plugin will not work :(
	    }else{	    	
			$this->set_tm_meta($product_id);
			$this->is_bto=true;
			if (($this->tm_epo_display=='normal' || $this->tm_meta_cpf['override_display']=='normal') && $this->tm_meta_cpf['override_display']!='action'){		
				$this->frontend_display($product_id, $item_id);
			}
		}
	}

	/**
	 * Adds an item to the cart.
	 */
	public function add_cart_item( $cart_item ) {

		$cart_item['tm_epo_product_original_price']=$cart_item['data']->price;

		if ( ! empty( $cart_item['tmcartepo'] ) ) {
			$tmcp_prices = 0;
			foreach ( $cart_item['tmcartepo'] as $tmcp ) {
				$tmcp['price']=(float)wc_format_decimal($tmcp['price'],false,true);
				$tmcp_prices += $tmcp['price'];
			}

			if ($this->tm_epo_no_negative_priced_products=="yes"){
				if (apply_filters('tm_epo_cart_options_prices',$tmcp_prices)<0){
					throw new Exception( __( "You cannot add negative priced products to the cart.", TM_EPO_TRANSLATION  ) );
				}
			}

			$cart_item['tm_epo_options_prices']=apply_filters('tm_epo_cart_options_prices',$tmcp_prices);

			$cart_item['tm_epo_product_original_price']=$cart_item['data']->price;

			$cart_item['data']->adjust_price( apply_filters('tm_epo_cart_options_prices',$tmcp_prices) );

			$cart_item['tm_epo_product_price_with_options']=$cart_item['data']->price;

		}

		/**
		 * variation slug-to-name-for order again
		 */
		if ( isset( $cart_item["variation"] ) && is_array( $cart_item["variation"] ) ) {
			$_variation_name_fix=array();
			$_temp=array();
			foreach ( $cart_item["variation"] as $meta_name => $meta_value ) {
				if ( strpos( $meta_name, "attribute_" )!==0 ) {
					$_variation_name_fix["attribute_".$meta_name]=$meta_value;
					$_temp[$meta_name]=$meta_value;
				}
			}
			$cart_item["variation"]=array_diff_key( $cart_item["variation"], $_temp );
			$cart_item["variation"]=array_merge( $cart_item["variation"], $_variation_name_fix );
		}

		return $cart_item;
	}

	/**
	 * Gets the cart from session.
	 */
	public function get_cart_item_from_session( $cart_item, $values ) {
		if ( ! empty( $values['tmcartepo'] ) ) {
			$cart_item['tmcartepo'] = $values['tmcartepo'];
			$cart_item = $this->add_cart_item( $cart_item );
		}
		if ( ! empty( $values['tmcartepo_bto'] ) ) {
			$cart_item['tmcartepo_bto'] = $values['tmcartepo_bto'];
		}
		if ( ! empty( $values['tmsubscriptionfee'] ) ) {
			$cart_item['tmsubscriptionfee'] = $values['tmsubscriptionfee'];
		}
		if ( ! empty( $values['tmcartfee'] ) ) {
			$cart_item['tmcartfee'] = $values['tmcartfee'];
		}
		return apply_filters('tm_cart_contents', $cart_item, $values);
	}

	private function filtered_get_item_data_get_array_data( $tmcp ) {
		return array(
						'label' 		=> $tmcp['section_label'],
						'other_data' 	=> array( 
							array(
								'name'    	=> $tmcp['name'],
								'value'   	=> $tmcp['value'],
								'display' 	=> isset( $tmcp['display'] ) ? $tmcp['display'] : '',
								'images' 	=> isset( $tmcp['images'] ) ? $tmcp['images'] : '',
								'quantity' 	=> isset($tmcp['quantity'])?$tmcp['quantity']:1
							) ),
						'price' 		=> $tmcp['price'],
						'quantity' 		=> isset($tmcp['quantity'])?$tmcp['quantity']:1,
						'percentcurrenttotal' => isset($tmcp['percentcurrenttotal'])?$tmcp['percentcurrenttotal']:0,
						'items' => 1
					);
	}
	/**
	 * Filters our cart items.
	 */
	private function filtered_get_item_data( $cart_item ) {
		$filtered_array=array();
		if (isset($cart_item['tmcartepo'] )){
			foreach ( $cart_item['tmcartepo'] as $tmcp ) {
				if($tmcp){
					if ( !isset( $filtered_array[$tmcp['section']] ) ) {
						$filtered_array[$tmcp['section']]=$this->filtered_get_item_data_get_array_data( $tmcp );
					}else {
						if ($this->tm_epo_cart_field_display=="advanced" || $this->tm_epo_cart_field_display=="link"){
							$filtered_array[$tmcp['section']."_".uniqid('', true)]=$this->filtered_get_item_data_get_array_data( $tmcp );
						}else{						
							$filtered_array[$tmcp['section']]['items'] +=1;
							$filtered_array[$tmcp['section']]['price'] +=$tmcp['price'];
							$filtered_array[$tmcp['section']]['quantity'] += isset($tmcp['quantity'])?$tmcp['quantity']:1;
							$filtered_array[$tmcp['section']]['other_data'][] =  array(
								'name'    	=> $tmcp['name'],
								'value'   	=> $tmcp['value'],
								'display' 	=> isset( $tmcp['display'] ) ? $tmcp['display'] : '',
								'images' 	=> isset( $tmcp['images'] ) ? $tmcp['images'] : '',
								'quantity' 	=> isset( $tmcp['quantity'] )?$tmcp['quantity']:1,
							);
						}
					}
				}
			}
		}

		return $filtered_array;
	}

	public function get_price_for_cart($price=0,$cart_item,$symbol=false){
		global $woocommerce;
		$product 			= $cart_item['data'];
		$cart 				= $woocommerce->cart;
		$taxable 			= $product->is_taxable();
		$tax_display_cart 	= $cart->tax_display_cart;
		$tax_string="";

		if ($price===false){
			$price=$product->price;
		}
		// Taxable
		if ( $taxable ) {

			if ( $tax_display_cart == 'excl' ) {

				if ( $cart->tax_total > 0 && $cart->prices_include_tax ) {
					$tax_string = ' <small>' . WC()->countries->ex_tax_or_vat() . '</small>';
				}
				if (floatval($price)!=0){
					$price = $product->get_price_excluding_tax(1,$price);
				}

			} else {

				if ( $cart->tax_total > 0 && !$cart->prices_include_tax ) {
					$tax_string = ' <small>' . WC()->countries->inc_tax_or_vat() . '</small>';
				}
				if (floatval($price)!=0){
					$price = $product->get_price_including_tax(1,$price);
				}
			}

		}
		if ($symbol===false){
			if ($this->tm_epo_cart_field_display!="advanced"){
				$symbol="+";
			}
			if (floatval($price)<0){
				$symbol="-";
			}
		}
		if (floatval($price)==0){
			$symbol="";
		}else{
			$price = ' <small>' .( wc_price( abs($price) ) ). '</small>';
						
			$symbol=" $symbol" .$price.$tax_string."";

			if ($this->tm_epo_strip_html_from_emails=="yes"){
				$symbol=strip_tags($symbol);
			}
		}	
		return $symbol;
	}

	public function cacl_fee_price($price, $product_id){
		global $woocommerce;
		$product 			= wc_get_product($product_id);
		$cart 				= $woocommerce->cart;
		$taxable 			= $product->is_taxable();
		$tax_display_cart 	= get_option( 'woocommerce_tax_display_shop' );
		$tax_string="";

		// Taxable
		if ( $taxable ) {

			if ( $tax_display_cart == 'excl' ) {

				if (floatval($price)!=0){
					$price = $product->get_price_excluding_tax(1,$price);
				}

			} else {

				if (floatval($price)!=0){
					$price = $product->get_price_including_tax(1,$price);
				}
			}

		}
		return $price;
	}

	public function get_item_data_array( $other_data, $cart_item ) {
		$filtered_array=$this->filtered_get_item_data( $cart_item );
			$price=0;
			$link_data=array();
			$quantity=$cart_item['quantity'];
			foreach ( $filtered_array as $section ) {

				$value=array();

				foreach ( $section['other_data'] as $key=>$data ) {
					$display_value = ! empty( $data['display'] ) ? $data['display'] : $data['value'];

					if (!empty($data['images']) && $this->tm_epo_strip_html_from_emails=="no"){
						$display_value ='<div class="cpf-img-on-cart"><img alt="" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="'.$data['images'].'" /></div>'.$display_value;
					}
					$value[]=$display_value;
				}

				if ( !empty( $value ) && count( $value )>0 ) {
					$value=implode( " , ", $value );
				}else {
					$value="";
				}

				if (empty($section['quantity'])){
					$section['quantity']=1;
				}
				
				// WooCommerce Dynamic Pricing & Discounts
				$original_price=$section['price']/$section['quantity'];
				$original_price_q=$original_price*$quantity*$section['quantity'];
				
				$section['price']=$this->get_RP_WCDPD($cart_item['data'],$section['price'],$cart_item['tm_cart_item_key']);
				$after_price=$section['price']/$section['quantity'];
				
				$price=$price+(float)$section['price'];
				
				if ($this->tm_epo_hide_options_prices_in_cart=="normal"){

					$format_price=$this->get_price_for_cart( $after_price * $section['items'] ,$cart_item);
					$format_price_total=$this->get_price_for_cart($section['price']*$quantity,$cart_item);
					$format_price_total2=$this->get_price_for_cart($section['price'] ,$cart_item);
					if ($original_price!=$after_price){
						$original_price=$this->get_price_for_cart($original_price,$cart_item);
						$original_price_total=$this->get_price_for_cart($original_price_q,$cart_item);
						$format_price = '<span class="rp_wcdpd_cart_price"><del>' . $original_price . '</del> <ins>' . $format_price . '</ins></span>';
						//$format_price_total = '<span class="rp_wcdpd_cart_price"><del>' . $original_price_total . '</del> <ins>' . $format_price_total . '</ins></span>';
					}
				}else{
					$format_price='';
					$format_price_total='';
					$format_price_total2='';
				}
				$single_price=$this->get_price_for_cart((float)$section['price']/$section['quantity'],$cart_item);
				$quantity_string = ($section['quantity']>1)?' x '.$section['quantity']:'';
				$other_data[] = array(
					'name'    => $section['label'],
					'value'   => do_shortcode(html_entity_decode ($value)). '<small>'.$format_price . $quantity_string . '</small>',
					'tm_label' => $section['label'],
					'tm_value' => do_shortcode(html_entity_decode ($value)),
					'tm_price' => $format_price,
					'tm_total_price' => $format_price_total,
					'tm_quantity' => $section['quantity'],
					'tm_image' => $section['other_data'][0]['images'],
				);
				$link_data[] = array(
					'name'    => $section['label'] ,
					'value'   => $value,
					'price'   => $format_price,
					'tm_price' => $single_price,
					'tm_total_price' => $format_price_total,
					'tm_quantity' => $section['quantity'],
					'tm_total_price2' => $format_price_total2
				);
			}

			if ($this->tm_epo_cart_field_display=="link"){
				if (empty($price) || $this->tm_epo_hide_options_prices_in_cart!="normal"){
					$price='';
				}else{
					$price=$this->get_price_for_cart($price,$cart_item);
				}
				$uni=uniqid('');
				$data='<div class="tm-extra-product-options">';
					$data .= '<div class="row tm-cart-row">'
							. '<div class="cell col-4 cpf-name">&nbsp;</div>'
							. '<div class="cell col-4 cpf-value">&nbsp;</div>'
							. '<div class="cell col-2 cpf-price">'.__( 'Price', 'woocommerce' ).'</div>'
							. '<div class="cell col-1 cpf-quantity">'.__( 'Quantity', 'woocommerce' ).'</div>'
							. '<div class="cell col-1 cpf-total-price">'.__( 'Total', 'woocommerce' ).'</div>'
							. '</div>';

				foreach ( $link_data as $link ) {
					$data .= '<div class="row tm-cart-row">'
							. '<div class="cell col-4 cpf-name">'.$link['name'].'</div>'
							. '<div class="cell col-4 cpf-value">'.do_shortcode(html_entity_decode ($link['value'])).'</div>'
							. '<div class="cell col-2 cpf-price">'.$link['tm_price'].'</div>'
							. '<div class="cell col-1 cpf-quantity">'.$link['tm_quantity'].'</div>'
							. '<div class="cell col-1 cpf-total-price">'.$link['tm_total_price2'].'</div>'
							. '</div>';

				}
				$data .='</div>';
				$other_data=array(
					array(
						'name' 	=> '<a href="#tm-cart-link-data-'.$uni.'" class="tm-cart-link">'.__( 'Additional options', TM_EPO_TRANSLATION ).'</a>',
						'value' => $price.'<div id="tm-cart-link-data-'.$uni.'" class="tm-cart-link-data tm-hidden">'.$data.'</div>'
						)
					);
			}
		return $other_data;
	}
	/**
	 * Gets cart item to display in the frontend.
	 */
	public function get_item_data( $other_data, $cart_item ) {

		if ( $this->tm_epo_hide_options_in_cart=="normal" &&  $this->tm_epo_cart_field_display!="advanced" && !empty( $cart_item['tmcartepo'] ) ) {

			$other_data = $this->get_item_data_array( $other_data, $cart_item );

		}	
		
		return $other_data;
	}

	public function calculate_price( $element, $key, $attribute, $per_product_pricing, $cpf_product_price, $variation_id ) {
		$_price=0;
		$_price_type="";
		$key=esc_attr($key);
		if ($per_product_pricing){

			if ( !isset( $element['price_rules'][$key] ) ) {// field price rule
				if ( $variation_id && isset( $element['price_rules'][0][$variation_id] ) ) {// general variation rule
					$_price=$element['price_rules'][0][$variation_id];
				}elseif ( isset( $element['price_rules'][0][0] ) ) {// general rule
					$_price=$element['price_rules'][0][0];
				}
			}else {
				if ( $variation_id && isset( $element['price_rules'][$key][$variation_id] ) ) {// field price rule
					$_price=$element['price_rules'][$key][$variation_id];
				}elseif ( isset( $element['price_rules'][$key][0] ) ) {// general field variation rule
					$_price=$element['price_rules'][$key][0];
				}elseif ( $variation_id && isset( $element['price_rules'][0][$variation_id] ) ) {// general variation rule
					$_price=$element['price_rules'][0][$variation_id];
				}elseif ( isset( $element['price_rules'][0][0] ) ) {// general rule
					$_price=$element['price_rules'][0][0];
				}
			}

			if ( !isset( $element['price_rules_type'][$key] ) ) {// field price rule
				if ( $variation_id && isset( $element['price_rules_type'][0][$variation_id] ) ) {// general variation rule
					$_price_type=$element['price_rules_type'][0][$variation_id];
				}elseif ( isset( $element['price_rules_type'][0][0] ) ) {// general rule
					$_price_type=$element['price_rules_type'][0][0];
				}
			}else {
				if ( $variation_id && isset( $element['price_rules_type'][$key][$variation_id] ) ) {// field price rule
					$_price_type=$element['price_rules_type'][$key][$variation_id];
				}elseif ( isset( $element['price_rules_type'][$key][0] ) ) {// general field variation rule
					$_price_type=$element['price_rules_type'][$key][0];
				}elseif ( $variation_id && isset( $element['price_rules_type'][0][$variation_id] ) ) {// general variation rule
					$_price_type=$element['price_rules_type'][0][$variation_id];
				}elseif ( isset( $element['price_rules_type'][0][0] ) ) {// general rule
					$_price_type=$element['price_rules_type'][0][0];
				}
			}
			$_price= wc_format_decimal( $_price, false, true );

			switch ($_price_type) {
				case 'percent':
					if ($cpf_product_price){
						$cpf_product_price=apply_filters( 'woocommerce_tm_epo_price2_remove',$cpf_product_price,"percent");
						$_price=($_price/100)*floatval($cpf_product_price);
					}
					break;
				case 'percentcurrenttotal':
					if (isset($_POST[$attribute.'_hidden'])){
						$_price=floatval($_POST[$attribute.'_hidden']);
						$_price=apply_filters( 'woocommerce_tm_epo_price2_remove',$_price,"percentcurrenttotal");
					}
					break;
				case 'char':
					$_price=floatval($_price*strlen(stripcslashes($_POST[$attribute])));
					break;
				case 'charpercent':
					if ($cpf_product_price){
						$cpf_product_price=apply_filters( 'woocommerce_tm_epo_price2_remove',$cpf_product_price,"percent");
						$_price=floatval(strlen(stripcslashes($_POST[$attribute])))*(($_price/100)*floatval($cpf_product_price));
					}
					break;
				case 'step':
					$_price=floatval($_price*floatval(stripcslashes($_POST[$attribute])));
					break;
				case 'currentstep':
					$_price=floatval(stripcslashes($_POST[$attribute]));
					break;
				
				default:
					// fixed price
					break;
			}

			// quantity button
			if (isset($_POST[$attribute.'_quantity'])){
				$_price=$_price*floatval($_POST[$attribute.'_quantity']);
			} 

		}

		return apply_filters('wcml_raw_price_amount',$_price);
	}

	/**
	 * Adds meta data to the order.
	 */
	public function order_item_meta( $item_id, $values ) {
		if ( ! empty( $values['tmcartepo'] ) ) {			
			wc_add_order_item_meta( $item_id, '_tmcartepo_data', $values['tmcartepo'] );
			wc_add_order_item_meta( $item_id, '_tm_epo_product_original_price', array($values['tm_epo_product_original_price']) );
			if(class_exists('RP_WCDPD')){
				wc_add_order_item_meta( $item_id, 'tm_has_dpd', $values['tmcartepo'] );
			}
			wc_add_order_item_meta( $item_id, '_tm_epo', array(1) );
		}
		if ( ! empty( $values['tmsubscriptionfee'] ) ) {
			wc_add_order_item_meta( $item_id, '_tmsubscriptionfee_data', array($values['tmsubscriptionfee']) );
			wc_add_order_item_meta( $item_id, __("Options Subscription fee",TM_EPO_TRANSLATION), $values['tmsubscriptionfee'] );
		}
		if ( ! empty( $values['tmcartfee'] ) ) {
			wc_add_order_item_meta( $item_id, '_tmcartfee_data', array($values['tmcartfee']) );
		}
	}

	/**
	 * Validates the cart data.
	 */

	public function add_to_cart_validation( $passed, $product_id, $qty, $variation_id = '', $variations = array(), $cart_item_data = array() ) {
		/* disables add_to_cart_button class on shop page */
		if (is_ajax() && $this->tm_epo_force_select_options=="display" ){
			
			if ($this->cpf){
				$cpf=$this->cpf;
			}else{
				$cpf=$this->get_product_tm_epos($product_id);
			}
			if (is_array($cpf) && (!empty($cpf['global']) || !empty($cpf['local']))) {
				return false;
			}
		 
		}

		$is_validate=true;

		// Get product type
		$terms 			= get_the_terms( $product_id, 'product_type' );
		$product_type 	= ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';
		if ( $product_type == 'bto' || $product_type == 'composite') {

			$bto_data 	= maybe_unserialize( get_post_meta( $product_id, '_bto_data', true ) );
			$valid_ids 	= array_keys( $bto_data );

			foreach ( $valid_ids as $bundled_item_id ) {

				if ( isset( $_REQUEST[ 'add-product-to-cart' ][ $bundled_item_id ] ) && $_REQUEST[ 'add-product-to-cart' ][ $bundled_item_id ] !== '' ) {
					$bundled_product_id = $_REQUEST[ 'add-product-to-cart' ][ $bundled_item_id ];
				} elseif ( isset( $cart_item_data[ 'composite_data' ][ $bundled_item_id ][ 'product_id' ] ) && isset( $_GET[ 'order_again' ] ) ) {
					$bundled_product_id = $cart_item_data[ 'composite_data' ][ $bundled_item_id ][ 'product_id' ];
				}

				if (isset($bundled_product_id) && !empty($bundled_product_id)){

					$_passed=true;

					if ( isset( $_REQUEST[ 'item_quantity' ][ $bundled_item_id ] ) && is_numeric( $_REQUEST[ 'item_quantity' ][ $bundled_item_id ] ) ) {
						$item_quantity = absint( $_REQUEST[ 'item_quantity' ][ $bundled_item_id ] );
					} elseif ( isset( $cart_item_data[ 'composite_data' ][ $bundled_item_id ][ 'quantity' ] ) && isset( $_GET[ 'order_again' ] ) ) {
						$item_quantity = $cart_item_data[ 'composite_data' ][ $bundled_item_id ][ 'quantity' ];
					}
					if ( !empty($item_quantity)){
						$item_quantity = absint( $item_quantity );
						
						$_passed = $this->validate_product_id( $bundled_product_id, $item_quantity, $bundled_item_id );
						
					}

					if (!$_passed){
						$is_validate=false;
					}
					
				}
			}
		}

		if (!$this->validate_product_id( $product_id, $qty )){
			$passed=false;
		}

		/* Try to validate uploads before they happen */
		$files=array();
		foreach ( $_FILES as $k=>$file){
			if (!empty($file['name'])){
				if(!empty($file['error'])){
					$passed=false;
					// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
					$upload_error_strings = array( false,
						__( "The uploaded file exceeds the upload_max_filesize directive in php.ini.", TM_EPO_TRANSLATION  ),
						__( "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.", TM_EPO_TRANSLATION  ),
						__( "The uploaded file was only partially uploaded.", TM_EPO_TRANSLATION  ),
						__( "No file was uploaded.", TM_EPO_TRANSLATION  ),
						'',
						__( "Missing a temporary folder.", TM_EPO_TRANSLATION  ),
						__( "Failed to write file to disk.", TM_EPO_TRANSLATION  ),
						__( "File upload stopped by extension.", TM_EPO_TRANSLATION  ));
					if (isset($upload_error_strings[$file['error']])){
						wc_add_notice( $upload_error_strings[$file['error']] , 'error' );
					}
				}
				$check_filetype=wp_check_filetype( $file['name'] ) ;
				$check_filetype=$check_filetype['ext'];
				if (!$check_filetype && !empty($file['name'])){
					$passed=false;
					wc_add_notice( __( "Sorry, this file type is not permitted for security reasons.", TM_EPO_TRANSLATION  ) , 'error' );
				}
			}			

		}

		if (!$is_validate){
			$passed=false;
		}		

		return $passed;

	}

	public function is_visible($element=array(), $section=array(), $sections=array() , $form_prefix=""){
		
		/* Element */
		$logic = false;
		if (isset($element['section'])){
			if(!$this->is_visible($section, array(), $sections, $form_prefix)){
				return false;
			}
			if (!isset($element['logic']) || empty($element['logic'])){
				return true;
			}
			$logic= (array) json_decode($element['clogic']) ;
		/* Section */
		}else{
			if (!isset($element['sections_logic']) || empty($element['sections_logic'])){
				return true;
			}
			$logic= (array) json_decode($element['sections_clogic']);
		}

		if ($logic){
			$rule_toggle=$logic['toggle'];
			$rule_what=$logic['what'];
			$matches = 0;
			$checked = 0;
			$show=true;
			switch ($rule_toggle){
				case "show":
                	$show=false;
					break;
				case "hide":
					$show=true;
					break;
			}
			
			foreach($logic['rules'] as $key=>$rule){
				$matches++;
				
				if ($this->tm_check_field_match($rule, $sections, $form_prefix)){
                    $checked++;
                }
				
			}
			if ($rule_what=="all"){
				if ($checked==$matches){
					$show=!$show;
				}
			}else{
				if ($checked>0){
					$show=!$show;
				}
			}
			return $show;

		}

		return false;
	}

	public function tm_check_field_match($rule=false, $sections=false, $form_prefix=""){
		if (empty($rule) || empty($sections)){
			return false;
		}

		$section_id=$rule->section;
		$element_id=$rule->element;
		$operator=$rule->operator;
		$value=$rule->value;

		if (!isset($sections[$section_id]) 
			|| !isset($sections[$section_id]['elements']) 
			|| !isset($sections[$section_id]['elements'][$element_id]) 
			|| !isset($sections[$section_id]['elements'][$element_id]['type']) 
			){
			return false;
		}

		// variations logic		
		if ($sections[$section_id]['elements'][$element_id]['type']=="variations"){
			return $this->tm_variation_check_match($form_prefix,$value,$operator);
		}

		if (!isset($sections[$section_id]['elements'][$element_id]['name_inc'])){
			return false;
		}

		/* element array cannot hold the form_prefix for bto support, so we append manually */
		$element_to_check = $sections[$section_id]['elements'][$element_id]['name_inc'];		
		$element_type = $sections[$section_id]['elements'][$element_id]['type'];
		$posted_value = null;

		switch ($element_type){
			case "radio":
				$radio_checked_length=0;
				$element_to_check=array_unique($element_to_check);

				$element_to_check=$element_to_check[0].$form_prefix;
		
				if (isset($_POST[$element_to_check])){
					$radio_checked_length++;
					$posted_value = $_POST[$element_to_check];
					$posted_value = stripslashes( $posted_value );
					$posted_value=TM_EPO_HELPER()->encodeURIComponent($posted_value);
					$posted_value=TM_EPO_HELPER()->reverse_strrchr($posted_value,"_");
				}
				if ($operator=='is' || $operator=='isnot'){
					if ($radio_checked_length==0){
						return false;
					}
				}else if ($operator=='isnotempty'){
					return $radio_checked_length>0;
				}else if ($operator=='isempty'){
					return $radio_checked_length==0;
				} 
			break;
			case "checkbox":
				$checkbox_checked_length=0;
				$ret=false;
				$element_to_check=array_unique($element_to_check);
				foreach ($element_to_check as $key => $name_value) {
					$element_to_check[$key]=$name_value.$form_prefix;	
					if (isset($_POST[$element_to_check[$key]])){
						$checkbox_checked_length++;
						$posted_value=$_POST[$element_to_check[$key]];
						$posted_value = stripslashes( $posted_value );
						$posted_value=TM_EPO_HELPER()->encodeURIComponent($posted_value);
						$posted_value=TM_EPO_HELPER()->reverse_strrchr($posted_value,"_");
					}
					if ($this->tm_check_match($posted_value,$value,$operator)){
						$ret=true;
					}
				}
				if ($operator=='is' || $operator=='isnot'){
					if ($checkbox_checked_length==0){
						return false;
					}
					return $ret;
				}else if ($operator=='isnotempty'){
					return $checkbox_checked_length>0;
				}else if ($operator=='isempty'){
					return $checkbox_checked_length==0;
				} 
			break;
			case "select":
			case "textarea":
			case "textfield":
				$element_to_check .=$form_prefix;
				if (isset($_POST[$element_to_check])){
					$posted_value = $_POST[$element_to_check];
					$posted_value = stripslashes( $posted_value );
					if ($element_type=="select"){
						$posted_value=TM_EPO_HELPER()->encodeURIComponent($posted_value);
						$posted_value=TM_EPO_HELPER()->reverse_strrchr($posted_value,"_");
					}
				}
			break;
		}
		return $this->tm_check_match($posted_value,$value,$operator);
	}

	public function tm_variation_check_match($form_prefix,$value,$operator){
		$posted_value = $this->get_posted_variation_id($form_prefix);
		return $this->tm_check_match($posted_value,$value,$operator);
	}

	public function tm_check_match($posted_value,$value,$operator){
		switch ($operator){
		case "is":
			return ($posted_value!=null && $value == $posted_value);
			break;
		case "isnot":
			return ($posted_value!=null && $value != $posted_value);
			break;
		case "isempty":								
			return (!(($posted_value != null && $posted_value!='')));
			break;
		case "isnotempty":
			return (($posted_value != null && $posted_value!=''));
			break;
		}
		return false;
	}

	/**
	 * Gets the stored card data for the order again functionality.
	 */
	public function order_again_cart_item_data( $cart_item_meta, $product, $order ) {
		global $woocommerce;

		// Disable validation
		remove_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 50, 6 );

		$_backup_cart = isset( $product['item_meta']['tmcartepo_data'] ) ? $product['item_meta']['tmcartepo_data'] : false;
		if ( !$_backup_cart ) {
			$_backup_cart = isset( $product['item_meta']['_tmcartepo_data'] ) ? $product['item_meta']['_tmcartepo_data'] : false;
		}
		if ( $_backup_cart && is_array( $_backup_cart ) && isset( $_backup_cart[0] ) ) {
			$_backup_cart=maybe_unserialize( $_backup_cart[0] );
			$cart_item_meta['tmcartepo'] = $_backup_cart;
		}

		$_backup_cart = isset( $product['item_meta']['tmsubscriptionfee_data'] ) ? $product['item_meta']['tmsubscriptionfee_data'] : false;
		if ( !$_backup_cart ) {
			$_backup_cart = isset( $product['item_meta']['_tmsubscriptionfee_data'] ) ? $product['item_meta']['_tmsubscriptionfee_data'] : false;
		}
		if ( $_backup_cart && is_array( $_backup_cart ) && isset( $_backup_cart[0] ) ) {
			$_backup_cart=maybe_unserialize( $_backup_cart[0] );
			$cart_item_meta['tmsubscriptionfee'] = $_backup_cart[0];
		}
		
		$_backup_cart = isset( $product['item_meta']['tmcartfee_data'] ) ? $product['item_meta']['tmcartfee_data'] : false;
		if ( !$_backup_cart ) {
			$_backup_cart = isset( $product['item_meta']['_tmcartfee_data'] ) ? $product['item_meta']['_tmcartfee_data'] : false;
		}
		if ( $_backup_cart && is_array( $_backup_cart ) && isset( $_backup_cart[0] ) ) {
			$_backup_cart=maybe_unserialize( $_backup_cart[0] );
			$cart_item_meta['tmcartfee'] = $_backup_cart[0];
		}

		
		return $cart_item_meta;
	}

	/**
	 * Handles the display of all the extra options on the product page.
	 *
	 * IMPORTANT:
	 * We do not support plugins that pollute the global $woocommerce.
	 *
	 */
	public function frontend_display($product_id=0, $form_prefix="") {
		global $product,$woocommerce;
		if (!property_exists( $woocommerce, 'product_factory') || $woocommerce->product_factory===NULL || ($this->tm_options_have_been_displayed && (!($this->is_bto || $this->is_enabled_shortcodes()) ) )){
			return;// bad function call
		}
		$this->tm_epo_fields($product_id, $form_prefix);
		$this->tm_epo_totals($product_id, $form_prefix);
		$this->tm_options_have_been_displayed=true;
	}

	public function tm_epo_totals($product_id=0, $form_prefix="") {
		global $product,$woocommerce;
		if (!property_exists( $woocommerce, 'product_factory') || $woocommerce->product_factory===NULL || ($this->tm_options_have_been_displayed && (!($this->is_bto || $this->is_enabled_shortcodes()) ) )){
			return;// bad function call
		}
		$this->print_price_fields( $product_id, $form_prefix );		
		$this->tm_options_totals_have_been_displayed=true;
	}

	public function tm_epo_fields($product_id=0, $form_prefix="") {
		global $woocommerce;
		if (!property_exists( $woocommerce, 'product_factory') || $woocommerce->product_factory===NULL || ($this->tm_options_have_been_displayed && (!($this->is_bto || $this->is_enabled_shortcodes()) ) )){
			return;// bad function call
		}
		if (!$product_id){
			global $product;
			if ($product){
				$product_id=$product->id;
			}
		}else{
			$product=wc_get_product($product_id);
		}
		if (!$product_id || empty($product) ){
			return;
		}
		
		$post_id=$product_id;

		if ($form_prefix){
			$_bto_id=$form_prefix;
			$form_prefix="_".$form_prefix;
			echo '<input type="hidden" class="cpf-bto-id" name="cpf_bto_id[]" value="'.$form_prefix.'" />';
			echo '<input type="hidden" value="" name="cpf_bto_price['.$_bto_id.']" class="cpf-bto-price" />';
			echo '<input type="hidden" value="0" name="cpf_bto_optionsprice[]" class="cpf-bto-optionsprice" />';
		}
		if ($this->cpf && !$this->is_bto && $this->noactiondisplay){
			$cpf_price_array=$this->cpf;
		}else{
			$cpf_price_array=$this->get_product_tm_epos($post_id);
		}
		if (!$cpf_price_array){
			return;
		}
		$global_price_array = $cpf_price_array['global'];
		$local_price_array  = $cpf_price_array['local'];
		if ( empty($global_price_array) && empty($local_price_array) ){
			return;
		}
		$global_prices=array( 'before'=>array(), 'after'=>array() );
		foreach ( $global_price_array as $priority=>$priorities ) {
			foreach ( $priorities as $pid=>$field ) {
				if (isset($field['sections']) && is_array($field['sections'])){
					foreach ( $field['sections'] as $section_id=>$section ) {
						if ( isset( $section['sections_placement'] ) ) {
							$global_prices[$section['sections_placement']][$priority][$pid]['sections'][$section_id]=$section;
						}
					}
				}
			}
		}

		$tabindex   		= 0;
		$_currency   		= get_woocommerce_currency_symbol();
		$unit_counter  		= 0;
		$field_counter  	= 0;
		$element_counter	= 0;

		wc_get_template(
			'start.php',
			array('form_prefix' => $form_prefix) ,
			$this->_namespace,
			$this->template_path
		);

		// global options before local
		foreach ( $global_prices['before'] as $priorities ) {
			foreach ( $priorities as $field ) {
				$args=array(
					'tabindex'   		=> $tabindex,
					'unit_counter'  	=> $unit_counter,
					'field_counter'  	=> $field_counter,
					'element_counter'  	=> $element_counter,
					'_currency'   		=> $_currency
				);
				$_return=$this->get_builder_display( $field, 'before', $args , $form_prefix, $product_id);
				extract( $_return, EXTR_OVERWRITE );
			}
		}

		// local options
		if ( is_array( $local_price_array ) && sizeof( $local_price_array ) > 0 ) {

			$attributes = maybe_unserialize( get_post_meta( floatval(TM_EPO_WPML()->get_original_id( $post_id ) ), '_product_attributes', true ) );
			$wpml_attributes = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

			if ( is_array( $attributes ) && count( $attributes )>0 ) {
				foreach ( $local_price_array as $field ) {
					if ( isset( $field['name'] ) && isset( $attributes[$field['name']] ) && !$attributes[$field['name']]['is_variation'] ) {

						$attribute=$attributes[$field['name']];
						$wpml_attribute=$wpml_attributes[$field['name']];					

						$empty_rules="";
						if ( isset( $field['rules_filtered'][0] ) ) {
							$empty_rules=esc_html( json_encode( ( $field['rules_filtered'][0] ) ) );
						}
						$empty_rules_type="";
						if ( isset( $field['rules_type'][0] ) ) {
							$empty_rules_type=esc_html( json_encode( ( $field['rules_type'][0] ) ) );
						}

						$args = array(
							'title'  	=> ( !$attribute['is_taxonomy'] && isset($attributes[$field['name']]["name"]))
							?esc_html(wc_attribute_label($attributes[$field['name']]["name"]))
							:esc_html( wc_attribute_label( $field['name'] ) ),
							'required'  => esc_html( wc_attribute_label( $field['required'] ) ),
							'field_id'  => 'tm-epo-field-'.$unit_counter,
							'type'      => $field['type'],
							'rules'     => $empty_rules,
							'rules_type'     => $empty_rules_type
						);
						wc_get_template(
							'field-start.php',
							$args ,
							$this->_namespace,
							$this->template_path
						);

						$name_inc="";
						$field_counter=0;
						if ( $attribute['is_taxonomy'] ) {
						
							$_original_terms = TM_EPO_WPML()->get_terms( null, $attribute['name'], 'orderby=name&hide_empty=0' );

							switch ( $field['type'] ) {

							case "select":
								$name_inc ="select_".$element_counter;
								$tabindex++;

								$args = array(
									'options'   	=> '',
									'textafterprice' => '',
									'id'    		=> 'tmcp_select_'.$tabindex.$form_prefix,
									'name'    		=> 'tmcp_'.$name_inc.$form_prefix,
									'amount'     	=> '0 '.$_currency,
									'hide_amount'  	=> !empty( $field['hide_price'] )?" hidden":"",
									'tabindex'   	=> $tabindex
								);
								if ( $_original_terms ) {
					                foreach ( $_original_terms as $term ) {
										$has_term = has_term( (int) $term->term_id, $attribute['name'], floatval(TM_EPO_WPML()->get_original_id( $post_id )) ) ? 1 : 0;
										
										$wpml_term_id = TM_EPO_WPML()->is_active()? icl_object_id($term->term_id,  $attribute['name'], false):false;

					                    if ($has_term ){
											if ($wpml_term_id){
												$wpml_term = get_term( $wpml_term_id, $attribute['name'] );
											}else{
												$wpml_term = $term;
											}
					                    	$args['options'] .='<option '.( isset( $_POST['tmcp_'.$name_inc.$form_prefix] )?selected( $_POST['tmcp_'.$name_inc.$form_prefix], esc_attr( sanitize_title( $term->slug ) ), 0 ) :"" ).' value="'.sanitize_title( $term->slug ).'" data-price="" data-rules="'.( isset( $field['rules_filtered'][$term->slug] )?esc_html( json_encode( ( $field['rules_filtered'][$term->slug] ) ) ):'' ).'" data-rulestype="'.( isset( $field['rules_type'][$term->slug] )?esc_html( json_encode( ( $field['rules_type'][$term->slug] ) ) ):'' ).'">'.wptexturize( $wpml_term->name ).'</option>';					                        
					                    }
					                }
					            }
								
								wc_get_template(
									$field['type'].'.php',
									$args ,
									$this->_namespace,
									$this->template_path
								);
								$element_counter++;
								break;

							case "radio":
							case "checkbox":
								if ( $_original_terms ) {
									foreach ( $_original_terms as $term ) {
										
										$has_term = has_term( (int) $term->term_id, $attribute['name'], floatval(TM_EPO_WPML()->get_original_id( $post_id )) ) ? 1 : 0;
										
										$wpml_term_id = TM_EPO_WPML()->is_active()?icl_object_id($term->term_id,  $attribute['name'], false):false;
										
										if ($has_term ){

											if ($wpml_term_id){
												$wpml_term = get_term( $wpml_term_id, $attribute['name'] );
											}else{;
												$wpml_term = $term;
											}
											
											$tabindex++;

											if ( $field['type']=='radio' ) {
												$name_inc ="radio_".$element_counter;
											}
											if ( $field['type']=='checkbox' ) {
												$name_inc ="checkbox_".$element_counter."_".$field_counter;
											}

											$args = array(
												'label'   		=> wptexturize( $wpml_term->name ),
												'textafterprice' => '',
												'value'   		=> sanitize_title( $term->slug ),
												'rules'   		=> isset( $field['rules_filtered'][$term->slug] )?esc_html( json_encode( ( $field['rules_filtered'][$term->slug] ) ) ):'',
												'rules_type' 	=> isset( $field['rules_type'][$term->slug] )?esc_html( json_encode( ( $field['rules_type'][$term->slug] ) ) ):'',
												'id'    		=> 'tmcp_choice_'.$element_counter."_".$field_counter."_".$tabindex.$form_prefix,
												'name'    		=> 'tmcp_'.$name_inc.$form_prefix,
												'amount'     	=> '0 '.$_currency,
												'hide_amount'  	=> !empty( $field['hide_price'] )?" hidden":"",
												'tabindex'   	=> $tabindex,
												'use_images'	=> "",
												'grid_break'	=> "",
												'percent'		=> "",
												'limit' 		=> empty( $field['limit'] )?"":$field['limit']
											);
											wc_get_template(
												$field['type'].'.php',
												$args ,
												$this->_namespace,
												$this->template_path
											);

											$field_counter++;
										}
					                }
					            }								

								$element_counter++;
								break;

							}
						} else {

							$options = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
							$wpml_options = array_map( 'trim', explode( WC_DELIMITER, $wpml_attribute['value'] ) );

							switch ( $field['type'] ) {

							case "select":
								$name_inc ="select_".$element_counter;
								$tabindex++;

								$args = array(
									'options'   	=> '',
									'textafterprice' => '',
									'id'    		=> 'tmcp_select_'.$tabindex.$form_prefix,
									'name'    		=> 'tmcp_'.$name_inc.$form_prefix,
									'amount'     	=> '0 '.$_currency,
									'hide_amount'  	=> !empty( $field['hide_price'] )?" hidden":"",
									'tabindex'   	=> $tabindex
								);
								foreach ( $options as $k=>$option ) {
									$args['options'] .='<option '.( isset( $_POST['tmcp_'.$name_inc.$form_prefix] )?selected( $_POST['tmcp_'.$name_inc.$form_prefix], esc_attr( sanitize_title( $option ) ), 0 ) :"" ).' value="'.esc_attr( sanitize_title( $option ) ).'" data-price="" data-rules="'.( isset( $field['rules_filtered'][esc_attr( sanitize_title( $option ) )] )?esc_html( json_encode( ( $field['rules_filtered'][esc_attr( sanitize_title( $option ) )] ) ) ):'' ).'" data-rulestype="'.( isset( $field['rules_type'][esc_attr( sanitize_title( $option ) )] )?esc_html( json_encode( ( $field['rules_type'][esc_attr( sanitize_title( $option ) )] ) ) ):'' ).'">'.wptexturize( apply_filters( 'woocommerce_tm_epo_option_name', isset($wpml_options[$k])?$wpml_options[$k]:$option ) ).'</option>';
								}
								wc_get_template(
									$field['type'].'.php',
									$args ,
									$this->_namespace,
									$this->template_path
								);
								$element_counter++;
								break;

							case "radio":
							case "checkbox":
								foreach ( $options as $k=> $option ) {
									$tabindex++;

									if ( $field['type']=='radio' ) {
										$name_inc ="radio_".$element_counter;
									}
									if ( $field['type']=='checkbox' ) {
										$name_inc ="checkbox_".$element_counter."_".$field_counter;
									}

									$args = array(
										'label'   		=> wptexturize( apply_filters( 'woocommerce_tm_epo_option_name', isset($wpml_options[$k])?$wpml_options[$k]:$option ) ),
										'textafterprice' => '',
										'value'   		=> esc_attr( sanitize_title( $option ) ),
										'rules'   		=> isset( $field['rules_filtered'][sanitize_title( $option )] )?esc_html( json_encode( ( $field['rules_filtered'][sanitize_title( $option )] ) ) ):'',
										'rules_type' 	=> isset( $field['rules_type'][sanitize_title( $option )] )?esc_html( json_encode( ( $field['rules_type'][sanitize_title( $option )] ) ) ):'',
										'id'    		=> 'tmcp_choice_'.$element_counter."_".$field_counter."_".$tabindex.$form_prefix,
										'name'    		=> 'tmcp_'.$name_inc.$form_prefix,
										'amount'     	=> '0 '.$_currency,
										'hide_amount'  	=> !empty( $field['hide_price'] )?" hidden":"",
										'tabindex'   	=> $tabindex,
										'use_images'	=> "",
										'grid_break'	=> "",
										'percent'		=> "",
										'limit' 		=> empty( $field['limit'] )?"":$field['limit']
									);
									wc_get_template(
										$field['type'].'.php',
										$args ,
										$this->_namespace,
										$this->template_path
									);
									$field_counter++;
								}
								$element_counter++;
								break;

							}
						}

						wc_get_template(
							'field-end.php',
							array() ,
							$this->_namespace,
							$this->template_path
						);

						$unit_counter++;
					}
				}
			}
		}

		// global options after local
		foreach ( $global_prices['after'] as $priorities ) {
			foreach ( $priorities as $field ) {
				$args=array(
					'tabindex'   		=> $tabindex,
					'unit_counter'  	=> $unit_counter,
					'field_counter'  	=> $field_counter,
					'element_counter'  	=> $element_counter,
					'_currency'   		=> $_currency
				);
				$_return=$this->get_builder_display( $field, 'after', $args, $form_prefix, $product_id );
				extract( $_return, EXTR_OVERWRITE );
			}
		}

		wc_get_template(
			'end.php',
			array() ,
			$this->_namespace,
			$this->template_path
		);
		
		$this->tm_options_single_have_been_displayed=true;
	}

	public function is_supported_quick_view(){
		$theme=$this->get_theme('Name');
		if ($theme=='Flatsome'){
			return true;
		}
		return false;
	}
	public function frontend_scripts() {
		global $product;
		if ( 
			( (class_exists( 'WC_Quick_View' ) || $this->is_supported_quick_view()) && ( is_shop() || is_product_category() || is_product_tag() ) ) 
			|| $this->is_enabled_shortcodes() 
			|| is_product() 
			|| is_cart() 
			|| is_checkout() 
			|| is_order_received_page() 
			) {
			$this->custom_frontend_scripts();	
		}else{
			return;
		}		
	}

	public function custom_frontend_scripts() {
		do_action('tm_epo_register_addons_scripts');
		$product = wc_get_product();
		
		$css_array = array(
			'tm-font-awesome' => array(
				'src'     => $this->plugin_url .'/external/font-awesome/css/font-awesome.min.css',
				'deps'    => false,
				'version' => '4.2',
				'media'   => 'screen'
			),
			'tm-epo-animate-css' => array(
				'src'     =>  $this->plugin_url  . '/assets/css/animate.css',
				'deps'    => false,
				'version' => $this->version,
				'media'   => 'only screen and (max-width: ' . apply_filters( 'woocommerce_style_smallscreen_breakpoint', $breakpoint = '768px' ) . ')'
			),
			'tm-epo-css' => array(
				'src'     => $this->plugin_url . '/assets/css/tm-epo.css',
				'deps'    => false,
				'version' => $this->version,
				'media'   => 'all'
			),
		);
		if ( $enqueue_styles = apply_filters( 'tm_epo_enqueue_styles', $css_array ) ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				wp_enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
			}
		}

        wp_enqueue_style( 'tm-font-awesome', $this->plugin_url .'/external/font-awesome/css/font-awesome.min.css', false, '4.2', 'screen' );
        wp_enqueue_style( 'tm-epo-animate-css', $this->plugin_url  . '/assets/css/animate.css' );
		wp_enqueue_style( 'tm-epo-css', $this->plugin_url . '/assets/css/tm-epo.css' );

		wp_register_script( 'tm-accounting', $this->plugin_url . '/assets/js/accounting.min.js', '', '0.3.2', true );
		wp_register_script( 'tm-modernizr', $this->plugin_url. '/assets/js/modernizr.js', '', '2.8.2' );
		wp_register_script( 'tm-scripts', $this->plugin_url . '/assets/js/tm-scripts.js', '', $this->version, true );
		
		wp_enqueue_script( 'tm-epo', $this->plugin_url. '/assets/js/tm-epo.js', array( 'jquery', 'jquery-ui-datepicker', 'tm-accounting', 'tm-modernizr', 'tm-scripts' ), $this->version, true );

		$extra_fee=0;
		global $wp_locale;
		$args = array(
			'extra_fee' 					=> apply_filters( 'woocommerce_tm_final_price_extra_fee', $extra_fee,$product ),
			'tm_epo_final_total_box' 		=> (empty($this->tm_meta_cpf['override_final_total_box']))?$this->tm_epo_final_total_box:$this->tm_meta_cpf['override_final_total_box'],
			'i18n_extra_fee'           		=> __( 'Extra fee', TM_EPO_TRANSLATION ),
			'i18n_options_total'           	=> (!empty($this->tm_epo_options_total_text))?$this->tm_epo_options_total_text:__( 'Options amount', TM_EPO_TRANSLATION ),
			'i18n_final_total'             	=> (!empty($this->tm_epo_final_total_text))?$this->tm_epo_final_total_text:__( 'Final total', TM_EPO_TRANSLATION ),
			'i18n_sign_up_fee' 				=> (!empty($this->tm_epo_subscription_fee_text))?$this->tm_epo_subscription_fee_text:__( 'Sign up fee', TM_EPO_TRANSLATION ),
			'i18n_cancel'					=> __( 'Cancel', TM_EPO_TRANSLATION ),
			'i18n_close'					=> __( 'Close', TM_EPO_TRANSLATION ),
			'i18n_addition_options'			=> __( 'Additional Options', TM_EPO_TRANSLATION ),
			'currency_format_num_decimals' 	=> absint( get_option( 'woocommerce_price_num_decimals' ) ),
			'currency_format_symbol'       	=> get_woocommerce_currency_symbol(),
			'currency_format_decimal_sep'  	=> esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
			'currency_format_thousand_sep' 	=> esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
			'currency_format'              	=> esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ),
			'css_styles' 					=> $this->tm_epo_css_styles,
			'css_styles_style' 				=> $this->tm_epo_css_styles_style,
			'tm_epo_options_placement' 		=> $this->tm_epo_options_placement,
			'tm_epo_totals_box_placement' 	=> $this->tm_epo_totals_box_placement,
			'tm_epo_no_lazy_load' 			=> $this->tm_epo_no_lazy_load,
			'tm_epo_show_only_active_quantities' => $this->tm_epo_show_only_active_quantities,

			'monthNames'        			=> $this->strip_array_indices( $wp_locale->month ),
			'monthNamesShort'   			=> $this->strip_array_indices( $wp_locale->month_abbrev ),
			'dayNames' 						=> $this->strip_array_indices( $wp_locale->weekday ),
 			'dayNamesShort' 				=> $this->strip_array_indices( $wp_locale->weekday_abbrev ),
			'dayNamesMin' 					=> $this->strip_array_indices( $wp_locale->weekday_initial ),
 			'isRTL'             			=> $wp_locale->text_direction=='rtl',

 			'floating_totals_box' 			=> $this->tm_epo_floating_totals_box,

		);
		wp_localize_script( 'tm-epo', 'tm_epo_js', $args );
	}

	/**
	 * Format array for the datepicker
	 *
	 * WordPress stores the locale information in an array with a alphanumeric index, and
	 * the datepicker wants a numerical index. This function replaces the index with a number
	*/
	private function strip_array_indices( $ArrayToStrip ) {
		$NewArray=array();
		foreach( $ArrayToStrip as $objArrayItem) {
			$NewArray[] = $objArrayItem;
		}
		return( $NewArray );
	}

	// get WooCommerce Dynamic Pricing & Discounts price for options
	// modified from get version from Pricing class
	private function get_RP_WCDPD_single($field_price,$cart_item_key,$pricing){
		if ($this->tm_epo_dpd_enable == 'no' || !isset($pricing->items[$cart_item_key])) {
			return $field_price;
		}

            $price = $field_price;
            $original_price = $price;

            if (in_array($pricing->pricing_settings['apply_multiple'], array('all', 'first'))) {
                foreach ($pricing->apply['global'] as $rule_key => $apply) {
                    if ($deduction = $pricing->apply_rule_to_item($rule_key, $apply, $cart_item_key, $pricing->items[$cart_item_key], false, $price)) {

                        if ($apply['if_matched'] == 'other' && isset($pricing->applied) && isset($pricing->applied['global'])) {
                            if (count($pricing->applied['global']) > 1 || !isset($pricing->applied['global'][$rule_key])) {
                                continue;
                            }
                        }

                        $pricing->applied['global'][$rule_key] = 1;
                        $price = $price - $deduction;
                    }
                }
            }
            else if ($pricing->pricing_settings['apply_multiple'] == 'biggest') {

                $price_deductions = array();

                foreach ($pricing->apply['global'] as $rule_key => $apply) {

                    if ($apply['if_matched'] == 'other' && isset($pricing->applied) && isset($pricing->applied['global'])) {
                        if (count($pricing->applied['global']) > 1 || !isset($pricing->applied['global'][$rule_key])) {
                            continue;
                        }
                    }

                    if ($deduction = $pricing->apply_rule_to_item($rule_key, $apply, $cart_item_key, $pricing->items[$cart_item_key], false)) {
                        $price_deductions[$rule_key] = $deduction;
                    }
                }

                if (!empty($price_deductions)) {
                    $max_deduction = max($price_deductions);
                    $rule_key = array_search($max_deduction, $price_deductions);
                    $pricing->applied['global'][$rule_key] = 1;
                    $price = $price - $max_deduction;
                }

            }

            // Make sure price is not negative
            // $price = ($price < 0) ? 0 : $price;

            if ($price != $original_price) {
                return $price;
            }
            else {
                return $field_price;
            }
	}

	// get WooCommerce Dynamic Pricing & Discounts price rules
	public function get_RP_WCDPD($product,$field_price=null,$cart_item_key=null){
		$price = null;
				
		if(class_exists('RP_WCDPD') && class_exists('RP_WCDPD_Pricing') && !empty($GLOBALS['RP_WCDPD'])){
			
			$tm_RP_WCDPD=$GLOBALS['RP_WCDPD'];

			$selected_rule = null;

			if ($field_price!==null && $cart_item_key!==null){
	            return $this->get_RP_WCDPD_single($field_price,$cart_item_key,$tm_RP_WCDPD->pricing);
	        }
	        
	        $dpd_version_compare=version_compare( RP_WCDPD_VERSION, '1.0.13', '<' );
			// Iterate over pricing rules and use the first one that has this product in conditions (or does not have if condition "not in list")
			if (isset($tm_RP_WCDPD->opt['pricing']['sets']) 
				&& count($tm_RP_WCDPD->opt['pricing']['sets']) ) {
				foreach ($tm_RP_WCDPD->opt['pricing']['sets'] as $rule_key => $rule) {
					if ($rule['method'] == 'quantity' && $validated_rule = RP_WCDPD_Pricing::validate_rule($rule)) {
						if ($dpd_version_compare){
							if ($validated_rule['selection_method'] == 'all' && $tm_RP_WCDPD->user_matches_rule($validated_rule['user_method'], $validated_rule['roles'])) {
								$selected_rule = $validated_rule;
								break;
							}
							if ($validated_rule['selection_method'] == 'categories_include' && count(array_intersect($tm_RP_WCDPD->get_product_categories($product->id), $validated_rule['categories'])) > 0 && $tm_RP_WCDPD->user_matches_rule($validated_rule['user_method'], $validated_rule['roles'])) {
								$selected_rule = $validated_rule;
								break;
							}
							if ($validated_rule['selection_method'] == 'categories_exclude' && count(array_intersect($tm_RP_WCDPD->get_product_categories($product->id), $validated_rule['categories'])) == 0 && $tm_RP_WCDPD->user_matches_rule($validated_rule['user_method'], $validated_rule['roles'])) {
								$selected_rule = $validated_rule;
								break;
							}
							if ($validated_rule['selection_method'] == 'products_include' && in_array($product->id, $validated_rule['products']) && $tm_RP_WCDPD->user_matches_rule($validated_rule['user_method'], $validated_rule['roles'])) {
								$selected_rule = $validated_rule;
								break;
							}
							if ($validated_rule['selection_method'] == 'products_exclude' && !in_array($product->id, $validated_rule['products']) && $tm_RP_WCDPD->user_matches_rule($validated_rule['user_method'], $validated_rule['roles'])) {
								$selected_rule = $validated_rule;
								break;
							}
						}else{
							if ($validated_rule['selection_method'] == 'all' && $tm_RP_WCDPD->user_matches_rule($validated_rule)) {
	                            $selected_rule = $validated_rule;
	                            break;
	                        }
	                        if ($validated_rule['selection_method'] == 'categories_include' && count(array_intersect($tm_RP_WCDPD->get_product_categories($product->id), $validated_rule['categories'])) > 0 && $tm_RP_WCDPD->user_matches_rule($validated_rule)) {
	                            $selected_rule = $validated_rule;
	                            break;
	                        }
	                        if ($validated_rule['selection_method'] == 'categories_exclude' && count(array_intersect($tm_RP_WCDPD->get_product_categories($product->id), $validated_rule['categories'])) == 0 && $tm_RP_WCDPD->user_matches_rule($validated_rule)) {
	                            $selected_rule = $validated_rule;
	                            break;
	                        }
	                        if ($validated_rule['selection_method'] == 'products_include' && in_array($product->id, $validated_rule['products']) && $tm_RP_WCDPD->user_matches_rule($validated_rule)) {
	                            $selected_rule = $validated_rule;
	                            break;
	                        }
	                        if ($validated_rule['selection_method'] == 'products_exclude' && !in_array($product->id, $validated_rule['products']) && $tm_RP_WCDPD->user_matches_rule($validated_rule)) {
	                            $selected_rule = $validated_rule;
	                            break;
	                        }
						}
					}
				}
			}
			
			if (is_array($selected_rule)) {

	            // Quantity
	            if ($selected_rule['method'] == 'quantity' && isset($selected_rule['pricing']) && in_array($selected_rule['quantities_based_on'], array('exclusive_product','exclusive_variation','exclusive_configuration')) ) {

		                if ($product->product_type == 'variable' || $product->product_type == 'variable-subscription') {
		                    $product_variations = $product->get_available_variations();
		                }

		                // For variable products only - check if prices differ for different variations
		                $multiprice_variable_product = false;

		                if ( ($product->product_type == 'variable' || $product->product_type == 'variable') && !empty($product_variations)) {
		                    $last_product_variation = array_slice($product_variations, -1);
		                    $last_product_variation_object = new WC_Product_Variable($last_product_variation[0]['variation_id']);
		                    $last_product_variation_price = $last_product_variation_object->get_price();

		                    foreach ($product_variations as $variation) {
		                        $variation_object = new WC_Product_Variable($variation['variation_id']);

		                        if ($variation_object->get_price() != $last_product_variation_price) {
		                            $multiprice_variable_product = true;
		                        }
		                    }
		                }

		                if ($multiprice_variable_product) {
		                    $variation_table_data = array();

		                    foreach ($product_variations as $variation) {
		                        $variation_product = new WC_Product_Variation($variation['variation_id']);
		                        $variation_table_data[$variation['variation_id']] = $tm_RP_WCDPD->pricing_table_calculate_adjusted_prices($selected_rule['pricing'], $variation_product->get_price());
		                    }
		                    $price=array();
		                    $price['is_multiprice']=true;
		                    $price['rules']=$variation_table_data;
		                }
		                else {
		                    if ($product->product_type == 'variable' && !empty($product_variations)) {
		                        $variation_product = new WC_Product_Variation($last_product_variation[0]['variation_id']);
		                        $table_data = $tm_RP_WCDPD->pricing_table_calculate_adjusted_prices($selected_rule['pricing'], $variation_product->get_price());
		                    }
		                    else {
		                        $table_data = $tm_RP_WCDPD->pricing_table_calculate_adjusted_prices($selected_rule['pricing'], $product->get_price());
		                    }
		                    $price=array();
		                    $price['is_multiprice']=false;
		                    $price['rules']=$table_data;
		                }	            	
	            }

	        }
	    }
        if ($field_price!==null){
        	$price=$field_price;
        }
        return $price;
	}

	private function print_price_fields( $product_id=0, $form_prefix="") {		
		if (!$product_id){
			global $product;
			if ($product){
				$product_id=$product->id;
			}
		}else{
			$product=wc_get_product($product_id);
		}
		if (!$product_id || empty($product) ){
			return;
		}
		if($this->cpf && $this->tm_epo_enable_final_total_box_all=="no"){
			$global_price_array = $this->cpf['global'];
			$local_price_array  = $this->cpf['local'];
			if (empty($global_price_array) && empty($local_price_array)){
				return;
			}
		}
		if ($form_prefix){	
			$form_prefix="_".$form_prefix;
		}

		// WooCommerce Dynamic Pricing & Discounts
		if(class_exists('RP_WCDPD') && !empty($GLOBALS['RP_WCDPD'])){			
			$price=$this->get_RP_WCDPD($product);
			if ($price){
				$price['product']=array();
				if ($price['is_multiprice']){
					foreach ($price['rules'] as $variation_id => $variation_rule) {
						foreach ($variation_rule as $rulekey => $pricerule) {
							$price['product'][$variation_id][]=array(
								"min"=>$pricerule["min"],
								"max"=>$pricerule["max"],
								"value"=>($pricerule["type"]!="percentage")?apply_filters( 'woocommerce_tm_epo_price', $pricerule["value"],""):$pricerule["value"],
								"type"=>$pricerule["type"]
								);
						}
					}
				}else{
					foreach ($price['rules'] as $rulekey => $pricerule) {
						$price['product'][0][]=array(
							"min"=>$pricerule["min"],
							"max"=>$pricerule["max"],
							"value"=>($pricerule["type"]!="percentage")?apply_filters( 'woocommerce_tm_epo_price', $pricerule["value"],""):$pricerule["value"],
							"type"=>$pricerule["type"]
							);
					}
				}
			}else{
				$price=array();
				$price['product']=array();
			}

			$price['price']=apply_filters( 'woocommerce_tm_epo_price', $product->get_price(),"");
            
		}else{

			if (class_exists('WC_Dynamic_Pricing')){
				$id = isset($product->variation_id) ? $product->variation_id : $product->id;
				$dp=WC_Dynamic_Pricing::instance();
				if ($dp && 
					is_object($dp) && property_exists($dp, "discounted_products") 
					&& isset($dp->discounted_products[$id]) ){
					$_price= $dp->discounted_products[$id];
				}else{
					$_price=$product->get_price();
				}

			}else{
				$_price=$product->get_price();
			}
			$price=array();
			$price['product']=array();
			$price['price']=apply_filters( 'woocommerce_tm_epo_price', $_price,"");
		}

			
		$variations = array();
		$variations_subscription_period = array();
		$variations_subscription_sign_up_fee = array();
		foreach ( $product->get_children() as $child_id ) {

			$variation = $product->get_child( $child_id );
			if ( ! $variation->exists() ){
				continue;
			}
			$variations[$child_id] = apply_filters( 'woocommerce_tm_epo_price', $variation->get_price(),"");
			$variations_subscription_period[$child_id] = $variation->subscription_period;
			$variations_subscription_sign_up_fee[$child_id] = $variation->subscription_sign_up_fee;
		}

		$is_subscription=false;
		$subscription_period='';
		$subscription_sign_up_fee=0;
		if (class_exists('WC_Subscriptions_Product')){
			if (WC_Subscriptions_Product::is_subscription( $product )){
				$is_subscription=true;
				$subscription_period = WC_Subscriptions_Product::get_period( $product );
				$subscription_sign_up_fee= WC_Subscriptions_Product::get_sign_up_fee( $product );
			}
		}

		global $woocommerce;
		$cart = $woocommerce->cart;
		$_tax  = new WC_Tax();
		$taxrates=$_tax->get_rates( $product->get_tax_class() );
		unset($_tax);
		$tax_rate=0;
		foreach ($taxrates as $key => $value) {
			$tax_rate=$tax_rate+floatval($value['rate']);
		}
		$taxable 			= $product->is_taxable();
		$tax_display_mode 	= get_option( 'woocommerce_tax_display_shop' );
		$tax_string 		= "";
		if ( $taxable ) {

			if ( $tax_display_mode == 'excl' ) {

				//if ( $cart->tax_total > 0 && $cart->prices_include_tax ) {
					$tax_string = ' <small>' . WC()->countries->ex_tax_or_vat() . '</small>';
				//}
		
			} else {

				//if ( $cart->tax_total > 0 && !$cart->prices_include_tax ) {
					$tax_string = ' <small>' . WC()->countries->inc_tax_or_vat() . '</small>';
				//}

			}

		}
		$force_quantity=0;
		if(isset($_GET['tm_cart_item_key'])){
			$cart_item_key = $_GET['tm_cart_item_key'];
			$cart_item = WC()->cart->get_cart_item( $cart_item_key );

			if (isset($cart_item["quantity"])){
				$force_quantity = $cart_item["quantity"];
			}
		}
		wc_get_template(
			'totals.php',
			array(
				'theme_name' 			=> $this->get_theme('Name'),
				'variations' 			=> esc_html(json_encode( (array) $variations ) ),
				'variations_subscription_period' => esc_html(json_encode( (array) $variations_subscription_period ) ),
				'variations_subscription_sign_up_fee' => esc_html(json_encode( (array) $variations_subscription_sign_up_fee ) ),
				'subscription_period' 	=> $subscription_period,
				'subscription_sign_up_fee' 	=> $subscription_sign_up_fee,
				'is_subscription' 		=> $is_subscription,
				'is_sold_individually' 	=> $product->is_sold_individually(),
				'hidden' 				=> ($this->tm_meta_cpf['override_final_total_box'])?(($this->tm_epo_final_total_box=='hide')?' hidden':''):(($this->tm_meta_cpf['override_final_total_box']=='hide')?' hidden':''),
				'form_prefix' 			=> $form_prefix,
				'type'  				=> esc_html( $product->product_type ),
				'price' 				=> esc_html( ( is_object( $product ) ? apply_filters( 'woocommerce_tm_final_price', $price['price'],$product ) : '' ) ),
				'taxable' 				=> $taxable,
				'tax_display_mode' 		=> $tax_display_mode,
				'prices_include_tax' 	=> $cart->prices_include_tax,
				'tax_rate' 				=> $tax_rate,
				'tax_string' 			=> $tax_string,
				'product_price_rules' 	=> esc_html(json_encode( (array) $price['product'] ) ),
				'fields_price_rules' 	=> ($this->tm_epo_dpd_enable=="no")?0:1,
				'force_quantity' 		=> $force_quantity
			) ,
			$this->_namespace,
			$this->template_path
		);
		
		if ($this->is_quick_view()){
			remove_filter( 'wp_footer', array( $this, 'tm_add_inline_style' ), 99999 );
			$this->tm_add_inline_style();
		}		

	}

	public function get_theme($var=''){
		$out='';
		if (function_exists('wp_get_theme')){
			$theme = wp_get_theme();
			if ($theme){
				$out=$theme->get( $var );
			}			
		}
		return $out;
	}
	
	public function tm_add_inline_style(){
		if (!empty($this->inline_styles)){
			echo '<style type="text/css">';
			echo $this->inline_styles;
			echo '</style>';
		}
	}

	public function upload_file($file) {
		include_once( ABSPATH . 'wp-admin/includes/file.php' );
		include_once( ABSPATH . 'wp-admin/includes/media.php' );
		add_filter( 'upload_dir',  array( $this, 'upload_dir_trick' ) );
		$upload = wp_handle_upload( $file, array( 'test_form' => false ) );
		remove_filter( 'upload_dir',  array( $this, 'upload_dir_trick' ) );
		return $upload;
	}

	public function upload_dir_trick( $param ) {
		global $woocommerce;
		
		$this->unique_dir=md5( $woocommerce->session->get_customer_id() );
		if ( empty( $param['subdir'] ) ) {
			$param['path']   = $param['path'] . $this->upload_dir . $this->unique_dir;
			$param['url']    = $param['url']. $this->upload_dir . $this->unique_dir;
			$param['subdir'] = $this->upload_dir . $this->unique_dir;
		} else {
			$subdir             = $this->upload_dir . $this->unique_dir;
			$param['path']   = str_replace( $param['subdir'], $subdir, $param['path'] );
			$param['url']    = str_replace( $param['subdir'], $subdir, $param['url'] );
			$param['subdir'] = str_replace( $param['subdir'], $subdir, $param['subdir'] );
		}
		return $param;
	}	

	/* APPEND name_inc functions (required for condition logic to check if an element is visible) */
	public function tm_fill_element_names($post_id=0, $global_epos=array(), $product_epos=array(), $form_prefix="") {
		$global_price_array = $global_epos;
		$local_price_array  = $product_epos;

		$global_prices=array( 'before'=>array(), 'after'=>array() );
		foreach ( $global_price_array as $priority=>$priorities ) {
			foreach ( $priorities as $pid=>$field ) {
				if (isset($field['sections']) && is_array($field['sections'])){
					foreach ( $field['sections'] as $section_id=>$section ) {
						if ( isset( $section['sections_placement'] ) ) {
							$global_prices[$section['sections_placement']][$priority][$pid]['sections'][$section_id]=$section;
						}
					}
				}
			}
		}
		$unit_counter  		= 0;
		$field_counter  	= 0;
		$element_counter	= 0;
		// global options before local
		foreach ( $global_prices['before'] as $priority=>$priorities ) {
			foreach ( $priorities as $pid=>$field ) {
				$args=array(
					'priority'  		=> $priority,
					'pid'  				=> $pid,
					'unit_counter'  	=> $unit_counter,
					'field_counter'  	=> $field_counter,
					'element_counter'  	=> $element_counter
				);
				$_return=$this->fill_builder_display( $global_epos, $field, 'before', $args , $form_prefix);
				extract( $_return, EXTR_OVERWRITE );
			}
		}
		// local options
		if ( is_array( $local_price_array ) && sizeof( $local_price_array ) > 0 ) {
			$attributes = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );
			if ( is_array( $attributes ) && count( $attributes )>0 ) {
				foreach ( $local_price_array as $field ) {
					if ( isset( $field['name'] ) && isset( $attributes[$field['name']] ) && !$attributes[$field['name']]['is_variation'] ) {
						$attribute=$attributes[$field['name']];
						$name_inc="";
						$field_counter=0;
						if ( $attribute['is_taxonomy'] ) {													
							switch ( $field['type'] ) {
							case "select":								
								$element_counter++;
								break;
							case "radio":
							case "checkbox":					
								$element_counter++;
								break;
							}
						} else {
							switch ( $field['type'] ) {
							case "select":
								$element_counter++;
								break;
							case "radio":
							case "checkbox":
								$element_counter++;
								break;
							}
						}
						$unit_counter++;
					}
				}
			}
		}
		// global options after local
		foreach ( $global_prices['after'] as $priority=>$priorities ) {
			foreach ( $priorities as $pid=>$field ) {
				$args=array(
					'priority'  		=> $priority,
					'pid'  				=> $pid,
					'unit_counter'  	=> $unit_counter,
					'field_counter'  	=> $field_counter,
					'element_counter'  	=> $element_counter
				);
				$_return=$this->fill_builder_display( $global_epos, $field, 'after', $args, $form_prefix );
				extract( $_return, EXTR_OVERWRITE );
			}
		}
		return $global_epos;
	}

	private function get_builder_element($element,$builder,$current_builder,$index=false,$alt="",$wpml_section_fields=array(),$identifier="sections"){
		$use_wpml=false;
		$use_original_builder=false;
		if(TM_EPO_WPML()->is_active() && $index!==false){
			if(isset( $current_builder[$identifier."_uniqid"] ) 
				&& isset($builder[$identifier."_uniqid"]) 
				&& isset($builder[$identifier."_uniqid"][$index]) ){
				// get index of element id in internal array
				$get_current_builder_uniqid_index = array_search($builder[$identifier."_uniqid"][$index], $current_builder[$identifier."_uniqid"] );
				if ($get_current_builder_uniqid_index!==NULL && $get_current_builder_uniqid_index!==FALSE){
					$index = $get_current_builder_uniqid_index;
					$use_wpml=true;
				}else{
					$use_original_builder=true;
				}
			}				
		}
		if ( isset($builder[$element]) ){
			if(!$use_original_builder && $use_wpml && ( (is_array($wpml_section_fields) && in_array($element, $wpml_section_fields)) || $wpml_section_fields===true)){
				if(isset($current_builder[$element])){
					if($index!==false){
						if(isset($current_builder[$element][$index])){
							return $current_builder[$element][$index];
						}else{
							return $alt;
						}
					}else{
						return $current_builder[$element];
					}
				}
			}
			if($index!==false){
				if(isset($builder[$element][$index])){
					return $builder[$element][$index];
				}else{
					return $alt;
				}
			}else{
				return $builder[$element];
			}
		}else{
			return $alt;
		}
		
	}

	/**
	 * Gets a list of all the Extra Product Options (local and global)
	 * for the specific $post_id.
	 */
	public function get_product_tm_epos( $post_id=0 ) {
		if ( empty( $post_id ) ) {
			return array();
		}

		$in_cat=array();

		$tmglobalprices=array();

		$terms = get_the_terms( $post_id, 'product_cat' );
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$in_cat[] = $term->term_id;
			}
		}

		// get all categories (no matter the language)
		$_all_categories = TM_EPO_WPML()->get_terms( null, 'product_cat', array( 'fields' => "ids", 'hide_empty' => false ) );

		if ( !$_all_categories ) {
			$_all_categories = array();
		}
		
		/* Get Local options */
		$args = array(
			'post_type'     => TM_EPO_LOCAL_POST_TYPE,
			'post_status'   => array( 'publish' ), // get only enabled extra options
			'numberposts'   => -1,
			'orderby'       => 'menu_order',
			'order'       	=> 'asc','suppress_filters' => true,
			'post_parent'   => floatval(TM_EPO_WPML()->get_original_id( $post_id ))
		);
		TM_EPO_WPML()->remove_sql_filter();
		$tmlocalprices = get_posts( $args );
		TM_EPO_WPML()->restore_sql_filter();

		if (empty($this->tm_meta_cpf['metainit'])){
			$this->set_tm_meta();
		}

		if (!$this->tm_meta_cpf['exclude']){
			
			$meta_array = TM_EPO_HELPER()->build_meta_query('OR','tm_meta_disable_categories',1,'!=', 'NOT EXISTS');
			
			$args = array(
				'post_type'     => TM_EPO_GLOBAL_POST_TYPE,
				'post_status'   => array( 'publish' ), // get only enabled global extra options
				'numberposts'   => -1,
				'orderby'       => 'date',
				'order'       	=> 'asc',
				'meta_query' => $meta_array
			);
			$tmp_tmglobalprices  = get_posts( $args );
						
			if ( $tmp_tmglobalprices ) {
				$wpml_tmp_tmglobalprices=array();
				$wpml_tmp_tmglobalprices_added=array();
				foreach ( $tmp_tmglobalprices as $price ) {
					/* Get Global options that belong to the product categories */		
					if(!empty($in_cat) && has_term( $in_cat, 'product_cat', $price) ) {
						$tmglobalprices[]=$price;
					}
					/* Get Global options that have no catergory set (they apply to all products) */
					if( !has_term( $_all_categories, 'product_cat', $price) ) {
						if (TM_EPO_WPML()->is_active()){
							$price_meta_lang=get_post_meta( $price->ID, TM_EPO_WPML_LANG_META, true );
							$original_product_id = floatval(TM_EPO_WPML()->get_original_id( $price->ID,$price->post_type ));
							if ($price_meta_lang==TM_EPO_WPML()->get_lang() 
								|| ($price_meta_lang=='' && TM_EPO_WPML()->get_lang()==TM_EPO_WPML()->get_default_lang()) 
								){
								$tmglobalprices[]=$price;
								if ($price_meta_lang!=TM_EPO_WPML()->get_default_lang() && $price_meta_lang!=''){
									$wpml_tmp_tmglobalprices_added[$original_product_id]=$price;
								}
							}else{
								if ($price_meta_lang==TM_EPO_WPML()->get_default_lang() || $price_meta_lang==''){
									$wpml_tmp_tmglobalprices[$original_product_id]=$price;
								}
							}
						}else{
							$tmglobalprices[]=$price;
						}
					}
				}
				// replace missing translation with original
				if (TM_EPO_WPML()->is_active()){
					$wpml_gp_keys = array_keys($wpml_tmp_tmglobalprices);
					foreach ($wpml_gp_keys as $key => $value) {
						if (!isset($wpml_tmp_tmglobalprices_added[$value])){
							$tmglobalprices[]=$wpml_tmp_tmglobalprices[$value];
						}
					}
				}

			}
			
			/* Get Global options that apply to the product */
			$args = array(
				'post_type'     => TM_EPO_GLOBAL_POST_TYPE,
				'post_status'   => array( 'publish' ), // get only enabled global extra options
				'numberposts'   => -1,
				'orderby'       => 'date',
				'order'       	=> 'asc',
				'meta_query' => array(
					array(
						'key' => 'tm_meta_product_ids',
						'value' => '"'.$post_id.'";',
						'compare' => 'LIKE'
					)
				)
			);
			$tmglobalprices_products = get_posts( $args );
			
			/* Merge Global options */
			if ( $tmglobalprices_products ) {
				$global_id_array=array();
				if ( isset($tmglobalprices) ) {
					foreach ( $tmglobalprices as $price ) {
						$global_id_array[]=$price->ID;
					}
				}else{
					$tmglobalprices=array();
				}
				foreach ( $tmglobalprices_products as $price ) {
					if (!in_array($price->ID, $global_id_array)){
						$tmglobalprices[]=$price;	
					}				
				}
			}
		}

		// Add current product to Global options array (has to be last to not conflict)
		$tmglobalprices[]=get_post($post_id);

		// End of DB init

		$product_epos=array();
		$global_epos=array();

		if ( $tmglobalprices ) {
			$wpml_section_fields=array();			
			foreach (TM_EPO_BUILDER()->_section_elements as $key => $value) {
				if(isset($value['id']) && empty($value['wpmldisable'])){
					$wpml_section_fields[$value['id']]=$value['id'];
				}
			}			
			
			foreach ( $tmglobalprices as $price ) {
				if (!is_object($price)){
					continue;
				}

				$original_product_id = $price->ID;
				if(TM_EPO_WPML()->is_active()){
					$wpml_is_original_product=TM_EPO_WPML()->is_original_product($price->ID,$price->post_type);
					if (!$wpml_is_original_product){
						$original_product_id = floatval(TM_EPO_WPML()->get_original_id( $price->ID,$price->post_type ));
					}
		        }

				$tmcp_id  	= absint( $original_product_id );
				$tmcp_meta	= get_post_meta( $original_product_id, 'tm_meta', true );

				$current_builder	= get_post_meta( $price->ID, 'tm_meta_wpml', true );
				if (!$current_builder){
					$current_builder=array();
				}else{
					if (!isset($current_builder['tmfbuilder'])){
						$current_builder['tmfbuilder'] = array();
					}
					$current_builder = $current_builder['tmfbuilder'];
				}

				$priority  	= isset( $tmcp_meta['priority'] )?absint( $tmcp_meta['priority'] ):1000;

				if ( isset( $tmcp_meta['tmfbuilder'] ) ) {

					$global_epos[$priority][$tmcp_id]['is_form']   	= 1;
					$global_epos[$priority][$tmcp_id]['is_taxonomy'] 	= 0;
					$global_epos[$priority][$tmcp_id]['name']    		= $price->post_title;
					$global_epos[$priority][$tmcp_id]['description'] 	= $price->post_excerpt;
					$global_epos[$priority][$tmcp_id]['sections'] 		= array();

					$builder=$tmcp_meta['tmfbuilder'];
					if ( is_array( $builder ) && count( $builder )>0 && isset( $builder['element_type'] ) && is_array( $builder['element_type'] ) && count( $builder['element_type'] )>0 ) {
						// All the elements
						$_elements=$builder['element_type'];
						// All element sizes
						$_div_size=$builder['div_size'];

						// All sections (holds element count for each section)
						$_sections=$builder['sections'];
						// All section sizes
						$_sections_size=$builder['sections_size'];
						// All section styles
						$_sections_style=$builder['sections_style'];
						// All section placements
						$_sections_placement=$builder['sections_placement'];


						if ( !is_array( $_sections ) ) {
							$_sections=array( count( $_elements ) );
						}
						if ( !is_array( $_sections_size ) ) {
							$_sections_size=array_fill(0, count( $_sections ) ,"w100");
						}
						if ( !is_array( $_sections_style ) ) {
							$_sections_style=array_fill(0, count( $_sections ) ,"");
						}
						if ( !is_array( $_sections_placement ) ) {
							$_sections_placement=array_fill(0, count( $_sections ) ,"before");
						}

						$_helper_counter=0;
						$_counter=array();

						for ( $_s = 0; $_s < count( $_sections ); $_s++ ) {
							$_sections_uniqid 	= $this->get_builder_element('sections_uniqid',$builder,$current_builder,$_s,TM_EPO_HELPER()->tm_temp_uniqid(count( $_sections )),$wpml_section_fields);
							
							$global_epos[$priority][$tmcp_id]['sections'][$_s]=array(
								'total_elements'		=> $_sections[$_s],
								'sections_size'			=> $_sections_size[$_s],
								'sections_style'		=> $_sections_style[$_s],
								'sections_placement'	=> $_sections_placement[$_s],
								'sections_uniqid'		=> $_sections_uniqid,
								'sections_clogic'		=> $this->get_builder_element('sections_clogic',$builder,$current_builder,$_s,false,$wpml_section_fields),
								'sections_logic'		=> $this->get_builder_element('sections_logic',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'sections_class'		=> $this->get_builder_element('sections_class',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'sections_type'			=> $this->get_builder_element('sections_type',$builder,$current_builder,$_s,"",$wpml_section_fields),

								'label_size'			=> $this->get_builder_element('section_header_size',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'label'					=> $this->get_builder_element('section_header_title',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'label_color' 			=> $this->get_builder_element('section_header_title_color',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'description' 			=> $this->get_builder_element('section_header_subtitle',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'description_position' 	=> $this->get_builder_element('section_header_subtitle_position',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'description_color' 	=> $this->get_builder_element('section_header_subtitle_color',$builder,$current_builder,$_s,"",$wpml_section_fields),
								'divider_type' 			=> $this->get_builder_element('section_divider_type',$builder,$current_builder,$_s,"",$wpml_section_fields),
							);

							for ( $k0 = $_helper_counter; $k0 < intval( $_helper_counter+intval( $_sections[$_s] ) ); $k0++ ) {
								$wpml_element_fields=array();
								if (isset(TM_EPO_BUILDER()->elements_array[$_elements[$k0]])){
									foreach (TM_EPO_BUILDER()->elements_array[$_elements[$k0]] as $key => $value) {
										if(isset($value['id']) && empty($value['wpmldisable'])){
											$wpml_element_fields[$value['id']]=$value['id'];
										}
									}
								}
								if ( isset( $_elements[$k0] ) && isset($this->tm_original_builder_elements[$_elements[$k0]]) ) {
									if ( !isset( $_counter[$_elements[$k0]] ) ) {
										$_counter[$_elements[$k0]]=0;
									}else {
										$_counter[$_elements[$k0]]++;
									}

									$_options=array();									
									$_regular_price=array();
									$_regular_price_filtered=array();
									$_regular_price_type=array();
									$_new_type=$_elements[$k0];
									$_prefix="";

									if ($this->tm_original_builder_elements[$_elements[$k0]]){
										if ($this->tm_original_builder_elements[$_elements[$k0]]["type"]=="single"){
											$_prefix=$_elements[$k0]."_";
											$_changes_product_image=$this->get_builder_element($_prefix.'changes_product_image',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]);
											$_use_images=$this->get_builder_element($_prefix.'use_images',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]);
											if ( empty( $builder[$_elements[$k0].'_price'][$_counter[$_elements[$k0]]] ) ) {
												$builder[$_elements[$k0].'_price'][$_counter[$_elements[$k0]]]=0;
											}

											$_price	= $builder[$_elements[$k0].'_price'][$_counter[$_elements[$k0]]];
											$_regular_price=array( array( wc_format_decimal( $_price, false, true ) ) );
											
											$_regular_price_type=isset($builder[$_elements[$k0].'_price_type'][$_counter[$_elements[$k0]]])?array( array( ( $builder[$_elements[$k0].'_price_type'][$_counter[$_elements[$k0]]] ) ) ):array();

											$_for_filter_price_type=isset($builder[$_elements[$k0].'_price_type'][$_counter[$_elements[$k0]]])?$builder[$_elements[$k0].'_price_type'][$_counter[$_elements[$k0]]] :"";

											$_price	= apply_filters( 'woocommerce_tm_epo_price2', $_price, $_for_filter_price_type );

											$_regular_price_filtered=array( array( wc_format_decimal( $_price, false, true ) ) );

										}elseif ($this->tm_original_builder_elements[$_elements[$k0]]["type"]=="multiple" || $this->tm_original_builder_elements[$_elements[$k0]]["type"]=="multipleall"  || $this->tm_original_builder_elements[$_elements[$k0]]["type"]=="multiplesingle"){
											$_prefix=$_elements[$k0]."_";
											$_changes_product_image=$this->get_builder_element($_prefix.'changes_product_image',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]);
											$_use_images=$this->get_builder_element($_prefix.'use_images',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]);

											if ( isset( $builder['multiple_'.$_elements[$k0].'_options_price'][$_counter[$_elements[$k0]]] ) ) {
												if ( empty( $builder['multiple_'.$_elements[$k0].'_options_price'][$_counter[$_elements[$k0]]] ) ) {
													$builder['multiple_'.$_elements[$k0].'_options_price'][$_counter[$_elements[$k0]]]=0;
												}

												$_prices=$builder['multiple_'.$_elements[$k0].'_options_price'][$_counter[$_elements[$k0]]];
												$_values=$this->get_builder_element('multiple_'.$_elements[$k0].'_options_value',$builder,$current_builder,$_counter[$_elements[$k0]],"",true,$_elements[$k0]);
												$_titles=$this->get_builder_element('multiple_'.$_elements[$k0].'_options_title',$builder,$current_builder,$_counter[$_elements[$k0]],"",true,$_elements[$k0]);
												$_images=$this->get_builder_element('multiple_'.$_elements[$k0].'_options_image',$builder,$current_builder,$_counter[$_elements[$k0]],array(),true,$_elements[$k0]);
												$_imagesp=$this->get_builder_element('multiple_'.$_elements[$k0].'_options_imagep',$builder,$current_builder,$_counter[$_elements[$k0]],array(),true,$_elements[$k0]);
												
												if ($_changes_product_image=="images" && $_use_images==""){
													$_imagesp=$_images;
													$_images=array();
													$_changes_product_image="custom";
												}

												$_url=$this->get_builder_element('multiple_'.$_elements[$k0].'_options_url',$builder,$current_builder,$_counter[$_elements[$k0]],array(),true,$_elements[$k0]);
												$_prices_type=$this->get_builder_element('multiple_'.$_elements[$k0].'_options_price_type',$builder,$current_builder,$_counter[$_elements[$k0]],array(),true,$_elements[$k0]);
												$_regular_price=array();
												$_regular_price_type=array();
												$_values_c=$_values;
												foreach ( $_prices as $_n=>$_price ) {
													$_regular_price[esc_attr( ( $_values[$_n] ) )."_".$_n]=array( wc_format_decimal( $_price, false, true ) );
													$_for_filter_price_type = isset($_prices_type[$_n])? $_prices_type[$_n] :"";
													$_price	= apply_filters( 'woocommerce_tm_epo_price2',$_price, $_for_filter_price_type);

													$_regular_price_filtered[esc_attr( ( $_values[$_n] ) )."_".$_n]=array( wc_format_decimal( $_price, false, true ) );
													$_regular_price_type[esc_attr( ( $_values[$_n] ) )."_".$_n]=isset($_prices_type[$_n])?array( ( $_prices_type[$_n] ) ):array('');
													$_options[esc_attr( ( $_values[$_n] ) )."_".$_n]=$_titles[$_n];	
													$_values_c[$_n]=$_values[$_n]."_".$_n;
												}
											}											
										}
									}
									$default_value ="";
									if(isset( $builder['multiple_'.$_elements[$k0].'_options_default_value'][$_counter[$_elements[$k0]]] )){
										$default_value = $builder['multiple_'.$_elements[$k0].'_options_default_value'][$_counter[$_elements[$k0]]];
									}elseif( isset($builder[$_prefix.'default_value']) && isset( $builder[$_prefix.'default_value'][$_counter[$_elements[$k0]]] ) ){
										$default_value = $builder[$_prefix.'default_value'][$_counter[$_elements[$k0]]];
									}
									$selectbox_fee=false;
									$selectbox_cart_fee=false;
									switch ( $_elements[$k0] ) {

									case "selectbox":
										$_new_type="select";
										$selectbox_fee=isset($builder[$_elements[$k0].'_price_type'][$_counter[$_elements[$k0]]])?array( array( ( $builder[$_elements[$k0].'_price_type'][$_counter[$_elements[$k0]]] ) ) ):false;
										$selectbox_cart_fee=isset($builder[$_elements[$k0].'_price_type'][$_counter[$_elements[$k0]]])?array( array( ( $builder[$_elements[$k0].'_price_type'][$_counter[$_elements[$k0]]] ) ) ):false;
										break;

									case "radiobuttons":
										$_new_type="radio";
										break;

									case "checkboxes":
										$_new_type="checkbox";
										break;

									}

									$_rules=$_regular_price;
									$_rules_filtered=$_regular_price_filtered;
									foreach ( $_regular_price as $key=>$value ) {
										foreach ( $value as $k=>$v ) {																						
											$_regular_price[$key][$k]=wc_format_localized_price( $v );
											$_regular_price_filtered[$key][$k]=wc_format_localized_price( $v );
										}
									}
									$_rules_type=$_regular_price_type;
									foreach ( $_regular_price_type as $key=>$value ) {
										foreach ( $value as $k=>$v ) {
											$_regular_price_type[$key][$k]= $v ;
										}
									}

									if ($_elements[$k0]!="header" && $_elements[$k0]!="divider"){										
										$global_epos[$priority][$tmcp_id]['sections'][$_s]['elements'][]=
										array_merge(
											TM_EPO_BUILDER()->get_custom_properties($builder,$_prefix,$_counter,$_elements,$k0),
										array(
											'builder' 			=> $builder,
											'section' 			=> $_sections_uniqid,
											'type'				=> $_new_type,
											'size'				=> $_div_size[$k0],
											'required'			=> $this->get_builder_element($_prefix.'required',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'use_images'		=> $_use_images,
											'use_url'			=> $this->get_builder_element($_prefix.'use_url',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'items_per_row'		=> $this->get_builder_element($_prefix.'items_per_row',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'label_size'		=> $this->get_builder_element($_prefix.'header_size',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'label'				=> $this->get_builder_element($_prefix.'header_title',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'label_position' 	=> $this->get_builder_element($_prefix.'header_title_position',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'label_color'		=> $this->get_builder_element($_prefix.'header_title_color',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'description'		=> $this->get_builder_element($_prefix.'header_subtitle',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'description_position' => $this->get_builder_element($_prefix.'header_subtitle_position',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'description_color'	=> $this->get_builder_element($_prefix.'header_subtitle_color',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'divider_type'		=> $this->get_builder_element($_prefix.'divider_type',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'placeholder'		=> $this->get_builder_element($_prefix.'placeholder',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'max_chars'			=> $this->get_builder_element($_prefix.'max_chars',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'hide_amount'		=> $this->get_builder_element($_prefix.'hide_amount',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'options'			=> $_options,
											'rules'				=> $_rules,
											'price_rules'		=> $_regular_price,
											'rules_filtered' 	=> $_rules_filtered,
											'price_rules_filtered' => $_regular_price_filtered,
											'price_rules_type'	=> $_regular_price_type,
											'rules_type'		=> $_rules_type,
											'images'			=> isset( $_images )?$_images:"",
											'imagesp'			=> isset( $_imagesp )?$_imagesp:"",
											'url' 				=> isset( $_url )?$_url:"",
											'limit'				=> $this->get_builder_element($_prefix.'limit_choices',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'exactlimit' 		=> $this->get_builder_element($_prefix.'exactlimit_choices',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'option_values'		=> isset( $_values_c )?$_values_c:array(),
											'button_type' 		=> $this->get_builder_element($_prefix.'button_type',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'uniqid' 			=> $this->get_builder_element($_prefix.'uniqid',$builder,$current_builder,$_counter[$_elements[$k0]],uniqid('', true) ,$wpml_element_fields,$_elements[$k0]),
											'clogic' 			=> $this->get_builder_element($_prefix.'clogic',$builder,$current_builder,$_counter[$_elements[$k0]],false,$wpml_element_fields,$_elements[$k0]),
											'logic' 			=> $this->get_builder_element($_prefix.'logic',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'format' 			=> $this->get_builder_element($_prefix.'format',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'start_year' 		=> $this->get_builder_element($_prefix.'start_year',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'end_year' 			=> $this->get_builder_element($_prefix.'end_year',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'min_date' 			=> $this->get_builder_element($_prefix.'min_date',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'max_date' 			=> $this->get_builder_element($_prefix.'max_date',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'disabled_dates' 	=> $this->get_builder_element($_prefix.'disabled_dates',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'disabled_weekdays' => $this->get_builder_element($_prefix.'disabled_weekdays',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'tranlation_day' 	=> $this->get_builder_element($_prefix.'tranlation_day',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'tranlation_month' 	=> $this->get_builder_element($_prefix.'tranlation_month',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'tranlation_year' 	=> $this->get_builder_element($_prefix.'tranlation_year',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											"default_value" 	=> $default_value,
											'text_after_price' 	=> $this->get_builder_element($_prefix.'text_after_price',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'selectbox_fee' 	=> $selectbox_fee,
											'selectbox_cart_fee' => $selectbox_cart_fee,
											'class' 			=> $this->get_builder_element($_prefix.'class',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'swatchmode' 		=> $this->get_builder_element($_prefix.'swatchmode',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'changes_product_image' => $_changes_product_image,
											'min' 				=> $this->get_builder_element($_prefix.'min',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'max' 				=> $this->get_builder_element($_prefix.'max',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'step' 				=> $this->get_builder_element($_prefix.'step',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'pips' 				=> $this->get_builder_element($_prefix.'pips',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'quantity' 			=> $this->get_builder_element($_prefix.'quantity',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
										));
									
									}elseif ($_elements[$k0]=="header"){

										$global_epos[$priority][$tmcp_id]['sections'][$_s]['elements'][]=array(
											'section' 			=> $_sections_uniqid,
											'type'				=> $_new_type,
											'size'				=> $_div_size[$k0],
											'required'			=> "",
											'use_images' 		=> "",
											'use_url' 			=> "",
											'items_per_row' 	=> "",
											'label_size'		=> $this->get_builder_element($_prefix.'header_size',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'label'				=> $this->get_builder_element($_prefix.'header_title',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'label_position' 	=> $this->get_builder_element($_prefix.'header_title_position',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'label_color'		=> $this->get_builder_element($_prefix.'header_title_color',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'description'		=> $this->get_builder_element($_prefix.'header_subtitle',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'description_color'	=> $this->get_builder_element($_prefix.'header_subtitle_color',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'description_position' => $this->get_builder_element($_prefix.'header_subtitle_position',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'divider_type'		=> "",
											'placeholder'		=> "",
											'max_chars'			=> "",
											'hide_amount'		=> "",
											"options"			=> $_options,
											'rules'				=> $_rules,
											'price_rules'		=> $_regular_price,
											'rules_filtered' 	=> $_rules_filtered,
											'price_rules_filtered' => $_regular_price_filtered,
											'price_rules_type'	=> $_regular_price_type,
											'rules_type'		=> $_rules_type,
											'images'			=> "",
											'limit'				=> "",
											'exactlimit' 		=> "",
											'option_values'		=> array(),
											'button_type' 		=>'',
											'class' 			=> $this->get_builder_element('header_class',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'uniqid' 			=> $this->get_builder_element('header_uniqid',$builder,$current_builder,$_counter[$_elements[$k0]],uniqid('', true) ,$wpml_element_fields,$_elements[$k0]), 
											'clogic' 			=> $this->get_builder_element('header_clogic',$builder,$current_builder,$_counter[$_elements[$k0]],false,$wpml_element_fields,$_elements[$k0]),
											'logic' 			=> $this->get_builder_element('header_logic',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'format' 			=> '',
											'start_year' 		=> '',
											'end_year' 			=> '',
											'tranlation_day' 	=> '',
											'tranlation_month' 	=> '',
											'tranlation_year' 	=> '',
											'swatchmode' 		=> "",
											'changes_product_image' => "",
											'min' 				=> "",
											'max' 				=> "",
											'step' 				=> "",
											'pips' 				=> "",

										);									

									}elseif ($_elements[$k0]=="divider"){

										$global_epos[$priority][$tmcp_id]['sections'][$_s]['elements'][]=array(
											'section' 			=> $_sections_uniqid,
											'type'				=> $_new_type,
											'size'				=> $_div_size[$k0],
											'required'			=> "",
											'use_images' 		=> "",
											'use_url' 			=> "",
											'items_per_row' 	=> "",
											'label_size'		=> "",
											'label'				=> "",
											'label_color'		=> "",
											'label_position' 	=> "",
											'description'		=> "",
											'description_color'	=> "",
											'divider_type'		=> $this->get_builder_element($_prefix.'divider_type',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'placeholder'		=> "",
											'max_chars'			=> "",
											'hide_amount'		=> "",
											"options"			=> $_options,
											'rules'				=> $_rules,
											'price_rules'		=> $_regular_price,
											'rules_filtered' 	=> $_rules_filtered,
											'price_rules_filtered' => $_regular_price_filtered,
											'price_rules_type'	=> $_regular_price_type,
											'rules_type'		=> $_rules_type,
											'images'			=> "",
											'limit'				=> "",
											'exactlimit' 		=> "",
											'option_values'		=> array(),
											'button_type'=>'',
											'class' 			=> $this->get_builder_element('divider_class',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'uniqid' 			=> $this->get_builder_element('divider_uniqid',$builder,$current_builder,$_counter[$_elements[$k0]],uniqid('', true),$wpml_element_fields,$_elements[$k0]),
											'clogic' 			=> $this->get_builder_element('divider_clogic',$builder,$current_builder,$_counter[$_elements[$k0]],false,$wpml_element_fields,$_elements[$k0]),
											'logic' 			=> $this->get_builder_element('divider_logic',$builder,$current_builder,$_counter[$_elements[$k0]],"",$wpml_element_fields,$_elements[$k0]),
											'format' 			=> '',
											'start_year' 		=> '',
											'end_year' 			=> '',
											'tranlation_day' 	=> '',
											'tranlation_month' 	=> '',
											'tranlation_year' 	=> '',
											'swatchmode' 		=> "",
											'changes_product_image' => "",
											'min' 				=> "",
											'max' 				=> "",
											'step' 				=> "",
											'pips' 				=> "",
										);									

									}
								}
							}

							$_helper_counter=intval( $_helper_counter+intval( $_sections[$_s] ) );

						}
					}
				}
			}
		}

		ksort( $global_epos );

		if ( $tmlocalprices ) {
			TM_EPO_WPML()->remove_sql_filter();
			$attributes = maybe_unserialize( get_post_meta( floatval(TM_EPO_WPML()->get_original_id( $post_id )), '_product_attributes', true ) );
			$wpml_attributes = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

			foreach ( $tmlocalprices as $price ) {
				$tmcp_id           								= absint( $price->ID );
				$tmcp_required          						= get_post_meta( $tmcp_id, 'tmcp_required', true );
				$tmcp_hide_price          						= get_post_meta( $tmcp_id, 'tmcp_hide_price', true );
				$tmcp_limit          							= get_post_meta( $tmcp_id, 'tmcp_limit', true );
				$product_epos[$tmcp_id]['is_form']  			= 0;
				$product_epos[$tmcp_id]['required']  			= empty( $tmcp_required )?0:1;
				$product_epos[$tmcp_id]['hide_price']  			= empty( $tmcp_hide_price )?0:1;
				$product_epos[$tmcp_id]['limit']  				= empty( $tmcp_limit )?"":$tmcp_limit;
				$product_epos[$tmcp_id]['name']   				= get_post_meta( $tmcp_id, 'tmcp_attribute', true );
				$product_epos[$tmcp_id]['is_taxonomy'] 			= get_post_meta( $tmcp_id, 'tmcp_attribute_is_taxonomy', true );
				$product_epos[$tmcp_id]['label']   				= wc_attribute_label( $product_epos[$tmcp_id]['name'] );
				$product_epos[$tmcp_id]['type']   				= get_post_meta( $tmcp_id, 'tmcp_type', true );
											
				// Retrieve attributes
				$product_epos[$tmcp_id]['attributes']  = array();
				$product_epos[$tmcp_id]['attributes_wpml']  = array();
				if ( $product_epos[$tmcp_id]['is_taxonomy'] ) {
					if ( !( $attributes[$product_epos[$tmcp_id]['name']]['is_variation'] ) ) {
						$all_terms = TM_EPO_WPML()->get_terms( null, $attributes[$product_epos[$tmcp_id]['name']]['name'] , 'orderby=name&hide_empty=0' );
						if ( $all_terms ) {
			                foreach ( $all_terms as $term ) {
			                    $has_term = has_term( (int) $term->term_id, $attributes[$product_epos[$tmcp_id]['name']]['name'], floatval(TM_EPO_WPML()->get_original_id( $post_id )) ) ? 1 : 0;
			                    $wpml_term_id = TM_EPO_WPML()->is_active()?icl_object_id($term->term_id,  $attributes[$product_epos[$tmcp_id]['name']]['name'], false):false;
			                    if ($has_term ){
			                        $product_epos[$tmcp_id]['attributes'][esc_attr( $term->slug )]=apply_filters( 'woocommerce_tm_epo_option_name', esc_html( $term->name ) ) ;
			                        if ($wpml_term_id){
										$wpml_term = get_term( $wpml_term_id, $attributes[$product_epos[$tmcp_id]['name']]['name'] );
										$product_epos[$tmcp_id]['attributes_wpml'][esc_attr( $term->slug )]=apply_filters( 'woocommerce_tm_epo_option_name', esc_html( $wpml_term->name ) ) ;
									}else{;
										$product_epos[$tmcp_id]['attributes_wpml'][esc_attr( $term->slug )]=$product_epos[$tmcp_id]['attributes'][esc_attr( $term->slug )];
									}
			                    }
			                }
			            }
						
					}
				}else {
					if ( isset( $attributes[$product_epos[$tmcp_id]['name']] ) ) {
						$options = array_map( 'trim', explode( WC_DELIMITER, $attributes[$product_epos[$tmcp_id]['name']]['value'] ) );
						$wpml_options = array_map( 'trim', explode( WC_DELIMITER, $wpml_attributes[$product_epos[$tmcp_id]['name']]['value'] ) );
						foreach ( $options as $k=>$option ) {
							$product_epos[$tmcp_id]['attributes'][esc_attr( sanitize_title( $option ) )]=esc_html( apply_filters( 'woocommerce_tm_epo_option_name', $option ) ) ;
							$product_epos[$tmcp_id]['attributes_wpml'][esc_attr( sanitize_title( $option ) )]=esc_html( apply_filters( 'woocommerce_tm_epo_option_name', isset($wpml_options[$k])?$wpml_options[$k]:$option ) ) ;
						}
					}
				}

				// Retrieve price rules
				$_regular_price=get_post_meta( $tmcp_id, '_regular_price', true );
				$_regular_price_type=get_post_meta( $tmcp_id, '_regular_price_type', true );
				$product_epos[$tmcp_id]['rules']=$_regular_price;
				
				$_regular_price_filtered= TM_EPO_HELPER()->array_map_deep($_regular_price, $_regular_price_type, array($this, 'tm_epo_price_filtered'));
				$product_epos[$tmcp_id]['rules_filtered']=$_regular_price_filtered;

				$product_epos[$tmcp_id]['rules_type']=$_regular_price_type;
				if ( !is_array( $_regular_price ) ) {
					$_regular_price=array();
				}
				if ( !is_array( $_regular_price_type ) ) {
					$_regular_price_type=array();
				}
				foreach ( $_regular_price as $key=>$value ) {
					foreach ( $value as $k=>$v ) {
						$_regular_price[$key][$k]=wc_format_localized_price( $v );						
					}
				}
				foreach ( $_regular_price_type as $key=>$value ) {
					foreach ( $value as $k=>$v ) {
						$_regular_price_type[$key][$k]= $v ;
					}
				}
				$product_epos[$tmcp_id]['price_rules']=$_regular_price;
				$product_epos[$tmcp_id]['price_rules_filtered']=$_regular_price_filtered;
				$product_epos[$tmcp_id]['price_rules_type']=$_regular_price_type;
			}
			TM_EPO_WPML()->restore_sql_filter();
		}
		$global_epos = $this->tm_fill_element_names($post_id,$global_epos, $product_epos, "");

		return array(
			'global'=> $global_epos,
			'local' => $product_epos
		);
	}

	/**
	 * Translate $attributes to post names.
	 */
	public function translate_fields( $attributes, $type, $section, $form_prefix="",$name_prefix="" ) {
		$fields=array();
		$loop=0;

		/* $form_prefix should be passed with _ if not empty */
		if ( !empty( $attributes ) ) {

			foreach ( $attributes as $key=>$attribute ) {
				$name_inc="";
				if ( !empty($this->tm_builder_elements[$type]["post_name_prefix"]) ){
					if ($this->tm_builder_elements[$type]["type"]=="multiple" || $this->tm_builder_elements[$type]["type"]=="multiplesingle"){
						$name_inc ="tmcp_".$name_prefix.$this->tm_builder_elements[$type]["post_name_prefix"]."_".$section.$form_prefix;
					}elseif ($this->tm_builder_elements[$type]["type"]=="multipleall"){
						$name_inc ="tmcp_".$name_prefix.$this->tm_builder_elements[$type]["post_name_prefix"]."_".$section."_".$loop.$form_prefix;
					}
				}				
				$fields[]=$name_inc;
				$loop++;
			}

		}else {
			if ( !empty($this->tm_builder_elements[$type]["type"]) && !empty($this->tm_builder_elements[$type]["post_name_prefix"]) ){
				$name_inc ="tmcp_".$name_prefix.$this->tm_builder_elements[$type]["post_name_prefix"]."_".$section.$form_prefix;
			}
			
			if (!empty($name_inc)){
				$fields[]=$name_inc;
			}

		}

		return $fields;
	}

	public function add_cart_item_data_loop($global_prices,$where,$cart_item_meta,$tmcp_post_fields,$product_id,$per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $form_prefix){
		
		foreach ( $global_prices[$where] as $priorities ) {
			foreach ( $priorities as $field ) {
				foreach ( $field['sections'] as $section_id=>$section ) {
					if ( isset( $section['elements'] ) ) {
						foreach ( $section['elements'] as $element ) {

							$init_class="TM_EPO_FIELDS_".$element['type'];
							if( !class_exists($init_class) && !empty($this->tm_builder_elements[$element['type']]["_is_addon"]) ){
								$init_class="TM_EPO_FIELDS";
							}
							if(class_exists($init_class)){
								$field_obj= new $init_class( $product_id, $element, $per_product_pricing, $cpf_product_price, $variation_id );

								/* Cart fees */
								$current_tmcp_post_fields=array_intersect_key(  $tmcp_post_fields , array_flip( $this->translate_fields( $element['options'], $element['type'], $field_loop, $form_prefix ,$this->cart_fee_name) )  );
								foreach ( $current_tmcp_post_fields as $attribute=>$key ) {
									if (!empty($field_obj->holder_cart_fees)){
										$cart_item_meta['tmcartfee'][] = $field_obj->add_cart_item_data_cart_fees($attribute, $key);	
									}
								}

								/* Subscription fees */
								$current_tmcp_post_fields=array_intersect_key(  $tmcp_post_fields , array_flip( $this->translate_fields( $element['options'], $element['type'], $field_loop, $form_prefix ,$this->fee_name) )  );							
								foreach ( $current_tmcp_post_fields as $attribute=>$key ) {;
									if (!empty($field_obj->holder_subscription_fees)){
										$cart_item_meta['tmcartepo'][] = $field_obj->add_cart_item_data_subscription_fees($attribute, $key);	
									}
									$cart_item_meta['tmsubscriptionfee'] = $this->tmfee;							
								}
								
								/* Normal fields */
								$current_tmcp_post_fields=array_intersect_key(  $tmcp_post_fields , array_flip( $this->translate_fields( $element['options'], $element['type'], $field_loop, $form_prefix ,"") )  );
								foreach ( $current_tmcp_post_fields as $attribute=>$key ) {
									if (!empty($field_obj->holder)){
										$cart_item_meta['tmcartepo'][] = $field_obj->add_cart_item_data($attribute, $key);	
									}								
								}

								unset($field_obj); // clear memory
							}

							if (in_array($element['type'], $this->element_post_types)   ){
								$field_loop++;
							}
							$loop++;							

						}
					}
				}
			}
		}
		return array('loop'=>$loop,'field_loop'=>$field_loop,'cart_item_meta'=>$cart_item_meta);

	}

	/* LOCAL FIELDS (to be deprecated) */
	public function add_cart_item_data_loop_local($local_price_array,$cart_item_meta,$tmcp_post_fields,$product_id,$per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $form_prefix){
		if ( ! empty( $local_price_array ) && is_array( $local_price_array ) && count( $local_price_array ) > 0 ) {

			if ( is_array( $tmcp_post_fields ) ) {

				foreach ( $local_price_array as $tmcp ) {
					if ( empty( $tmcp['type'] ) ) {
						continue;
					}

					$current_tmcp_post_fields=array_intersect_key(  $tmcp_post_fields , array_flip( $this->translate_fields( $tmcp['attributes'], $tmcp['type'], $field_loop, $form_prefix ) ) );

					foreach ( $current_tmcp_post_fields as $attribute=>$key ) {
						
						switch ( $tmcp['type'] ) {

						case "checkbox" :
						case "radio" :
						case "select" :
							$_price=$this->calculate_price( $tmcp, $key, $attribute, $per_product_pricing, $cpf_product_price, $variation_id );
							
							$cart_item_meta['tmcartepo'][] = array(
								'mode' 	=> 'local',
								'key' => $key,
								'is_taxonomy' => $tmcp['is_taxonomy'],
								'name'   => esc_html( $tmcp['name'] ),
								'value'  =>  esc_html( wc_attribute_label($tmcp['attributes_wpml'][$key])  ),
								'price'  => esc_attr( $_price ),
								'section'  => esc_html( $tmcp['name'] ),
								'section_label'  => esc_html( urldecode($tmcp['label']) ),
								'percentcurrenttotal' => isset($_POST[$attribute.'_hidden'])?1:0,
								'quantity' => 1
							);
							break;

						}
					}
					if (in_array($tmcp['type'], $this->element_post_types)   ){
						$field_loop++;
					}
					$loop++;

				}
			}
		}

		return array('loop'=>$loop,'field_loop'=>$field_loop,'cart_item_meta'=>$cart_item_meta);
	
	}

	public function get_posted_variation_id($form_prefix=""){
		$variation_id=null;
		if (isset($_POST['variation_id'.$form_prefix])){
			$variation_id=$_POST['variation_id'.$form_prefix];
		}
		return $variation_id;
	}
	/**
	 * Adds data to the cart.
	 */
	public function add_cart_item_data( $cart_item_meta, $product_id ) {

		/* Workaround to get unique items in cart for bto */
		$terms 			= get_the_terms( $product_id, 'product_type' );
		$product_type 	= ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';
		if ( ($product_type == 'bto' || $product_type == 'composite') && isset( $_REQUEST[ 'add-product-to-cart' ] ) && is_array( $_REQUEST[ 'add-product-to-cart' ] ) ) {
			$copy=array();
			foreach ( $_REQUEST[ 'add-product-to-cart' ] as $bundled_item_id => $bundled_product_id ) {
				$copy=array_merge($copy,TM_EPO_HELPER()->array_filter_key( $_POST ,$bundled_item_id,"end"));				
			}
			$copy=TM_EPO_HELPER()->array_filter_key( $copy);
			$cart_item_meta['tmcartepo_bto']=$copy;
		}

		$form_prefix="";
		$variation_id=false;
		$cpf_product_price=false;
		$per_product_pricing=true;

		if (isset($cart_item_meta['composite_item'])){
			global $woocommerce;
			$cart_contents = $woocommerce->cart->get_cart();

			if ( isset( $cart_item_meta[ 'composite_parent' ] ) && ! empty( $cart_item_meta[ 'composite_parent' ] ) ) {
				$parent_cart_key = $cart_item_meta[ 'composite_parent' ];
				$per_product_pricing 	= $cart_contents[ $parent_cart_key ][ 'data' ]->per_product_pricing;
				if ( $per_product_pricing == 'no' ) {
					$per_product_pricing=false;
				}
			}
			
			$form_prefix="_".$cart_item_meta['composite_item'];
			$bundled_item_id= $cart_item_meta['composite_item'];
			if (isset($_REQUEST[ 'bto_variation_id' ][ $bundled_item_id ])){
				$variation_id=$_REQUEST[ 'bto_variation_id' ][ $bundled_item_id ];
			}
			if (isset($_POST['cpf_bto_price'][$bundled_item_id])){
				$cpf_product_price=$_POST['cpf_bto_price'][$bundled_item_id];
			}
		}else{
			if (isset($_POST['variation_id'])){
				$variation_id=$_POST['variation_id'];
			}
			if (isset($_POST['cpf_product_price'])){
				$cpf_product_price=$_POST['cpf_product_price'];
			}		
		}

		$cpf_price_array  = $this->get_product_tm_epos( $product_id );
		$global_price_array = $cpf_price_array['global'];
		$local_price_array  = $cpf_price_array['local'];

		if ( empty($global_price_array) && empty($local_price_array) ){
			return $cart_item_meta;
		}

		if(in_array($product_type, array("simple","variable","subscription","variable-subscription"))){
			$cart_item_meta['tmhasepo']=1;
		}

		$global_prices=array( 'before'=>array(), 'after'=>array() );
		foreach ( $global_price_array as $priority=>$priorities ) {
			foreach ( $priorities as $pid=>$field ) {
				foreach ( $field['sections'] as $section_id=>$section ) {
					if ( isset( $section['sections_placement'] ) ) {
						$global_prices[$section['sections_placement']][$priority][$pid]['sections'][$section_id]=$section;
					}
				}
			}
		}

		$files=array();
		foreach ( $_FILES as $k=>$file){
			if (!empty($file['name'])){
				$files[$k]=$file['name'];
			}
		}

		$tmcp_post_fields = array_merge(TM_EPO_HELPER()->array_filter_key( $_POST ),TM_EPO_HELPER()->array_filter_key( $files ));
		if ( is_array( $tmcp_post_fields ) ) {
			$tmcp_post_fields = array_map( 'stripslashes_deep', $tmcp_post_fields );
		}

		if ( empty( $cart_item_meta['tmcartepo'] ) ) {
			$cart_item_meta['tmcartepo'] = array();
		}
		if ( empty( $cart_item_meta['tmsubscriptionfee'] ) ) {
			$cart_item_meta['tmsubscriptionfee'] = 0;
		}
		if ( empty( $cart_item_meta['tmcartfee'] ) ) {
			$cart_item_meta['tmcartfee'] = array();
		}

		$loop=0;
		$field_loop=0;

		$_return=$this->add_cart_item_data_loop($global_prices,'before',$cart_item_meta,$tmcp_post_fields,$product_id,$per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $form_prefix);
		extract( $_return, EXTR_OVERWRITE );

		/* LOCAL FIELDS (to be deprecated) */
		$_return=$this->add_cart_item_data_loop_local($local_price_array,$cart_item_meta,$tmcp_post_fields,$product_id,$per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $form_prefix);
		extract( $_return, EXTR_OVERWRITE );

		$_return=$this->add_cart_item_data_loop($global_prices,'after',$cart_item_meta,$tmcp_post_fields,$product_id,$per_product_pricing, $cpf_product_price, $variation_id, $field_loop, $loop, $form_prefix);
		extract( $_return, EXTR_OVERWRITE );

		return $cart_item_meta;
	}

	public function validate_product_id_loop($global_sections,$global_prices,$where,$tmcp_post_fields, $passed, $loop, $form_prefix){

		foreach ( $global_prices[$where] as $priorities ) {
			foreach ( $priorities as $field ) {
				foreach ( $field['sections'] as $section_id=>$section ) {
					if ( isset( $section['elements'] ) ) {
						foreach ( $section['elements'] as $element ) {
							
							if (in_array($element['type'], $this->element_post_types)   ){
								$loop++;
							}
							
							if ( $element['required'] && $this->tm_builder_elements[$element['type']]["is_post"]!="display" && $this->is_visible($element, $section, $global_sections, $form_prefix)) {
								$tmcp_attributes=$this->translate_fields( $element['options'], $element['type'], $loop, $form_prefix );
								$tmcp_attributes_fee=$this->translate_fields( $element['options'], $element['type'], $loop, $form_prefix,$this->cart_fee_name );
								
								$_passed = true;

								$init_class="TM_EPO_FIELDS_".$element['type'];
								if( !class_exists($init_class) && !empty($this->tm_builder_elements[$element['type']]["_is_addon"]) ){
									$init_class="TM_EPO_FIELDS";
								}								
								if (class_exists($init_class)){
									$field_obj= new $init_class();
									$_passed = $field_obj->validate_field($tmcp_post_fields,$element,$loop, $form_prefix);
									unset($field_obj); // clear memory
								}								

								if ( ! $_passed ) {

									$passed = false;
									wc_add_notice( sprintf( __( '"%s" is a required field.', TM_EPO_TRANSLATION ), $element['label'] ) , 'error' );
									
								}
							}
						}
					}
				}
			}
		}

		return array('loop'=>$loop,'passed'=>$passed);
	}

	public function validate_product_id( $product_id, $qty, $form_prefix="" ) {		

		$passed=true;

		if ($form_prefix){
			$form_prefix="_".$form_prefix;
		}
		if ($this->cpf){
			$cpf_price_array=$this->cpf;
		}else{
			$cpf_price_array=$this->get_product_tm_epos($product_id);
		}
		$global_price_array = $cpf_price_array['global'];
		$local_price_array  = $cpf_price_array['local'];
		if ( empty($global_price_array) && empty($local_price_array) ){
			return $passed;
		}
		$global_prices=array( 'before'=>array(), 'after'=>array() );
		$global_sections=array();
		foreach ( $global_price_array as $priority=>$priorities ) {
			foreach ( $priorities as $pid=>$field ) {
				if (isset($field['sections'])){
					foreach ( $field['sections'] as $section_id=>$section ) {
						if ( isset( $section['sections_placement'] ) ) {
							$global_prices[$section['sections_placement']][$priority][$pid]['sections'][$section_id]=$section;
							$global_sections[$section['sections_uniqid']]=$section;
						}
					}
				}
			}
		}

		if ( ( ! empty( $global_price_array ) && is_array( $global_price_array ) && count( $global_price_array ) > 0 ) || ( ! empty( $local_price_array ) && is_array( $local_price_array ) && count( $local_price_array ) > 0 ) ) {
			$tmcp_post_fields = TM_EPO_HELPER()->array_filter_key( $_POST);			
			if ( is_array( $tmcp_post_fields ) && !empty( $tmcp_post_fields ) && count( $tmcp_post_fields )>0  ) {
				$tmcp_post_fields = array_map( 'stripslashes_deep', $tmcp_post_fields );
			}
		

			$loop=-1;

			$_return=$this->validate_product_id_loop($global_sections,$global_prices,'before',$tmcp_post_fields, $passed, $loop, $form_prefix);
			extract( $_return, EXTR_OVERWRITE );
			// todo: move this code to a function
			if ( ! empty( $local_price_array ) && is_array( $local_price_array ) && count( $local_price_array ) > 0 ) {

				foreach ( $local_price_array as $tmcp ) {

					if (in_array($tmcp['type'], $this->element_post_types)   ){
						$loop++;
					}
					if ( empty( $tmcp['type'] ) || empty( $tmcp['required'] ) ) {
						continue;
					}

					if ( $tmcp['required'] ) {

						$tmcp_attributes=$this->translate_fields( $tmcp['attributes'], $tmcp['type'], $loop, $form_prefix );
						$_passed=true;

						switch ( $tmcp['type'] ) {

						case "checkbox" :
							$_check=array_intersect( $tmcp_attributes, array_keys( $tmcp_post_fields ) );
							if ( empty( $_check ) || count( $_check )==0 ) {
								$_passed = false;
							}
							break;

						case "radio" :
							foreach ( $tmcp_attributes as $attribute ) {
								if ( !isset( $tmcp_post_fields[$attribute] ) ) {
									$_passed = false;
								}
							}
							break;

						case "select" :
							foreach ( $tmcp_attributes as $attribute ) {
								if ( !isset( $tmcp_post_fields[$attribute] ) ||  $tmcp_post_fields[$attribute]=="" ) {
									$_passed = false;
								}
							}
							break;

						}

						if ( ! $_passed ) {
							$passed=false;
							wc_add_notice( sprintf( __( '"%s" is a required field.', TM_EPO_TRANSLATION ), $tmcp['label'] ) , 'error' );
							
						}
					}
				}

			}

			$_return=$this->validate_product_id_loop($global_sections,$global_prices,'after',$tmcp_post_fields, $passed, $loop, $form_prefix);
			extract( $_return, EXTR_OVERWRITE );

		}

		return $passed;
	}

	public function tm_woocommerce_product_single_add_to_cart_text(){
		return __('Update Cart','woocommerce');
	}
	/**
	 * Handles the display of builder sections.
	 */
	public function get_builder_display( $field, $where, $args, $form_prefix="", $product_id=0 ) {
		
		/* $form_prefix	shoud be passed with _ if not empty */			
		
		$columns=array(
			"w25"=>array( "col-3", 25 ),
			"w33"=>array( "col-4", 33 ),
			"w50"=>array( "col-6", 50 ),
			"w66"=>array( "col-8", 66 ),
			"w75"=>array( "col-9", 75 ),
			"w100"=>array( "col-12", 100 )
		);

		extract( $args, EXTR_OVERWRITE );

		if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {

			$args = array(
				'field_id'  => 'tm-epo-field-'.$unit_counter
			);
			wc_get_template(
				'builder-start.php',
				$args ,
				$this->_namespace,
				$this->template_path
			);

			$_section_totals=0;

			foreach ( $field['sections'] as $section ) {
				if ( !isset( $section['sections_placement'] ) || $section['sections_placement']!=$where ) {
					continue;
				}
				if ( isset( $section['sections_size'] ) && isset( $columns[$section['sections_size']] ) ) {
					$size=$columns[$section['sections_size']][0];
				}else {
					$size="col-12";
				}

				$_section_totals=$_section_totals+$columns[$section['sections_size']][1];
				if ( $_section_totals>100 ) {
					$_section_totals=$columns[$section['sections_size']][1];
					echo '<div class="cpfclear"></div>';
				}

				$divider="";
				if ( isset( $section['divider_type'] ) ) {
					switch ( $section['divider_type'] ) {
					case "hr":
						$divider='<hr>';
						break;
					case "divider":
						$divider='<div class="tm_divider"></div>';
						break;
					case "padding":
						$divider='<div class="tm_padding"></div>';
						break;
					}
				}
				$label_size='h3';
				if ( !empty( $section['label_size'] )){
					switch($section['label_size']){
						case "1":
							$label_size='h1';
						break;
						case "2":
							$label_size='h2';
						break;
						case "3":
							$label_size='h3';
						break;
						case "4":
							$label_size='h4';
						break;
						case "5":
							$label_size='h5';
						break;
						case "6":
							$label_size='h6';
						break;
						case "7":
							$label_size='p';
						break;
						case "8":
							$label_size='div';
						break;
						case "9":
							$label_size='span';
						break;
					}
				}
				$args = array(
					'column' 			=> $size,
					'style' 			=> $section['sections_style'],
					'uniqid' 			=> $section['sections_uniqid'],
					'logic' 			=> esc_html(json_encode( (array) json_decode( $section['sections_clogic']) ) ),
					'haslogic' 			=> $section['sections_logic'],
					'sections_class' 	=> $section['sections_class'],
					'sections_type' 	=> $section['sections_type'],
					'title_size'   		=> $label_size,
					'title'    			=> !empty( $section['label'] )? $section['label'] :"",
					'title_color'   	=> !empty( $section['label_color'] )? $section['label_color'] :"",
					'description'   	=> !empty( $section['description'] )?  $section['description']  :"",
					'description_color' => !empty( $section['description_color'] )? $section['description_color'] :"",
					'description_position' => !empty( $section['description_position'] )? $section['description_position'] :"",
					'divider'    		=> $divider							
				);
				// custom variations check
				if ( 
					isset( $section['elements'] ) 
					&& is_array( $section['elements'] ) 
					&& isset($section['elements'][0])
					&& is_array( $section['elements'][0] )
					&& isset($section['elements'][0]['type'])
					&& $section['elements'][0]['type']=='variations'
					) {
					$args['sections_class'] = $args['sections_class']." tm-epo-variation-section";
				}
				wc_get_template(
					'builder-section-start.php',
					$args ,
					$this->_namespace,
					$this->template_path
				);

				if ( isset( $section['elements'] ) && is_array( $section['elements'] ) ) {
					$totals=0;
					foreach ( $section['elements'] as $element ) {

						$empty_rules="";
						if ( isset( $element['rules_filtered'] ) ) {
							$empty_rules=esc_html( json_encode( ( $element['rules_filtered'] ) ) );
						}
						$empty_rules_type="";
						if ( isset( $element['rules_type'] ) ) {
							$empty_rules_type=esc_html( json_encode( ( $element['rules_type'] ) ) );
						}
						if ( isset( $element['size'] ) && isset( $columns[$element['size']] ) ) {
							$size=$columns[$element['size']][0];
						}else {
							$size="col-12";
						}
						
						$fee_name=$this->fee_name;
						$cart_fee_name=$this->cart_fee_name;
						$totals=$totals+$columns[$element['size']][1];
						if ( $totals>100 ) {
							$totals=$columns[$element['size']][1];
							echo '<div class="cpfclear"></div>';
						}
						$divider="";
						if ( isset( $element['divider_type'] ) ) {
							$divider_class="";
							if ( $element['type']=='divider' && !empty( $element['class'] ) ) {
								$divider_class=" ".$element['class'];
							}
							switch ( $element['divider_type'] ) {
							case "hr":
								$divider='<hr'.$divider_class.'>';
								break;
							case "divider":
								$divider='<div class="tm_divider'.$divider_class.'"></div>';
								break;
							case "padding":
								$divider='<div class="tm_padding'.$divider_class.'"></div>';
								break;
							}
						}
						$label_size='h3';
						if ( !empty( $element['label_size'] )){
							switch($element['label_size']){
								case "1":
									$label_size='h1';
								break;
								case "2":
									$label_size='h2';
								break;
								case "3":
									$label_size='h3';
								break;
								case "4":
									$label_size='h4';
								break;
								case "5":
									$label_size='h5';
								break;
								case "6":
									$label_size='h6';
								break;
								case "7":
									$label_size='p';
								break;
								case "8":
									$label_size='div';
								break;
								case "9":
									$label_size='span';
								break;
							}
						}

						$variations_builder_element_start_args = array();
						
						$args = array(
							'column'    		=> $size,
							'class'   			=> !empty( $element['class'] )? $element['class'] :"",
							'title_size'   		=> $label_size,
							'title'    			=> !empty( $element['label'] )? $element['label'] :"",
							'title_position'   	=> !empty( $element['label_position'] )? $element['label_position'] :"",
							'title_color'   	=> !empty( $element['label_color'] )? $element['label_color'] :"",
							'description'   	=> !empty( $element['description'] )?  $element['description']  :"",
							'description_color' => !empty( $element['description_color'] )? $element['description_color'] :"",
							'description_position' => !empty( $element['description_position'] )? $element['description_position'] :"",
							'divider'    		=> $divider,
							'required'    		=> esc_html( ( $element['required'] ) ),
							'type'        		=> $element['type'],
							'use_images'        => $element['use_images'],
							'use_url'        	=> $element['use_url'],
							'rules'       		=> $empty_rules,
							'rules_type' 		=> $empty_rules_type,
							'element'			=> $element['type'],
							'class_id'			=> "tm-element-ul-".$element['type']." element_".$element_counter.$form_prefix,// this goes on ul
							'uniqid' 			=> $element['uniqid'],
							'logic' 			=> esc_html(json_encode( (array) json_decode($element['clogic']) ) ),
							'haslogic' 			=> $element['logic'],
							'exactlimit' 		=> empty( $element['exactlimit'] )?"":'tm-exactlimit'
						);
						if ($element['type']!="variations"){
							wc_get_template(
								'builder-element-start.php',
								$args ,
								$this->_namespace,
								$this->template_path
							);
						}else{
							$variations_builder_element_start_args = $args;
							$css_string="table.variations{display:none;}";
							$this->inline_styles=$this->inline_styles.$css_string;
						}
						$field_counter=0;

						$init_class="TM_EPO_FIELDS_".$element['type'];
						if( !class_exists($init_class) && !empty($this->tm_builder_elements[$element['type']]["_is_addon"]) ){
							$init_class="TM_EPO_FIELDS";
						}						
						
						if (isset($this->tm_builder_elements[$element['type']]) 
							&& ($this->tm_builder_elements[$element['type']]["is_post"]=="post" || $this->tm_builder_elements[$element['type']]["is_post"]=="display")
							&& class_exists($init_class)
							){
							
							$field_obj= new $init_class();

							if ( $this->tm_builder_elements[$element['type']]["is_post"]=="post" ){
								
								if($this->tm_builder_elements[$element['type']]["type"]=="single" || $this->tm_builder_elements[$element['type']]["type"]=="multiplesingle"){
									
									$tabindex++;
									$name_inc =$this->tm_builder_elements[$element['type']]["post_name_prefix"]."_".$element_counter.$form_prefix;
									if($this->tm_builder_elements[$element['type']]["type"]=="single"){
										$is_fee=(isset( $element['rules_type'] ) && $element['rules_type'][0][0]=="subscriptionfee");
										$is_cart_fee=(isset( $element['rules_type'] ) && isset($element['rules_type'][0]) && isset($element['rules_type'][0][0]) && $element['rules_type'][0][0]=="fee");
									}elseif($this->tm_builder_elements[$element['type']]["type"]=="multiplesingle"){
										$is_fee=(isset( $element['selectbox_fee'] ) && $element['selectbox_fee'][0][0]=="subscriptionfee");
										$is_cart_fee=(isset( $element['selectbox_cart_fee'] ) && $element['selectbox_cart_fee'][0][0]=="fee");
									}
									if ($is_fee){
										$name_inc = $fee_name.$name_inc;
									}elseif ($is_cart_fee){
										$name_inc = $cart_fee_name.$name_inc;
									}


									if ( isset ( $_GET['switch-subscription'] ) && class_exists('WC_Subscriptions_Manager') && class_exists('WC_Subscriptions_Order') ) {
										$subscription = WC_Subscriptions_Manager::get_subscription( $_GET['switch-subscription'] );
										$original_order = new WC_Order( $subscription['order_id'] );
										$item           = WC_Subscriptions_Order::get_item_by_product_id( $original_order, $subscription['product_id'] );
										$saved_data=maybe_unserialize($item["item_meta"]["_tmcartepo_data"][0]);
										foreach ($saved_data as $key => $val) {
											if (isset($val["key"])){
														if ($element['uniqid']==$val["section"] ){
															$_GET['tmcp_'.$name_inc]=$val["key"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}else{
														if ($element['uniqid']==$val["section"]){
															$_GET['tmcp_'.$name_inc]=$val["value"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}
										}
									}elseif ( !empty( $_GET['tm_cart_item_key']) && isset($_GET['_wpnonce']) && wp_verify_nonce( $_GET['_wpnonce'], 'tm-edit' ) ){
										add_filter('woocommerce_product_single_add_to_cart_text',array($this,'tm_woocommerce_product_single_add_to_cart_text'),9999);
										$_cart=WC()->cart;
										if(isset($_cart->cart_contents) && isset($_cart->cart_contents[$_GET['tm_cart_item_key']]) ){
											if(!empty($_cart->cart_contents[$_GET['tm_cart_item_key']]['tmcartepo'])){
												$saved_epos=$_cart->cart_contents[$_GET['tm_cart_item_key']]['tmcartepo'];												
												foreach ($saved_epos as $key => $val) {
													if (isset($val["key"])){
														if ($element['uniqid']==$val["section"] ){
															$_GET['tmcp_'.$name_inc]=$val["key"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}else{
														if ($element['uniqid']==$val["section"]){
															$_GET['tmcp_'.$name_inc]=$val["value"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}														
												}												
											}
											if(!empty($_cart->cart_contents[$_GET['tm_cart_item_key']]['tmcartfee'])){
												$saved_fees=$_cart->cart_contents[$_GET['tm_cart_item_key']]['tmcartfee'];
												foreach ($saved_fees as $key => $val) {
													if (isset($val["key"])){
														if ($element['uniqid']==$val["section"] ){
															$_GET['tmcp_'.$name_inc]=$val["key"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}else{
														if ($element['uniqid']==$val["section"]){
															$_GET['tmcp_'.$name_inc]=$val["value"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}
												}
											}
											if(!empty($_cart->cart_contents[$_GET['tm_cart_item_key']]['tmsubscriptionfee'])){
												$saved_subscriptionfees=$_cart->cart_contents[$_GET['tm_cart_item_key']]['tmsubscriptionfee'];
												foreach ($saved_subscriptionfees as $key => $val) {
													if (isset($val["key"])){
														if ($element['uniqid']==$val["section"] ){
															$_GET['tmcp_'.$name_inc]=$val["key"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}else{
														if ($element['uniqid']==$val["section"]){
															$_GET['tmcp_'.$name_inc]=$val["value"];
															if (isset($val['quantity'])){
																$_GET['tmcp_'.$name_inc.'_quantity']=$val['quantity'];
															}
														}
													}
												}
											}
										}
									}

									$display = $field_obj->display_field($element, array(
											'name_inc'=>$name_inc, 
											'element_counter'=>$element_counter, 
											'tabindex'=>$tabindex, 
											'form_prefix'=>$form_prefix, 
											'field_counter'=>$field_counter) );

									if (is_array($display)){
										$args = array(
											'id'    		=> 'tmcp_'.$this->tm_builder_elements[$element['type']]["post_name_prefix"].'_'.$tabindex.$form_prefix,
											'name'    		=> 'tmcp_'.$name_inc,
											'class'   		=> !empty( $element['class'] )? $element['class'] :"",								
											'tabindex'  	=> $tabindex,
											'rules'   		=> isset( $element['rules_filtered'] )?esc_html( json_encode( ( $element['rules_filtered'] ) ) ):'',
											'rules_type'   	=> isset( $element['rules_type'] )?esc_html( json_encode( ( $element['rules_type'] ) ) ):'',
											'amount'     	=> '0 '.$_currency,
											'fieldtype' 	=> $is_fee?$this->fee_name_class:($is_cart_fee?$this->cart_fee_class:"tmcp-field")
										);

										$args=array_merge($args,$display);
										
										if( $this->tm_builder_elements[$element['type']]["_is_addon"] ){
											do_action( "tm_epo_display_addons" , $element, $args, array(
											'name_inc'=>$name_inc, 
											'element_counter'=>$element_counter, 
											'tabindex'=>$tabindex, 
											'form_prefix'=>$form_prefix, 
											'field_counter'=>$field_counter) );
										}

										elseif(is_readable($this->template_path.$element['type'].'.php')){
											wc_get_template(
												$element['type'].'.php',
												$args ,
												$this->_namespace,
												$this->template_path
											);
										}										
									}

								}elseif($this->tm_builder_elements[$element['type']]["type"]=="multipleall" || $this->tm_builder_elements[$element['type']]["type"]=="multiple"){
									
									$field_obj->display_field_pre($element, array(
											'element_counter'=>$element_counter, 
											'tabindex'=>$tabindex, 
											'form_prefix'=>$form_prefix, 
											'field_counter'=>$field_counter) );

									foreach ( $element['options'] as $value=>$label ) {

										$tabindex++;
										if ($this->tm_builder_elements[$element['type']]["type"]=="multipleall"){
											$name_inc = $this->tm_builder_elements[$element['type']]["post_name_prefix"]."_".$element_counter."_".$field_counter.$form_prefix;
										}else{
											$name_inc = $this->tm_builder_elements[$element['type']]["post_name_prefix"]."_".$element_counter.$form_prefix;
										}										
										
										$is_fee=(isset( $element['rules_type'][$value] ) && $element['rules_type'][$value][0]=="subscriptionfee");
										$is_cart_fee=(isset( $element['rules_type'][$value]) && $element['rules_type'][$value][0]=="fee");
										if ($is_fee){
											$name_inc = $fee_name.$name_inc;
										}elseif($is_cart_fee){
											$name_inc = $cart_fee_name.$name_inc;
										}

										$display = $field_obj->display_field($element, array(
											'name_inc'=>$name_inc, 
											'value'=>$value, 
											'label'=>$label, 
											'element_counter'=>$element_counter, 
											'tabindex'=>$tabindex, 
											'form_prefix'=>$form_prefix, 
											'field_counter'=>$field_counter) );

										if (is_array($display)){
											$args = array(
												'id'    		=> 'tmcp_'.$this->tm_builder_elements[$element['type']]["post_name_prefix"].'_'.$element_counter."_".$field_counter."_".$tabindex.$form_prefix,
												'name'    		=> 'tmcp_'.$name_inc,
												'class'   		=> !empty( $element['class'] )? $element['class'] :"",								
												'tabindex'  	=> $tabindex,
												'rules'   		=> isset( $element['rules_filtered'][$value] )?esc_html( json_encode( ( $element['rules_filtered'][$value] ) ) ):'',
												'rules_type' 	=> isset( $element['rules_type'][$value] )?esc_html( json_encode( ( $element['rules_type'][$value] ) ) ):'',
												'amount'     	=> '0 '.$_currency,
												'fieldtype' 	=> $is_fee?$this->fee_name_class:($is_cart_fee?$this->cart_fee_class:"tmcp-field"),
												'border_type' 	=> $this->tm_epo_css_selected_border
											);

											$args=array_merge($args,$display);
											if ( isset ( $_GET['switch-subscription'] ) && class_exists('WC_Subscriptions_Manager') && class_exists('WC_Subscriptions_Order') ) {
												$subscription = WC_Subscriptions_Manager::get_subscription( $_GET['switch-subscription'] );
												$original_order = new WC_Order( $subscription['order_id'] );
												$item           = WC_Subscriptions_Order::get_item_by_product_id( $original_order, $subscription['product_id'] );
												$saved_data=maybe_unserialize($item["item_meta"]["_tmcartepo_data"][0]);
												foreach ($saved_data as $key => $val) {
													if ($element['uniqid']==$val["section"] && $args["value"]==$val["key"]){
														$_GET[$args['name']]=$val["key"];
														if (isset($val['quantity'])){
															$_GET[$args['name'].'_quantity']=$val['quantity'];
														}
													}
												}
											}elseif ( !empty( $_GET['tm_cart_item_key']) && isset($_GET['_wpnonce']) && wp_verify_nonce( $_GET['_wpnonce'], 'tm-edit' ) ){
												add_filter('woocommerce_product_single_add_to_cart_text',array($this,'tm_woocommerce_product_single_add_to_cart_text'),9999);
												$_cart=WC()->cart;
												if(isset($_cart->cart_contents) && isset($_cart->cart_contents[$_GET['tm_cart_item_key']]) ){
													if(!empty($_cart->cart_contents[$_GET['tm_cart_item_key']]['tmcartepo'])){
														$saved_epos=$_cart->cart_contents[$_GET['tm_cart_item_key']]['tmcartepo'];
														foreach ($saved_epos as $key => $val) {
															if ($element['uniqid']==$val["section"] && $args["value"]==$val["key"]){ 
																$_GET[$args['name']]=$val["key"];
																if (isset($val['quantity'])){
																	$_GET[$args['name'].'_quantity']=$val['quantity'];
																}
															}
														}
													}
													if(!empty($_cart->cart_contents[$_GET['tm_cart_item_key']]['tmcartfee'])){
														$saved_fees=$_cart->cart_contents[$_GET['tm_cart_item_key']]['tmcartfee'];
														foreach ($saved_fees as $key => $val) {
															if ($element['uniqid']==$val["section"] && $args["value"]==$val["key"]){
																$_GET[$args['name']]=$val["key"];
																if (isset($val['quantity'])){
																	$_GET[$args['name'].'_quantity']=$val['quantity'];
																}
															}
														}
													}
													if(!empty($_cart->cart_contents[$_GET['tm_cart_item_key']]['tmsubscriptionfee'])){
														$saved_subscriptionfees=$_cart->cart_contents[$_GET['tm_cart_item_key']]['tmsubscriptionfee'];
														foreach ($saved_subscriptionfees as $key => $val) {
															if ($element['uniqid']==$val["section"] && $args["value"]==$val["key"]){
																$_GET[$args['name']]=$val["key"];
																if (isset($val['quantity'])){
																	$_GET[$args['name'].'_quantity']=$val['quantity'];
																}
															}
														}
													}
												}
											}
											if( $this->tm_builder_elements[$element['type']]["_is_addon"] ){
												do_action( "tm_epo_display_addons" , $element, $args, array(
												'name_inc'=>$name_inc, 
												'element_counter'=>$element_counter, 
												'tabindex'=>$tabindex, 
												'form_prefix'=>$form_prefix, 
												'field_counter'=>$field_counter,
												'border_type'=> $this->tm_epo_css_selected_border) );
											}

											elseif(is_readable($this->template_path.$element['type'].'.php')){
												wc_get_template(
													$element['type'].'.php',
													$args ,
													$this->_namespace,
													$this->template_path
												);
											}
										}

										$field_counter++;

									}

								}

								$element_counter++;
								
							}elseif ( $this->tm_builder_elements[$element['type']]["is_post"]=="display" ){
								
								$display = $field_obj->display_field($element, array(
											'element_counter'=>$element_counter, 
											'tabindex'=>$tabindex, 
											'form_prefix'=>$form_prefix, 
											'field_counter'=>$field_counter) );

								if (is_array($display)){
									$args = array(
										'class'   			=> !empty( $element['class'] )? $element['class'] :"",
										'form_prefix' 		=> $form_prefix,
										'field_counter' 	=> $field_counter,
										'tm_element' 		=> $element,
										'tm__namespace' 	=> $this->_namespace,
										'tm_template_path' 	=> $this->template_path,
										'tm_product_id' 	=> $product_id
									);

									if ($element['type']=="variations"){
										$args["variations_builder_element_start_args"] = $variations_builder_element_start_args;
										$args["variations_builder_element_end_args"] = array(
											'element' 				=> $element['type'],
											'description'   		=> !empty( $element['description'] )?  $element['description']  :"",
											'description_color' 	=> !empty( $element['description_color'] )? $element['description_color'] :"",
											'description_position' 	=> !empty( $element['description_position'] )? $element['description_position'] :"",
										);
									}

									$args=array_merge($args,$display);

									if( $this->tm_builder_elements[$element['type']]["_is_addon"] ){
										do_action( "tm_epo_display_addons" , $element, $args, array(
										'name_inc'=>$name_inc, 
										'element_counter'=>$element_counter, 
										'tabindex'=>$tabindex, 
										'form_prefix'=>$form_prefix, 
										'field_counter'=>$field_counter) );
									}

									elseif(is_readable($this->template_path.$element['type'].'.php')){
										wc_get_template(
											$element['type'].'.php',
											$args ,
											$this->_namespace,
											$this->template_path
										);
									}
								}
							}

							unset($field_obj); // clear memory	
						}

						if ($element['type']!="variations"){
							wc_get_template(
								'builder-element-end.php',
								array(
									'element' 				=> $element['type'],
									'description'   		=> !empty( $element['description'] )?  $element['description']  :"",
									'description_color' 	=> !empty( $element['description_color'] )? $element['description_color'] :"",
									'description_position' 	=> !empty( $element['description_position'] )? $element['description_position'] :"",
								) ,
								$this->_namespace,
								$this->template_path
							);
						}

					}
				}
				$args = array(
					'column' 		=> $size,
					'style' 		=> $section['sections_style'],
					'sections_type' => $section['sections_type']
				);
				wc_get_template(
					'builder-section-end.php',
					$args ,
					$this->_namespace,
					$this->template_path
				);

			}

			wc_get_template(
				'builder-end.php',
				array() ,
				$this->_namespace,
				$this->template_path
			);

			$unit_counter++;

		}
		return array(
			'tabindex'   		=> $tabindex,
			'unit_counter'  	=> $unit_counter,
			'field_counter'  	=> $field_counter,
			'element_counter'  	=> $element_counter,
			'_currency'   		=> $_currency
		);

	}

	public function fill_builder_display( $global_epos, $field, $where, $args, $form_prefix="" ) {
		/* $form_prefix	shoud be passed with _ if not empty */			
		extract( $args, EXTR_OVERWRITE );
		if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {
			foreach ( $field['sections'] as $_s => $section ) {
				if ( !isset( $section['sections_placement'] ) || $section['sections_placement']!=$where ) {
					continue;
				}
				if ( isset( $section['elements'] ) && is_array( $section['elements'] ) ) {
					foreach ( $section['elements'] as $arr_element_counter=>$element ) {
						$fee_name=$this->fee_name;
						$cart_fee_name=$this->cart_fee_name;
						$field_counter=0;
						
						if ( isset($this->tm_builder_elements[$element['type']]) && $this->tm_builder_elements[$element['type']]["is_post"]=="post" ){

							if ($this->tm_builder_elements[$element['type']]["type"]=="multipleall" || $this->tm_builder_elements[$element['type']]["type"]=="multiple"){

								foreach ( $element['options'] as $value=>$label ) {

									if ($this->tm_builder_elements[$element['type']]["type"]=="multipleall"){
										$name_inc = $this->tm_builder_elements[$element['type']]["post_name_prefix"]."_".$element_counter."_".$field_counter.$form_prefix;
									}else{
										$name_inc = $this->tm_builder_elements[$element['type']]["post_name_prefix"]."_".$element_counter.$form_prefix;
									}

									$is_fee=(isset( $element['rules_type'][$value] ) && $element['rules_type'][$value][0]=="subscriptionfee");
									$is_cart_fee=(isset( $element['rules_type'][$value] ) && $element['rules_type'][$value][0]=="fee");
									if ($is_fee){
										$name_inc = $fee_name.$name_inc;
									}elseif($is_cart_fee){
										$name_inc = $cart_fee_name.$name_inc;
									}
									$name_inc = 'tmcp_'.$name_inc.$form_prefix;
									$global_epos[$priority][$pid]['sections'][$_s]['elements'][$arr_element_counter]['name_inc'][]=$name_inc;
									$global_epos[$priority][$pid]['sections'][$_s]['elements'][$arr_element_counter]['is_fee'][]=$is_fee;
									$global_epos[$priority][$pid]['sections'][$_s]['elements'][$arr_element_counter]['is_cart_fee'][]=$is_cart_fee;

									$field_counter++;

								}

							}elseif ($this->tm_builder_elements[$element['type']]["type"]=="single" || $this->tm_builder_elements[$element['type']]["type"]=="multiplesingle"){

								$name_inc = $this->tm_builder_elements[$element['type']]["post_name_prefix"]."_".$element_counter.$form_prefix;
								if($this->tm_builder_elements[$element['type']]["type"]=="single"){
									$is_fee=(isset( $element['rules_type'] ) && $element['rules_type'][0][0]=="subscriptionfee");
									$is_cart_fee=(isset( $element['rules_type'] ) && isset($element['rules_type'][0]) && isset($element['rules_type'][0][0]) && $element['rules_type'][0][0]=="fee");
								}elseif($this->tm_builder_elements[$element['type']]["type"]=="multiplesingle"){
									$is_fee=(isset( $element['selectbox_fee'] ) && $element['selectbox_fee'][0][0]=="subscriptionfee");
									$is_cart_fee=(isset( $element['selectbox_cart_fee'] ) && $element['selectbox_cart_fee'][0][0]=="fee");
								}
								if ($is_fee){
									$name_inc = $fee_name.$name_inc;
								}elseif ($is_cart_fee){
									$name_inc = $cart_fee_name.$name_inc;
								}
								$name_inc = 'tmcp_'.$name_inc.$form_prefix;
								$global_epos[$priority][$pid]['sections'][$_s]['elements'][$arr_element_counter]['name_inc']=$name_inc;
								$global_epos[$priority][$pid]['sections'][$_s]['elements'][$arr_element_counter]['is_fee']=$is_fee;
								$global_epos[$priority][$pid]['sections'][$_s]['elements'][$arr_element_counter]['is_cart_fee']=$is_cart_fee;
								
							}
							$element_counter++;
						}
											
					}
				}				
			}
			$unit_counter++;
		}
		return array(
			'global_epos' 		=> $global_epos,
			'unit_counter'  	=> $unit_counter,
			'field_counter'  	=> $field_counter,
			'element_counter'  	=> $element_counter
		);

	}

}

define('TM_EPO_INCLUDED',1);
?>