<?php

/*============================== TESLA FRAMEWORK ======================================================================================================================*/

require_once locate_template('tesla_framework/tesla.php');

require_once locate_template('tesla_dev/tt_log.php');

TT_ENQUEUE::$enabled = FALSE;



/*============================== THEME FEATURES ======================================================================================================================*/

function biznex_theme_features() {

    register_nav_menus(array(
        'biznex_menu' => 'Header Menu'
    ));

    if (!isset($content_width))
        $content_width = 1170;

    add_theme_support('post-thumbnails');

    add_theme_support( 'automatic-feed-links' );
}

add_action('after_setup_theme', 'biznex_theme_features');



/*============================== SIDEBARS ======================================================================================================================*/

function biznex_sidebars() {

    register_sidebar(array(
        'name' => 'Blog Sidebar',
        'id' => 'blog-sidebar',
        'description' => 'This sidebar is located on the right side of the content on the blog page.',
        'class' => '',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>'
    ));

}

add_action('widgets_init', 'biznex_sidebars');



/*============================== LANGUAGE SETUP ======================================================================================================================*/

function biznex_language_setup(){

    load_theme_textdomain('biznex', locate_template('languages'));

}

add_action('after_setup_theme', 'biznex_language_setup');



/*============================== SCRIPTS & STYLES ======================================================================================================================*/

function biznex_scripts_and_styles() {

    $protocol = is_ssl() ? 'https' : 'http';

    $font_custom = _go('logo_text_font');

    if($font_custom){

        $font_custom = str_replace(' ', '+', $font_custom);

        wp_enqueue_style( 'biznex-font-custom', $protocol.'://fonts.googleapis.com/css?family='.$font_custom, false, null);

    }

    $theme_data = wp_get_theme();

    $biznex_version = $theme_data['Version'];

	wp_enqueue_style('biznex-css-bootstrap', tesla_locate_uri('css/bootstrap.min.css'), false, $biznex_version);
    wp_enqueue_style('biznex-css-font-awesome', tesla_locate_uri('libraries/font-awesome/css/font-awesome.min.css'), false, $biznex_version);
    wp_enqueue_style('biznex-css-easy-responsive-tabs', tesla_locate_uri('css/easy-responsive-tabs.css'), false, $biznex_version);
    wp_enqueue_style('biznex-css-flexslider', tesla_locate_uri('libraries/flexslider/flexslider.css'), false, $biznex_version);
    wp_enqueue_style('biznex-css-nivo-lightbox', tesla_locate_uri('libraries/nivo-lightbox/nivo-lightbox.css'), false, $biznex_version);
    wp_enqueue_style('biznex-css-nivo-lightbox-theme', tesla_locate_uri('libraries/nivo-lightbox/themes/default/default.css'), false, $biznex_version);
    wp_enqueue_style('biznex-css-styles', tesla_locate_uri('css/styles.css'), false, $biznex_version);
    wp_enqueue_style('biznex-css-assets', tesla_locate_uri('css/assets.css'), false, $biznex_version);
    wp_enqueue_style('biznex-css-screen-small', tesla_locate_uri('css/screen-small.css'), false, $biznex_version);
    wp_enqueue_style('biznex-css-screen-medium', tesla_locate_uri('css/screen-medium.css'), false, $biznex_version);
    wp_enqueue_style('biznex-css-screen-large', tesla_locate_uri('css/screen-large.css'), false, $biznex_version);
    wp_enqueue_style('biznex-css-styles-less', tesla_locate_uri('css/styles-less.css'), false, $biznex_version);
    wp_enqueue_style('biznex-css-blog', tesla_locate_uri('css/blog.css'), false, $biznex_version);
    wp_enqueue_style('biznex-css-root', tesla_locate_uri('style.css'), false, $biznex_version);

	wp_enqueue_script('jquery');
    wp_enqueue_script('biznex-js-es5-shim', tesla_locate_uri('js/es5-shim.min.js'), false, $biznex_version, true);
    wp_enqueue_script('biznex-js-bootstrap', tesla_locate_uri('js/bootstrap.min.js'), false, $biznex_version, true);
    wp_enqueue_script('biznex-js-modernizr', tesla_locate_uri('js/modernizr.js'), false, $biznex_version, true);
    wp_enqueue_script('biznex-js-jquery-bez', tesla_locate_uri('js/jquery.bez.min.js'), false, $biznex_version, true);
    wp_enqueue_script('biznex-js-jquery-scrollTo', tesla_locate_uri('js/jquery.scrollTo.min.js'), false, $biznex_version, true);
    wp_enqueue_script('biznex-js-easyResponsiveTabs', tesla_locate_uri('js/easyResponsiveTabs.js'), false, $biznex_version, true);
    wp_enqueue_script('biznex-js-flexslider', tesla_locate_uri('libraries/flexslider/jquery.flexslider-min.js'), false, $biznex_version, true);
    wp_enqueue_script('biznex-js-nivo-lightbox', tesla_locate_uri('libraries/nivo-lightbox/nivo-lightbox.min.js'), false, $biznex_version, true);
    wp_enqueue_script('biznex-js-styles', tesla_locate_uri('js/styles.js'), false, $biznex_version, true);
    wp_enqueue_script('biznex-js-plugins', tesla_locate_uri('js/plugins.js'), false, $biznex_version, true);
    wp_enqueue_script('biznex-js-sharethis', tesla_locate_uri('js/buttons.js'), false, $biznex_version, true);
    wp_enqueue_script('biznex-js-smoothscroll', tesla_locate_uri('js/smoothscroll.js'), false, $biznex_version, true);

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply', array('jquery') );

    wp_localize_script('biznex-js-styles', 'biznex', array('ajaxurl'=>admin_url('admin-ajax.php'), 'root'=>get_stylesheet_directory_uri()));

}

function biznex_admin_scripts_and_styles($hook_suffix) {

    if ('widgets.php' === $hook_suffix) {
        wp_enqueue_style('biznex-css-admin-widgets', tesla_locate_uri('admin/widgets.css'), false, null);

        wp_enqueue_media();
        wp_enqueue_script('biznex-js-admin-widgets', tesla_locate_uri('admin/widgets.js'), array('media-upload', 'media-views'), null);
    }

    $screen = get_current_screen();

    if('page'===$screen->id){

        wp_enqueue_style('biznex-css-admin-page', tesla_locate_uri('admin/page.css'), false, null);

        wp_enqueue_script('biznex-js-admin-page', tesla_locate_uri('admin/page.js'), false, null);

    }

}

if(!is_admin())
    add_action('wp_enqueue_scripts', 'biznex_scripts_and_styles');
else
    add_action('admin_enqueue_scripts', 'biznex_admin_scripts_and_styles');

