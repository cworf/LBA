<div class="services clearfix">
    <?php foreach($slides as $i => $slide): ?>
    <?php if($i): ?>
    <div class="arrow"></div>
    <?php endif; ?>
    <div class="item">
        <h2><?php echo get_the_title($slide['post']->ID); ?></h2>
        <div class="graphic">
            <div class="hover"></div>
            <img src="<?php echo htmlspecialchars($slide['options']['image']); ?>" alt="" />
            <?php if(!empty($slide['options']['url'])): ?>
            <a href="<?php echo htmlspecialchars($slide['options']['url']); ?>" class="process-link"></a>
            <?php endif; ?>
        </div>
        <div class="bottom"></div>
        <div class="content"><?php echo $slide['options']['description']; ?></div>
    </div>
    <?php endforeach; ?>
</div>