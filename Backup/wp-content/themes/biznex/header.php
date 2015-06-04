<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>

        <meta charset="<?php bloginfo( 'charset' ); ?>" />

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title><?php wp_title( '|', true, 'right' ); ?></title>

        <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
        <link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
        <link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
        <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-57343618-1', 'auto');
  ga('send', 'pageview');

</script>

        <?php wp_head(); ?>

    </head>
    <body <?php body_class(); ?>>

        <!-- Header -->
        <header>
            <div class="container">
                <!-- Navigational bar -->
                <div class="navbar">
                    <!-- Logo and responsive navigation toggle -->
                    <div class="navbar-header">
                        <a href="<?php echo esc_url(home_url()); ?>" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"><img src="<?php echo tesla_locate_uri('img/navbar-toggle.png'); ?>" alt="" /></a>
                        <a href="<?php echo esc_url(home_url()); ?>" class="logo">
                            <?php
                                $logo_text = _go('logo_text');
                                if(empty($logo_text)){
                                    $logo_image = _go('logo_image');
                                    if(empty($logo_image))
                                        echo '<strong>'.get_bloginfo('name').'</strong><br/><em>'.get_bloginfo('description').'</em>';
                                    else
                                        echo '<img src="'.$logo_image.'" alt="logo" />';
                                }else{
                                    $logo_text_color = _go('logo_text_color');
                                    if(empty($logo_text_color))
                                        $logo_text_color = '';
                                    else
                                        $logo_text_color = 'color:'.$logo_text_color.';';
                                    $logo_text_font = _go('logo_text_font');
                                    if(empty($logo_text_font))
                                        $logo_text_font = '';
                                    else
                                        $logo_text_font = 'font-family:'.$logo_text_font.';';
                                    $logo_text_size = _go('logo_text_size');
                                    if(empty($logo_text_size))
                                        $logo_text_size = '';
                                    else
                                        $logo_text_size = 'font-size:'.$logo_text_size.'px;';
                                    echo '<span style="'.$logo_text_color.$logo_text_font.$logo_text_size.'">'.$logo_text.'</span>';
                                }
                            ?>
                        </a>
                    </div>
                </div>
                
                <!-- Collapsable navigation -->
                <?php
                if(has_nav_menu('biznex_menu')){
                    wp_nav_menu(array(
                        'theme_location'  => 'biznex_menu',
                        'walker'          => new Biznex_Nav_Menu_Walker,
                        'menu_class'      => 'nav navbar-nav navbar-right',
                        'container_class' => 'navbar-collapse collapse'
                    ));
                }else{
                    echo '<div class="navbar-collapse collapse"><ul class="nav navbar-nav navbar-right">'.wp_list_pages(array(
                        'echo'            => 0,
                        'title_li'        => '',
                    )).'</ul></div>';
                }
                ?>
            </div>
        </header>