function biznex_header(){
    ?>
    <!-- IE8 -->
    <!--[if lt IE 9]>
        <link href="<?php echo tesla_locate_uri('css/ie8.css'); ?>" rel="stylesheet">
    <![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="<?php echo tesla_locate_uri('js/html5shiv.js'); ?>"></script>
        <script src="<?php echo tesla_locate_uri('js/respond.min.js'); ?>"></script>
    <![endif]-->
    <?php
    $background_image = _go('bg_image');
    $background_position = _go('bg_image_position');
    $background_repeat = _go('bg_image_repeat');
    $background_attachment = _go('bg_image_attachment');
    $background_color = _go('bg_color');
    ?>
    <style type="text/css">
    .biznex_video_wrapper,
    .video-player {
        position: relative !important;
        padding-bottom: 56.25% !important;
        overflow: hidden !important;
        height: 0 !important;
        width: auto !important;
    }

    .biznex_video_wrapper>iframe,
    .biznex_video_wrapper>object,
    .biznex_video_wrapper>embed,
    .video-player>iframe,
    .video-player>object,
    .video-player>embed {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
    }
    .contact_map{
        height: 100%;
    }
    <?php
    echo 'body{'."\n";
    if(!empty($background_image))
        echo 'background-image: url('.$background_image.');'."\n";
    if(!empty($background_position)){
        echo 'background-position: ';
        switch($background_position){
            case 'Left':
                echo 'top left';
                break;
            case 'Center':
                echo 'top center';
                break;
            case 'Right':
                echo 'top right';
                break;
            default:
                break;
        }
        echo ';'."\n";
    }
    if(!empty($background_repeat)){
        echo 'background-repeat: ';
        switch($background_repeat){
            case 'No Repeat':
                echo 'no-repeat';
                break;
            case 'Tile':
                echo 'repeat';
                break;
            case 'Tile Horizontally':
                echo 'repeat-x';
                break;
            case 'Tile Vertically':
                echo 'repeat-y';
                break;
            default:
                break;
        }
        echo ';'."\n";
    }
    if(!empty($background_attachment)){
        echo 'background-attachment: ';
        switch($background_attachment){
            case 'Scroll':
                echo 'scroll';
                break;
            case 'Fixed':
                echo 'fixed';
                break;
            default:
                break;
        }
        echo ';'."\n";
    }
    if(!empty($background_color))
        echo 'background-color: '.$background_color.';'."\n";
    echo '}'."\n";
    $default = _go('site_color');
    $default2 = _go('site_color_2');
    $default3 = _go('site_color_3');
    $default4 = _go('site_color_4');
    $default5 = _go('site_color_5');
    if(empty($default))
        $default = '#c90000';
    if(empty($default2))
        $default2 = '#a13233';
    if(empty($default3))
        $default3 = '#bd3b3c';
    if(empty($default4))
        $default4 = '#ab0000';
    if(empty($default5))
        $default5 = '#279cbe';
    ?>
    /* first color */
    
    <?php
    $map_height = _go('map_height');
    if(!empty($map_height))
        echo '.map{'."\n".'height: '.$map_height.'px;'."\n".'}'."\n";
    echo _go('custom_css');
    ?>
    </style>
    <?php
    $favicon = _go('favicon');
    if(!empty($favicon))
        echo '<link rel="icon" type="image/png" href="'.$favicon.'">';
}

add_action('wp_head','biznex_header',1000);

function biznex_footer(){

    return;

}

add_action('wp_footer','biznex_footer',1000);



/*============================== FILTERS ======================================================================================================================*/

function biznex_wp_title( $title, $sep ) {
    global $paged, $page;

    if ( is_feed() )
        return $title;

    $title .= get_bloginfo( 'name' );
    
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) )
        $title = "$title $sep $site_description";

    if ( $paged >= 2 || $page >= 2 )
        $title = "$title $sep " . sprintf( __( 'Page %s', 'tesla' ), max( $paged, $page ) );

    return $title;
}

add_filter( 'wp_title', 'biznex_wp_title', 10, 2 );

function biznex_the_title( $title, $id ) {
    
    if(''===$title)
        $title = _x('Untitled', 'post or page without title', 'biznex');

    return $title;

}

add_filter( 'the_title', 'biznex_the_title', 10, 2 );

function biznex_wp_link_pages_link( $link, $i ) {
    
    return '<li>'.$link.'</li>';

}

add_filter( 'wp_link_pages_link', 'biznex_wp_link_pages_link', 10, 2 );

function biznex_excerpt_length( $length ) {

    $biznex_excerpt_length = _go('excerpt_length');

    return $biznex_excerpt_length ? (int) $biznex_excerpt_length : $length;

}

add_filter('excerpt_length', 'biznex_excerpt_length');



/*============================== COMMENTS ======================================================================================================================*/

