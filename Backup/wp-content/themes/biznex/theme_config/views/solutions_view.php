<div class="flexslider mini">
    <ul class="slides">
        <?php foreach($slides as $i => $slide): ?>
        <li class="slide">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <img src="<?php echo htmlspecialchars($slide['options']['image']); ?>" alt="" />
                </div>
                <div class="col-lg-6 col-md-6">
                    <h3 class="align-left"><?php echo get_the_title($slide['post']->ID); ?></h3>
                    <?php echo wpautop($slide['options']['description']); ?>
                    <?php if($slide['options']['highlights']): ?>
                    <ul class="list">
                        <?php foreach($slide['options']['highlights'] as $highlight): ?>
                        <li><?php echo $highlight['item']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</div>