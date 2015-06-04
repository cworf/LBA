<div class="vertical-tabs">
    <ul class="resp-tabs-list">
        <?php foreach($slides as $i => $slide): ?>
        <li><?php echo get_the_title($slide['post']->ID); ?><span class="number"><?php echo sprintf('%02d', $i+1); ?></span></li>
        <?php endforeach; ?>
    </ul>
    <div class="resp-tabs-container">
        <?php foreach($slides as $i => $slide): ?>
        <div>
            <h4><?php echo get_the_title($slide['post']->ID); ?></h4>
            <?php echo wpautop($slide['options']['description']); ?>
            <p><img src="<?php echo htmlspecialchars($slide['options']['image']); ?>" style="max-width:100%;" alt="" /></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>