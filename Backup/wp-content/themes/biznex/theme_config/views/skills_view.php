<div class="row counters">
    <?php foreach($slides as $i => $slide): ?>
    <div class="col-lg-2 col-md-2 col-sm-4">
        <div class="counter">
            <div class="left"><img src="<?php echo tesla_locate_uri('img/counter-left.png'); ?>" alt="" /></div>
            <div class="right"><img src="<?php echo tesla_locate_uri('img/counter-right.png'); ?>" alt="" /></div>
            <div class="value" data-value="<?php echo $slide['options']['value']; ?>%">0%</div>
        </div>
        <div class="title"><?php echo get_the_title($slide['post']->ID); ?></div>
    </div>
    <?php endforeach; ?>
</div>