<?php

/*
Plugin Name: Spider Catalog
Plugin URI: http://web-dorado.com/products/wordpress-catalog.html
Description: Spider Catalog is a convenient tool for organizing the products represented on your website into catalogs. Each product on the catalog is assigned with a relevant category, which makes it easier for the customers to search and identify the needed products within the catalog.
Version: 1.6.4
Author: http://web-dorado.com/
License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

add_action('init', 'catalog_lang_load');

function catalog_lang_load()
{
    load_plugin_textdomain('sp_catalog', false, basename(dirname(__FILE__)) . '/Languages');

}

$ident = 1;

add_action('admin_head', 'spider_ajax_func');
function spider_ajax_func()
{
    ?>
    <script>
        var spider_ajax = '<?php echo admin_url("admin-ajax.php"); ?>';
    </script>
<?php
}

function Spider_Catalog_Products_list_shotrcode($atts)
{
    extract(shortcode_atts(array(
        'id' => 'no Spider catalog',
        'details' => '1',
        'type' => '',
        'showsub' => '1',
        'showsubprod' => '1',
        'showprod' => '1',
    ), $atts));
    if (!(is_numeric($atts['id']) || $atts['id'] == 'ALL_CAT'))
        return 'insert numerical or `ALL_CAT` shortcode in `id`';

    if (!($atts['details'] == 1 || $atts['details'] == 0))
        return 'insert valid `detalis`';

    if (!($atts['type'] == 'list' || $atts['type'] == '' || $atts['type'] == 'cells2' || $atts['type'] == 'wcells' || $atts['type'] == 'thumb' || $atts['type'] == 'cells3'))
        return 'insert valid `type`';

    if (!($atts['showsub'] == 1 || $atts['showsub'] == 0))
        return 'insert valid `showsub`';

    if (!($atts['showsubprod'] == 0 || $atts['showsubprod'] == 1 || $atts['showsubprod'] == 2))
        return 'insert valid `showsubprod`';

    if (!($atts['showprod'] == 0 || $atts['showprod'] == 1))
        return 'insert valid `showprod`';

    return spider_cat_Products_list($atts['id'], $atts['details'], $atts['type'], $atts['showsub'], $atts['showsubprod'], $atts['showprod']);

}


/////////////// Filter catalog


function catalog_after_search_results($query)
{
    global $wpdb;
    if (isset($_REQUEST['s']) && $_REQUEST['s']) {
        $serch_word = htmlspecialchars(($_REQUEST['s']));

        $query = str_replace($wpdb->prefix . "posts.post_content", gen_string_catalog_search($serch_word, $wpdb->prefix . 'posts.post_content') . " " . $wpdb->prefix . "posts.post_content", $query);
    }
    return $query;

}

add_filter('posts_request', 'catalog_after_search_results');


function gen_string_catalog_search($serch_word, $wordpress_query_post)
{
    $string_search = '';

    global $wpdb;
    if ($serch_word) {
        $rows_category = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "spidercatalog_product_categories WHERE (description LIKE %s) OR (name LIKE %s)", '%' . $serch_word . '%', "%" . $serch_word . "%"));

        $count_cat_rows = count($rows_category);
        if ($count_cat_rows) {
            $string_search .= $wordpress_query_post . ' LIKE \'%[Spider_Catalog_Category id="ALL_CAT" details="1" %\' OR ';
        }
        for ($i = 0; $i < $count_cat_rows; $i++) {
            $string_search .= $wordpress_query_post . ' LIKE \'%[Spider_Catalog_Category id="' . $rows_category[$i]->id . '" details="1" %\' OR ' . $wordpress_query_post . ' LIKE \'%[Spider_Catalog_Category id="' . $rows_category[$i]->id . '" details="1"%\' OR ';
        }


        $rows_category = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "spidercatalog_product_categories WHERE (name LIKE '%" . $serch_word . "%')");
        $count_cat_rows = count($rows_category);
        for ($i = 0; $i < $count_cat_rows; $i++) {
            $string_search .= $wordpress_query_post . ' LIKE \'%[Spider_Catalog_Category id="' . $rows_category[$i]->id . '" details="0"%\' OR ' . $wordpress_query_post . ' LIKE \'%[Spider_Catalog_Category id="' . $rows_category[$i]->id . '" details="0"%\' OR ';
        }

        $rows_single = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "spidercatalog_products WHERE name LIKE '%" . $serch_word . "%'");

        $count_sing_rows = count($rows_single);
        if ($count_sing_rows) {
            for ($i = 0; $i < $count_sing_rows; $i++) {
                $string_search .= $wordpress_query_post . ' LIKE \'%[Spider_Catalog_Product id="' . $rows_single[$i]->id . '"]%\' OR ';
            }

        }
    }
    return $string_search;
}


///////////////////// end filter


add_shortcode('Spider_Catalog_Category', 'Spider_Catalog_Products_list_shotrcode');


function Spider_Catalog_Single_product_shotrcode($atts)
{
    extract(shortcode_atts(array(
        'id' => '',
    ), $atts));
    if (!(is_numeric($atts['id'])))
        return 'insert numerical  shortcode in `id`';

    return spider_cat_Single_product($id);
}

add_shortcode('Spider_Catalog_Product', 'Spider_Catalog_Single_product_shotrcode');


//// singl product
function   spider_cat_Products_list($id, $details, $type, $showsub, $showsubprod, $showprod)
{
    require_once("front_end_functions.html.php");
    require_once("front_end_functions.php");
    if (isset($_GET['product_id'])) {
        if (isset($_GET['view'])) {
            if ($_GET['view'] == 'spidercatalog') {
                return showPublishedProducts_1($id, $details, $type, $showsub, $showsubprod, $showprod);
            } else {
                return front_end_single_product($_GET['product_id']);
            }
        } else {
            return front_end_single_product($_GET['product_id']);
        }
    } else {
        return showPublishedProducts_1($id, $details, $type, $showsub, $showsubprod, $showprod);
    }
}


// prodact list
function spider_cat_Single_product($id)
{


    require_once("front_end_functions.html.php");
    require_once("front_end_functions.php");
    return front_end_single_product($id);


}


//// add editor new mce button
add_filter('mce_external_plugins', "Spider_Catalog_register");
add_filter('mce_buttons', 'Spider_Catalog_add_button', 0);


/// function for add new button
function Spider_Catalog_add_button($buttons)
{
    array_push($buttons, "Spider_Catalog_mce");
    return $buttons;
}


/// function for registr new button
function Spider_Catalog_register($plugin_array)
{
    $url = plugins_url('js/editor_plugin.js', __FILE__);
    $plugin_array["Spider_Catalog_mce"] = $url;
    return $plugin_array;

}


function add_button_style_Spider_Catalog()
{
    echo '<script>var wd_cat_plugin_url = "' . plugins_url('', __FILE__) . '";</script>';
}

add_action('admin_head', 'add_button_style_Spider_Catalog');


function spiderbox_scripts_method()
{

    wp_enqueue_script('spidersuperbox', admin_url('admin-ajax.php?action=spiderboxjsphp'));
    wp_enqueue_script('my_common', plugins_url("js/common.js", __FILE__));
    wp_enqueue_style('spider_cat_main', plugins_url("spidercatalog_main.css", __FILE__));
}

add_action('wp_head', 'spiderbox_scripts_method', 1);


add_filter('admin_head', 'spider_cat_ShowTinyMCE');
function spider_cat_ShowTinyMCE()
{
    // conditions here
    wp_enqueue_script('common');
    wp_enqueue_script('jquery-color');
    wp_print_scripts('editor');
    if (function_exists('add_thickbox')) add_thickbox();
    wp_print_scripts('media-upload');
    if (version_compare(get_bloginfo('version'), 3.3) < 0) {
        if (function_exists('wp_tiny_mce')) wp_tiny_mce();
    }
    wp_admin_css();
    wp_enqueue_script('utils');
    do_action("admin_print_styles-post-php");
    do_action('admin_print_styles');
}


add_action('admin_menu', 'Spider_Catalog_options_panel');
function Spider_Catalog_options_panel()
{
    $page_cat = add_menu_page('Theme page title', 'Spider Catalog', 'manage_options', 'Categories_Spider_Catalog', 'Categories_Spider_Catalog', plugins_url('images/Spider_CatalogLogoHover -for_menu.png', __FILE__));
    add_submenu_page('Categories_Spider_Catalog', 'Categories', 'Categories', 'manage_options', 'Categories_Spider_Catalog', 'Categories_Spider_Catalog');
    $page_prad = add_submenu_page('Categories_Spider_Catalog', 'Products', 'Products', 'manage_options', 'Products_Spider_Catalog', 'Products_Spider_Catalog');
    add_submenu_page('Categories_Spider_Catalog', 'Global Options', 'Global Options', 'manage_options', 'Options_Catalog_global', 'Options_Catalog_global');
    $page_option = add_submenu_page('Categories_Spider_Catalog', 'Styles and Colors', 'Styles and Colors', 'manage_options', 'Options_Catalog_styles', 'Options_Catalog_styles');
    add_submenu_page( 'Categories_Spider_Catalog', 'Licensing', 'Licensing', 'manage_options', 'Spider_catalog_Licensing', 'Spider_catalog_Licensing');
    $Featured_Plugins = add_submenu_page('Categories_Spider_Catalog', 'Featured Plugins', 'Featured Plugins', 'manage_options', 'catalog_Featured_Plugins', 'catalog_Featured_Plugins');
    add_submenu_page('Categories_Spider_Catalog', 'Uninstall Spider_Catalog ', 'Uninstall  Spider Catalog', 'manage_options', 'Uninstall_Spider_Catalog', 'Uninstall_Spider_Catalog');

    add_action('admin_print_styles-' . $Featured_Plugins, 'catalog_Featured_Plugins_styles');
    add_action('admin_print_styles-' . $page_cat, 'Spider_Category_admin_script');
    add_action('admin_print_styles-' . $page_prad, 'Spider_prodact_admin_script');
    add_action('admin_print_styles-' . $page_option, 'Spider_option_admin_script');
}


/////////////////////             Spider_Category print styles


function Spider_Category_admin_script()
{

    wp_enqueue_script('param_block', plugins_url("js/param_block.js", __FILE__));
}

function Spider_prodact_admin_script()
{

    wp_enqueue_script('param_block', plugins_url("js/param_block.js", __FILE__));
}

function Spider_option_admin_script()
{

    wp_enqueue_script('param_block1', plugins_url("js/mootools.js", __FILE__));
    wp_enqueue_script('param_block2', plugins_url("elements/moorainbow/mooRainbow.js", __FILE__));
    wp_enqueue_script('param_block3', plugins_url("elements/moorainbow/mootools.js", __FILE__));
    wp_enqueue_script('param_block4', plugins_url("js/joomla.javascript.js", __FILE__));
    wp_enqueue_style('param_block5', plugins_url("elements/moorainbow/mooRainbow.css", __FILE__));


}


//////////////////////////////////////////
//           LICENS
/////////////////////////////////////
function Spider_catalog_Licensing(){
	
	?>
    <div style="width:95%">
    <p>
	This plugin is the non-commercial version of the Spider Catalog. If you want to customize to the styles and colors of your website,than you need to buy a license.
Purchasing a license will add possibility to customize the styles and colors, global options of the Spider Catalog. 

 </p>
<br /><br />
<a href="http://web-dorado.com/files/fromSpiderCatalog.php" class="button-primary" target="_blank">Purchase a License</a>
<br /><br /><br />
<p>After the purchasing the commercial version follow this steps:</p>
<ol>
	<li>Deactivate Spider Catalog Plugin</li>
	<li>Delete Spider Catalog Plugin</li>
	<li>Install the downloaded commercial version of the plugin</li>
</ol>
</div>
<?php
    
    
	
	}


///////////////////////////////////////////////////////////////////// TAGS

//////////require_once("nav_function/nav_html_func.php");
/*
add_filter('admin_head','ShowTinyMCE');
function ShowTinyMCE() {
	// conditions here
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'jquery-color' );
	wp_print_scripts('editor');
	if (function_exists('add_thickbox')) add_thickbox();
	wp_print_scripts('media-upload');
	if (function_exists('wp_tiny_mce')) wp_tiny_mce();
	wp_admin_css();
	wp_enqueue_script('utils');
	do_action("admin_print_styles-post-php");
	do_action('admin_print_styles');
}
*/
function Categories_Spider_Catalog()
{
    ////////including functions for categories
    require_once("Categories.php");
    require_once("Categories.html.php");
    if (!function_exists('print_html_nav'))
        require_once("nav_function/nav_html_func.php");


    if (isset($_GET["task"]))
        $task = $_GET["task"]; //get task for choosing function
    else
        $task = '';
    if (isset($_GET["id"]))
        $id = $_GET["id"];
    else
        $id = 0;
    global $wpdb;
    switch ($task) {

        case 'add_cat':
            add_category();
            break;
        case 'publish_cat':
            change_cat($id);
            showCategory();
            break;
        case 'publish':
            publish_all(TRUE);
            showCategory();
            break;
        case 'unpublish_cat':
            change_cat($id);
            showCategory();
            break;
        case 'unpublish':
            publish_all(FALSE);
            showCategory();
            break;

        case 'edit_cat':
            if ($id)
                editCategory($id);
            else {
                $id = $wpdb->get_var("SELECT MAX( id ) FROM " . $wpdb->prefix . "spidercatalog_product_categories");
                editCategory($id);
            }
            break;

        case 'save':
            if ($id)
                apply_cat($id);
            else
                save_cat();
            showCategory();
            break;

        case 'apply':
            if ($id) {
                apply_cat($id);
                editCategory($id);

            } else {
                $true = save_cat();
                if ($true) {
                    $id = $wpdb->get_var("SELECT MAX( id ) FROM " . $wpdb->prefix . "spidercatalog_product_categories");
                    editCategory($id);
                } else {
                    ?><h1>Database Error Please install plugin again</h1><?php
                    showCategory();
                }
            }

            break;


        case 'remove_cat':
            removeCategory($id);
            showCategory();
            break;
        case 'delete':
            delete_all();
            showCategory();
            break;

        default:
            showCategory();
            break;
    }


}


