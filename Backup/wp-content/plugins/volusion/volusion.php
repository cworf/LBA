<?php
/*
Plugin Name: Volusion
Plugin URI: http://katz.co/volusion/
Description: Integrate Volusion products into your WordPress website
Author: Katz Web Services, Inc.
Version: 1.2
Author URI: http://www.katzwebservices.com

------------------------------------------------------------------------
Copyright 2013 Katz Web Services, Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

add_action('plugins_loaded', 'construct_WP_Volusion');

function construct_WP_Volusion() {
    $WP_Volusion = new WP_Volusion;
}

class WP_Volusion {

	var $configured = false;

    function __construct() {

        add_shortcode('Volusion', array(&$this, 'shortcode'));
        add_shortcode('volusion', array(&$this, 'shortcode'));

        add_action('admin_menu', array(&$this, 'admin_menu'));
        add_filter('plugin_action_links', array(&$this, 'settings_link'), 10, 2 );
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('wp_footer', array(&$this, 'give_thanks'));

        $this->options = get_option('wpvolusion', array('storepath' => ''));

        // Set each setting...
        foreach($this->options as $key=> $value) {
            $this->{$key} = trim($value);
        }

        $this->baseurl = $this->icon = WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)) ."/";
        $this->icon = WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)) ."/volusion-button.png";
        $this->showbutton = WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)) ."/show-button.gif";
    }

    /**
     * Output debugging content
     * @param  mixed  $content Content to print
     * @param  boolean $die     Die after printing
     */
    function r($content = '', $die = false) {
        echo '<pre>'.print_r($content, true).'</pre>';
        if($die) { die(); }
    }

	function init() {

        $plugin_dir = basename(dirname(__FILE__)).'/languages';
        load_plugin_textdomain( 'wpvolusion', 'wp-content/plugins/' . $plugin_dir, $plugin_dir );
	}

    function shortcode($atts, $content = null) {
        if(empty($content)) { return ''; }
        extract(shortcode_atts(array(
            'link' => '',
            'rel' => '',
            'target' => '',
            'nofollow' => '',
        ), $atts));

            $options = get_option('wpvolusion');
            $storepath = $options['storepath'];
            $seofriendly = $options['seofriendly'];
            if(substr($link, 0, 1) == '/' && substr($storepath, -1, 1) == '/') { // If they start and end with // we strip one.
                $link = substr($link, 1);
            };
            $storepath = untrailingslashit($storepath);
            if(!isset($options['seofriendly'])) {
                $link = $storepath.'/ProductDetails.asp?ProductCode='.$link;
            } else {
                $link = $storepath.'/product_p/'.strtolower($link).'.htm';
            }

        if(isset($rel) && $rel !='') {$nofollow=' rel="'.$rel.'"';}
        if($target) { $target = ' target="'.$target.'"'; };
        return '<a href="'.$link.'"'.$nofollow.$target.$nofollow.'>' . $content . '</a>';
    }

    function give_thanks() {

        if(!empty($this->showlink)) {

            mt_srand(crc32($_SERVER['REQUEST_URI'])); // Keep links the same on the same page

            $urls = array('https://katz.co/volusion/?ref=foot', 'http://wordpress.org/extend/plugins/volusion/');
            $url = $urls[mt_rand(0, count($urls)-1)];
            $links = array(
                'This blog uses the <a href="'.$url.'">Volusion</a> WordPress Plugin',
                'We are using the <a href="'.$url.'">Volusion</a> plugin for WordPress.',
                'Our WordPress blog is integrated with <a href="'.$url.'">Volusion</a>.',
                'WordPress + <a href="'.$url.'">Volusion</a> = awesome.'
            );
            $link = '<p style="text-align:center;">'.trim($links[mt_rand(0, count($links)-1)]).'</p>';

            echo apply_filters('volusion_thanks', $link);

            mt_srand(); // Make it random again.
        }
    }

	function add_media_upload_tabs($tabs) {
		$newtab = array('volusion' => __('Volusion', 'wpvolusion'));
		return array_merge($tabs, $newtab);
	}

	function menu_handle() {
		return wp_iframe( array(&$this, 'media_process') );
	}

    function admin_init() {
        global $plugin_page;

        register_setting( 'wpvolusion_options', 'wpvolusion', array(&$this, 'sanitize_settings') );

        $this->CheckSettings();

        // If we're on a post page, add Volusion stuff for media button & popup
        if(in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php', 'wpvolusion'))) {

            add_action('admin_footer',  array(&$this, 'int_add_mce_popup'));

            if($this->configured) {
                wp_enqueue_style('media');
                add_action('media_buttons_context', array(&$this, 'add_volusion_button'));
                global $volusion_icon;
                $volusion_icon = $this->icon;
            }
        }

        if(!empty($this->configured)) {
            add_filter('media_upload_tabs', array(&$this, 'add_media_upload_tabs'));
            add_action('media_upload_volusion', array(&$this,'menu_handle'));

            $this->productsselect = $this->BuildProductsSelect(isset($_REQUEST['wpvolusionrebuild']));
        }

        if($plugin_page === 'wpvolusion') {
            add_thickbox();
        }
    }

    function sanitize_settings($input) {
        return $input;
    }

    function settings_link( $links, $file ) {
        static $this_plugin;
        if( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);
        if ( $file == $this_plugin ) {
            $settings_link = '<a href="' . admin_url( 'options-general.php?page=wpvolusion' ) . '">' . __('Settings', 'wpvolusion') . '</a>';
            array_unshift( $links, $settings_link ); // before other links
        }
        return $links;
    }

    function admin_menu() {
        add_options_page('Volusion', 'Volusion', 'manage_options', 'wpvolusion', array(&$this, 'admin_page'));
    }

	function admin_page() {
        ?>
        <div class="wrap">
        <h2><?php _e('Volusion for WordPress', 'wpvolusion'); ?></h2>
        <div class="postbox-container" style="width:62%; margin-right:3%;">
            <div class="metabox-holder">
                <div class="meta-box-sortables">
                    <form action="options.php" method="post">
                   <?php
                        $buttonClass = '';
                   		$this->show_configuration_check(false);
                    	wp_nonce_field('update-options');
                        settings_fields('wpvolusion_options');

                       	$rows[] = array(
                                'id' => 'wpvolusion_storepath',
                                'label' => __('Store Path (required)', 'wpvolusion'),
                                'desc' => 'Your Store\'s URL, including <code>http://</code>. Entering this into your browser should take you to your home page. This is optional, and only to shorten the shortcode when linking to your products.',
                                'content' => "<input type='text' name='wpvolusion[storepath]' id='wpvolusion_storepath' value='".esc_attr($this->storepath)."' size='40' style='width:95%!important;' />"
                        );
		if(!empty($this->configured) && !is_wp_error( $this->configured )) {
                        $checked = (!empty($this->seofriendly)) ? ' checked="checked"' : '';

                        $rows[] = array(
                                'id' => 'wpvolusion_seofriendly',
                                'label' => __('Use SEO-Friendly Links', 'wpvolusion'),
                                'desc' => 'This is configured in the Volusion store settings.',
                                'content' => "<p><label for='wpvolusion_seofriendly'><input type='checkbox' name='wpvolusion[seofriendly]' id='wpvolusion_seofriendly' $checked /> Use SEO-friendly links <span class='howto'>Check if your store's products are formatted like this: <code>/product_p/blue123.htm</code>, not this: <code>/ProductDetails.asp?ProductCode=BLUE123</code></span></label></p>"
                        );

                        $checked = (!empty($this->showlink)) ? ' checked="checked"' : '';

                        $rows[] = array(
                                'id' => 'wpvolusion_showlink',
                                'label' => __('Give Thanks (optional)', 'wpvolusion'),
                                'desc' => 'Please show support for this plugin by enabling.',
                                'content' => "<p><label for='wpvolusion_showlink'><input type='checkbox' name='wpvolusion[showlink]' id='wpvolusion_showlink' $checked /> Show some love<span class='howto'>Tell the world you use this plugin. A link will be added to your footer.</span></label></p>"
                        );


                        if(!empty($this->productsselect)) {
                        	$rebuildText = __("Your product list has been built:", 'wpvolusion')."</p>".$this->BuildProductsSelect();
                            $rebuildText .= '<p><strong>'.__("Has the list changed?", 'wpvolusion').'</strong>';
                        	$rebuildLink = __('Re-build your products list', 'wpvolusion');
                        } else {
                        	$rebuildText = __("Your product list has not yet been built. ", 'wpvolusion');
                        	$rebuildLink = __('Build your products list', 'wpvolusion');
                            $buttonClass = 'button-large button-hero button-primary';
                        }

                       $rows['unset'] = array(
                                'id' => 'wpvolusionrebuild',
                                'label' => __('Products', 'wpvolusion'),
                                'desc' => '',
                                'content' => "<p>$rebuildText <a href='".wp_nonce_url(admin_url('options-general.php?page=wpvolusion&amp;wpvolusionrebuild=all'), 'rebuild')."' class='button {$buttonClass}'>$rebuildLink</a><span class='howto'>Note: this may take a long time, depending on the size of your products list.</span></p>"
                        	);
			} else {
						$checked1 = (!empty($this->seofriendly)) ? " <input type='hidden' name='wpvolusion[seofriendly]' id='wpvolusion_seofriendly' value='on' checked='checked' />" : '';
						$checked2 = (!empty($this->showlink)) ? "<input type='hidden' name='wpvolusion[showlink]' id='wpvolusion_showlink' value='on' checked='checked' />" : '';
				 		$rows[] = array(
                                'id' => 'wpvolusionrebuild',
                                'label' => __('', 'wpvolusion'),
                                'desc' => "<p class='howto'>{$checked1} {$checked2}<span>Once properly configured, other settings will appear below.</span></p>",
                                'content' => ''
                        	);
			}
                        $this->postbox('wpvolusionsettings',__('Store Settings', 'wpvolusion'), $this->form_table($rows), false);

                    ?>

                        <input type="hidden" name="page_options" value="<?php foreach($rows as $row) { $output .= $row['id'].','; } echo substr($output, 0, -1);?>" />
                        <input type="hidden" name="action" value="update" />
                        <p class="submit">
                            <input type="submit" class="button-primary" name="save" value="<?php _e('Save Changes', 'wpvolusion') ?>" />
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <div class="postbox-container" style="width:34%;">
            <div class="metabox-holder">
                <div class="meta-box-sortables">
                <?php $this->postbox('wpvolusionhelp',__('Configuring This Plugin', 'wpvolusion'), $this->configuration(), true);  ?>
                </div>
            </div>
        </div>

    </div>
    <?php
    }

    function configuration() {
    	$showbutton = $this->baseurl.'show-button.gif';
    	$baseurl = $this->baseurl;
	$html = <<<EOD
	<h4>Enabling API Access</h4>
<ol>
<li>Log into your Volusion store administration <a href="{$baseurl}tutorial-0.jpg" class="thickbox"><img alt="Show" src="$showbutton"></a></li>
<li>Go to <span style="font-style: italic;">Inventory</span> &gt; <span style="font-style: italic;">Import / Export. <a href="{$baseurl}tutorial-1.jpg" class="thickbox"><img alt="Show" src="$showbutton"></a></span></li>
<li>Click the <span style="font-style: italic;">Volusion API</span> link near the bottom of the page.<a href="{$baseurl}tutorial-2.jpg" class="thickbox"><img alt="Show" src="$showbutton"></a></li>
<li>Enabling generic exports is easy. Simply click the check box of each of the two exports ("Enable public XML for Featured Products" and "Enable public XML for All Products.") to enable them. Click the save button next to each export option to engage this setting within the Volusion store. <a href="{$baseurl}tutorial-3.jpg" class="thickbox"><img alt="Show" src="$showbutton"></a></li>
</ol>
	<hr />
	<h4>This plugin requires a Volusion account.</h4>
	<p><strong>What is Volusion?</strong><br />
	If you're looking to start an online business, Volusion is the place. Since day one, Volusion has offered more than just shopping cart software. Volusion offers the features, services and add-ons your business needs to shine. Whether you're an ecommerce newbie or an experienced online seller, Volusion is the platform that will help you succeed.
</p>
EOD;
	return $html;
    }

    function show_configuration_check($link = true) {
    	global $volusion_icon;
    	$options = $this->options;

            if(!empty($this->configured) && !is_wp_error($this->configured)) {
                $content = __('Your '); if($link) { $content .= '<a href="' . admin_url( 'options-general.php?page=wpvolusion' ) . '">'; } $content .=  __('settings', 'wpvolusion'); if($link) { $content .= '</a>'; } $content .= __(' are configured properly');

                if(empty($this->productsselect)) {
                	$content .= sprintf(__(', however your product list has not yet been built. %sBuild it now%s.', 'wpvolusion'), '<strong><a href="?page=wpvolusion&amp;wpvolusionrebuild=all">', '</a></strong>');
                } else {
                 	$content .= __('. When editing posts, look for the <img src="'.$this->icon.'" width="14" height="14" alt="Add a Product" style="border:1px solid #ccc;" /> icon; click it to add a product link to your post or page.');
                 }
                echo $this->make_notice_box($content, 'success');
            } else {
            	if(is_wp_error($this->configured)) {
                    $error = $this->configured->get_error_message();
                    $content = sprintf(__('There was an error checking your settings: %s', 'wpvolusion'), '<strong>'.$error.'</strong>');
                    echo $this->make_notice_box($content, 'error');
            	} else if(!empty($this->storepath)) {
                    $content = '';
                    $content .= 'Your '; if($link) { $content .= '<a href="' . admin_url( 'options-general.php?page=wpvolusion' ) . '">'; } $content .=  __('Volusion API settings', 'wpvolusion') ; if($link) { $content .= '</a>'; } $content .= '  are <strong>not configured properly.</strong><h3>What to do:</h3><ol><li>Check your Store Path ';
	                if(!empty($this->storepath)) {
	                $content .= '(<a href="'.$this->storepath.'" rel="nofollow" target="_blank">This link should go to your store</a>)';
	                }
	                $content .= '</li>
	                <li>Make sure you have enabled API access in your Volusion store settings ';
	                if(!empty($this->storepath)) {
	                $content .= '(<a href="'.str_replace('//net/webservice', '/net/webservice', $this->storepath.'/net/webservice.aspx?api_name=generic\all_products').'" rel="nofollow" target="_blank">This link should be a bunch of plain text</a>; if it is a blank page, an error page, or a webpage with other content, you have not enabled API access.)';
	                }
	                $content .= '</li></ol>';
	                echo $this->make_notice_box($content, 'error');
                } else {
                    $content = '<h2 style="font-size:1.5em;">'.sprintf(__('This plugin requires a %sVolusion%s account. If you do not have a Volusion account, %1$ssign up for a 14 day free trial now%2$s.', 'wpvolusion'), '<a href="http://katz.si/volusion">', '</a>').'</h2>';
                	$content .= wpautop('<strong>To get started, please:</strong><ol><li>Enable API access in your Volusion store (see "Enabling API Access" to the right)</li><li>Enter your store\'s URL below;</li></ol>');
                	echo $this->make_notice_box($content, 'success');
                }
            };
    }

    function make_notice_box($content, $type="error") {
        $output = '';
        if($type!='error') { $output .= '<div class="updated inline">';
        } else {
            $output .= '<div class="error inline">';
        }
        $output .= '<p style="line-height: 1; margin: 0.5em 0; padding: 2px;">'.$content.'</div>';
        return($output);
    }

	private function CheckSettings() {
        global $plugin_page;

		if($plugin_page === 'wpvolusion') {

            if((!empty($this->storepath) && empty($this->configured)) || !empty($_REQUEST['settings-updated']) || !empty($_GET['cache'])) {

                // We just want to verify that the path is working.
                $request = wp_remote_request(untrailingslashit($this->storepath).'/net/webservice.aspx?api_name=generic\all_products', array('method' => 'HEAD'));

                if(!is_wp_error($request) && $request['response']['code'] === 200) {
                    $this->configured = $this->options['configured'] = true;
                } elseif(is_wp_error($request)) {
                    $this->configured = $request;
                } else {
                    $this->configured = $this->options['configured'] = false;
                }

                update_option('wpvolusion', $this->options);
            }
		}
	}

	private function BuildProductsSelect($rebuild = false) {

			if(!$rebuild) {
				$result = get_transient('volusion_select');
				if(!is_wp_error($result) && $result && strpos($result, 'volusion_add_product_id')) {
					return $result;
				}
                return false;
			}

            $Products = $this->GetProducts($rebuild);

            if(!$Products) {
				return new WP_Error('Product List Retrieval Failed', $Products[0]);
			} else if(is_wp_error($Products)) {
				return $Products;
			} else if(!$Products) {
				return false;
			}

			$options = '';
			foreach($Products as $key => $Product){
		    	$options .= '<option value="'.htmlentities($Product->ProductCode).'">'.esc_html($Product->ProductName).'</option>';
		    }
		    $select = '
			<select id="volusion_add_product_id"  style="width:90%;">
				<option value="" disabled="disabled" selected="selected">'.__('Select a product&hellip;', 'wpvolusion').'</option>
				'.$options.'
			</select>';

			set_transient('volusion_select', $select, WEEK_IN_SECONDS);

		    return $select;
	}


	public function GetProducts($force_rebuild = true) {

        $Products = get_transient('volusion_all_p');

        if(!$Products || $force_rebuild) {
            $request = wp_remote_get( untrailingslashit($this->storepath).'/net/webservice.aspx?api_name=generic\all_products', array('timeout' => 300, 'compress'=>true));
            $result = wp_remote_retrieve_body($request);

            if(is_wp_error($result)) {
    			return $result->get_error_message();
    		}

    		@libxml_use_internal_errors(true);
    		$Result = @simplexml_load_string($result);
            $Products = array();

            // Get rid of tons of weight
            foreach($Result->Product as $Product) {
                unset($Product->Categories);
                unset($Product->OptionCategory);
                unset($Product->ExtInfo);
                unset($Product->FreeShippingItem);
                unset($Product->TaxableProduct);
                unset($Product->Availability);
                $Products[] = $Product;
            }
            set_transient('volusion_all_p', json_encode($Products), DAY_IN_SECONDS * 3);
        } elseif($Products) {
            $Products = json_decode($Products);
        }

		if(!empty($Products)) { return $Products; }

		return false;
	}

    /**
     * Add the button to Post/Page editor to insert product link
     * @param string $context HTML pf buttons
     */
    public function add_volusion_button($context){
    	global $volusion_icon;
        $out = '<a href="#TB_inline?width=640&inlineId=volusion_select_product" class="thickbox button add_media" title="' . __("Add Volusion Product(s)", 'wpvolusion') . '"><img src="'.$volusion_icon.'" width="14" height="14" alt="' . __("Add a Product", 'wpvolusion') . '" style="padding:0" /> Insert Product</a>';
        return $context . $out;
    }

    function int_add_mce_popup(){
    	if(empty($this->configured) || is_wp_error( $this->configured )) {
    	?>
    		<div id="volusion_select_product">
    			<div id="media-upload">
                	<div class="error"><p>Your Volusion plugin settings appear not to be configured properly. <a href="<?php echo admin_url('options-general.php?page=wpvolusion'); ?>">Please check your settings</a> and re-save them just to make sure.</p>
                	<p>If everything is configured properly and you still get this message, please <a href="http://wordpress.org/tags/volusion?forum_id=10">post your issue on the support forum</a>.</p>
                	</div>
                </div>
             </div>
        <?php
        return;
    	}
        ?>
        <script>
            function InsertProduct(){
                var product_id = jQuery("#volusion_add_product_id").val();

                if(product_id == ""){
                    alert("<?php _e("The product you selected does not have a link. Try rebuilding your product list in settings.", "wpvolusion") ?>");
                    jQuery("#volusion_add_product_id").focus();
                    return;
                } else if (product_id === null) {
                    alert("<?php _e("Please select a product.", "wpvolusion") ?>");
                    jQuery("#volusion_add_product_id").focus();
                    return;
                }
				var display_title = jQuery("#volusion_display_title").val();

				if(display_title == ""){
					alert("<?php _e("Link Text is required.", "wpvolusion") ?>");
					jQuery("#volusion_display_title").focus();
					return false;
				}
                var link_target = '';
                var link_nofollow = '';
                <?php
                // If the path to the store is set, we only need the end of the URL;
                // this is to de-clutter the editor
                if(!empty($this->storepath)) { ?>
                product_id = product_id.replace("<?php echo $this->storepath; ?>", '');
                <?php } ?>
                if(jQuery("#link_target").is(":checked")) { link_target = ' target="blank"'; }
                if(jQuery("#link_nofollow").is(":checked")) { link_nofollow = ' rel="nofollow"'; }

                var win = window.dialogArguments || opener || parent || top;
				win.send_to_editor("[volusion link=\"" + product_id +"\""+link_target+link_nofollow+"]"+display_title+"[/volusion]");
            }
        </script>

        <div id="volusion_select_product" style="display:none;">
                <div id="media-upload">
                	<div class="media-upload-form type-form">
                	<h3 class="media-title"><?php _e("Insert a Product", "wpvolusion"); ?></h3>
                    </div>
                    <?php
                    $select = $this->BuildProductsSelect();
                   	if(empty($select)) {
                   		echo '<p>Your settings are correct, however your product list has not been generated. (<em>This may take a while if you have lots of products.</em>)</p>
                   		<p><a href="' . admin_url( 'options-general.php?page=wpvolusion&wpvolusionrebuild=all' ) . '" class="button">Generate your list now</a></p>';
                   	} else {
                   	?>

                    <div id="media-items" style="width:auto; overflow:hidden;">
					<div class="media-item media-blank" style="border:none;">
						<h4 class="media-sub-title"><?php _e("Select a product below to add it to your post or page.", "wpvolusion"); ?></h4>
						<table class="describe"><tbody>
							<tr>
								<th valign="top" scope="row" class="label" style="width:130px;">
									<span class="alignleft"><label for="volusion_display_title"><?php _e("Link Text", "wpvolusion"); ?></label></span>
								</th>
								<td class="field"><input type="text" id="volusion_display_title" size="100" style="width:90%;" />
							</tr>

							<tr>
								<th valign="top" scope="row" class="label">
									<span class="alignleft"><label for="volusion_add_product_id">Select the Product</label></span>
								</th>
								<td class="field">

                            <?php
                    			if(is_wp_error($select)) {
                    				echo $select->get_error_message();
                    			} else {
                    				echo $select;
                    			}
                            ?></td>
							</tr>

							<tr>
								<th valign="top" scope="row" class="label">
									<span class="alignleft"><label for="url">Additional options:</label></span>
								</th>
								<td class="field">
								<input type="checkbox" id="link_nofollow" /> <label for="link_nofollow"><?php _e("Nofollow the link", "wpvolusion"); ?></label><br />
		                        <input type="checkbox" id="link_target" /> <label for="link_target"><?php _e("Open link in a new window", "wpvolusion"); ?></label>
								</td>
							</tr>

							<tr>
								<td></td>
								<td>
									<input type="button" class="button-primary" value="Insert Product" onclick="InsertProduct();"/>&nbsp;&nbsp;&nbsp;
				                    <a class="button" style="color:#bbb;" href="#" title="Cancel" onclick="tb_remove(); return false;"><?php _e("Cancel", "wpvolusion"); ?></a>
								</td>
							</tr>

						</tbody></table>
					</div>
					</div>
					<?php } ?>
            </div>
        </div>

        <?php
    }



   // THANKS JOOST!
    function form_table($rows) {
        $content = '<table class="form-table" width="100%">';
        foreach ($rows as $row) {
            $content .= '<tr><th valign="top" scope="row" style="width:50%">';
            if (isset($row['id']) && $row['id'] != '')
            	if(!empty($row['label'])) {
                $content .= '<label for="'.$row['id'].'" style="font-weight:bold;">'.$row['label'].':</label>';
                }
            else
                $content .= $row['label'];
            if (isset($row['desc']) && $row['desc'] != '')
                $content .= '<br/><small>'.$row['desc'].'</small>';
            $content .= '</th><td valign="top">';
            $content .= $row['content'];
            $content .= '</td></tr>';
        }
        $content .= '</table>';
        return $content;
    }

    function postbox($id, $title, $content, $padding=false) {
        ?>
            <div id="<?php echo $id; ?>" class="postbox">
                <div class="handlediv" title="Click to toggle"><br /></div>
                <h3 class="hndle"><span><?php echo $title; ?></span></h3>
                <div class="inside" <?php if($padding) { echo 'style="padding:10px; padding-top:0;"'; } ?>>
                    <?php echo $content; ?>
                </div>
            </div>
        <?php
    }


	function media_process() {
		media_upload_header();

		$Products = $this->GetProducts(false);
		if(is_wp_error($Products) || !$Products) { return false; }

        include dirname(__FILE__).'/media-process.php';
	}
}
