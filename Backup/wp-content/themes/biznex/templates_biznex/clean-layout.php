<?php
/*
Template Name: Clean Layout
*/
?>
<?php get_header(); ?>
<?php while(have_posts()): the_post(); ?>
<section class="light no-bottom-padding no-top-padding">
    <div class="container compact">
		<?php the_content(); ?>
	</div>
</section>
<?php endwhile; ?>
<?php get_footer(); ?>