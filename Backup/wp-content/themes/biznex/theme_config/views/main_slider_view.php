<!-- Main slider -->
<div id="<?php echo htmlspecialchars($shortcode['section_id']); ?>" class="slider" <?php if(!empty($shortcode['height'])) echo 'style="min-height:'.$shortcode['height'].'px;max-height:'.$shortcode['height'].'px;"' ?>>
    <?php foreach($slides as $i => $slide): ?>
    <div class="slide<?php if(!$i) echo ' active'; ?>">
        <img src="<?php echo htmlspecialchars($slide['options']['image']); ?>" class="image" alt="" />
        <div class="content">
            <div class="h1"><?php echo get_the_title($slide['post']->ID); ?></div>
            <div class="h2"><?php echo $slide['options']['subtitle']; ?></div>
            <div class="align-center">
                <?php echo wpautop($slide['options']['description']); ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>