function Products_Spider_Catalog()
{

    global $wpdb;
    require_once("products.php");
    require_once("Products.html.php");
    if (!function_exists('print_html_nav'))
        require_once("nav_function/nav_html_func.php");

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        $id = 0;
    }
    if (isset($_GET['task']))
        $task = $_GET['task'];
    else
        $task = "";

    switch ($task) {
        case 'edit_prad':
            editProduct($id);
            break;
        case 'add_prad':
            addProduct();
            break;
        case 'apply':
            if ($id) {
                update_prad_cat($id);
            } else {
                save_prad_cat();
                $id = $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "spidercatalog_products");
            }
            editProduct($id);
            break;
        case 'save':
            if ($id)
                update_prad_cat($id);
            else
                save_prad_cat();
            showProducts();
            break;

        case 'saveorder':

            break;
        case 'publish':
            publish_all(TRUE);
            showProducts();
            break;
        case 'unpublish':
            publish_all(FALSE);
            showProducts();
            break;
        case 'delete':
            delete_all();
            showProducts();
            break;
        case 'unpublish_prad':
            change_prod($id);
            showProducts();
            break;
        case 'unpublish_prad':
            change_prod($id);
            showProducts();
            break;
        case 'remove_prod':
            removeProduct($id);
            showProducts();
            break;
        case 'edit_reviews':
            spider_cat_prod_rev($id);

            break;

        case 'delete_reviews':
            delete_rev($id);
            spider_cat_prod_rev($id);

            break;
        case 'delete_review':
            delete_single_review($id);
            spider_cat_prod_rev($id);

            break;
        case 'edit_rating':
            spider_cat_prod_rating($id);

            break;

        case 'delete_ratings':
            delete_ratings($id);
            spider_cat_prod_rating($id);

            break;
        case 'delete_rating':
            delete_single_rating($id);
            spider_cat_prod_rating($id);

            break;
        case 's_p_apply_rating':
            update_s_c_rating($id);
            spider_cat_prod_rating($id);

            break;
        case 's_p_save_rating':
            update_s_c_rating($id);
            editProduct($id);

            break;

        default:
            showProducts();

            break;
    }


}


function Options_Catalog_styles()
{

    require_once("catalog_Options.php");
    require_once("catalog_Options.html.php");
    if (isset($_GET['task']))
        if ($_GET['task'] == 'save')
            save_styles_options();
    showStyles();


}

function Options_Catalog_global()
{

    require_once("catalog_Options.php");
    require_once("catalog_Options.html.php");
    if (isset($_GET['task']))
        if ($_GET['task'] == 'save')
            save_global_options();
    showGloballll();


}


//////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////               Uninstall                     ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////


function Uninstall_Spider_Catalog()
{

    global $wpdb;
    $base_name = plugin_basename('Spider_Catalog');
    $base_page = 'admin.php?page=' . $base_name;
    if (isset($_GET['mode']))
        $mode = trim($_GET['mode']);
    else
        $mode = '';
    if (!empty($_POST['do'])) {

        if ($_POST['do'] == "UNINSTALL Spider Catalog") {
            check_admin_referer('Spider_Catalog uninstall');
            if (trim($_POST['Spider_Catalog_yes']) == 'yes') {

                echo '<div id="message" class="updated fade">';

                echo '<p>';
                echo "Table 'spidercatalog_id' has been deleted.";
                $wpdb->query("DROP TABLE " . $wpdb->prefix . "spidercatalog_id");
                echo '<font style="color:#000;">';
                echo '</font><br />';
                echo '</p>';

                echo '<p>';
                echo "Table 'spidercatalog_params' has been deleted.";
                $wpdb->query("DROP TABLE " . $wpdb->prefix . "spidercatalog_params");
                echo '<font style="color:#000;">';
                echo '</font><br />';
                echo '</p>';
                echo '<p>';
                echo "Table 'spidercatalog_products' has been deleted.";
                $wpdb->query("DROP TABLE " . $wpdb->prefix . "spidercatalog_products");
                echo '<font style="color:#000;">';
                echo '</font><br />';
                echo '</p>';
                echo "Table 'spidercatalog_product_categories' has been deleted.";
                $wpdb->query("DROP TABLE " . $wpdb->prefix . "spidercatalog_product_categories");
                echo '<font style="color:#000;">';
                echo '</font><br />';
                echo '</p>';
                echo "Table 'spidercatalog_product_reviews' has been deleted.";
                $wpdb->query("DROP TABLE " . $wpdb->prefix . "spidercatalog_product_reviews");
                echo '<font style="color:#000;">';
                echo '</font><br />';
                echo '</p>';
                echo "Table 'spidercatalog_product_votes' has been deleted.";
                $wpdb->query("DROP TABLE " . $wpdb->prefix . "spidercatalog_product_votes");

                echo '<font style="color:#000;">';
                echo '</font><br />';
                echo '</p>';
                echo '</div>';

                $mode = 'end-UNINSTALL';
            }
        }
    }


    switch ($mode) {

        case 'end-UNINSTALL':
            $deactivate_url = wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . plugin_basename(__FILE__), 'deactivate-plugin_' . plugin_basename(__FILE__));
            echo '<div class="wrap">';
            echo '<h2>Uninstall Spider Catalog</h2>';
            echo '<p><strong>' . sprintf('<a href="%s">Click Here</a> To Finish The Uninstallation And Spider Catalog Will Be Deactivated Automatically.', $deactivate_url) . '</strong></p>';
            echo '</div>';
            break;
        // Main Page
        default:
            ?>
                <form method="post" action="<?php echo admin_url('admin.php?page=Uninstall_Spider_Catalog'); ?>">
                    <?php wp_nonce_field('Spider_Catalog uninstall'); ?>
                    <div class="wrap">
                        <div id="icon-Spider_Catalog" class="icon32"><br/></div>
                        <h2><?php echo 'Uninstall Spider Catalog'; ?></h2>

                        <p>
                            <?php echo 'Deactivating Spider Catalog plugin does not remove any data that may have been created. To completely remove this plugin, you can uninstall it here.'; ?>
                        </p>

                        <p style="color: red">
                            <strong><?php echo 'WARNING:'; ?></strong><br/>
                            <?php echo 'Once uninstalled, this cannot be undone. You should use a Database Backup plugin of WordPress to back up all the data first.'; ?>
                        </p>

                        <p style="color: red">
                            <strong><?php echo 'The following WordPress Options/Tables will be DELETED:'; ?></strong><br/>
                        </p>
                        <table class="widefat">
                            <thead>
                            <tr>
                                <th><?php echo 'WordPress Tables'; ?></th>
                            </tr>
                            </thead>
                            <tr>
                                <td valign="top">
                                    <ol>
                                        <?php
                                        echo '<li>spidercatalog_id</li>' . "\n";
                                        echo '<li>spidercatalog_params</li>' . "\n";
                                        echo '<li>spidercatalog_products</li>' . "\n";
                                        echo '<li>spidercatalog_product_categories</li>' . "\n";
                                        echo '<li>spidercatalog_product_reviews</li>' . "\n";
                                        echo '<li>spidercatalog_product_votes</li>' . "\n";
                                        ?>
                                    </ol>
                                </td>
                            </tr>
                        </table>
                        <p style="text-align: center;">
                            <?php echo 'Do you really want to uninstall Spider Catalog?'; ?><br/><br/>
                            <input type="checkbox" name="Spider_Catalog_yes" value="yes"/>&nbsp;<?php echo 'Yes'; ?>
                            <br/><br/>
                            <input type="submit" name="do" value="<?php echo 'UNINSTALL Spider Catalog'; ?>"
                                   class="button-primary"
                                   onClick="return confirm('<?php echo 'You Are About To Uninstall Spider Catalog From WordPress.\nThis Action Is Not Reversible.\n\n Choose [Cancel] To Stop, [OK] To Uninstall.'; ?>')"/>
                        </p>
                    </div>
                </form>
            <?php
    } // End switch($mode)


}

////////////////////////////////////////////////// ajax function

