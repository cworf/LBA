<?php get_header(); ?>
<section class="light no-bottom-padding">
    <div class="container compact">
        <div class="row">
            <div class="col-md-<?php echo is_active_sidebar('blog-sidebar')?8:12; ?>">
                <?php while(have_posts()): the_post(); ?>
                <div <?php post_class('blog-entry '.(biznex_has_featured()?'blog-entry-image':'blog-entry-no-image')); ?>>
                    <div class="entry-header">
                        <div class="entry-details">
                            <span><?php the_time('j M'); ?></span>
                            <h2><?php the_title(); ?></h2>
                        </div>
                        <?php if(biznex_has_featured()): ?>
                        <div class="entry-cover">
                            <?php echo biznex_get_featured(); ?>
                        </div>
                        <?php endif; ?>
                        <div class="entry-comments">
                            <a href="post.html">(<?php echo get_comments_number(); ?>)</a>
                        </div>
                    </div>
                    <div class="entry-content">
                        <?php the_content(); ?>
                        <?php wp_link_pages(array(
                            'before'           => '<ul class="page-numbers">',
                            'after'            => '</ul>',
                            'link_before'      => '',
                            'link_after'       => '',
                            'next_or_number'   => 'number',
                            'separator'        => '',
                            'nextpagelink'     => _x( '&rarr;', 'pagination', 'biznex' ),
                            'previouspagelink' => _x( '&larr;', 'pagination', 'biznex' ),
                            'pagelink'         => '%',
                            'echo'             => 1
                        )); ?>
                    </div>
                    <?php if(has_category()): ?><div class="biznex-categories"><?php _e('Categories: '); ?><?php the_category(', '); ?></div><?php endif; ?>
                    <?php if(has_tag()): ?><div class="biznex-tags"><?php the_tags(); ?></div><?php endif; ?>
                    <div class="entry-footer">
                        <div class="social-icons align-right">
                            <span>Share on :</span>
                            <a class="st_facebook"><img src="<?php echo tesla_locate_uri('img/social-facebook.png'); ?>" alt="" /></a>
                            <a class="st_twitter"><img src="<?php echo tesla_locate_uri('img/social-twitter.png'); ?>" alt="" /></a>
                            <a class="st_googleplus"><img src="<?php echo tesla_locate_uri('img/social-google-plus.png'); ?>" alt="" /></a>
                            <a class="st_linkedin"><img src="<?php echo tesla_locate_uri('img/social-linkedin.png'); ?>" alt="" /></a>
                            <a class="st_pinterest"><img src="<?php echo tesla_locate_uri('img/social-pinterest.png'); ?>" alt="" /></a>
                        </div>
                    </div>
                </div>
                <?php comments_template(); ?>
                <?php endwhile; ?>
            </div>
            <?php if(is_active_sidebar('blog-sidebar')): ?>
            <div class="col-md-4">
                <div class="sidebar">
                    <?php dynamic_sidebar('blog-sidebar'); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php get_footer(); ?>
