<?php get_header(); ?>
<section class="light no-bottom-padding">
    <div class="container compact">
        <div class="row">
                <div class="col-md-<?php echo is_active_sidebar('blog-sidebar')?8:12; ?>">
                    <?php if(have_posts()): ?>
                    <?php while(have_posts()): the_post(); ?>
                    <div <?php post_class('blog-entry '.(biznex_has_featured()?'blog-entry-image':'blog-entry-no-image')); ?>>
                        <div class="entry-header">
                            <div class="entry-details">
                                <span><?php the_time('j M'); ?></span>
                                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            </div>
                            <?php if(biznex_has_featured()): ?>
                            <div class="entry-cover">
                                <?php echo biznex_get_featured(); ?>
                            </div>
                            <?php endif; ?>
                            <div class="entry-comments">
                                <a href="<?php the_permalink(); ?>">(<?php echo get_comments_number(); ?>)</a>
                            </div>
                        </div>
                        <div class="entry-content">
                            <?php the_excerpt(); ?>
                            <a class="link-more" href="<?php the_permalink(); ?>"><?php _ex('full story','blog','biznex'); ?></a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php elseif(is_search()): ?>
                    <div class="novelty-no-results">
                        <p>
                            <?php _ex('No results.', 'blog', 'biznex'); ?>
                        </p>
                        <?php get_search_form(); ?>
                    </div>
                    <?php endif; ?>
                    <!-- === PAGINATION === -->
                    <?php
                    global $wp_query;
                    $big = 999999999; // need an unlikely integer
                    $total_pages = $wp_query->max_num_pages;
                    if ($total_pages > 1) {
                        $current_page = max(1, get_query_var('paged'));
                        echo paginate_links(array(
                            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                            'format' => '/page/%#%',
                            'current' => $current_page,
                            'total' => $total_pages,
                            'type' => 'list',
                            'next_text' => _x('NEXT', 'pagination', 'biznex'),
                            'prev_text' => _x('PREV', 'pagination', 'biznex'),
                        ));
                    }
                    ?>
                    <!-- === PAGINATION === -->
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
<?php return; ?>