add_action('wp_ajax_spidercatalogwindow', 'spider_catalog_window');
function spider_catalog_window()
{

    global $wpdb;

    function open_cat_in_tree($catt, $tree_problem = '', $hihiih = 1)
    {

        global $wpdb;
        $search_tag = '';
        static $trr_cat = array();
        if ($hihiih)
            $trr_cat = array();
        foreach ($catt as $local_cat) {
            $local_cat->name = $tree_problem . $local_cat->name;
            array_push($trr_cat, $local_cat);
            $new_cat_query = "SELECT  a.* ,  COUNT(b.id) AS count, g.par_name AS par_name FROM " . $wpdb->prefix . "spidercatalog_product_categories  AS a LEFT JOIN " . $wpdb->prefix . "spidercatalog_product_categories AS b ON a.id = b.parent LEFT JOIN (SELECT  " . $wpdb->prefix . "spidercatalog_product_categories.ordering as ordering," . $wpdb->prefix . "spidercatalog_product_categories.id AS id, COUNT( " . $wpdb->prefix . "spidercatalog_products.category_id ) AS prod_count
FROM " . $wpdb->prefix . "spidercatalog_products, " . $wpdb->prefix . "spidercatalog_product_categories
WHERE " . $wpdb->prefix . "spidercatalog_products.category_id = " . $wpdb->prefix . "spidercatalog_product_categories.id
GROUP BY " . $wpdb->prefix . "spidercatalog_products.category_id) AS c ON c.id = a.id LEFT JOIN
(SELECT " . $wpdb->prefix . "spidercatalog_product_categories.name AS par_name," . $wpdb->prefix . "spidercatalog_product_categories.id FROM " . $wpdb->prefix . "spidercatalog_product_categories) AS g
 ON a.parent=g.id WHERE a.name LIKE '%" . esc_html($search_tag) . "%' AND a.parent=" . $local_cat->id . " group by a.id";
            $new_cat = $wpdb->get_results($new_cat_query);
            open_cat_in_tree($new_cat, $tree_problem . "— ", 0);
        }
        return $trr_cat;

    }

    $single_products = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'spidercatalog_products WHERE published=\'1\'');
    $categories = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'spidercatalog_product_categories WHERE parent=0 AND published=\'1\'');
    $categories = open_cat_in_tree($categories);

    ?>
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Spider Catalog</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script language="javascript" type="text/javascript"
                src="<?php echo get_option("siteurl"); ?>/wp-includes/js/jquery/jquery.js"></script>
        <script language="javascript" type="text/javascript"
                src="<?php echo get_option("siteurl"); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>        
        <script language="javascript" type="text/javascript"
                src="<?php echo get_option("siteurl"); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
        <script language="javascript" type="text/javascript"
                src="<?php echo get_option("siteurl"); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
        <base target="_self">
        <style>
            #link .panel_wrapper,
            #link div.current {
                height: 160px !important;
            }

        </style>
    </head>
    <body id="link" onLoad="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="" dir="ltr"
          class="forceColors">
    <form name="spider_cat" action="#" method="post">
        <div class="tabs" role="tablist" tabindex="-1">
            <ul>
                <li id="Single_product_tab" class="current" role="tab" tabindex="0"><span><a
                            href="javascript:mcTabs.displayTab('Single_product_tab','Single_product_panel');"
                            onMouseDown="return false;" tabindex="-1">Single product</a></span></li>
                <li id="Products_list_tab" role="tab" tabindex="-1"><span><a
                            href="javascript:mcTabs.displayTab('Products_list_tab','Products_list_panel');"
                            onMouseDown="return false;" tabindex="-1">Products list</a></span></li>

            </ul>
        </div>

        <div class="panel_wrapper">
            <div id="Single_product_panel" class="panel current">
                <br>
                <table border="0" cellpadding="4" cellspacing="0" style="font-size: 11px !important;">
                    <tbody>
                    <tr>
                        <td nowrap="nowrap"><label for="gallerytag">Select product</label></td>
                        <td><select name="spider_catalog_product" id="spider_catalog_product">
                                <option value="- Select a Product -" selected="selected">- Select a Product -</option>
                                <?php
                                foreach ($single_products as $single_product) {
                                    ?>
                                    <option
                                        value="<?php echo $single_product->id; ?>"><?php echo $single_product->name; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="Products_list_panel" class="panel">
                <table border="0" cellspacing="0" style="font-size: 11px !important;">
                    <tbody>
                    <tr>
                        <td nowrap="nowrap"><label for="Spider_cat_Category">Select Category</label></td>
                        <td><select name="Spider_cat_Category" id="Spider_cat_Category">
                                <option value="ALL_CAT" selected="selected">- All categories -</option>
                                <?php
                                foreach ($categories as $categorie) {
                                    ?>
                                    <option
                                        value="<?php echo $categorie->id; ?>"><?php echo $categorie->name; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" valign="top"><label for="showtype">Show category details</label></td>
                        <td><input type="radio" name="cat_show" id="paramsshow_category_details1" value="1"
                                   checked="checked">
                            <label for="paramsshow_category_details1">Show</label>
                            <input type="radio" name="cat_show" id="paramsshow_category_details0" value="0">
                            <label for="paramsshow_category_details0">Hide</label></td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" valign="top"><label for="showtype">Product display type</label></td>
                        <td>
                            <div style="float:left"><input type="radio" name="cat_list" id="paramslist_category_type0" value="" checked="checked" class="paramslist_category">
                            <label for="paramslist_category_type0">Cells 1</label></div>
                            <div style="float:left"><input type="radio" name="cat_list" id="paramslist_category_type1" value="list"class="paramslist_category">
                            <label for="paramslist_category_type1">List</label></div>
                            <div style="float:left"><input type="radio" name="cat_list" id="paramslist_category_type2" value="cells2" class="paramslist_category">
                            <label for="paramslist_category_type2">Cells 2</label></div>
                            <div style="float:left"><input type="radio" name="cat_list" id="paramslist_category_type3" value="wcells" class="paramslist_category">
                            <label for="paramslist_category_type3">Wide Cells</label></div>
                            <div style="float:left"><input type="radio" name="cat_list" id="paramslist_category_type4" value="thumb" class="paramslist_category">
                            <label for="paramslist_category_type4">Thumbnails</label></div>
                            <div style="float:left"><input type="radio" name="cat_list" id="paramslist_category_type5" value="cells3" class="paramslist_category">
                            <label for="paramslist_category_type5">Cells 3</label></div>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" valign="top"><label for="showtype">Show Subcategories</label></td>
                        <td><input type="radio" name="show_sub" id="show_subategory1" value="1" checked="checked">
                            <label for="show_subategory1">Yes</label>
                            <input type="radio" name="show_sub" style="margin-left: 12px;" id="show_subcategory0"
                                   value="0">
                            <label for="show_subcategory0">No</label></td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" valign="top"><label for="showtype">Show Subcategory Products</label></td>
                        <td><input type="radio" name="show_sub_prod" id="show_sub_products1" value="1">
                            <label for="show_sub_products1">All</label>
                            <input type="radio" name="show_sub_prod" style="margin-left: 19px;" id="show_sub_products0"
                                   value="0">
                            <label for="show_sub_products0">No</label>
                            <input type="radio" name="show_sub_prod" id="show_sub_products2" style="margin-left: 12px;"
                                   value="2" checked="checked">
                            <label for="show_sub_products2">Chosen</label></td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" valign="top"><label for="showtype">Show Products</label></td>
                        <td><input type="radio" name="show_prod" id="show_products1" value="1" checked="checked">
                            <label for="show_products1">Yes</label>
                            <input type="radio" name="show_prod" style="margin-left: 12px;" id="show_products0"
                                   value="0">
                            <label for="show_products0">No</label></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mceActionPanel">
            <div style="float: left">
                <input type="button" id="cancel" name="cancel" value="Cancel" onClick="tinyMCEPopup.close();"/>
            </div>

            <div style="float: right">
                <input type="submit" id="insert" name="insert" value="Insert" onClick="insert_spider_catalog();"/>
                <input type="hidden" name="iden" value="1"/>
            </div>
        </div>

    </form>

    <script type="text/javascript">
        function insert_spider_catalog() {

            if (document.getElementById('Single_product_panel').className === 'panel') {


                var lists;
                var show;
                var showsub;
                var showsubprod;
                var showprod;

                lists = "";
                show = 0;
                showsub = 0;
                showsubprod = 0;
                showprod = 0;
                var ttt = document.getElementsByClassName('paramslist_category');
                for (var i=0; i<ttt.length; i++) {
                    if (ttt[i].checked) {
                        lists = ttt[i].value;
                        break;
                    }
                }
                if (document.getElementById('paramsshow_category_details1').checked) {
                    show = 1;
                }
                if (document.getElementById('show_subategory1').checked) {
                    showsub = 1;
                }
                if (document.getElementById('show_sub_products1').checked) {
                    showsubprod = 1;
                }
                if (document.getElementById('show_sub_products2').checked) {
                    showsubprod = 2;
                }
                if (document.getElementById('show_products1').checked) {
                    showprod = 1;
                }

                var tagtext;
                tagtext = '[Spider_Catalog_Category id="' + document.getElementById('Spider_cat_Category').value + '" details="' + show + '" type="' + lists + '" showsub="' + showsub + '" showsubprod="' + showsubprod + '" showprod="' + showprod + '"]';
                window.tinyMCE.execCommand('mceInsertContent', false, tagtext);
                tinyMCEPopup.close();


            }
            else {


                if (document.getElementById('spider_catalog_product').value == '- Select a Product -') {
                    tinyMCEPopup.close();
                }
                else {
                    var tagtext;
                    tagtext = '[Spider_Catalog_Product id="' + document.getElementById('spider_catalog_product').value + '"]';
                    window.tinyMCE.execCommand('mceInsertContent', false, tagtext);
                    tinyMCEPopup.close();
                }


            }
        }

    </script>
    </body>
    </html>


    <?php
    die();
}


add_action('wp_ajax_spiderboxjsphp', 'spider_box_js_php');
add_action('wp_ajax_nopriv_spiderboxjsphp', 'spider_box_js_php');

function spider_box_js_php()
{
    header('Content-Type: text/javascript; charset=UTF-8');
    ?>
    var keyOfOpenImage;
    var listOfImages=Array();
    var slideShowOn;
    var globTimeout;
    var slideShowDelay;
    var viewportheight;
    var viewportwidth;
    var newImg = new Image();
    var LoadingImg = new Image();
    var spiderShop;

    function SetOpacity(elem, opacityAsInt)
    {
        var opacityAsDecimal = opacityAsInt;

        if (opacityAsInt > 100)
        opacityAsInt = opacityAsDecimal = 100;
        else if (opacityAsInt < 0)
        opacityAsInt = opacityAsDecimal = 0;

        opacityAsDecimal /= 100;
        if (opacityAsInt < 1)
        opacityAsInt = 1; // IE7 bug, text smoothing cuts out if 0

        elem.style.opacity = (opacityAsDecimal);
        elem.style.filter  = "alpha(opacity=" + opacityAsInt + ")";
    }

    function FadeOpacity(elemId, fromOpacity, toOpacity, time, fps)
    {
        var steps = Math.ceil(fps * (time / 1000));
        var delta = (toOpacity - fromOpacity) / steps;

        FadeOpacityStep(elemId, 0, steps, fromOpacity,
        delta, (time / steps));
    }

    function FadeOpacityStep(elemId, stepNum, steps, fromOpacity, delta, timePerStep)
    {
        SetOpacity(document.getElementById(elemId),
        Math.round(parseInt(fromOpacity) + (delta * stepNum)));

        if (stepNum < steps)
        setTimeout("FadeOpacityStep('" + elemId + "', " + (stepNum+1)
        + ", " + steps + ", " + fromOpacity + ", "
        + delta + ", " + timePerStep + ");",
        timePerStep);
    }

    function getWinHeight() {
        winH=0;
        if (navigator.appName=="Netscape") {
        winH = window.innerHeight;
        }
        if (navigator.appName.indexOf("Microsoft")!=-1) {
        winH = document.body.offsetHeight;
        }
        return winH;
    }

    function getImageKey(href)
    {
    var key=-1;
    for(i=0; i<listOfImages.length; i++)	{
		if(encodeURI(href)==encodeURI(listOfImages[i]) || href==encodeURI(listOfImages[i]) || encodeURI(href)==listOfImages[i])
			{
				key=i;				break;
			}	}	return key;	
}

function hidePictureAnimated()
{
    FadeOpacity("showPictureAnimated", 100, 0, 500, 10);
    FadeOpacity("showPictureAnimatedBg", 70, 0, 500, 10);
    setTimeout('document.getElementById("showPictureAnimated").style.display="none";',700);
    setTimeout('document.getElementById("showPictureAnimatedBg").style.display="none";',700);
    setTimeout('document.getElementById("showPictureAnimatedTable").style.display="none";',700);
        keyOfOpenImage=-1;
    clearTimeout(globTimeout);
    slideShowOn=0;
}

function showPictureAnimated(href)
{
    newImg = new Image();

    newImg.onload=function() {if(encodeURI(href)==encodeURI(newImg.src) || href==encodeURI(newImg.src) || encodeURI(href)==newImg.src) showPictureAnimatedInner(href, newImg.height, newImg.width);}

    if(document.getElementById("showPictureAnimated").clientHeight>0)
    {
        document.getElementById("showPictureAnimated").style.height=""+document.getElementById("showPictureAnimated").clientHeight+"px";
        LoadingImgMargin=(document.getElementById("showPictureAnimated").clientHeight-LoadingImg.height)/2;
    }
    else
    {
        document.getElementById("showPictureAnimated").style.height="400px";
        LoadingImgMargin=(400-LoadingImg.height)/2;
    }


    document.getElementById("showPictureAnimated").innerHTML='<img style=\'margin-top:"'+LoadingImgMargin+'px\'; src="'+spiderBoxBase+'loadingAnimation.gif"/>';

    if(document.getElementById("showPictureAnimatedBg").style.display=="none")
    FadeOpacity("showPictureAnimatedBg", 0, 70, 500, 10);
    document.getElementById("showPictureAnimatedTable").style.display="table";
    if(darkBG) document.getElementById("showPictureAnimatedBg").style.display="block";
    SetOpacity(document.getElementById("showPictureAnimated"), 100);
    document.getElementById("showPictureAnimated").style.display="block";
    newImg.src = href;
}

function showPictureAnimatedInner(href, newImgheight, newImgwidth)
{
    document.getElementById("showPictureAnimated").style.display="none";

    if(keyOfOpenImage<0) keyOfOpenImage = getImageKey(href);
    boxContainerWidth=0;
    if(newImgheight<=(viewportheight-100) && newImgwidth<=(viewportwidth-50))
    {
        document.getElementById("showPictureAnimated").innerHTML='<img src="'+href+'" border="0" style="cursor:url(\''+spiderBoxBase+'cursor_magnifier_minus.cur\'),pointer;" onClick="hidePictureAnimated();"/>';

        boxContainerWidth=newImgwidth;
    }
    else
    {
        if((newImgheight/viewportheight)>(newImgwidth/viewportwidth))
        {
            document.getElementById("showPictureAnimated").innerHTML='<img src="'+href+'" border="0" style="cursor:url(\''+spiderBoxBase+'cursor_magnifier_minus.cur\'),pointer;" onClick="hidePictureAnimated();" height="'+(viewportheight-100)+'"/>';

            boxContainerWidth=((newImgwidth*(viewportheight-100))/newImgheight);
        }
        else
        {
            document.getElementById("showPictureAnimated").innerHTML='<img src="'+href+'" border="0" style="cursor:url(\''+spiderBoxBase+'cursor_magnifier_minus.cur\'),pointer;" onClick="hidePictureAnimated();" width="'+(viewportwidth-50)+'"/>';
            boxContainerWidth=(viewportwidth-40);
        }
    }
    document.getElementById("boxContainer").style.width=(boxContainerWidth>300)?(""+boxContainerWidth+"px"):"300px";

    document.getElementById("showPictureAnimated").style.height="";

    FadeOpacity("showPictureAnimated", 0, 100, 500, 10);
    document.getElementById("showPictureAnimated").style.display="block";

    if(slideShowOn)
    {
        clearTimeout(globTimeout);
        globTimeout=setTimeout('nextImage()',slideShowDelay);
    }
}

function nextImage()
{
	if(keyOfOpenImage<listOfImages.length-1)
		keyOfOpenImage=keyOfOpenImage+1;
	else
		keyOfOpenImage=0;
		
		showPictureAnimated(listOfImages[keyOfOpenImage]);
}

function prevImage()
{
	if(keyOfOpenImage>0)
		keyOfOpenImage=keyOfOpenImage-1;
	else
		keyOfOpenImage=listOfImages.length-1;		

		showPictureAnimated(listOfImages[keyOfOpenImage]);
}

function toggleSlideShow()
{
	clearTimeout(globTimeout);
    if(!(slideShowOn))
    	{
			document.getElementById("toggleSlideShowCheckbox").src=spiderBoxBase+"pause.png";
			slideShowOn=1;
    		nextImage();
        }
    else
		{
			document.getElementById("toggleSlideShowCheckbox").src=spiderBoxBase+"play.png";
			slideShowOn=0;
		}
}

window.onresize=getViewportSize;

function getViewportSize()
{
 if (typeof window.innerWidth != 'undefined')
 {
      viewportwidth = window.innerWidth,
      viewportheight = window.innerHeight
 }
 
 else if (typeof document.documentElement != 'undefined'
     && typeof document.documentElement.clientWidth !=
     'undefined' && document.documentElement.clientWidth != 0)
 {
       viewportwidth = document.documentElement.clientWidth,
       viewportheight = document.documentElement.clientHeight
 }
 
 else
 {
       viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
       viewportheight = document.getElementsByTagName('body')[0].clientHeight
 }
 
 if(document.getElementById("tdviewportheight")!=undefined)
 document.getElementById("tdviewportheight").style.height=(viewportheight-35)+"px"; 
 
if(document.getElementById("showPictureAnimatedBg")!=undefined)
document.getElementById("showPictureAnimatedBg").style.height=(viewportheight+300)+"px";
}



function SpiderCatAddToOnload()
{
    if((listOfImages.length) == 0){
	

    getViewportSize();
	slideShowDelay=3000;
	slideShowQ=0;	
	allImagesQ=0;
	spiderShop=1;
	darkBG=1;
	keyOfOpenImage=-1;
	spiderBoxBase="<?php echo plugins_url("", __FILE__); ?>/spiderBox/";
	LoadingImg.src=spiderBoxBase+"loadingAnimation.gif";

	
		var spiderBoxElement = document.createElement('span');
	spiderBoxElement.innerHTML+='<div style="position:fixed; top:0px; left:0px; width:100%; height:'+(viewportheight+300)+'px; background-color:#000000; z-index:98; display:none" id="showPictureAnimatedBg"></div>  <table border="0" cellpadding="0" cellspacing="0" id="showPictureAnimatedTable" style="position:fixed; top:0px; left:0px; width:100%; vertical-align:middle; text-align:center; z-index:99; display:none;border:0;"><tr><td valign="middle" id="tdviewportheight" style="height:'+(viewportheight-35)+'px; text-align:center;"><div id="boxContainer" style="margin:auto;width:400px;"><div id="showPictureAnimated" style=" height:400px">&nbsp;</div><div style="text-align:center;background-color:white;padding:5px;padding-bottom:0px;"><div style="width:48px;float:left;">&nbsp;</div><span onclick="prevImage();" style="cursor:pointer;"><img src="'+spiderBoxBase+'prev.png" /></span>&nbsp;&nbsp;<span onclick="nextImage();" style="cursor:pointer;"><img src="'+spiderBoxBase+'next.png" /></span><span onclick="hidePictureAnimated();" style="cursor:pointer;"><img src="'+spiderBoxBase+'close.png" align="right" /></span></div></div></td></tr></table>';

	document.body.appendChild(spiderBoxElement);

	
			for ( i = 0; i < document.getElementsByTagName( 'a' ).length; i++ )
				if(document.getElementsByTagName( 'a' )[i].target=="spiderbox" || ((allImagesQ || spiderShop) && (document.getElementsByTagName( 'a' )[i].href.substr(document.getElementsByTagName( 'a' )[i].href.length-4)==".jpg" || document.getElementsByTagName( 'a' )[i].href.substr(document.getElementsByTagName( 'a' )[i].href.length-4)==".png" || document.getElementsByTagName( 'a' )[i].href.substr(document.getElementsByTagName( 'a' )[i].href.length-4)==".gif" || document.getElementsByTagName( 'a' )[i].href.substr(document.getElementsByTagName( 'a' )[i].href.length-4)==".bmp" || document.getElementsByTagName( 'a' )[i].href.substr(document.getElementsByTagName( 'a' )[i].href.length-4)==".JPG" || document.getElementsByTagName( 'a' )[i].href.substr(document.getElementsByTagName( 'a' )[i].href.length-4)==".PNG" || document.getElementsByTagName( 'a' )[i].href.substr(document.getElementsByTagName( 'a' )[i].href.length-4)==".GIF" || document.getElementsByTagName( 'a' )[i].href.substr(document.getElementsByTagName( 'a' )[i].href.length-4)==".BMP"))) 
					{
						if(typeof(listOfImages.indexOf)=="function")
						{
							if(listOfImages.indexOf(document.getElementsByTagName( 'a' )[i].href)===-1)
								listOfImages[listOfImages.length]=document.getElementsByTagName( 'a' )[i].href;
						}
						else
							listOfImages[listOfImages.length]=document.getElementsByTagName( 'a' )[i].href;

						document.getElementsByTagName( 'a' )[i].href="javascript:showPictureAnimated('"+document.getElementsByTagName( 'a' )[i].href+"')";
                        document.getElementsByTagName( 'a' )[i].style.cursor="url('<?php echo plugins_url("", __FILE__); ?>/spiderBox/cursor_magnifier_plus.cur'),pointer";
						document.getElementsByTagName( 'a' )[i].target="";
						
					}
   }
}
	<?php
	die();
	
	}


add_action('wp_ajax_catalogstarerate', 'spider_catalog_stare_rate');
add_action('wp_ajax_nopriv_catalogstarerate', 'spider_catalog_stare_rate');
function spider_catalog_stare_rate()
{
    spider_starrating();
    die();
}


function spider_starrating()
{
    global $wpdb;
    if (isset($_POST['product_id']))
        $product_id = $_POST['product_id'];
    else
        $product_id = 0;

    if (isset($_POST['vote_value']))
        $vote_value = $_POST['vote_value'];
    else
        $vote_value = 0;


    $save_or_no = $wpdb->insert($wpdb->prefix . 'spidercatalog_product_votes',
        array(
            'product_id' => $product_id,
            'vote_value' => $vote_value,
            'remote_ip' => $_SERVER['REMOTE_ADDR']
        ),
        array(
            '%d',
            '%d',
            '%s'
        )
    );


    if (!$save_or_no) {
        echo "<script> alert('" . $row->getError() . "');
					window.history.go(-1); </script>\n";
        exit();
    }

    $query = $wpdb->prepare("SELECT AVG(vote_value) as rating FROM " . $wpdb->prefix . "spidercatalog_product_votes  WHERE product_id =%d ", $product_id);
    $row1 = $wpdb->get_var($query);

    $rating = substr($row1, 0, 3);

    spider_print_starin_catalog($product_id, $rating);

}


function    spider_print_starin_catalog($product_id, $rating)
{


    $title = __('Rating', 'sp_catalog') . ' ' . $rating . '&nbsp;&nbsp;&nbsp;&nbsp;&#013;' . __('You have already rated this product.', 'sp_catalog');

    echo "<ul class='star-rating1'>
<li class='current-rating' id='current-rating' style=\"width: " . ($rating * 25) . "px\"></li>
	<li><a  title='" . $title . "'  class='one-star'>1</a></li>
	<li><a   title='" . $title . "'  class='two-stars'>2</a></li>
	<li><a  title='" . $title . "'  class='three-stars'>3</a></li>
	<li><a  title='" . $title . "'  class='four-stars'>4</a></li>
	<li><a  title='" . $title . "'  class='five-stars'>5</a></li>
	</ul>
	</td></tr>";
}


add_action('wp_ajax_spidercatalogwdcaptchae', 'spider_catalog_wd_captcha');
add_action('wp_ajax_nopriv_spidercatalogwdcaptchae', 'spider_catalog_wd_captcha');
function spider_catalog_wd_captcha()
{

    $cap_width = 80;
    $cap_height = 30;
    $cap_quality = 100;
    $cap_length_min = 6;
    $cap_length_max = 6;
    $cap_digital = 1;
    $cap_latin_char = 1;
    function code_generic($_length, $_digital = 1, $_latin_char = 1)
    {
        $dig = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
        $lat = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'o',
            'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        $main = array();
        if ($_digital) $main = array_merge($main, $dig);
        if ($_latin_char) $main = array_merge($main, $lat);
        shuffle($main);
        $pass = substr(implode('', $main), 0, $_length);
        return $pass;
    }

    if (isset($_GET['checkcap']) == '1') {
        if ($_GET['checkcap'] == '1') {
            @session_start();
            if (isset($_GET['cap_code'])) {
                if ($_GET['cap_code'] == $_SESSION['captcha_code'])
                    echo 1;
            } else echo 0;
        }
    } else {
        $l = rand($cap_length_min, $cap_length_max);
        $code = code_generic($l, $cap_digital, $cap_latin_char);
        @session_start();
        $_SESSION['captcha_code'] = $code;
        $canvas = imagecreatetruecolor($cap_width, $cap_height);
        $c = imagecolorallocate($canvas, rand(150, 255), rand(150, 255), rand(150, 255));
        imagefilledrectangle($canvas, 0, 0, $cap_width, $cap_height, $c);
        $count = strlen($code);
        $color_text = imagecolorallocate($canvas, 0, 0, 0);
        for ($it = 0; $it < $count; $it++) {
            $letter = $code[$it];
            imagestring($canvas, 6, (10 * $it + 10), $cap_height / 4, $letter, $color_text);
        }
        for ($c = 0; $c < 150; $c++) {
            $x = rand(0, 79);
            $y = rand(0, 29);
            $col = '0x' . rand(0, 9) . '0' . rand(0, 9) . '0' . rand(0, 9) . '0';
            imagesetpixel($canvas, $x, $y, $col);
        }
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-Type: image/jpeg');
        imagejpeg($canvas, null, $cap_quality);

    }
    die('');
}


//////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////               Activate                      ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////


function Spider_Catalog_activate()
{
    global $wpdb;

/// creat database tables

    $sql_spidercatalog_id = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "spidercatalog_id` (
`id1` int(11)  NOT NULL AUTO_INCREMENT,
  `proid` int(11) NOT NULL,
   `cateid` int(11) NOT NULL,

  PRIMARY KEY (`id1`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5";

    $sql_spidercatalog_params = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "spidercatalog_params`(
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(50) 
CHARACTER SET utf8 NOT NULL,
  `title` varchar(200) CHARACTER SET utf8 NOT NULL,
 `description` text CHARACTER SET utf8 NOT NULL,
  `value` varchar(200) CHARACTER SET utf8 NOT NULL,
  
 PRIMARY KEY (`id`)
 
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=89 ";

    $sql_spidercatalog_products = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "spidercatalog_products` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
 `category_id` varchar(200) ,
 `description` text,
  `image_url` text,
  `cost` varchar(128) DEFAULT NULL,
  `market_cost` varchar(128) DEFAULT NULL,
  `param` text,
  `ordering` int(11) NOT NULL,
  `published` tinyint(4) unsigned DEFAULT NULL,
  `published_in_parent` tinyint(4) unsigned DEFAULT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5";

    $sql_spidercatalog_product_categories = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "spidercatalog_product_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `parent` int(11) unsigned DEFAULT NULL,
  `category_image_url` text,
  `description` text,
  `param` text,
  `ordering` int(11) NOT NULL,
  `published` tinyint(4) unsigned DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
  
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ";

    $sql_spidercatalog_product_reviews = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "spidercatalog_product_reviews` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `product_id` int(11) 

NOT NULL,
  `remote_ip` varchar(15) NOT NULL,
  
  PRIMARY KEY (`id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3";

    $sql_spidercatalog_product_votes = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "spidercatalog_product_votes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `remote_ip` varchar(15) NOT NULL,
  `vote_value` int(3) NOT NULL,
  `product_id` int(11) NOT NULL,
  
  PRIMARY KEY (`id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3";


    $table_name = $wpdb->prefix . "spidercatalog_params";
    $sql_1 = <<<query1
INSERT INTO `$table_name` (`name`, `title`,`description`, `value`) VALUES
('price', 'Price', 'Show or hide Price', '1'),
('market_price', 'Market Price', 'Show or hide market Price', '1'),
('currency_symbol', 'Currency Symbol', 'Currency Symbol', '$'),
('currency_symbol_position', 'Currency Symbol Position', 'Currency Symbol Position (after or before number )', '0'),
('enable_rating', 'Enable/Disable Product Ratings', 'Enable/Disable Product Ratings', '1'),
('enable_review', 'Enable/Disable Customer Reviews', 'Enable/Disable Customer Reviews', '1'),
('choose_category', 'Choose category', 'Search product on frontend by category', '1'),
('search_by_name', 'Search by name', 'Search product on frontend by name', '1'),

('single_background_color', 'Background color', 'Product page background color', '#F4F4F4'),
('single_params_background_color1', 'Parameters Background color 1', 'Product page background color of odd parameters', '#F4F2F2'),
('single_params_background_color2', 'Parameters Background color 2', 'Product page background color of odd parameters', '#F4F2F2'),
('single_border_style', 'Border Style', 'Border Style', 'ridge'),
('single_border_width', 'Border Width', 'Border Width', '0'),
('single_border_color', 'Border Color', 'Border Color', '#00AEEF'),
('single_text_color', 'Text Color', 'Text Color', '#636363'),
('single_params_color', 'Color of Parameter Values', 'Color of Parameter Values', '#2F699E'),
('single_price_color', 'Price Color', 'Price Color', '#FFFFFF'),
('market_price_size_big', 'Market Price Size', 'Market Price Size', '12'),
('single_market_price_color', 'Market Price Color', 'Market Price Color', '#FFFFFF'),
('single_title_color', 'Title Color', 'Title Color', '#004372'),
('single_title_background_color', 'Title Background color', 'Title Background color', '#F4F4F4'),
('single_rating_star', 'Rating Star Design', 'Rating Star Design', '3'),
('product_big_title_size', 'Title Size', 'Product Big Title Size', '28'),
('large_picture_width', 'Picture Width', 'Picture Width', '300'),
('large_picture_height', 'Picture Height', 'Picture Height', '200'),
('text_size_big', 'Text Size', 'Text Size', ''),
('price_size_big', 'Price Size', 'Price Size', '20'),
('title_size_big', 'Title Size', 'Title Size', '16'),
('review_background_color', 'Background Color of ''Add your review here'' block', 'Background Color of ''Add your review here'' block', '#F4F4F4'),
('review_color', 'Color of Review Texts', 'Color of Review Texts', '#2F699E'),
('review_author_background_color', 'Background Color of Review Author block', 'Background Color of Review Author block', '#C9EFFE'),
('review_text_background_color', 'Background Color of Author block', 'Background Color of Review text', '#EFF9FD'),
('reviews_perpage', 'Number of reviews per page', 'Number of reviews per page', '10'),
('product_color_add_your_review_here', 'Product color Add your review here', 'Product color Add your review here', '#ffffff'),
('product_back_add_your_review_here', 'Product back Add your review here', 'Product back Add your review here', '#004372'),
('product_price_background_color', 'Product Price Background color', 'Product Price Background color', '#004372'),

('list_background_color', 'Background color', 'Background color', '#FFFFFF'),
('list_border_color', 'Border Color', 'Border Color', '#00AEEF'),
('list_list_border_color', 'Border Color', 'Border Color', '#E8E8E8'),
('list_border_width', 'Border Width', 'Border Width', '0'),
('list_border_style', 'Border Style', 'Border Style', 'ridge'),
('list_text_color', 'Text Color', 'Text Color', '#636363'),
('list_button_color', 'Buttons Text color', 'Color of text of buttons', '#FFFFFF'),
('list_button_background_color', 'Buttons Background color', 'Background Color of buttons', '#004372'),
('list_category_title_size', 'Category title size', 'Category title size', '22'),
('list_category_picture_width', 'Category picture width', 'Category picture width', '300'),
('list_category_picture_height', 'Category picture height', 'Category picture height', '200'),
('list_list_picture_width', 'Picture Width', 'Picture Width', '100'),
('list_list_picture_height', 'Picture Height', 'Picture Height', '100'),
('list_hyperlink_color', 'Hyperlink Color', 'Hyperlink Color', '#000000'),
('list_search_icon_color', 'Search Icon Color', 'Search Icon Color', '#004372'),
('list_reset_icon_color', 'Reset Icon Color', 'Reset Icon Color', '#004372'),
('list_select_icon_color', 'Select icon color', 'Select icon color', '#004372'),
('list_rating_star', 'Rating Star Design', 'Rating Star Design', '3'),
('list_count_of_products_in_the_page', 'Count of Products in the page', 'Count of Products in the page', '10'),
('list_categories_row_color1', 'Categories row color 1', 'Categories row color 1', '#F4F2F2'),
('list_categories_row_color2', 'Categories row color 2', 'Categories row color 2', '#F4F2F2'),
('list_categories_header_background_color', 'Categories header background color', 'Categories header background color', '#E2E2E2'),
('list_categories_header_color', 'Categories text color', 'Categories header text color', '#3D3D3D'),

('cells1_background_color', 'Background color', 'Background color', '#FFFFFF'),
('cells1_border_color', 'Border Color', 'Border Color', '#00AEEF'),
('cells1_border_width', 'Border Width', 'Border Width', '0'),
('cells1_border_style', 'Border Style', 'Border Style', 'ridge'),
('cells1_text_color', 'Text Color', 'Text Color', '#636363'),
('cells1_button_color', 'Buttons Text color', 'Color of text of buttons', '#FFFFFF'),
('cells1_button_background_color', 'Buttons Background color', 'Background Color of buttons', '#004372'),
('cells1_category_title_size', 'Category title size', 'Category title size', '22'),
('cells1_category_picture_width', 'Category picture width', 'Category picture width', '300'),
('cells1_category_picture_height', 'Category picture height', 'Category picture height', '200'),
('cells1_list_picture_width', 'Picture Width', 'Picture Width', '100'),
('cells1_list_picture_height', 'Picture Height', 'Picture Height', '100'),
('cells1_large_picture_width', 'Picture Width', 'Picture Width', '300'),
('cells1_large_picture_height', 'Picture Height', 'Picture Height', '200'),
('cells1_small_picture_width', 'Picture Width', 'Picture Width', '210'),
('cells1_small_picture_height', 'Picture Height', 'Picture Height', '140'),
('cells1_hyperlink_color', 'Hyperlink Color', 'Hyperlink Color', '#000000'),
('cells1_search_icon_color', 'Search Icon Color', 'Search Icon Color', '#004372'),
('cells1_reset_icon_color', 'Reset Icon Color', 'Reset Icon Color', '#004372'),
('cells1_select_icon_color', 'Select icon color', 'Select icon color', '#004372'),
('cells1_rating_star', 'Rating Star Design', 'Rating Star Design', '3'),
('cells1_params_background_color1', 'Parameters Background color 1', 'Background Color of odd parameters', '#F4F2F2'),
('cells1_params_background_color2', 'Parameters Background color 2', 'Background Color of odd parameters', '#F4F2F2'),
('cells1_price_color', 'Price Color', 'Price Color', '#FFFFFF'),
('cells1_market_price_color', 'Market Price Color', 'Market Price Color', '#FFFFFF'),
('cells1_title_color', 'Title Color', 'Title Color', '#004372'),
('cells1_title_background_color', 'Title Background color', 'Title Background color', '#F4F2F2'),
('cells1_count_of_product_in_the_row', 'Count of Products in the Row', 'Count of Products in the Row', '2'),
('cells1_count_of_rows_in_the_page', 'Count of Rows in the Page', 'Count of Rows in the Page', '3'),
('cells1_categories_row_color1', 'Categories row color 1', 'Categories row color 1', '#F4F2F2'),
('cells1_categories_row_color2', 'Categories row color 2', 'Categories row color 2', '#F4F2F2'),
('cells1_categories_header_background_color', 'Categories header background color', 'Categories header background color', '#E2E2E2'),
('cells1_categories_header_color', 'Categories text color', 'Categories header text color', '#3D3D3D'),

('cells2_background_color', 'Background color', 'Background color', '#FFFFFF'),
('cells2_cell_background_color', 'Background color', 'Background color', '#FFFFFF'),
('cells2_border_color', 'Border Color', 'Border Color', '#00AEEF'),
('cells2_border_width', 'Border Width', 'Border Width', '0'),
('cells2_border_style', 'Border Style', 'Border Style', 'ridge'),
('cells2_text_color', 'Text Color', 'Text Color', '#636363'),
('cells2_button_color', 'Buttons Text color', 'Color of text of buttons', '#FFFFFF'),
('cells2_button_background_color', 'Buttons Background color', 'Background Color of buttons', '#004372'),
('cells2_category_title_size', 'Category title size', 'Category title size', '22'),
('cells2_category_picture_width', 'Category picture width', 'Category picture width', '300'),
('cells2_category_picture_height', 'Category picture height', 'Category picture height', '200'),
('cells2_list_picture_width', 'Picture Width', 'Picture Width', '100'),
('cells2_list_picture_height', 'Picture Height', 'Picture Height', '100'),
('cells2_hyperlink_color', 'Hyperlink Color', 'Hyperlink Color', '#000000'),
('cells2_search_icon_color', 'Search Icon Color', 'Search Icon Color', '#004372'),
('cells2_reset_icon_color', 'Reset Icon Color', 'Reset Icon Color', '#004372'),
('cells2_select_icon_color', 'Select icon color', 'Select icon color', '#004372'),
('cells2_rating_star', 'Rating Star Design', 'Rating Star Design', '3'),
('cells2_count_of_product_in_the_row', 'Count of Products in the Row', 'Count of Products in the Row', '2'),
('cells2_count_of_rows_in_the_page', 'Count of Rows in the Page', 'Count of Rows in the Page', '3'),
('cells2_categories_row_color1', 'Categories row color 1', 'Categories row color 1', '#F4F2F2'),
('cells2_categories_row_color2', 'Categories row color 2', 'Categories row color 2', '#F4F2F2'),
('cells2_categories_header_background_color', 'Categories header background color', 'Categories header background color', '#E2E2E2'),
('cells2_categories_header_color', 'Categories text color', 'Categories header text color', '#3D3D3D'),
('cells2_price_color', 'Price color', 'Price color', '#d80303'),
('cells2_market_price_color', 'Market price color', 'Market price color', '#d80303'),

('cells3_background_color', 'Background color', 'Background color', '#FFFFFF'),
('cells3_cell_background_color', 'Background color', 'Background color', '#F5F5F5'),
('cells3_border_color', 'Border Color', 'Border Color', '#00AEEF'),
('cells3_border_width', 'Border Width', 'Border Width', '0'),
('cells3_border_style', 'Border Style', 'Border Style', 'ridge'),
('cells3_text_color', 'Text Color', 'Text Color', '#636363'),
('cells3_button_color', 'Buttons Text color', 'Color of text of buttons', '#FFFFFF'),
('cells3_button_background_color', 'Buttons Background color', 'Background Color of buttons', '#004372'),
('cells3_category_title_size', 'Category title size', 'Category title size', '22'),
('cells3_category_picture_width', 'Category picture width', 'Category picture width', '300'),
('cells3_category_picture_height', 'Category picture height', 'Category picture height', '200'),
('cells3_list_picture_width', 'Picture Width', 'Picture Width', '100'),
('cells3_list_picture_height', 'Picture Height', 'Picture Height', '100'),
('cells3_hyperlink_color', 'Hyperlink Color', 'Hyperlink Color', '#000000'),
('cells3_search_icon_color', 'Search Icon Color', 'Search Icon Color', '#004372'),
('cells3_reset_icon_color', 'Reset Icon Color', 'Reset Icon Color', '#004372'),
('cells3_select_icon_color', 'Select icon color', 'Select icon color', '#004372'),
('cells3_rating_star', 'Rating Star Design', 'Rating Star Design', '3'),
('cells3_count_of_product_in_the_row', 'Count of Products in the Row', 'Count of Products in the Row', '2'),
('cells3_count_of_rows_in_the_page', 'Count of Rows in the Page', 'Count of Rows in the Page', '3'),
('cells3_categories_row_color1', 'Categories row color 1', 'Categories row color 1', '#F4F2F2'),
('cells3_categories_row_color2', 'Categories row color 2', 'Categories row color 2', '#F4F2F2'),
('cells3_categories_header_background_color', 'Categories header background color', 'Categories header background color', '#E2E2E2'),
('cells3_categories_header_color', 'Categories text color', 'Categories header text color', '#3D3D3D'),

('wcells_background_color', 'Background color', 'Background color', '#FFFFFF'),
('wcells_cell_background_color', 'Background color', 'Background color', '#F5F5F5'),
('wcells_border_color', 'Border Color', 'Border Color', '#00AEEF'),
('wcells_border_width', 'Border Width', 'Border Width', '0'),
('wcells_border_style', 'Border Style', 'Border Style', 'ridge'),
('wcells_text_color', 'Text Color', 'Text Color', '#636363'),
('wcells_button_color', 'Buttons Text color', 'Color of text of buttons', '#FFFFFF'),
('wcells_button_background_color', 'Buttons Background color', 'Background Color of buttons', '#004372'),
('wcells_category_title_size', 'Category title size', 'Category title size', '22'),
('wcells_category_picture_width', 'Category picture width', 'Category picture width', '300'),
('wcells_category_picture_height', 'Category picture height', 'Category picture height', '200'),
('wcells_list_picture_width', 'Picture Width', 'Picture Width', '100'),
('wcells_list_picture_height', 'Picture Height', 'Picture Height', '100'),
('wcells_hyperlink_color', 'Hyperlink Color', 'Hyperlink Color', '#000000'),
('wcells_search_icon_color', 'Search Icon Color', 'Search Icon Color', '#004372'),
('wcells_reset_icon_color', 'Reset Icon Color', 'Reset Icon Color', '#004372'),
('wcells_select_icon_color', 'Select icon color', 'Select icon color', '#004372'),
('wcells_rating_star', 'Rating Star Design', 'Rating Star Design', '3'),
('wcells_count_of_products_in_the_page', 'Count of Products in the page', 'Count of Products in the page', '10'),
('wcells_categories_row_color1', 'Categories row color 1', 'Categories row color 1', '#F4F2F2'),
('wcells_categories_row_color2', 'Categories row color 2', 'Categories row color 2', '#F4F2F2'),
('wcells_categories_header_background_color', 'Categories header background color', 'Categories header background color', '#E2E2E2'),
('wcells_categories_header_color', 'Categories text color', 'Categories header text color', '#3D3D3D'),
('wcells_price_size', 'Price Size', 'Price Size', '16'),
('wcells_price_color', 'Price Color', 'Price Color', '#004372'),
('wcells_more_font_color', 'More text color', 'More text color', '#ffffff'),
('wcells_more_background_color', 'More background color', 'More background color', '#004372'),

('thumb_background_color', 'Background color', 'Background color', '#FFFFFF'),
('thumb_cell_background_color', 'Background color', 'Background color', '#F5F5F5'),
('thumb_border_color', 'Border Color', 'Border Color', '#00AEEF'),
('thumb_border_width', 'Border Width', 'Border Width', '0'),
('thumb_border_style', 'Border Style', 'Border Style', 'ridge'),
('thumb_text_color', 'Text Color', 'Text Color', '#636363'),
('thumb_button_color', 'Buttons Text color', 'Color of text of buttons', '#FFFFFF'),
('thumb_button_background_color', 'Buttons Background color', 'Background Color of buttons', '#004372'),
('thumb_category_title_size', 'Category title size', 'Category title size', '22'),
('thumb_category_picture_width', 'Category picture width', 'Category picture width', '300'),
('thumb_category_picture_height', 'Category picture height', 'Category picture height', '200'),
('thumb_list_picture_width', 'Picture Width', 'Picture Width', '100'),
('thumb_list_picture_height', 'Picture Height', 'Picture Height', '100'),
('thumb_hyperlink_color', 'Hyperlink Color', 'Hyperlink Color', '#000000'),
('thumb_search_icon_color', 'Search Icon Color', 'Search Icon Color', '#004372'),
('thumb_reset_icon_color', 'Reset Icon Color', 'Reset Icon Color', '#004372'),
('thumb_select_icon_color', 'Select icon color', 'Select icon color', '#004372'),
('thumb_rating_star', 'Rating Star Design', 'Rating Star Design', '3'),
('thumb_count_of_product_in_the_row', 'Count of Products in the Row', 'Count of Products in the Row', '2'),
('thumb_count_of_rows_in_the_page', 'Count of Rows in the Page', 'Count of Rows in the Page', '3'),
('thumb_categories_row_color1', 'Categories row color 1', 'Categories row color 1', '#F4F2F2'),
('thumb_categories_row_color2', 'Categories row color 2', 'Categories row color 2', '#F4F2F2'),
('thumb_categories_header_background_color', 'Categories header background color', 'Categories header background color', '#E2E2E2'),
('thumb_categories_header_color', 'Categories text color', 'Categories header text color', '#3D3D3D'),


('parameters_select_box_width', 'Parameters Select Box Width', 'Parameters Select Box Width', '104'),
('params_color', 'Color of Parameter Values', 'Color of Parameter Values', '#2F699E'),
('product_cell_width', 'Product Cell Width', 'Product Cell Width', '290'),
('product_cell_height', 'Product Cell Height', 'Product Cell Height', '750'),
('price_size_small', 'Price Size', 'Price Size', '20'),
('market_price_size_small', 'Market Price Size', 'Market Price Size', '12'),
('title_size_small', 'Title Size', 'Title Size', '16'),
('text_size_list', 'List text size', 'List text size', '14'),
('price_size_list', 'List price size', 'List price size', '18'),
('market_price_size_list', 'List Market price size', 'List Market price size', '11'),
('cell_show_category', 'Show Category', 'Show Category', '1'),
('list_show_category', 'Show Category', 'Show Category', '1'),
('cell_show_parameters', 'Show Parameters', 'Show Parameters', '1'),
('list_show_parameters', 'Show Parameters', 'Show Parameters', '1'),
('list_show_description', 'Show Description', 'Show Description', '1'),
('width_spider_main_table_lists', 'Product List  Width', 'Product List  Width', '620'),
('category_details_width', 'Category Details Width', 'Category Details Width', '600'),
('spider_catalog_product_page_width', 'Product Page Width', 'Product Page Width', '600'),
('description_size_list', 'Description Text Size', 'Description Text Size', '12'),
('name_price_size_list', 'Name Price List Text Size', 'Name Price List Text Size', '12'),
('Parameters_size_list', 'Parameters List Text Size', 'Parameters List Text Size', '12'),
('cell_big_title_size', 'Cell Big Title Size', 'Cell Big Title Size', '34'),
('cell_price_background_color', 'Cell Price Background Color', 'Cell Price Background Color', '#004372'),
('cell_small_image_backround_color', 'Cell Small Image Backround Color', 'Cell Small Image Backround Color', '#F4F2F2'),
('cell_parameters_left_size', 'Cell Parameters Left Size', 'Cell Parameters Left Size', '13'),
('cell_more_font_size', 'Cell More Font size', 'Cell More Font size', '15'),
('cell_more_font_color', 'Cell More Font Color', 'Cell More Font Color', '#FFFFFF'),
('cell_more_background_color', 'Cell More Background Color', 'Cell More Background Color', '#004372'),
('cell_params_text_color', 'Cell Params Text Color', 'Cell Params Text Color', '#3E3E3E'),
('cell_new_big_title_size', 'Cell New Big Title Size', 'Cell New Big Title Size', '28'),
('cell_new_title_size', 'Cell New Title Size', 'Cell New Title Size', '13'),
('cell_new_price_size', 'Cell New Price Size', 'Cell New Price Size', '20'),
('cell_new_market_price_size', 'Cell New Market Price Size', 'Cell New Market Price Size', '12'),
('cell_new_picture_width', 'Cell New Picture Width', 'Cell New Picture Width', '110'),
('cell_new_picture_height', 'Cell New Picture Height', 'Cell New Picture Height', '95'),
('cell_new_title_color', 'Cell New Title Color', 'Cell New Title Color', '#004372'),
('new_cell_all_width', 'New Cell Width', 'New Cell Width', '290'),
('new_cell_all_height', 'New Cell All Height', 'New Cell All Height', '580'),
('cell_new_text_size', 'Cell New Text Size', 'Cell New Text Size', '12'),
('cell_new_text_back_color_1', 'Cell New Text Background Color 1', 'Cell New Text Background Color 1', '#F6F6F6'),
('cell_new_text_back_color_2', 'Cell New Text Background Color 2', 'Cell New Text Background Color 2', '#F0EDED'),
('cell_new_text_color', 'Cell New Text Color', 'Cell New Text Color', '#3D3D3D'),
('new_cell_more_font_size', 'New Cell More Font Size', 'New Cell More Font Size', '17'),
('cell_new_more_font_color', 'More Font Color', 'More Font Color', '#FFFFFF'),
('cell_new_more_background_color', 'More Background Color', 'More Background Color', '#004372'),
('cell_tumble_title_size', 'Title Size', 'Title Size', '14'),
('cell_tumble_title_font_color', 'Title Font Color', 'Title Font Color', '#004372'),
('cell_tumble_price_size', 'Price Size', 'Price Size', '14'),
('cell_tumble_price_text_color', 'Price Text Color', 'Price Text Color', '#FFFFFF'),
('cell_tumble_picture_width', 'Picture Width', 'Picture Width', '120'),
('cell_tumble_picture_height', 'Picture Height', 'Picture Height', '120'),
('cell_tumble_text_size', 'Text Size', 'Text Size', '12'),
('cell_tumble_text_color', 'Text Color', 'Text Color', '#434242'),
('cell_tumble_all_width', 'All Width', 'All Width', '290'),
('cell_tumble_all_height', 'All Height', 'All Height', '225'),
('all_cell_title_size', 'Title Size', 'Title Sizes', '18'),
('all_cell_title_color', 'Title Color', 'Title Color', '#004372'),
('all_cell_price_size', 'Price Size', 'Price Size', '18'),
('all_cell_price_text_color', 'Price Text Color', 'Price Text Color', '#FFFFFF'),
('all_cell_picture_width', 'Picture Width', 'Picture Width', '285'),
('all_cell_picture_height', 'Picture Height', 'Picture Height', '200'),
('all_cell_text_size', 'Text Size', 'Text Size', '15'),
('all_cell_text_color', 'Text Color', 'Text Color', '#434242'),
('all_cell_all_width', 'All Width', 'All Width', '290'),
('all_cell_all_height', 'All Height', 'All Height', '470'),
('single_cell_title_size', 'Title Size', 'Title Size', '18'),
('single_cell_title_color', 'Title Color', 'Title Color', '#004372'),
('single_cell_font_1_size', 'Font 1 Size', 'Font 1 Size', '14'),
('single_cell_font_2_size', 'Font 2 Size', 'Font 2 Size', '12'),
('single_cell_background_color_1', 'Background Color 1', 'Background Color 1', '#F5F4F4'),
('single_cell_background_color_2', 'Background Color 2', 'Background Color 2', '#FFFFFF'),
('single_cell_text_color_1', 'Text Color 1', 'Text Color 1', '#004372'),
('single_cell_text_color_2', 'Text Color 2', 'Text Color 2', '#636363'),
('single_cell_picture_width', 'Picture Width', 'Picture Width', '215'),
('single_cell_picture_height', 'Picture Height', 'Picture Height', '200'),
('list_page_up_names_text_color', 'List Page Up Names Text Color', 'List Page Up Names Text Color', '#3D3D3D'),
('list_page_up_names_background_color', 'List Page Up names Background Color', 'List Page Up names Background Color', '#E2E2E2'),
('list_page_background_color_1', 'List Page Background Color 1', 'List Page Background Color 1', '#F6F6F6'),
('list_page_background_color_2', 'List Page Background Color 2', 'List Page Background Color 2', '#FFFFFF'),
('list_cell_price_color', 'List Cell Price Color', 'List Cell Price Color', '#B02E2E'),
('list_cell_market_price_color', 'List Cell Market Price Color', 'List Cell Market Price Color', '#3C6680'),
('list_page_text_color_1', 'Text Color 1', 'Text Color 1', '#3E3E3E'),
('list_page_text_color_2', 'Text Color 2', 'Text Color 2', '#235775');

query1;

    $table_name = $wpdb->prefix . "spidercatalog_products";
    $sql_2 = "
INSERT INTO 

`" . $table_name . "` (`id`, `name`, `category_id`, `description`, `image_url`, `cost`, `market_cost`, `param`,

`ordering`, `published`, `published_in_parent`) VALUES
(1, 'Panasonic Television TX-PR50U30', '1,', '<p>50&quot; / 127 cm, Full-HD Plasma Display Panel,

600 Hz Sub Field Drive , DVB-T, DVB-C, RCA, RGB, VGA, HDMI x2, Scart, SD card</p>', 

'" . plugins_url("Front_images/sampleimages/7_19977_1324390185.jpg", __FILE__) . "******0;;;" . plugins_url("Front_images/sampleimages/11448_2.jpg", __FILE__) . "******0;;;" . plugins_url("Front_images/sampleimages/panasonictx_pr50u30.jpg", __FILE__) . "', '950.00', '1000.00', 'par_TV

System@@:@@DVB-T	DVB-C		par_Diagonal@@:@@50&quot; / 127 cm		par_Interface@@:@@RCA, RGB, VGA, HDMI 

x2, Scart, SD card		par_Refresh Rate@@:@@600 Hz Sub Field Drive		', 2, 1, 0),
(2, 'Sony KDL-46EX710AEP ', 

'1,', '<p><b>Sony Television KDL-46EX710AEP</b></p><p>46&quot; / 117 cm, MotionFlow 100Hz, Bravia Engine 3, Analog, DVB-T, DVB-

C, 4xHDMI, VGA, RGB, RCA, USB, 2xSCART </p>', 

'" . plugins_url("Front_images/sampleimages/7_7557_1298400832.jpg", __FILE__) . "******0;;;" . plugins_url("Front_images/sampleimages/r1.jpg", __FILE__) . "******0;;;" . plugins_url("Front_images/sampleimages/sony_kdl32ex700aep_3.jpg", __FILE__) . "', '1450.00', '1700.00', 'par_TV

System@@:@@Analog	DVB-T	DVB-C		par_Diagonal@@:@@46&quot; / 117 cm		par_Interface@@:@@4xHDMI, 

VGA, RGB, RCA, USB, 2xSCART		par_Refresh Rate@@:@@MotionFlow 100Hz		', 1, 1, 0),
(3, 'Samsung UE46D6100S', '1,',

'<p><strong>Samsung Television UE46D6100S </strong></p><p>46&quot; / 117 cm, 200Hz , 6 Series, 3D, SMART TV ,Smarthub, 3D 

HyperReal Engine, Samsung Apps, Social TV, WiFi Ready</p>', 

'" . plugins_url("Front_images/sampleimages/7_19644_1323935170.jpg", __FILE__) . "', '1630.00', '1900.00', 'par_TV System@@:@@DTV

DVB-T/C		par_Diagonal@@:@@46&quot; / 117 cm		par_Interface@@:@@4xHDMI,3xUSB, RGB, RCA, D-SUB,1xSCART, 

Ethernet (LAN)		par_Refresh Rate@@:@@200Hz		', 3, 1, 0),
(4, 'Sony KDL-32EX421BAEP ', '1,', '<p><strong>Sony

Television KDL-32EX421BAEP </strong></p><p>32&quot; / 80 cm, 50 Hz, Analog, DVB-T/T2/C, AV, SCART, RGB, VGA, HDMI x4, USB x2, 

Ethernet (RJ-45),24p True Cinema, X-Reality, DLNA, WiFi Ready, Internet Video, Internet Widgets, Web Browser, Skype, USB HDD 

Recording</p>', '" . plugins_url("Front_images/sampleimages/7_20107_1324712747.jpg", __FILE__) . "', '950.00', '0.00', 'par_TV

System@@:@@	par_Diagonal@@:@@32&quot; / 80 cm		par_Interface@@:@@AV, VGA, HDMI, USB, Ethernet 		

par_Refresh Rate@@:@@	', 4, 1, 0)";


    $table_name = $wpdb->prefix . "spidercatalog_product_categories";


    $sql_3 = "

INSERT INTO `$table_name` (`id`, `name`, `parent`, `category_image_url`, 

`description`, `param`, `ordering`, `published`) VALUES
(1, 'Televisions', 0,

'" . plugins_url("Front_images/sampleimages/category242.jpg", __FILE__) . "', 'Television (TV) is a telecommunication medium for

transmitting and receiving moving images that can be monochrome (black-and-white) or colored, with or without accompanying 

sound. &quot;Television&quot; may also refer specifically to a television set, television programming, or television 

transmission.The etymology of the word has a mixed Latin and Greek origin, meaning &quot;far sight&quot;: Greek tele, far, 

and Latin visio, sight (from video, vis- to see, or to view in the first person).', 'TV System	Diagonal	Interface	Refresh Rate		', 1, 1)";


    $table_name = $wpdb->prefix . "spidercatalog_product_reviews";

    $sql_4 = <<<query4

INSERT INTO `$table_name` (`id`, `name`, `content`, `product_id`, 

`remote_ip`) VALUES
(2, 'A Customer', 'A Good TV', 1, '127.0.0.1');

query4;

    $table_name = $wpdb->prefix . "spidercatalog_product_votes";
    $sql_5 = <<<query5
INSERT INTO `$table_name` (`id`, 

`remote_ip`, `vote_value`, `product_id`) VALUES
(6, '127.0.0.1', 4, 1),
(7, '127.0.0.1', 5, 2);

query5;

    $table_name = $wpdb->prefix . "spidercatalog_id";
    $sql_6 = <<<query6
INSERT INTO `$table_name` (`id1`, `proid`, `cateid`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1);

query6;


    $wpdb->query($sql_spidercatalog_id);
    $wpdb->query($sql_spidercatalog_params);
    $wpdb->query($sql_spidercatalog_products);
    $wpdb->query($sql_spidercatalog_product_categories);
    $wpdb->query($sql_spidercatalog_product_reviews);
    $wpdb->query($sql_spidercatalog_product_votes);


    if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "spidercatalog_params")) {
        $wpdb->query($sql_1);
    }
    if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "spidercatalog_products")) {
      $wpdb->query($sql_2);
    }
    if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "spidercatalog_product_categories")) {
      $wpdb->query($sql_3);
    }
    if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "spidercatalog_product_reviews")) {
      $wpdb->query($sql_4);
    }
    if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "spidercatalog_product_votes")) {
      $wpdb->query($sql_5);
    }
    if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "spidercatalog_id")) {
      $wpdb->query($sql_6);
    }

}


