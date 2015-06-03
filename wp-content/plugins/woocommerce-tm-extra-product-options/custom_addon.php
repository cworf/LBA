<?php
// don't load directly
if (!defined('ABSPATH')) die();

class TM_Extension_Class {
    function __construct() {
        // Register addon hook
        add_action( 'tm_epo_register_addons', array( $this, 'register_addon' ) );
 
        // Register CSS and JS
        //add_action( 'tm_epo_register_addons_scripts', array( $this, 'register_css_js' ) );
        
        // Display the addon
        add_action( 'tm_epo_display_addons', array( $this, 'display_addon' ), 10, 3 );
    }

    /*
     * Register addon to EPO
     */
    public function register_addon(){
        TM_EPO_BUILDER()->register_addon(
            array(
                "name" => "tm_button",
                
                "options" => array( 
                                "name"                  => __( "TM Buttom", TM_EPO_TRANSLATION ),
                                "width"                 => "w100",
                                "width_display"         => "1/1",
                                "icon"                  => "fa-terminal",
                                "is_post"               => "post",
                                "type"                  => "single",
                                "post_name_prefix"      => "tm_button",
                                "fee_type"              => "single",
                                "subscription_fee_type" => "single" ),
                
                "settings"=>array(
                    "required",
                    "price",
                    "text_after_price",
                    "price_type6",
                    "hide_amount",
                    array(
                        "id" => "my_custom_setting",
                        "default" => "",
                        "type" => "text",
                        "label" => __( 'My Custom Setting name', TM_EPO_TRANSLATION ),
                        "desc" => __( 'My Custom Setting description.', TM_EPO_TRANSLATION )
                    )
                )

            )
        );
    }

    /*
     * Load required addon CSS and JavaScript files only when EPO is loaded
     */
    public function register_css_js() {
        // CSS
        wp_register_style( 'tm_addon_style', plugins_url('path_to/tm_addon_style.css', __FILE__) );
        wp_enqueue_style( 'tm_addon_style' );

        // JavaScript
        wp_enqueue_script( 'tm_addon_js', plugins_url('path_to/tm_addon_js.js', __FILE__), array('jquery') );
    }    

    /*
     * Display the addon
     */
    public function display_addon( $element = array(), $args = array(), $internal_args=array() ) {
        extract( shortcode_atts( array(
            'required'          => '',
            'price'             => '',
            'text_after_price'  => '',
            'price_type'        => '',
            'hide_amount'       => '',
            'my_custom_setting' => ''
        ), $element ) );

        extract($args);

        if (!isset($fieldtype)){
            $fieldtype="tmcp-field";
        }
        if (isset($textafterprice) && $textafterprice!=''){
            $textafterprice = '<span class="after-amount'.(!empty($hide_amount)?" ".$hide_amount:"").'">'.$textafterprice.'</span>';
        }
        if (!empty($class)){
            $fieldtype .=" ".$class;
        }
     
        $output = '<li class="tmcp-field-wrap">'.
        '<label for="'.$id.'"></label>'.
        '<input class="'.$fieldtype.' tm-epo-field tmcp-textfield" '.
        'name="'.$name.'" '.
        'id="'.$id.'" '.
        'tabindex="'.$tabindex.'" '.
        'data-price="" '.
        'data-rules="'.$rules.'" '.
        'data-rulestype="'.$rules_type.'" '.
        'value="'.( isset($_POST[$name])?esc_attr(stripslashes($_POST[$name])):( isset($_GET[$name])?esc_attr(stripslashes($_GET[$name])):"" ) ).'" '.
        'type="text" />'.
        '</li>';

        echo $output;
    }

}

// Initialize addon class
new TM_Extension_Class();
?>