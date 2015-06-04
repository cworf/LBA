<?php get_header(); ?>
<section class="error-404">
    <div class="container compact">
    <h1><?php _ex('<b>Error 404</b> PAGE NOT FOUND','404','biznex'); ?></h1>

    <div class="error-img"><img src="<?php echo tesla_locate_uri('img/error-bg.png'); ?>" alt="error" /></div>

    <p><?php _ex('The page you are looking for might have been removed, had its name changed, or it is unavailable.','404','biznex'); ?></p>

    <div class="widget">
        <?php get_search_form(); ?>
    </div>

    </div>
</section>
<?php get_footer(); ?>