register_activation_hook(__FILE__, 'Spider_Catalog_activate');

function get_attachment_id_from_src($image_src)
{
    global $wpdb;
    $id = 0;
    $image_src = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $image_src);
    $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
    $id = $wpdb->get_var($query);
    if (!$id)
        $id = 0;
    return $image_src . '******' . $id;
}

if (get_bloginfo('version') >= 3.1) {

    add_action('plugins_loaded', 'spidercatalog');

} else {
    spidercatalog();
}
function spidercatalog()
{
    global $wpdb;

    $parent = $wpdb->get_results("DESCRIBE " . $wpdb->prefix . "spidercatalog_product_categories", ARRAY_A);
    $published_in_parent = $wpdb->get_results("DESCRIBE " . $wpdb->prefix . "spidercatalog_products", ARRAY_A);

    $exists_parent = 0;
    $exists_published_in_parent = 0;

    foreach ($parent as $parentt) {
        if ($parentt['Field'] == 'parent') $exists_parent = 1;
    }
    foreach ($published_in_parent as $published_in_parentt) {
        if ($published_in_parentt['Field'] == 'published_in_parent') $exists_published_in_parent = 1;
    }

    if (!$exists_parent) {
        $wpdb->query("ALTER TABLE " . $wpdb->prefix . "spidercatalog_product_categories ADD `parent` int(11) unsigned NOT NULL AFTER `name`");
    }
    if (!$exists_published_in_parent) {
        $wpdb->query("ALTER TABLE " . $wpdb->prefix . "spidercatalog_products ADD `published_in_parent` tinyint(4) unsigned NOT NULL AFTER `published`");
    }

    $product = $wpdb->get_results("DESCRIBE " . $wpdb->prefix . "spidercatalog_products", ARRAY_A);
    $isUpdate = 0;
    foreach ($product as $prod) {
        if ($prod['Field'] == 'category_id' && $prod['Type'] == 'int(11) unsigned') {
            $isUpdate = 1;
            break;
        }
    }
    if ($isUpdate) {
        $wpdb->query("ALTER TABLE `" . $wpdb->prefix . "spidercatalog_products` modify category_id  varchar(200) binary");
        $wpdb->query("UPDATE `" . $wpdb->prefix . "spidercatalog_products` SET category_id=CONCAT(category_id,',')");

        $wpdb->query("DELETE FROM `" . $wpdb->prefix . "spidercatalog_params`");
        

        $sql_query="INSERT INTO `" . $wpdb->prefix . "spidercatalog_params` (`name`, `title`, `description`, `value`) VALUES
            ('price', 'Price', 'Show or hide Price', '1'),
            ('market_price', 'Market Price', 'Show or hide market Price', '1'),
            ('currency_symbol', 'Currency Symbol', 'Currency Symbol', '$'),
            ('currency_symbol_position', 'Currency Symbol Position', 'Currency Symbol Position (after or before number )', '0'),
            ('enable_rating', 'Enable/Disable Product Ratings', 'Enable/Disable Product Ratings', '1'),
            ('enable_review', 'Enable/Disable Customer Reviews', 'Enable/Disable Customer Reviews', '1'),
            ('choose_category', 'Choose category', 'Search product on frontend by category', '1'),
            ('search_by_name', 'Search by name', 'Search product on frontend by name', '1'),

            ('single_background_color', 'Background color', 'Product page background color', '#F4F4F4'),
            ('single_params_background_color1', 'Parameters Background color 1', 'Product page background color of odd parameters', '#F4F2F2'),
            ('single_params_background_color2', 'Parameters Background color 2', 'Product page background color of odd parameters', '#F4F2F2'),
            ('single_border_style', 'Border Style', 'Border Style', 'ridge'),
            ('single_border_width', 'Border Width', 'Border Width', '0'),
            ('single_border_color', 'Border Color', 'Border Color', '#00AEEF'),
            ('single_text_color', 'Text Color', 'Text Color', '#636363'),
            ('single_params_color', 'Color of Parameter Values', 'Color of Parameter Values', '#2F699E'),
            ('single_price_color', 'Price Color', 'Price Color', '#FFFFFF'),
            ('market_price_size_big', 'Market Price Size', 'Market Price Size', '12'),
            ('single_market_price_color', 'Market Price Color', 'Market Price Color', '#FFFFFF'),
            ('single_title_color', 'Title Color', 'Title Color', '#004372'),
            ('single_title_background_color', 'Title Background color', 'Title Background color', '#F4F4F4'),
            ('single_rating_star', 'Rating Star Design', 'Rating Star Design', '3'),
            ('product_big_title_size', 'Title Size', 'Product Big Title Size', '28'),
            ('large_picture_width', 'Picture Width', 'Picture Width', '300'),
            ('large_picture_height', 'Picture Height', 'Picture Height', '200'),
            ('text_size_big', 'Text Size', 'Text Size', ''),
            ('price_size_big', 'Price Size', 'Price Size', '20'),
            ('title_size_big', 'Title Size', 'Title Size', '16'),
            ('review_background_color', 'Background Color of ''Add your review here'' block', 'Background Color of ''Add your review here'' block', '#F4F4F4'),
            ('review_color', 'Color of Review Texts', 'Color of Review Texts', '#2F699E'),
            ('review_author_background_color', 'Background Color of Review Author block', 'Background Color of Review Author block', '#C9EFFE'),
            ('review_text_background_color', 'Background Color of Author block', 'Background Color of Review text', '#EFF9FD'),
            ('reviews_perpage', 'Number of reviews per page', 'Number of reviews per page', '10'),
            ('product_color_add_your_review_here', 'Product color Add your review here', 'Product color Add your review here', '#ffffff'),
            ('product_back_add_your_review_here', 'Product back Add your review here', 'Product back Add your review here', '#004372'),
            ('product_price_background_color', 'Product Price Background color', 'Product Price Background color', '#004372'),

            ('list_background_color', 'Background color', 'Background color', '#FFFFFF'),
            ('list_border_color', 'Border Color', 'Border Color', '#00AEEF'),
            ('list_list_border_color', 'Border Color', 'Border Color', '#E8E8E8'),
            ('list_border_width', 'Border Width', 'Border Width', '0'),
            ('list_border_style', 'Border Style', 'Border Style', 'ridge'),
            ('list_text_color', 'Text Color', 'Text Color', '#636363'),
            ('list_button_color', 'Buttons Text color', 'Color of text of buttons', '#FFFFFF'),
            ('list_button_background_color', 'Buttons Background color', 'Background Color of buttons', '#004372'),
            ('list_category_title_size', 'Category title size', 'Category title size', '22'),
            ('list_category_picture_width', 'Category picture width', 'Category picture width', '300'),
            ('list_category_picture_height', 'Category picture height', 'Category picture height', '200'),
            ('list_list_picture_width', 'Picture Width', 'Picture Width', '100'),
            ('list_list_picture_height', 'Picture Height', 'Picture Height', '100'),
            ('list_hyperlink_color', 'Hyperlink Color', 'Hyperlink Color', '#000000'),
            ('list_search_icon_color', 'Search Icon Color', 'Search Icon Color', '#004372'),
            ('list_reset_icon_color', 'Reset Icon Color', 'Reset Icon Color', '#004372'),
            ('list_select_icon_color', 'Select icon color', 'Select icon color', '#004372'),
            ('list_rating_star', 'Rating Star Design', 'Rating Star Design', '3'),
            ('list_count_of_products_in_the_page', 'Count of Products in the page', 'Count of Products in the page', '10'),
            ('list_categories_row_color1', 'Categories row color 1', 'Categories row color 1', '#F4F2F2'),
            ('list_categories_row_color2', 'Categories row color 2', 'Categories row color 2', '#F4F2F2'),
            ('list_categories_header_background_color', 'Categories header background color', 'Categories header background color', '#E2E2E2'),
            ('list_categories_header_color', 'Categories text color', 'Categories header text color', '#3D3D3D'),

            ('cells1_background_color', 'Background color', 'Background color', '#FFFFFF'),
            ('cells1_border_color', 'Border Color', 'Border Color', '#00AEEF'),
            ('cells1_border_width', 'Border Width', 'Border Width', '0'),
            ('cells1_border_style', 'Border Style', 'Border Style', 'ridge'),
            ('cells1_text_color', 'Text Color', 'Text Color', '#636363'),
            ('cells1_button_color', 'Buttons Text color', 'Color of text of buttons', '#FFFFFF'),
            ('cells1_button_background_color', 'Buttons Background color', 'Background Color of buttons', '#004372'),
            ('cells1_category_title_size', 'Category title size', 'Category title size', '22'),
            ('cells1_category_picture_width', 'Category picture width', 'Category picture width', '300'),
            ('cells1_category_picture_height', 'Category picture height', 'Category picture height', '200'),
            ('cells1_list_picture_width', 'Picture Width', 'Picture Width', '100'),
            ('cells1_list_picture_height', 'Picture Height', 'Picture Height', '100'),
            ('cells1_large_picture_width', 'Picture Width', 'Picture Width', '300'),
            ('cells1_large_picture_height', 'Picture Height', 'Picture Height', '200'),
            ('cells1_small_picture_width', 'Picture Width', 'Picture Width', '210'),
            ('cells1_small_picture_height', 'Picture Height', 'Picture Height', '140'),
            ('cells1_hyperlink_color', 'Hyperlink Color', 'Hyperlink Color', '#000000'),
            ('cells1_search_icon_color', 'Search Icon Color', 'Search Icon Color', '#004372'),
            ('cells1_reset_icon_color', 'Reset Icon Color', 'Reset Icon Color', '#004372'),
            ('cells1_select_icon_color', 'Select icon color', 'Select icon color', '#004372'),
            ('cells1_rating_star', 'Rating Star Design', 'Rating Star Design', '3'),
            ('cells1_params_background_color1', 'Parameters Background color 1', 'Background Color of odd parameters', '#F4F2F2'),
            ('cells1_params_background_color2', 'Parameters Background color 2', 'Background Color of odd parameters', '#F4F2F2'),
            ('cells1_price_color', 'Price Color', 'Price Color', '#FFFFFF'),
            ('cells1_market_price_color', 'Market Price Color', 'Market Price Color', '#FFFFFF'),
            ('cells1_title_color', 'Title Color', 'Title Color', '#004372'),
            ('cells1_title_background_color', 'Title Background color', 'Title Background color', '#F4F2F2'),
            ('cells1_count_of_product_in_the_row', 'Count of Products in the Row', 'Count of Products in the Row', '2'),
            ('cells1_count_of_rows_in_the_page', 'Count of Rows in the Page', 'Count of Rows in the Page', '3'),
            ('cells1_categories_row_color1', 'Categories row color 1', 'Categories row color 1', '#F4F2F2'),
            ('cells1_categories_row_color2', 'Categories row color 2', 'Categories row color 2', '#F4F2F2'),
            ('cells1_categories_header_background_color', 'Categories header background color', 'Categories header background color', '#E2E2E2'),
            ('cells1_categories_header_color', 'Categories text color', 'Categories header text color', '#3D3D3D'),

            ('cells2_background_color', 'Background color', 'Background color', '#FFFFFF'),
            ('cells2_cell_background_color', 'Background color', 'Background color', '#FFFFFF'),
            ('cells2_border_color', 'Border Color', 'Border Color', '#00AEEF'),
            ('cells2_border_width', 'Border Width', 'Border Width', '0'),
            ('cells2_border_style', 'Border Style', 'Border Style', 'ridge'),
            ('cells2_text_color', 'Text Color', 'Text Color', '#636363'),
            ('cells2_button_color', 'Buttons Text color', 'Color of text of buttons', '#FFFFFF'),
            ('cells2_button_background_color', 'Buttons Background color', 'Background Color of buttons', '#004372'),
            ('cells2_category_title_size', 'Category title size', 'Category title size', '22'),
            ('cells2_category_picture_width', 'Category picture width', 'Category picture width', '300'),
            ('cells2_category_picture_height', 'Category picture height', 'Category picture height', '200'),
            ('cells2_list_picture_width', 'Picture Width', 'Picture Width', '100'),
            ('cells2_list_picture_height', 'Picture Height', 'Picture Height', '100'),
            ('cells2_hyperlink_color', 'Hyperlink Color', 'Hyperlink Color', '#000000'),
            ('cells2_search_icon_color', 'Search Icon Color', 'Search Icon Color', '#004372'),
            ('cells2_reset_icon_color', 'Reset Icon Color', 'Reset Icon Color', '#004372'),
            ('cells2_select_icon_color', 'Select icon color', 'Select icon color', '#004372'),
            ('cells2_rating_star', 'Rating Star Design', 'Rating Star Design', '3'),
            ('cells2_count_of_product_in_the_row', 'Count of Products in the Row', 'Count of Products in the Row', '2'),
            ('cells2_count_of_rows_in_the_page', 'Count of Rows in the Page', 'Count of Rows in the Page', '3'),
            ('cells2_categories_row_color1', 'Categories row color 1', 'Categories row color 1', '#F4F2F2'),
            ('cells2_categories_row_color2', 'Categories row color 2', 'Categories row color 2', '#F4F2F2'),
            ('cells2_categories_header_background_color', 'Categories header background color', 'Categories header background color', '#E2E2E2'),
            ('cells2_categories_header_color', 'Categories text color', 'Categories header text color', '#3D3D3D'),
            ('cells2_price_color', 'Price color', 'Price color', '#d80303'),
            ('cells2_market_price_color', 'Market price color', 'Market price color', '#d80303'),

            ('cells3_background_color', 'Background color', 'Background color', '#FFFFFF'),
            ('cells3_cell_background_color', 'Background color', 'Background color', '#F5F5F5'),
            ('cells3_border_color', 'Border Color', 'Border Color', '#00AEEF'),
            ('cells3_border_width', 'Border Width', 'Border Width', '0'),
            ('cells3_border_style', 'Border Style', 'Border Style', 'ridge'),
            ('cells3_text_color', 'Text Color', 'Text Color', '#636363'),
            ('cells3_button_color', 'Buttons Text color', 'Color of text of buttons', '#FFFFFF'),
            ('cells3_button_background_color', 'Buttons Background color', 'Background Color of buttons', '#004372'),
            ('cells3_category_title_size', 'Category title size', 'Category title size', '22'),
            ('cells3_category_picture_width', 'Category picture width', 'Category picture width', '300'),
            ('cells3_category_picture_height', 'Category picture height', 'Category picture height', '200'),
            ('cells3_list_picture_width', 'Picture Width', 'Picture Width', '100'),
            ('cells3_list_picture_height', 'Picture Height', 'Picture Height', '100'),
            ('cells3_hyperlink_color', 'Hyperlink Color', 'Hyperlink Color', '#000000'),
            ('cells3_search_icon_color', 'Search Icon Color', 'Search Icon Color', '#004372'),
            ('cells3_reset_icon_color', 'Reset Icon Color', 'Reset Icon Color', '#004372'),
            ('cells3_select_icon_color', 'Select icon color', 'Select icon color', '#004372'),
            ('cells3_rating_star', 'Rating Star Design', 'Rating Star Design', '3'),
            ('cells3_count_of_product_in_the_row', 'Count of Products in the Row', 'Count of Products in the Row', '2'),
            ('cells3_count_of_rows_in_the_page', 'Count of Rows in the Page', 'Count of Rows in the Page', '3'),
            ('cells3_categories_row_color1', 'Categories row color 1', 'Categories row color 1', '#F4F2F2'),
            ('cells3_categories_row_color2', 'Categories row color 2', 'Categories row color 2', '#F4F2F2'),
            ('cells3_categories_header_background_color', 'Categories header background color', 'Categories header background color', '#E2E2E2'),
            ('cells3_categories_header_color', 'Categories text color', 'Categories header text color', '#3D3D3D'),

            ('wcells_background_color', 'Background color', 'Background color', '#FFFFFF'),
            ('wcells_cell_background_color', 'Background color', 'Background color', '#F5F5F5'),
            ('wcells_border_color', 'Border Color', 'Border Color', '#00AEEF'),
            ('wcells_border_width', 'Border Width', 'Border Width', '0'),
            ('wcells_border_style', 'Border Style', 'Border Style', 'ridge'),
            ('wcells_text_color', 'Text Color', 'Text Color', '#636363'),
            ('wcells_button_color', 'Buttons Text color', 'Color of text of buttons', '#FFFFFF'),
            ('wcells_button_background_color', 'Buttons Background color', 'Background Color of buttons', '#004372'),
            ('wcells_category_title_size', 'Category title size', 'Category title size', '22'),
            ('wcells_category_picture_width', 'Category picture width', 'Category picture width', '300'),
            ('wcells_category_picture_height', 'Category picture height', 'Category picture height', '200'),
            ('wcells_list_picture_width', 'Picture Width', 'Picture Width', '100'),
            ('wcells_list_picture_height', 'Picture Height', 'Picture Height', '100'),
            ('wcells_hyperlink_color', 'Hyperlink Color', 'Hyperlink Color', '#000000'),
            ('wcells_search_icon_color', 'Search Icon Color', 'Search Icon Color', '#004372'),
            ('wcells_reset_icon_color', 'Reset Icon Color', 'Reset Icon Color', '#004372'),
            ('wcells_select_icon_color', 'Select icon color', 'Select icon color', '#004372'),
            ('wcells_rating_star', 'Rating Star Design', 'Rating Star Design', '3'),
            ('wcells_count_of_products_in_the_page', 'Count of Products in the page', 'Count of Products in the page', '10'),
            ('wcells_categories_row_color1', 'Categories row color 1', 'Categories row color 1', '#F4F2F2'),
            ('wcells_categories_row_color2', 'Categories row color 2', 'Categories row color 2', '#F4F2F2'),
            ('wcells_categories_header_background_color', 'Categories header background color', 'Categories header background color', '#E2E2E2'),
            ('wcells_categories_header_color', 'Categories text color', 'Categories header text color', '#3D3D3D'),
            ('wcells_price_size', 'Price Size', 'Price Size', '16'),
            ('wcells_price_color', 'Price Color', 'Price Color', '#004372'),
            ('wcells_more_font_color', 'More text color', 'More text color', '#ffffff'),
            ('wcells_more_background_color', 'More background color', 'More background color', '#004372'),

            ('thumb_background_color', 'Background color', 'Background color', '#FFFFFF'),
            ('thumb_cell_background_color', 'Background color', 'Background color', '#F5F5F5'),
            ('thumb_border_color', 'Border Color', 'Border Color', '#00AEEF'),
            ('thumb_border_width', 'Border Width', 'Border Width', '0'),
            ('thumb_border_style', 'Border Style', 'Border Style', 'ridge'),
            ('thumb_text_color', 'Text Color', 'Text Color', '#636363'),
            ('thumb_button_color', 'Buttons Text color', 'Color of text of buttons', '#FFFFFF'),
            ('thumb_button_background_color', 'Buttons Background color', 'Background Color of buttons', '#004372'),
            ('thumb_category_title_size', 'Category title size', 'Category title size', '22'),
            ('thumb_category_picture_width', 'Category picture width', 'Category picture width', '300'),
            ('thumb_category_picture_height', 'Category picture height', 'Category picture height', '200'),
            ('thumb_list_picture_width', 'Picture Width', 'Picture Width', '100'),
            ('thumb_list_picture_height', 'Picture Height', 'Picture Height', '100'),
            ('thumb_hyperlink_color', 'Hyperlink Color', 'Hyperlink Color', '#000000'),
            ('thumb_search_icon_color', 'Search Icon Color', 'Search Icon Color', '#004372'),
            ('thumb_reset_icon_color', 'Reset Icon Color', 'Reset Icon Color', '#004372'),
            ('thumb_select_icon_color', 'Select icon color', 'Select icon color', '#004372'),
            ('thumb_rating_star', 'Rating Star Design', 'Rating Star Design', '3'),
            ('thumb_count_of_product_in_the_row', 'Count of Products in the Row', 'Count of Products in the Row', '2'),
            ('thumb_count_of_rows_in_the_page', 'Count of Rows in the Page', 'Count of Rows in the Page', '3'),
            ('thumb_categories_row_color1', 'Categories row color 1', 'Categories row color 1', '#F4F2F2'),
            ('thumb_categories_row_color2', 'Categories row color 2', 'Categories row color 2', '#F4F2F2'),
            ('thumb_categories_header_background_color', 'Categories header background color', 'Categories header background color', '#E2E2E2'),
            ('thumb_categories_header_color', 'Categories text color', 'Categories header text color', '#3D3D3D'),


            ('parameters_select_box_width', 'Parameters Select Box Width', 'Parameters Select Box Width', '104'),
            ('params_color', 'Color of Parameter Values', 'Color of Parameter Values', '#2F699E'),
            ('product_cell_width', 'Product Cell Width', 'Product Cell Width', '290'),
            ('product_cell_height', 'Product Cell Height', 'Product Cell Height', '750'),
            ('price_size_small', 'Price Size', 'Price Size', '20'),
            ('market_price_size_small', 'Market Price Size', 'Market Price Size', '12'),
            ('title_size_small', 'Title Size', 'Title Size', '16'),
            ('text_size_list', 'List text size', 'List text size', '14'),
            ('price_size_list', 'List price size', 'List price size', '18'),
            ('market_price_size_list', 'List Market price size', 'List Market price size', '11'),
            ('cell_show_category', 'Show Category', 'Show Category', '1'),
            ('list_show_category', 'Show Category', 'Show Category', '1'),
            ('cell_show_parameters', 'Show Parameters', 'Show Parameters', '1'),
            ('list_show_parameters', 'Show Parameters', 'Show Parameters', '1'),
            ('list_show_description', 'Show Description', 'Show Description', '1'),
            ('width_spider_main_table_lists', 'Product List  Width', 'Product List  Width', '620'),
            ('category_details_width', 'Category Details Width', 'Category Details Width', '600'),
            ('spider_catalog_product_page_width', 'Product Page Width', 'Product Page Width', '600'),
            ('description_size_list', 'Description Text Size', 'Description Text Size', '12'),
            ('name_price_size_list', 'Name Price List Text Size', 'Name Price List Text Size', '12'),
            ('Parameters_size_list', 'Parameters List Text Size', 'Parameters List Text Size', '12'),
            ('cell_big_title_size', 'Cell Big Title Size', 'Cell Big Title Size', '34'),
            ('cell_price_background_color', 'Cell Price Background Color', 'Cell Price Background Color', '#004372'),
            ('cell_small_image_backround_color', 'Cell Small Image Backround Color', 'Cell Small Image Backround Color', '#F4F2F2'),
            ('cell_parameters_left_size', 'Cell Parameters Left Size', 'Cell Parameters Left Size', '13'),
            ('cell_more_font_size', 'Cell More Font size', 'Cell More Font size', '15'),
            ('cell_more_font_color', 'Cell More Font Color', 'Cell More Font Color', '#FFFFFF'),
            ('cell_more_background_color', 'Cell More Background Color', 'Cell More Background Color', '#004372'),
            ('cell_params_text_color', 'Cell Params Text Color', 'Cell Params Text Color', '#3E3E3E'),
            ('cell_new_big_title_size', 'Cell New Big Title Size', 'Cell New Big Title Size', '28'),
            ('cell_new_title_size', 'Cell New Title Size', 'Cell New Title Size', '13'),
            ('cell_new_price_size', 'Cell New Price Size', 'Cell New Price Size', '20'),
            ('cell_new_market_price_size', 'Cell New Market Price Size', 'Cell New Market Price Size', '12'),
            ('cell_new_picture_width', 'Cell New Picture Width', 'Cell New Picture Width', '110'),
            ('cell_new_picture_height', 'Cell New Picture Height', 'Cell New Picture Height', '95'),
            ('cell_new_title_color', 'Cell New Title Color', 'Cell New Title Color', '#004372'),
            ('new_cell_all_width', 'New Cell Width', 'New Cell Width', '290'),
            ('new_cell_all_height', 'New Cell All Height', 'New Cell All Height', '580'),
            ('cell_new_text_size', 'Cell New Text Size', 'Cell New Text Size', '12'),
            ('cell_new_text_back_color_1', 'Cell New Text Background Color 1', 'Cell New Text Background Color 1', '#F6F6F6'),
            ('cell_new_text_back_color_2', 'Cell New Text Background Color 2', 'Cell New Text Background Color 2', '#F0EDED'),
            ('cell_new_text_color', 'Cell New Text Color', 'Cell New Text Color', '#3D3D3D'),
            ('new_cell_more_font_size', 'New Cell More Font Size', 'New Cell More Font Size', '17'),
            ('cell_new_more_font_color', 'More Font Color', 'More Font Color', '#FFFFFF'),
            ('cell_new_more_background_color', 'More Background Color', 'More Background Color', '#004372'),
            ('cell_tumble_title_size', 'Title Size', 'Title Size', '14'),
            ('cell_tumble_title_font_color', 'Title Font Color', 'Title Font Color', '#004372'),
            ('cell_tumble_price_size', 'Price Size', 'Price Size', '14'),
            ('cell_tumble_price_text_color', 'Price Text Color', 'Price Text Color', '#FFFFFF'),
            ('cell_tumble_picture_width', 'Picture Width', 'Picture Width', '120'),
            ('cell_tumble_picture_height', 'Picture Height', 'Picture Height', '120'),
            ('cell_tumble_text_size', 'Text Size', 'Text Size', '12'),
            ('cell_tumble_text_color', 'Text Color', 'Text Color', '#434242'),
            ('cell_tumble_all_width', 'All Width', 'All Width', '290'),
            ('cell_tumble_all_height', 'All Height', 'All Height', '225'),
            ('all_cell_title_size', 'Title Size', 'Title Sizes', '18'),
            ('all_cell_title_color', 'Title Color', 'Title Color', '#004372'),
            ('all_cell_price_size', 'Price Size', 'Price Size', '18'),
            ('all_cell_price_text_color', 'Price Text Color', 'Price Text Color', '#FFFFFF'),
            ('all_cell_picture_width', 'Picture Width', 'Picture Width', '285'),
            ('all_cell_picture_height', 'Picture Height', 'Picture Height', '200'),
            ('all_cell_text_size', 'Text Size', 'Text Size', '15'),
            ('all_cell_text_color', 'Text Color', 'Text Color', '#434242'),
            ('all_cell_all_width', 'All Width', 'All Width', '290'),
            ('all_cell_all_height', 'All Height', 'All Height', '470'),
            ('single_cell_title_size', 'Title Size', 'Title Size', '18'),
            ('single_cell_title_color', 'Title Color', 'Title Color', '#004372'),
            ('single_cell_font_1_size', 'Font 1 Size', 'Font 1 Size', '14'),
            ('single_cell_font_2_size', 'Font 2 Size', 'Font 2 Size', '12'),
            ('single_cell_background_color_1', 'Background Color 1', 'Background Color 1', '#F5F4F4'),
            ('single_cell_background_color_2', 'Background Color 2', 'Background Color 2', '#FFFFFF'),
            ('single_cell_text_color_1', 'Text Color 1', 'Text Color 1', '#004372'),
            ('single_cell_text_color_2', 'Text Color 2', 'Text Color 2', '#636363'),
            ('single_cell_picture_width', 'Picture Width', 'Picture Width', '215'),
            ('single_cell_picture_height', 'Picture Height', 'Picture Height', '200'),
            ('list_page_up_names_text_color', 'List Page Up Names Text Color', 'List Page Up Names Text Color', '#3D3D3D'),
            ('list_page_up_names_background_color', 'List Page Up names Background Color', 'List Page Up names Background Color', '#E2E2E2'),
            ('list_page_background_color_1', 'List Page Background Color 1', 'List Page Background Color 1', '#F6F6F6'),
            ('list_page_background_color_2', 'List Page Background Color 2', 'List Page Background Color 2', '#FFFFFF'),
            ('list_cell_price_color', 'List Cell Price Color', 'List Cell Price Color', '#B02E2E'),
            ('list_cell_market_price_color', 'List Cell Market Price Color', 'List Cell Market Price Color', '#3C6680'),
            ('list_page_text_color_1', 'Text Color 1', 'Text Color 1', '#3E3E3E'),
            ('list_page_text_color_2', 'Text Color 2', 'Text Color 2', '#235775')";
        $wpdb->query($sql_query);
    }
}

