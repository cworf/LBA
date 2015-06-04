<!-- Testimonials slider -->
<ul class="testimonials">
    <?php foreach($slides as $i => $slide): ?>
    <li>
        <h3><?php echo get_the_title($slide['post']->ID); ?></h3>
        <img src="<?php echo htmlspecialchars($slide['options']['image']); ?>" class="image" alt="" />
        <blockquote>
            <?php echo wpautop($slide['options']['text']); ?>
            <cite>--- <?php echo $slide['options']['author']; ?> <span class="by"><?php echo $slide['options']['company']; ?></span></cite>
        </blockquote>
    </li>
    <?php endforeach; ?>
</ul>
<!-- Testimonials slider bullets navigation -->
<div class="bullets"></div>