function biznex_comment($comment, $args, $depth){

    $GLOBALS['comment'] = $comment;
    switch ($comment->comment_type) :
        case 'pingback' :
        case 'trackback' :
            ?>

            <li>
                <div <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
                    <span class="comment-pingback"><?php _ex('Pingback: ', 'comments', 'biznex'); ?></span>
                    <span class="comment-info"><?php echo biznex_comment_time(); ?> <span><?php echo get_comment_author_link(); ?></span> </span>
                    <?php edit_comment_link(_x('Edit', 'comments', 'biznex')); ?>
                </div>

            <?php
            break;
        default :
            ?>

            <li>
                <div <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
                    <span class="comment-image"><?php echo get_avatar($comment, 70); ?></span>
                    <span class="comment-info">
                        <?php echo get_comment_author_link(); ?> <span><?php comment_time('j M'); ?></span>
                    </span>
                    <?php comment_text(); ?>
                    <?php comment_reply_link(array_merge($args, array('reply_text' => _x('Reply', 'comments', 'biznex'), 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?><?php edit_comment_link(_x('Edit', 'comments', 'biznex')); ?>
                </div>

            <?php
            break;
    endswitch;

}

function biznex_comment_end($comment, $args, $depth){

    $GLOBALS['comment'] = $comment;
    switch ($comment->comment_type) :
        case 'pingback' :
        case 'trackback' :
            ?>

            </li>

            <?php
            break;
        default :
            ?>

            </li>

            <?php
            break;
    endswitch;

}



/*============================== FEATURED CONTENT UTILITIES ======================================================================================================================*/

function biznex_has_featured($id = null){

    if(is_null($id))
        $id = get_the_id();

    $featured_image = has_post_thumbnail($id);

    $biznex_metabox_video = biznex_metabox_video_get($id);

    $featured_video = !empty($biznex_metabox_video['video']);

    return $featured_image || $featured_video;
}

function biznex_get_featured($id = null, $echo = false){

    if(is_null($id))
        $id = get_the_id();

    $featured_image = has_post_thumbnail($id);

    $biznex_metabox_video = biznex_metabox_video_get($id);

    $featured_video = !empty($biznex_metabox_video['video']);

    switch(true){

        case $featured_video:
            $featured = '<div class="biznex_video_wrapper">'.$biznex_metabox_video['video'].'</div>';
            break;

        case $featured_image:
            $featured = get_the_post_thumbnail($id);
            break;

        default:
            $featured = '';
            break;

    }

    if($echo)
        echo $featured;
    
    return $featured;
}

function biznex_post_type($id = null){

    if(is_null($id))
        $id = get_the_id();
    elseif(is_object($id))
        $id = $id->ID;

    if(has_post_thumbnail($id))
        return 'image';

}



/*============================== COMMENTS NUMBER ======================================================================================================================*/

function biznex_comments_number( $post_id = 0 ) {

    $zero = false;
    $one = false;
    $more = false;
    $deprecated = '';

    if ( !empty( $deprecated ) )
        _deprecated_argument( __FUNCTION__, '1.3' );

    $number = get_comments_number($post_id);

    if ( $number > 1 )
        $output = str_replace('%', number_format_i18n($number), ( false === $more ) ? __('% Comments') : $more);
    elseif ( $number == 0 )
        $output = ( false === $zero ) ? __('No Comments') : $zero;
    else // must be one
        $output = ( false === $one ) ? __('1 Comment') : $one;

    return apply_filters( 'comments_number', $output, $number );
}



/*============================== QUERY VARS ======================================================================================================================*/

function biznex_get_query($var){
    
    if(isset($_GET[$var]))
        $categories = $_GET[$var];
    else
        $categories = get_query_var($var);

    return $categories;

}



//============================== METABOXES ======================================================================================================================

function biznex_array_filter_recursive_callback(&$value){

    if(is_array($value)){

        $value = array_filter($value,'biznex_array_filter_recursive_callback');

        return (bool)count($value);

    }else{

        return ''!==$value;

    }

}

function biznex_array_filter_recursive($array){

    return array_filter($array,'biznex_array_filter_recursive_callback');

}

function biznex_parse_args_callback(&$value, $key, $options){

    if(isset($options[$key])){

        if(is_array($value)&&isset($options[$key])){

            array_walk($value, 'biznex_parse_args_callback', $options[$key]);

        }else{

            $value = $options[$key];

        }

    }

}

function biznex_parse_args($options, $defaults){

    $options = biznex_array_filter_recursive($options);

    array_walk($defaults, 'biznex_parse_args_callback', $options);

    return $defaults;

}

function biznex_metabox_template_input_select_term($input_name, $taxonomy, $current_category){

    $output = '';

    $terms = get_terms($taxonomy);

    $output .= '<select name="'.$input_name.'" disabled="disabled">';
    $output .= '<option value="" '.selected($current_category, '', false).'> - no category - </option>';

    foreach($terms as $t)
        $output .= '<option value="'.$t->slug.'" '.selected($current_category, $t->slug, false).'>'.$t->name.' ('.$t->count.')</option>';

    $output .= '</select>';

    return $output;

}

function biznex_metabox_template_input_checkbox($input_name, $value, $echo = true){

    return '<input type="hidden" name="'.$input_name.'" value="0" /><input type="checkbox" name="'.$input_name.'" '.checked($value,true,false).' value="1" />';

}

function biznex_metabox_template_input_disable_section_checkbox($input_name, $value){

    return '<p><label><input type="hidden" name="'.$input_name.'" value="0" /><input class="tesla_template_disable_section" type="checkbox" name="'.$input_name.'" '.checked($value,true,false).' value="1" /> Disable section<br/><em>Check to disable current section.</em></label></p>';

}

function biznex_metabox_template_input_image($input_name, $value){

    return '<span class="tesla_template_meta_image">'.(''!==$value?'<img src="'.esc_attr($value).'" /><button type="button" class="button button-secondary">Remove image</button><input type="hidden" class="widefat" name="'.$input_name.'" value="'.esc_attr($value).'" disabled="disabled" />':'<button type="button" class="button button-secondary">Set image</button><input type="hidden" class="widefat" name="'.$input_name.'" value="'.esc_attr($value).'" disabled="disabled" />').'</span>';

}

function biznex_metabox_template_options_get($post_id){

    $options = (array) get_post_meta($post_id, 'biznex_metabox_template_options', true);

    $defaults = array(

        'contact' => array(
            'contact' => array(
                'disable_section' => false,
                'section_title' => '',
                'section_header_icon' => '',
                'section_header_left' => '',
                'section_header_right' => '',
                'form_title' => '',
                'placeholder_name' => '',
                'placeholder_email' => '',
                'placeholder_subject' => '',
                'placeholder_message' => '',
                'submit_text' => '',
                'section_id' => ''
            ),
            'subscribe' => array(
                'disable_section' => false,
                'title' => '',
                'placeholder' => '',
                'submit_text' => '',
                'section_id' => ''
            )
        )

    );

    return biznex_parse_args($options, $defaults);

}

function biznex_metabox_page_options_get($post_id){

    $options = (array) get_post_meta($post_id, 'biznex_metabox_page_options', true);

    $defaults = array(

        'layout' => 'default',
        'title_description' => '',

    );

    return biznex_parse_args($options, $defaults);

}

function biznex_metabox_video_get($post_id){

    $options = (array) get_post_meta($post_id, 'biznex_metabox_video', true);

    $defaults = array(

        'video' => ''

    );

    return biznex_parse_args($options, $defaults);

}

function biznex_metabox_template_options_array(){

    return array(
        'contact' => array(
            'template' => 'templates_biznex/contact.php',
            'sections' => array(
                'contact' => array(
                    'info' => '<strong>Contact section options:</strong>',
                    'inputs' => array(
                        'section_title' => array(
                            'type' => 'line',
                            'title' => 'Section title',
                            'description' => 'Set the title of the section.'
                        ),
                        'section_header_icon' => array(
                            'type' => 'image',
                            'title' => 'Header icon',
                            'description' => 'This image will appear in the header of the section.'
                        ),
                        'section_header_left' => array(
                            'type' => 'text',
                            'title' => 'Left header text',
                            'description' => 'This text will appear in the header of the section to the left of the header icon.'
                        ),
                        'section_header_right' => array(
                            'type' => 'text',
                            'title' => 'Right header text',
                            'description' => 'This text will appear in the header of the section to the right of the header icon.'
                        ),
                        'form_title' => array(
                            'type' => 'line',
                            'title' => 'Form title',
                            'description' => 'Set the title of the form.'
                        ),
                        'placeholder_name' => array(
                            'type' => 'line',
                            'title' => 'Name placeholder',
                            'description' => 'Set a placeholder for the name input.'
                        ),
                        'placeholder_email' => array(
                            'type' => 'line',
                            'title' => 'E-mail placeholder',
                            'description' => 'Set a placeholder for the e-mail input.'
                        ),
                        'placeholder_subject' => array(
                            'type' => 'line',
                            'title' => 'Subject placeholder',
                            'description' => 'Set a placeholder for the subject input.'
                        ),
                        'placeholder_message' => array(
                            'type' => 'line',
                            'title' => 'Message placeholder',
                            'description' => 'Set a placeholder for the message input.'
                        ),
                        'submit_text' => array(
                            'type' => 'line',
                            'title' => 'Submit button',
                            'description' => 'Set the text for the submit button.'
                        ),
                        'section_id' => array(
                            'type' => 'line',
                            'title' => 'Section id',
                            'description' => 'Set the id of the section. This id can be used in "Link anchor" field of the menu items to make links inside the pages.'
                        )
                    )
                ),
                'subscribe' => array(
                    'info' => '<strong>Subscribe section options:</strong>',
                    'inputs' => array(
                        'title' => array(
                            'type' => 'line',
                            'title' => 'Section title',
                            'description' => 'Set the title of the section.'
                        ),
                        'placeholder' => array(
                            'type' => 'line',
                            'title' => 'Subscribe placeholder',
                            'description' => 'Set the placeholder for the subscribe input.'
                        ),
                        'submit_text' => array(
                            'type' => 'line',
                            'title' => 'Submit button',
                            'description' => 'Set the text for the submit button.'
                        ),
                        'section_id' => array(
                            'type' => 'line',
                            'title' => 'Section id',
                            'description' => 'Set the id of the section. This id can be used in "Link anchor" field of the menu items to make links inside the pages.'
                        )
                    )
                )
            )
        )
    );

}

function biznex_metabox_template_options_input($template_key,$section_key,$input_key,$input_value,$options,$values){
    $output = '';
    $input = $options[$template_key]['sections'][$section_key]['inputs'][$input_key];
    switch($input_value['type']){
        case 'line':
            $output .= '<label>';
            $output .= $input['title'].':<br/>';
            $output .= '<input type="text" class="widefat" name="biznex_metabox_template_options['.$template_key.']['.$section_key.']['.$input_key.']" value="'.esc_attr($values[$template_key][$section_key][$input_key]).'" disabled="disabled" /><br/><em>'.$input['description'].'</em>';
            $output .= '</label>';
            break;
        case 'text':
            $output .= '<label>';
            $output .= $input['title'].':<br/>';
            $output .= '<textarea class="widefat" style="overflow:scroll;resize:vertical;white-space:nowrap;" cols="20" rows="5" name="biznex_metabox_template_options['.$template_key.']['.$section_key.']['.$input_key.']" disabled="disabled">'.esc_textarea($values[$template_key][$section_key][$input_key]).'</textarea><br/><em>'.$input['description'].'</em>';
            $output .= '</label>';
            break;
        case 'checkbox':
            $output .= '<label>';
            $output .= biznex_metabox_template_input_checkbox('biznex_metabox_template_options['.$template_key.']['.$section_key.']['.$input_key.']',$values[$template_key][$section_key][$input_key]).' '.$input['title'].'<br/><em>'.$input['description'].'</em>';
            $output .= '</label>';
            break;
        case 'image':
            $output .= $input['title'].':<br/>';
            $output .= biznex_metabox_template_input_image('biznex_metabox_template_options['.$template_key.']['.$section_key.']['.$input_key.']',$values[$template_key][$section_key][$input_key]).'<br/><em>'.$input['description'].'</em>';
            break;
        default:
            break;
    }
    return $output;
}

function biznex_metabox_template_options($post) {
    wp_nonce_field(-1, 'biznex_metabox_template_options_nonce');
    $biznex_metabox_template_options = biznex_metabox_template_options_get($post->ID);
    $biznex_metabox_template_options_array = biznex_metabox_template_options_array();
    ?>
    <div data-template="default" style="display: none;">
        <p>
            Select a template to see the available options.
        </p>
    </div>
    <?php foreach($biznex_metabox_template_options_array as $template_key => $template_value): ?>
    <div data-template="<?php echo $template_value['template']; ?>" style="display: none;">
        <?php foreach($template_value['sections'] as $section_key => $section_value): ?>
        <p>
            <?php echo $section_value['info']; ?>
        </p>
        <?php echo biznex_metabox_template_input_disable_section_checkbox('biznex_metabox_template_options['.$template_key.']['.$section_key.'][disable_section]',$biznex_metabox_template_options[$template_key][$section_key]['disable_section']); ?>
        <div class="tesla_template_section">
            <?php foreach($section_value['inputs'] as $input_key => $input_value): ?>
            <p>
                <?php echo biznex_metabox_template_options_input($template_key,$section_key,$input_key,$input_value,$biznex_metabox_template_options_array,$biznex_metabox_template_options); ?>
            </p>
            <?php endforeach; ?>
        </div>
        <br/>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
    <?php
}

function biznex_metabox_template_options_save($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (!isset($_POST['biznex_metabox_template_options_nonce']) || !wp_verify_nonce($_POST['biznex_metabox_template_options_nonce']))
        return;

    if (!current_user_can('edit_post', $post_id))
        return;

    if (wp_is_post_revision($post_id) === false) {

        add_post_meta($post_id, 'biznex_metabox_template_options', $_POST['biznex_metabox_template_options'], true) or
                update_post_meta($post_id, 'biznex_metabox_template_options', $_POST['biznex_metabox_template_options']);
    }
}

function biznex_add_meta_boxes() {

    add_meta_box('biznex_metabox_template_options', 'Template options', 'biznex_metabox_template_options', 'page', 'normal', 'high');

}

add_action('add_meta_boxes', 'biznex_add_meta_boxes');

add_action('save_post', 'biznex_metabox_template_options_save');



/*============================== AJAX ======================================================================================================================*/

function biznex_contact_ajax(){

    $receiver_mail = _go('email_contact');
    if(!empty($receiver_mail))
    {
        $mail_title_prefix = _go('email_prefix');
        if(empty($mail_title_prefix))
            $mail_title_prefix = '';
        if( !empty($_POST['biznex-name']) && !empty($_POST['biznex-email']) && !empty($_POST['biznex-subject']) && !empty($_POST['biznex-message']) ){
            $subject = $mail_title_prefix.$_POST['biznex-subject'];
            $reply_to = is_email($_POST['biznex-email']);
            if(false!==$reply_to){
                $reply_to = $_POST['biznex-name'] . '<' . $reply_to . '>';
                $headers = '';
                $headers .= 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/plain; charset=utf-8' . "\r\n";
                $headers .= 'Reply-to: ' . $reply_to . "\r\n";
                $message = 'Name: '.$_POST['biznex-name']."\r\n".'E-mail: '.$_POST['biznex-email']."\r\n".'Subject: '.$_POST['biznex-subject']."\r\n".'Message: '.$_POST['biznex-message'];
                if ( wp_mail($receiver_mail, $subject, $message, $headers) )
                    $result = _x("Your message was successfully sent.", 'contact form','biznex');
                else
                    $result = _x("Operation could not be completed.", 'contact form','biznex');
            }else{
                $result = _x("You have provided an invalid e-mail address.", 'contact form','biznex');
            }
        }else{
            $result = _x("Please fill in all the required fields.", 'contact form','biznex');
        }
    }else{
        $result = _x('Error! There is no e-mail configured to receive the messages.', 'contact form','biznex');
    }
    echo $result;
    die;

}
add_action( "wp_ajax_biznex_contact", "biznex_contact_ajax" );
add_action( "wp_ajax_nopriv_biznex_contact", "biznex_contact_ajax" );



/*============================== VIDEO ======================================================================================================================*/

function biznex_embed_oembed_html($html) {

    return '<div class="biznex_video_wrapper">'.$html.'</div>';
    
}

add_filter( 'embed_oembed_html', 'biznex_embed_oembed_html');



/*============================== EXCERPT & CONTENT ======================================================================================================================*/

function biznex_excerpt($q = null, $length = null, $more_text = null, $more_link = null){

    $q = get_post($q);

    if(''!==$q->post_excerpt){

        $text = $q->post_excerpt;

    }else{

        $text = biznex_content($q, '');

        $text = strip_shortcodes($text);

        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]&gt;', $text);

        $excerpt_length = $length ? $length : apply_filters('excerpt_length', 55);

        $more_text = $more_text ? $more_text : '[&hellip;]';
        if(true===$more_link)
            $more_link = get_permalink($q->ID);
        if($more_link)
            $more_text = '<a href="'.$more_link.'">'.$more_text.'</a>';
        $excerpt_more = apply_filters('excerpt_more', ' ' . $more_text);

        $text = wp_trim_words($text, $excerpt_length, $excerpt_more);

    }

    $text = apply_filters('wp_trim_excerpt', $text, $q->post_excerpt);

    $text = apply_filters('the_excerpt', $text);

    return $text;

}

function biznex_content($q = null, $more_link_text = null, $strip_teaser = false) {
    global $page, $more, $preview, $pages, $multipage;

    $post = get_post($q);

    if ( null === $more_link_text )
        $more_link_text = __( '(more&hellip;)' );

    $output = '';
    $has_teaser = false;

    if ( post_password_required( $post ) )
        return get_the_password_form( $post );

    if ( $page > count( $pages ) )
        $page = count( $pages );

    $content = $pages[$page - 1];
    if ( preg_match( '/<!--more(.*?)?-->/', $content, $matches ) ) {
        $content = explode( $matches[0], $content, 2 );
        if ( ! empty( $matches[1] ) && ! empty( $more_link_text ) )
            $more_link_text = strip_tags( wp_kses_no_null( trim( $matches[1] ) ) );

        $has_teaser = true;
    } else {
        $content = array( $content );
    }

    if ( false !== strpos( $post->post_content, '<!--noteaser-->' ) && ( ! $multipage || $page == 1 ) )
        $strip_teaser = true;

    $teaser = $content[0];

    if ( $more && $strip_teaser && $has_teaser )
        $teaser = '';

    $output .= $teaser;

    if ( count( $content ) > 1 ) {
        if ( $more ) {
            $output .= '<span id="more-' . $post->ID . '"></span>' . $content[1];
        } else {
            if ( ! empty( $more_link_text ) )
                $output .= apply_filters( 'the_content_more_link', ' <a href="' . get_permalink() . "#more-{$post->ID}\" class=\"more-link\">$more_link_text</a>", $more_link_text );
            $output = force_balance_tags( $output );
        }
    }

    if ( $preview )
        $output =   preg_replace_callback( '/\%u([0-9A-F]{4})/', '_convert_urlencoded_to_entities', $output );

    return $output;
}



/*============================== DATE & TIME ======================================================================================================================*/

function biznex_time($time, $format = null){

    if(is_null($format))
        $output = sprintf(__('%1$s at %2$s'), mysql2date(get_option('date_format'), $time), mysql2date(get_option('time_format'), $time));
    else
        $output = mysql2date($format, $time);

    return $output;

}

function biznex_post_time($q = null, $format = null){

    if(is_null($q)){

        global $post;

        $q = $post;

    }else{

        if(!is_object($q)){

            $q = get_post($q);

        }

    }

    $time = $q->post_date;

    return biznex_time($time, $format);

}

function biznex_comment_time($q = null, $format = null){

    if(is_null($q)){

        global $comment;

        $q = $comment;

    }else{

        if(!is_object($q)){

            $q = get_comment($q);

        }

    }

    $time = $q->comment_date;

    return biznex_time($time, $format);

}



/*============================== MENU ITEM OPTIONS ======================================================================================================================*/

function biznex_nav_update($menu_id, $menu_item_db_id, $args ) {
    if ( is_array($_REQUEST['menu-item-attr-anchor']) ) {
        $custom_value = $_REQUEST['menu-item-attr-anchor'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, 'biznex_menu_item_anchor', $custom_value );
    }
}
add_action('wp_update_nav_menu_item', 'biznex_nav_update', 10, 3);

function biznex_nav_item($menu_item) {
    $menu_item->anchor = get_post_meta( $menu_item->ID, 'biznex_menu_item_anchor', true );
    return $menu_item;
}
add_filter( 'wp_setup_nav_menu_item', 'biznex_nav_item');

function biznex_nav_edit_walker($walker,$menu_id) {
    return 'Biznex_Walker_Nav_Menu_Edit';
}
add_filter( 'wp_edit_nav_menu_walker', 'biznex_nav_edit_walker', 10, 2);

class Biznex_Walker_Nav_Menu_Edit extends Walker_Nav_Menu {

    function start_lvl( &$output, $depth = 0, $args = array() ) {}

    function end_lvl( &$output, $depth = 0, $args = array() ) {}

    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $_wp_nav_menu_max_depth;
        $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

        ob_start();
        $item_id = esc_attr( $item->ID );
        $removed_args = array(
            'action',
            'customlink-tab',
            'edit-menu-item',
            'menu-item',
            'page-tab',
            '_wpnonce',
        );

        $original_title = '';
        if ( 'taxonomy' == $item->type ) {
            $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
            if ( is_wp_error( $original_title ) )
                $original_title = false;
        } elseif ( 'post_type' == $item->type ) {
            $original_object = get_post( $item->object_id );
            $original_title = get_the_title( $original_object->ID );
        }

        $classes = array(
            'menu-item menu-item-depth-' . $depth,
            'menu-item-' . esc_attr( $item->object ),
            'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
        );

        $title = $item->title;

        if ( ! empty( $item->_invalid ) ) {
            $classes[] = 'menu-item-invalid';
            /* translators: %s: title of menu item which is invalid */
            $title = sprintf( __( '%s (Invalid)' ), $item->title );
        } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
            $classes[] = 'pending';
            /* translators: %s: title of menu item in draft status */
            $title = sprintf( __('%s (Pending)'), $item->title );
        }

        $title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

        $submenu_text = '';
        if ( 0 == $depth )
            $submenu_text = 'style="display: none;"';

        ?>
        <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
            <dl class="menu-item-bar">
                <dt class="menu-item-handle">
                    <span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" <?php echo $submenu_text; ?>><?php _e( 'sub item' ); ?></span></span>
                    <span class="item-controls">
                        <span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
                        <span class="item-order hide-if-js">
                            <a href="<?php
                                echo wp_nonce_url(
                                    add_query_arg(
                                        array(
                                            'action' => 'move-up-menu-item',
                                            'menu-item' => $item_id,
                                        ),
                                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                    ),
                                    'move-menu_item'
                                );
                            ?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up'); ?>">&#8593;</abbr></a>
                            |
                            <a href="<?php
                                echo wp_nonce_url(
                                    add_query_arg(
                                        array(
                                            'action' => 'move-down-menu-item',
                                            'menu-item' => $item_id,
                                        ),
                                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                    ),
                                    'move-menu_item'
                                );
                            ?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down'); ?>">&#8595;</abbr></a>
                        </span>
                        <a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item'); ?>" href="<?php
                            echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
                        ?>"><?php _e( 'Edit Menu Item' ); ?></a>
                    </span>
                </dt>
            </dl>

            <div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
                <?php if( 'custom' == $item->type ) : ?>
                    <p class="field-url description description-wide">
                        <label for="edit-menu-item-url-<?php echo $item_id; ?>">
                            <?php _e( 'URL' ); ?><br />
                            <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
                        </label>
                    </p>
                <?php endif; ?>
                <p class="description description-thin">
                    <label for="edit-menu-item-title-<?php echo $item_id; ?>">
                        <?php _e( 'Navigation Label' ); ?><br />
                        <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
                    </label>
                </p>
                <p class="description description-thin">
                    <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
                        <?php _e( 'Title Attribute' ); ?><br />
                        <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
                    </label>
                </p>
                <p class="description description-thin">
                    <label for="edit-menu-item-attr-anchor-<?php echo $item_id; ?>">
                        <?php _e( 'Link Anchor' ); ?><br />
                        <input type="text" id="edit-menu-item-attr-anchor-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-anchor" name="menu-item-attr-anchor[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->anchor ); ?>" />
                    </label>
                </p>
                <p class="field-link-target description">
                    <label for="edit-menu-item-target-<?php echo $item_id; ?>">
                        <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
                        <?php _e( 'Open link in a new window/tab' ); ?>
                    </label>
                </p>
                <p class="field-css-classes description description-thin">
                    <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
                        <?php _e( 'CSS Classes (optional)' ); ?><br />
                        <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
                    </label>
                </p>
                <p class="field-xfn description description-thin">
                    <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
                        <?php _e( 'Link Relationship (XFN)' ); ?><br />
                        <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
                    </label>
                </p>
                <p class="field-description description description-wide">
                    <label for="edit-menu-item-description-<?php echo $item_id; ?>">
                        <?php _e( 'Description' ); ?><br />
                        <textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
                        <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.'); ?></span>
                    </label>
                </p>

                <p class="field-move hide-if-no-js description description-wide">
                    <label>
                        <span><?php _e( 'Move' ); ?></span>
                        <a href="#" class="menus-move-up"><?php _e( 'Up one' ); ?></a>
                        <a href="#" class="menus-move-down"><?php _e( 'Down one' ); ?></a>
                        <a href="#" class="menus-move-left"></a>
                        <a href="#" class="menus-move-right"></a>
                        <a href="#" class="menus-move-top"><?php _e( 'To the top' ); ?></a>
                    </label>
                </p>

                <div class="menu-item-actions description-wide submitbox">
                    <?php if( 'custom' != $item->type && $original_title !== false ) : ?>
                        <p class="link-to-original">
                            <?php printf( __('Original: %s'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
                        </p>
                    <?php endif; ?>
                    <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
                    echo wp_nonce_url(
                        add_query_arg(
                            array(
                                'action' => 'delete-menu-item',
                                'menu-item' => $item_id,
                            ),
                            admin_url( 'nav-menus.php' )
                        ),
                        'delete-menu_item_' . $item_id
                    ); ?>"><?php _e( 'Remove' ); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => $item_id, 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
                        ?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel'); ?></a>
                </div>

                <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
                <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
                <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
                <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
                <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
                <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
            </div><!-- .menu-item-settings-->
            <ul class="menu-item-transport"></ul>
        <?php
        $output .= ob_get_clean();
    }

}



/*============================== SHORTCODES ======================================================================================================================*/

function biznex_before_shortcode(){

    return '</div></section>
    ';

}

function biznex_after_shortcode(){

    return '
    <section class="light no-bottom-padding no-top-padding"><div class="container compact">';

}

function biznex_column_first( $atts, $content = null ){
    extract(shortcode_atts(array(
            'size' => 4,
            'offset' => 0,
            'style' => '',
        ), $atts));
    $size = (int)$size;
    return '<div class="row"><div class="col-md-'.$size.($offset?' offset'.$offset:'').'" style="'.$style.'">'.biznex_shortcode_fix(do_shortcode($content)).'</div>';
}
function biznex_column( $atts, $content = null ){
    extract(shortcode_atts(array(
            'size' => 4,
            'offset' => 0,
            'style' => '',
        ), $atts));
    $size = (int)$size;
    return '<div class="col-md-'.$size.($offset?' offset'.$offset:'').'" style="'.$style.'">'.biznex_shortcode_fix(do_shortcode($content)).'</div>';
}
function biznex_column_last( $atts, $content = null ){
    extract(shortcode_atts(array(
            'size' => 4,
            'offset' => 0,
            'style' => '',
        ), $atts));
    $size = (int)$size;
    return '<div class="col-md-'.$size.($offset?' offset'.$offset:'').'" style="'.$style.'">'.biznex_shortcode_fix(do_shortcode($content)).'</div></div>';
}
add_shortcode( 'biznex_column_first', 'biznex_column_first' );
add_shortcode( 'biznex_column', 'biznex_column' );
add_shortcode( 'biznex_column_last', 'biznex_column_last' );

function biznex_subscribe_section( $atts, $content = null ){

    extract(shortcode_atts(array(
        'title' => _x('Enter your email address for latest updated!','[biznex_subscribe_section]','biznex'),
        'placeholder' => _x('YOUR EMAIL ADDRESS','[biznex_subscribe_section]','biznex'),
        'submit_text' => _x('Subscribe','[biznex_subscribe_section]','biznex'),
        'section_id' => ''
    ), $atts));

    if(''!==$title)
        $title = '<p>'.$title.'</p>';

    $output = '';

    $output .=
    '
    <!-- Subscribe section -->
    <div class="subscribe" id="'.$section_id.'">
        '.$title.'
        <form method="post">
            <input type="text" name="email" placeholder="'.$placeholder.'" value="" data-tt-subscription-required data-tt-subscription-type="email" />
            <button type="submit" class="button button-small">'.$submit_text.'</button>
        </form>
        <div class="result"></div>
    </div>
    ';

    return biznex_before_shortcode().$output.biznex_after_shortcode();

}

add_shortcode('biznex_subscribe_section', 'biznex_subscribe_section');

function biznex_contact_section( $atts, $content = null ){

    extract(shortcode_atts(array(
        'section_title' => _x('Contact us','[biznex_contact_section]','biznex'),
        'section_header_icon' => '',
        'section_header_left' => '',
        'section_header_right' => '',
        'form_title' => _x('We\'d love to hear from you! Send us a message using the form below.','[biznex_contact_section]','biznex'),
        'placeholder_name' => _x('NAME','[biznex_contact_section]','biznex'),
        'placeholder_email' => _x('EMAIL','[biznex_contact_section]','biznex'),
        'placeholder_subject' => _x('SUBJECT','[biznex_contact_section]','biznex'),
        'placeholder_message' => _x('MESSAGE','[biznex_contact_section]','biznex'),
        'submit_text' => _x('SEND MESSAGE','[biznex_contact_section]','biznex'),
        'section_id' => ''
    ), $atts));

    if(''!==$section_title)
        $section_title = '<h1>'.$section_title.'</h1>';

    if(''!==$form_title)
        $form_title = '<p class="align-center">'.$form_title.'</p>';

    if(''!==$section_header_icon)
        $section_header_icon = '<img src="'.$section_header_icon.'" class="arrow" alt="" />';

    if(''!==$section_header_left)
        $section_header_left = '<div class="left"><p>'.$section_header_left.'</p></div>';

    if(''!==$section_header_right)
        $section_header_right = '<div class="right"><p>'.$section_header_right.'</p></div>';

    if(''!==$section_header_icon||''!==$section_header_left||''!==$section_header_right)
        $section_header =
    '
    <div class="contact-line">
        '.$section_header_left.'
        '.$section_header_right.'
        '.$section_header_icon.'
    </div>
    ';
    else
        $section_header = '';

    $output = '';

    $output .=
    '
    <!-- Contact us section -->
    <section id="'.$section_id.'" class="light no-bottom-padding">
        '.$section_title.'
        '.$section_header.'
        <div class="container compact">
            '.$form_title.'
            <form method="post" class="contact-form row">
                <div class="col-lg-6 col-md-6">
                    <input type="text" name="biznex-name" value="" placeholder="'.$placeholder_name.'" /><br />
                    <input type="text" name="biznex-email" value="" placeholder="'.$placeholder_email.'" /><br />
                    <input type="text" name="biznex-subject" value="" placeholder="'.$placeholder_subject.'" />
                    <div class="result"></div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <textarea name="biznex-message" placeholder="'.$placeholder_message.'"></textarea><br />
                    <div class="align-right">
                        <button type="submit" class="button button-small">'.$submit_text.'</button>
                    </div>
                </div>
                <input type="hidden" name="action" value="biznex_contact" />
            </form>
        </div>
    ';

    ob_start();
    tt_gmap('contact_map','contact_map','map','false');
    $output .= ob_get_contents();
    ob_end_clean();

    $output .= '
    </section>
    ';

    return biznex_before_shortcode().$output.biznex_after_shortcode();

}

add_shortcode('biznex_contact_section', 'biznex_contact_section');

function biznex_blog_posts_section( $atts, $content = null ){

    extract(shortcode_atts(array(
        'nr' => 5,
        'category' => '',
        'enable_links' => true,
        'top_arrow' => false,
        'link_target' => '',
        'more_text' => _x('Full story','[biznex_blog_posts_section]','biznex'),
        'title' => _x('Blog posts','[biznex_blog_posts_section]','biznex'),
        'subtitle' => '',
        'section_id' => ''
    ), $atts));

    if(''!==$title)
        $title = '<h1>'.$title.'</h1>';

    if(''!==$subtitle)
        $subtitle = '<p class="align-center">'.$subtitle.'</p>';

    $enable_links = 'false'===strtolower($enable_links) ? false : (bool) $enable_links;

    $top_arrow = 'false'===strtolower($top_arrow) ? false : (bool) $top_arrow;

    if($top_arrow)
        $top_arrow_html =
    '
    <div class="overlay">
        <div class="semi-arrow"></div>
    </div>
    ';
    else
        $top_arrow_html = '';

    $output = '';

    $output .= 
    '
    <!-- Blog posts section -->
    <section id="'.$section_id.'" class="blue">
        '.$top_arrow_html.'
        <div class="container compact">
            '.$title.'
            '.$subtitle.'
            <div class="posts">
    ';

    $output .= biznex_blog_posts_get($category, $nr, 0, $enable_links, $link_target, $more_text);

    $options = array(
        'category' => $category,
        'nr' => $nr,
        'enable_links' => $enable_links,
        'link_target' => $link_target,
        'more_text' => $more_text,
        'page' => 0,
        'url' => admin_url('admin-ajax.php'),
        'action' => 'biznex_blog_posts'
    );

    $output .=
    '
            </div>
            <div class="posts_load" data-options="'.htmlspecialchars(json_encode($options)).'"></div>
        </div>
    </section>
    ';

    return biznex_before_shortcode().$output.biznex_after_shortcode();

}

function biznex_blog_posts_get($category = '', $nr = 5, $page = 0, $enable_links = true, $link_target = '', $more_text = ''){

    $output = '';

    $args = array(
        'numberposts' => $nr,
        'orderby' => 'post_date',
        'order' => 'DESC',
        'post_type' => 'post',
        'post_status' => 'publish',
        'post__not_in' => get_option( 'sticky_posts' ),
        'category_name' => $category,
        'offset' => $page * $nr
    );

    $query = get_posts($args);

    $count = count($query);

    foreach ($query as $i => $q) {

        setup_postdata($q);

        if(has_post_thumbnail($q->ID))
            $q_featured = get_the_post_thumbnail($q->ID);
        else
            $q_featured = '';

        if($enable_links)
            $q_more =
            '
            <div class="align-center">
                <a href="'.get_permalink($q->ID).'" target="'.$link_target.'" class="button button-small">'.$more_text.'</a>
            </div>
            ';
        else
            $q_more = '';

        switch (biznex_post_type($q)) {
            case 'image':
                $q_type = '<img src="'.tesla_locate_uri('img/blog-type-camera.png').'" class="type" alt="" />';
                break;
            
            default:
                $q_type = '<img src="'.tesla_locate_uri('img/blog-type-article.png').'" class="type" alt="" />';
                break;
        }

        $output .=
        '
        <div class="'.join(get_post_class('',$q->ID),' ').'">
            <a href="'.get_permalink($q->ID).'" class="line">
                '.$q_type.'
                <span class="title">'.get_the_title($q->ID).'</span>
                <span class="date">'.biznex_post_time($q, 'j M').'</span>
            </a>
            '.$q_featured.'
            <div class="content">
                '.biznex_excerpt($q).'
            </div>
            '.$q_more.'
        </div>
        ';

    }

    wp_reset_postdata();

    return $output;

}

function biznex_blog_posts_ajax(){

    echo biznex_blog_posts_get($_REQUEST['category'], $_REQUEST['nr'], $_REQUEST['page'], $_REQUEST['enable_links'], $_REQUEST['link_target'], $_REQUEST['more_text']);
    exit;

}

add_action('wp_ajax_biznex_blog_posts', 'biznex_blog_posts_ajax');
add_action('wp_ajax_nopriv_biznex_blog_posts', 'biznex_blog_posts_ajax');

add_shortcode('biznex_blog_posts_section', 'biznex_blog_posts_section');

function biznex_about_us_section( $atts, $content = null ){

    extract(shortcode_atts(array(
        'title' => '',
        'subtitle' => '',
        'image' => '',
        'section_id' => ''
    ), $atts));

    $content = biznex_shortcode_fix(do_shortcode($content));

    if(''!==$title)
        $title = '<h1>'.$title.'</h1>';

    if(''!==$subtitle)
        $subtitle = '<p class="align-center">'.$subtitle.'</p>';

    if(''!==$image)
        $image = '<img src="'.$image.'" class="asset-about-us-image" alt="" />';

    $output = '';

    $output .=
    '
    <section id="'.$section_id.'" class="light">
        <div class="container compact">
            '.$title.'
            '.$subtitle.'
            '.$content.'
            '.$image.'
        </div>
    </section>
    ';

    return biznex_before_shortcode().$output.biznex_after_shortcode();

}

add_shortcode('biznex_about_us_section', 'biznex_about_us_section');

function biznex_social( $atts, $content = null ){

    extract(shortcode_atts(array(
        'style' => ''
    ), $atts));

    $content = biznex_shortcode_fix(do_shortcode($content));

    if(''!==$style)
        $style = ' style="'.htmlspecialchars($style).'" ';

    $output = '';

    $social = array(
        'facebook'=>_go('social_platforms_facebook'),
        'twitter'=>_go('social_platforms_twitter'),
        'google-plus'=>_go('social_platforms_google'),
        'linkedin'=>_go('social_platforms_linkedin'),
        'dribbble'=>_go('social_platforms_dribbble'),
        'pinterest'=>_go('social_platforms_pinterest'),
        'instagram'=>_go('social_platforms_instagram')
    );

    $social_custom = _go_repeated('Custom Icons');

    $social_filtered = array_filter($social);

    $output .= '<div class="social-icons align-center" '.$style.' >';

    foreach($social_filtered as $social_key => $social_value){
        $output .= '<a href="'.htmlspecialchars($social_value).'"><img src="'.tesla_locate_uri('img/social-'.$social_key.'.png').'" alt="" /></a>';
    }

    foreach($social_custom as $social_custom_icon){
        if(''!==$social_custom_icon['custom_social_url']&&''!==$social_custom_icon['custom_social_image']){
            $output .= '<a href="'.htmlspecialchars($social_custom_icon['custom_social_url']).'"><img src="'.htmlspecialchars($social_custom_icon['custom_social_image']).'" alt="" /></a>';
        }
    }

    $output .= '</div>';

    return $output;

}

add_shortcode('biznex_social', 'biznex_social');

function biznex_parallax_section( $atts, $content = null ){

    extract(shortcode_atts(array(
        'big_title' => '',
        'medium_title' => '',
        'small_title' => '',
        'speed' => 5,
        'image' => '',
        'section_id' => '',
        'class' => '',
        'button_text' => '',
        'button_url' => '#'
    ), $atts));

    $content = biznex_shortcode_fix(do_shortcode($content));

    if(''!==$big_title)
        $big_title = '<h3 class="big">'.$big_title.'</h3>';

    if(''!==$medium_title)
        $medium_title = '<h3>'.$medium_title.'</h3>';

    if(''!==$small_title)
        $small_title = '<h3 class="small">'.$small_title.'</h3>';

    if(''!==$image)
        $image = 'background-image:url(\''.$image.'\');';

    if(''!==$button_text)
        $button = '<div class="align-center"><a href="'.$button_url.'" class="button">'.$button_text.'</a></div>';
    else
        $button = '';

    $output = '';

    $output .=
    '
    <div id="'.$section_id.'" class="parallax '.$class.'" data-speed="'.$speed.'" style="'.$image.'">
        <div class="overlay"></div>
        <div class="content">
            <div class="container">
                '.$big_title.'
                '.$medium_title.'
                '.$small_title.'
                '.$content.'
                '.$button.'
            </div>
        </div>
    </div>
    ';

    return biznex_before_shortcode().$output.biznex_after_shortcode();

}

add_shortcode('biznex_parallax', 'biznex_parallax_section');
add_shortcode('biznex_parallax_section', 'biznex_parallax_section');

function biznex_section( $atts, $content = null ){

    extract(shortcode_atts(array(
        'top_arrow' => '',
        'top_arrow_color' => '',
        'section_id' => '',
        'class' => 'light',
        'title' => '',
        'small_title' => '',
        'subtitle' => '',
        'compact' => true
    ), $atts));

    $compact = 'false' === $compact ? false : (bool) $compact;

    $content = biznex_shortcode_fix(do_shortcode($content));

    if(''!==$title)
        $title = '<h1>'.$title.'</h1>';

    if(''!==$small_title)
        $small_title = '<h4 class="section-title">'.$small_title.'</h4>';

    if(''!==$subtitle)
        $subtitle = '<p class="align-center">'.$subtitle.'</p>';

    if($top_arrow_color)
        $top_arrow_color = 'border-color:'.$top_arrow_color.';';
    else
        $top_arrow_color = '';

    if($top_arrow)
        $top_arrow_html = '<div class="overlay"><div class="'.$top_arrow.'" style="'.$top_arrow_color.'"></div></div>';
    else
        $top_arrow_html = '';

    $output = '';

    $output .= '<section id="'.$section_id.'" class="'.$class.'">'.$top_arrow_html.'<div class="container'.($compact?' compact':'').'">';

    $output .= $title;

    $output .= $small_title;

    $output .= $subtitle;

    $output .= $content;

    $output .= '</div></section>';

    return biznex_before_shortcode().$output.biznex_after_shortcode();

}

add_shortcode('biznex_section', 'biznex_section');

function biznex_scalable_websites_section( $atts, $content = null ){

    extract(shortcode_atts(array(
        'top_arrow' => false,
        'top_arrow_color' => false,
        'section_id' => '',
        'class' => 'light',
        'title' => '',
        'subtitle' => '',
        'trails_image' => tesla_locate_uri('assets/scalable-trails.png'),
        'devices_image' => tesla_locate_uri('assets/scalable-devices.png'),
        'button_text' => _x('Learn more','[biznex_scalable_websites_section]','biznex'),
        'button_url' => '#'
    ), $atts));

    $content = biznex_shortcode_fix(do_shortcode($content));

    if(''!==$title)
        $title = '<h3>'.$title.'</h3>';

    if(''!==$subtitle)
        $subtitle = '<p class="align-center">'.$subtitle.'</p>';

    if(''!==$trails_image)
        $trails_image = '<img src="'.$trails_image.'" class="trails" />';

    if(''!==$devices_image)
        $devices_image = '<img src="'.$devices_image.'" class="devices" />';

    if($top_arrow_color)
        $top_arrow_color = 'border-color:'.$top_arrow_color.';';
    else
        $top_arrow_color = '';

    if($top_arrow)
        $top_arrow_html = '<div class="overlay"><div class="'.$top_arrow.'" style="'.$top_arrow_color.'"></div></div>';
    else
        $top_arrow_html = '';

    if($button_text)
        $button = '<a href="'.$button_url.'" class="button button-small">'.$button_text.'</a>';
    else
        $button = '';

    $output = '';

    $output .= '<section id="'.$section_id.'" class="asset-scalable '.$class.'">'.$top_arrow_html.'<div class="container compact">';

    $output .= $title;

    $output .= $subtitle;

    $output .= $content;

    $output .= $button;

    $output .= $trails_image;

    $output .= $devices_image;

    $output .= '</div></section>';

    return biznex_before_shortcode().$output.biznex_after_shortcode();

}

add_shortcode('biznex_scalable_websites_section', 'biznex_scalable_websites_section');

function biznex_portfolio_section( $atts, $content = null ){

    extract(shortcode_atts(array(
        'nr' => 0,
        'title' => _x('Portfolio', '[biznex_portfolio_section]', 'biznex'),
        'subtitle' => '',
        'section_id' => '',
        'enable_slider' => true,
        'pagination' => false,
        'items_per_page' => 2,
        'category' => ''
    ), $atts));

    $items_per_page = (int) $items_per_page;

    $posts_count = wp_count_posts('biznex_portfolio');

    $pages = ceil($posts_count->publish/$items_per_page);

    $pagination = 'false' === strtolower($pagination) ? false : (bool) $pagination;

    $enable_slider = 'false' === strtolower($enable_slider) ? false : (bool) $enable_slider;

    $content = biznex_shortcode_fix(do_shortcode($content));

    if(''!==$title)
        $title = '<h1>'.$title.'</h1>';

    if(''!==$subtitle)
        $subtitle = '<p class="align-center">'.$subtitle.'</p>';

    $output = '';

    $output .= '<section id="'.$section_id.'" class="light no-bottom-padding"><div class="container compact">'.$title.$subtitle.$content.'</div>';

    $output .= Tesla_slider::get_slider_html('biznex_portfolio',array(
        'shortcode_parameters' => array(
            'nr' => $nr
        ),
        'custom' => array(
            'enable_slider' => $enable_slider,
            'pagination' => $pagination,
            'pages' => $pages,
            'items_per_page' => $items_per_page
        ),
        'category' => $category
    ));

    $output .= '</section>';

    return biznex_before_shortcode().$output.biznex_after_shortcode();

}

function biznex_portfolio_get($category = '', $nr = 5, $page = 0, $enable_links = true, $link_target = '', $more_text = ''){

    $output = '';

    $args = array(
        'numberposts' => $nr,
        'orderby' => 'post_date',
        'order' => 'DESC',
        'post_type' => 'post',
        'post_status' => 'publish',
        'post__not_in' => get_option( 'sticky_posts' ),
        'category_name' => $category,
        'offset' => $page * $nr
    );

    $query = get_posts($args);

    $count = count($query);

    foreach ($query as $i => $q) {

        setup_postdata($q);

        if(has_post_thumbnail($q->ID))
            $q_featured = get_the_post_thumbnail($q->ID);
        else
            $q_featured = '';

        if($enable_links)
            $q_more =
            '
            <div class="align-center">
                <a href="'.get_permalink($q->ID).'" target="'.$link_target.'" class="button button-small">'.$more_text.'</a>
            </div>
            ';
        else
            $q_more = '';

        switch (biznex_post_type($q)) {
            case 'image':
                $q_type = '<img src="'.tesla_locate_uri('img/blog-type-camera.png').'" class="type" alt="" />';
                break;
            
            default:
                $q_type = '<img src="'.tesla_locate_uri('img/blog-type-article.png').'" class="type" alt="" />';
                break;
        }

        $output .=
        '
        <div class="'.join(get_post_class('',$q->ID),' ').'">
            <a href="'.get_permalink($q->ID).'" class="line">
                '.$q_type.'
                <span class="title">'.get_the_title($q->ID).'</span>
                <span class="date">'.biznex_post_time($q, 'j M').'</span>
            </a>
            '.$q_featured.'
            <div class="content">
                '.biznex_excerpt($q).'
            </div>
            '.$q_more.'
        </div>
        ';

    }

    wp_reset_postdata();

    return $output;

}

function biznex_portfolio_ajax(){

    echo biznex_portfolio_get($_REQUEST['category'], $_REQUEST['nr'], $_REQUEST['page'], $_REQUEST['enable_links'], $_REQUEST['link_target'], $_REQUEST['more_text']);
    exit;

}

add_action('wp_ajax_biznex_portfolio', 'biznex_portfolio_ajax');
add_action('wp_ajax_nopriv_biznex_portfolio', 'biznex_portfolio_ajax');

add_shortcode('biznex_portfolio_section', 'biznex_portfolio_section');

function biznex_team_section( $atts, $content = null ){

    extract(shortcode_atts(array(
        'nr' => 0,
        'title' => _x('Meet our team', '[biznex_team_section]', 'biznex'),
        'subtitle' => '',
        'section_id' => ''
    ), $atts));

    $content = biznex_shortcode_fix(do_shortcode($content));

    if(''!==$title)
        $title = '<h1>'.$title.'</h1>';

    if(''!==$subtitle)
        $subtitle = '<p class="align-center">'.$subtitle.'</p>';

    $output = '';

    $output .= '<section id="'.$section_id.'" class="light"><div class="container compact">'.$title.$subtitle.$content;

    $output .= Tesla_slider::get_slider_html('biznex_team',array(
        'shortcode_parameters' => array(
            'nr' => $nr
        )
    ));

    $output .= '</div></section>';

    return biznex_before_shortcode().$output.biznex_after_shortcode();

}

add_shortcode('biznex_team_section', 'biznex_team_section');

function biznex_experience_section( $atts, $content = null ){

    extract(shortcode_atts(array(
        'nr' => 0,
        'title' => _x('The experience', '[biznex_experience_section]', 'biznex'),
        'subtitle' => '',
        'section_id' => ''
    ), $atts));

    $content = biznex_shortcode_fix(do_shortcode($content));

    if(''!==$title)
        $title = '<h1>'.$title.'</h1>';

    if(''!==$subtitle)
        $subtitle = '<p class="align-center">'.$subtitle.'</p>';

    $output = '';

    $output .= '<section id="'.$section_id.'" class="yellow"><div class="overlay"><div class="arrow" style=""></div></div><div class="container compact">'.$title.$subtitle.$content;

    $output .= Tesla_slider::get_slider_html('biznex_skills',array(
        'shortcode_parameters' => array(
            'nr' => $nr
        )
    ));

    $output .= '</div></section>';

    return biznex_before_shortcode().$output.biznex_after_shortcode();

}

add_shortcode('biznex_experience_section', 'biznex_experience_section');

function biznex_solutions_section( $atts, $content = null ){

    extract(shortcode_atts(array(
        'nr' => 0,
        'title' => _x('How can we help', '[biznex_solutions_section]', 'biznex'),
        'subtitle' => '',
        'section_id' => ''
    ), $atts));

    $content = biznex_shortcode_fix(do_shortcode($content));

    if(''!==$title)
        $title = '<h1>'.$title.'</h1>';

    if(''!==$subtitle)
        $subtitle = '<p class="align-center">'.$subtitle.'</p>';

    $output = '';

    $output .= '<section id="'.$section_id.'" class="green asset-mini-slider"><div class="overlay"><div class="arrow" style=""></div></div><div class="container compact">'.$title.$subtitle.$content.'<br/>';

    $output .= Tesla_slider::get_slider_html('biznex_solutions',array(
        'shortcode_parameters' => array(
            'nr' => $nr
        )
    ));

    $output .= '</div></section>';

    return biznex_before_shortcode().$output.biznex_after_shortcode();

}

add_shortcode('biznex_solutions_section', 'biznex_solutions_section');

function biznex_main_slider_section( $atts, $content = null ){

    extract(shortcode_atts(array(
        'section_id' => '',
        'height' => ''
    ), $atts));

    $output = '';

    $output .= Tesla_slider::get_slider_html('biznex_main',array(
        'shortcode_parameters' => array(
            'section_id' => $section_id,
            'height' => $height
        )
    ));

    return biznex_before_shortcode().$output.biznex_after_shortcode();

}

add_shortcode('biznex_main_slider_section', 'biznex_main_slider_section');

function biznex_history( $atts, $content = null ){

    extract(shortcode_atts(array(
        'title' => ''
    ), $atts));

    if(''!==$title)
        $title = '<h3>'.$title.'</h3>';

    $content = biznex_shortcode_fix(do_shortcode($content));

    $output = '';

    $output .= '<div class="marketing">';

    $output .= $title;

    $output .= $content;

    $output .= '</div>';

    return $output;

}

add_shortcode('biznex_history', 'biznex_history');

function biznex_twitter( $atts, $content = null ){

    extract(shortcode_atts(array(
        'user' => 'teslathemes',
        'nr' => 3
    ), $atts));

    $nr = (int) $nr;

    $output = '';

    $output .= twitter_generate_output($user, $nr, '', 'biznex_twitter_output','<div class="twitter-widget" data-tesla-plugin="slider" data-tesla-item=">p" data-tesla-container=">div"><div>','</div></div>');

    return $output;

}

function biznex_twitter_output($i, $text, $date){
    return '<p>'.$text.'<span>'.$date.'</span></p>';
}

add_shortcode('biznex_twitter', 'biznex_twitter');

function biznex_event_section( $atts, $content = null ){

    extract(shortcode_atts(array(
        'section_id' => '',
        'title' => '',
        'image' => '',
        'date' => '',
        'url' => ''
    ), $atts));

    if(''!==$title)
        if(''!=$url)
            $title = '<h2><a href="'.$url.'">'.$title.'</a></h2>';
        else
            $title = '<h2>'.$title.'</h2>';

    if(''!==$date)
        $date = '<h4>'.$date.'</h4>';

    if(''!==$image)
        $image = '<div class="col-md-6"><div class="event-cover"><img src="'.$image.'" alt="event"></div></div>';

    $content = biznex_shortcode_fix(do_shortcode($content));

    $output = '';

    $output .= '<section id="'.$section_id.'" class="events"><div class="container compact">';

    $output .= $image;

    $output .= '<div class="col-md-'.(''!==$image?6:12).'"><div class="event-details">';

    $output .= $title;

    $output .= $date;

    $output .= $content;

    $output .= '</div></div></div></section>';

    return biznex_before_shortcode().$output.biznex_after_shortcode();

}

add_shortcode('biznex_event_section', 'biznex_event_section');

function biznex_shortcode_fix($content){

    return preg_replace(array(
        '/^\s*<\/p>/',
        '/<p>\s*$/'
    ), '', $content);

}

class Biznex_Nav_Menu_Walker extends Walker_Nav_Menu {

    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        if(empty($item->anchor))
            $classes[] = 'biznex-no-anchor';

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $value . $class_names .'>';

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';
        if(!empty($item->anchor))
            $atts['href'] .= '#'.$item->anchor;
        $atts['data-anchor'] = ! empty( $item->anchor ) ? $item->anchor : '';

        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        
        $item_output .= $args->link_before . (''!==$item->description?'<span class="number">'.$item->description.'</span>':'') . apply_filters( 'the_title', strip_tags($item->title), $item->ID ) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}



/*============================== THEME VERSION COMPATIBILITY ======================================================================================================================*/

function biznex_compatibility($meta, $id, $context){
    $post_type = get_post_type($id);
    switch($post_type){
        case 'biznex_portfolio':
            if(isset($meta['big_image'])){
                if(!isset($meta['lightbox'])){
                    $meta['lightbox'] = array(array('image'=>$meta['big_image']));
                }
                unset($meta['big_image']);
            }
            break;

        default:
            break;
    }
    return $meta;
}

add_filter('tesla_slide_options', 'biznex_compatibility', 10, 3);