add_action('wp_ajax_catalogexportcsv', 'spider_catalog_export_csv');
function spider_catalog_export_csv()
{
    require_once("exportcsv.php");
	export_catalog_csv();
}

function catalog_Featured_Plugins_styles() {
  wp_enqueue_style("Featured_Plugins", plugins_url("featured_plugins.css", __FILE__));
}
function catalog_Featured_Plugins() {
  ?>
	<div id="main_featured_plugins_page">
		<table align="center" width="90%" style="margin-top: 0px;border-bottom: rgb(111, 111, 111) solid 2px;">
			<tr>
				<td colspan="2" style="height: 70px;"><h3 style="margin: 0px;font-family:Segoe UI;padding-bottom: 15px;color: rgb(111, 111, 111); font-size:18pt;">Featured Plugins</h3></td>
				<td  align="right" style="font-size:16px;">
           <a href="http://web-dorado.com/files/fromSpiderCatalog.php" target="_blank" style="color:red; text-decoration:none;">
              <img src="<?php echo plugins_url('images/header.png', __FILE__); ?>" border="0" alt="http://web-dorado.com/files/fromSpiderCatalog.php" width="215"><br>
                 Get the full version&nbsp;&nbsp;&nbsp;&nbsp;
           </a>
        </td>
			</tr>
		</table>
		<form method="post">
			<ul id="featured-plugins-list">
      <li class="photo-gallery">
            <div class="product">
              <div class="title">
                <strong class="heading">Photo Gallery</strong>
                <p>WordPress Photo Gallery Plugin</p>
              </div>
            </div>
            <div class="description">
                <p>Photo Gallery is a fully responsive WordPress Gallery plugin with advanced functionality. 
				It allows having different image galleries for your posts and pages, as well as different widgets. </p>
                <a target="_blank" href="http://web-dorado.com/products/wordpress-photo-gallery-plugin.html" class="download">Download</a>
            </div>
          </li>
        <li class="form-maker">
                 <div class="product">
                         <div class="title">
                                 <strong class="heading">Form Maker</strong>
                                 <p>Wordpress form builder plugin</p>
                         </div>
                 </div>
                 <div class="description">
                                 <p>Form Maker is a modern and advanced tool for creating WordPress forms easily and fast.</p>
                                 <a target="_blank" href="http://web-dorado.com/products/wordpress-form.html" class="download">Download</a>
                 </div>
         </li>
				<li class="spider-calendar">
					<div class="product">
						<div class="title">
							<strong class="heading">Spider Calendar</strong>
							<p>WordPress event calendar plugin</p>
						</div>
					</div>
					<div class="description">
							<p>Spider Event Calendar is a highly configurable product which allows you to have multiple organized events.</p>
							<a target="_blank" href="http://web-dorado.com/products/wordpress-calendar.html" class="download">Download</a>
					</div>
				</li>
        <li class="player">
					<div class="product">
						<div class="title">
							<strong class="heading">Video Player</strong>
							<p>WordPress Video player plugin</p>
						</div>
					</div>
					<div class="description">
							<p>Spider Video Player for WordPress is a Flash & HTML5 video player plugin that allows you to easily add videos to your website with the possibility</p>
							<a target="_blank" href="http://web-dorado.com/products/wordpress-player.html" class="download">Download</a>
					</div>
				</li>
				<li class="contacts">
					<div class="product">
						<div class="title">
							<strong class="heading">Spider Contacts</strong>
							<p>Wordpress staff list plugin</p>
						</div>
					</div>
					<div class="description">
							<p>Spider Contacts helps you to display information about the group of people more intelligible, effective and convenient.</p>
							<a target="_blank" href="http://web-dorado.com/products/wordpress-contacts-plugin.html" class="download">Download</a>
					</div>
				</li>
        <li class="facebook">
					<div class="product">
						<div class="title">
							<strong class="heading">Spider Facebook</strong>
							<p>WordPress Facebook plugin</p>
						</div>
					</div>
					<div class="description">
							<p>Spider Facebook is a WordPress integration tool for Facebook.It includes all the available Facebook social plugins and widgets to be added to your web</p>
							<a target="_blank" href="http://web-dorado.com/products/wordpress-facebook.html" class="download">Download</a>
					</div>
				</li>
                <li class="faq">
					<div class="product">
						<div class="title">
							<strong class="heading">Spider FAQ</strong>
							<p>WordPress FAQ Plugin</p>
						</div>
					</div>
					<div class="description">
							<p>The Spider FAQ WordPress plugin is for creating an FAQ (Frequently Asked Questions) section for your website.</p>
							<a target="_blank" href="http://web-dorado.com/products/wordpress-faq-plugin.html" class="download">Download</a>
					</div>
				</li>
                <li class="zoom">
					<div class="product">
						<div class="title">
							<strong class="heading">Zoom</strong>
							<p>WordPress text zoom plugin</p>
						</div>
					</div>
					<div class="description">
							<p>Zoom enables site users to resize the predefined areas of the web site.</p>
							<a target="_blank" href="http://web-dorado.com/products/wordpress-zoom.html" class="download">Download</a>
					</div>
				</li>
				<li class="flash-calendar">
					<div class="product">
						<div class="title">
							<strong class="heading">Flash Calendar</strong>
							<p>WordPress flash calendar plugin</p>
						</div>
					</div>
					<div class="description">
							<p>Spider Flash Calendar is a highly configurable Flash calendar plugin which allows you to have multiple organized events.</p>
							<a target="_blank" href="http://web-dorado.com/products/wordpress-events-calendar.html" class="download">Download</a>
					</div>
				</li>
        <li class="contact-maker">
                 <div class="product">
                         <div class="title">
                                 <strong class="heading">Contact Form Maker</strong>
                                 <p>WordPress contact form builder plugin</p>
                         </div>
                 </div>
                 <div class="description">
                                 <p>WordPress Contact Form Maker is an advanced and easy-to-use tool for creating forms.</p>
                                 <a target="_blank" href="http://web-dorado.com/products/wordpress-contact-form-maker-plugin.html" class="download">Download</a>
                 </div>
         </li>
          <li class="twitter">
                 <div class="product">
                         <div class="title">
                                 <strong class="heading">Widget Twitter</strong>
                                 <p>WordPress Twitter plugin</p>
                         </div>
                 </div>
                 <div class="description">
                                 <p>The Widget Twitter is a boosted WordPress plugin, which provides full Twitter Integration for your WordPress site.</p>
                                 <a target="_blank" href="http://web-dorado.com/products/wordpress-twitter-integration-plugin.html" class="download">Download</a>
                 </div>
         </li>
			</ul>
		</form>
	</div >
  <?php
}