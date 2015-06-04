<?php if($custom['enable_slider']): ?>
<div class="portfolio flexslider">
    <ul class="slides">
        <li>
            <?php foreach($slides as $i => $slide): ?>
            <?php if($i&&!($i%2)): ?>
        </li>
        <li>
            <?php endif; ?>
            <div class="item">
                <img src="<?php echo htmlspecialchars($slide['options']['small_image']); ?>" alt="" />
                <div class="overlay">
                    <span class="title"><?php echo get_the_title($slide['post']->ID); ?></span>
                    <?php
                    if(count($slide['categories'])){
                        echo '<span class="subtitle">';
                        foreach(array_values($slide['categories']) as $j => $category){
                            if($j) echo ' / ';
                            if(!($j%2))
                                echo $category;
                            else
                                echo '<em>'.$category.'</em>';
                        }
                        echo '</span>';
                    }
                    ?>
                    <?php foreach($slide['options']['lightbox'] as $key => $value): ?>
                    <a <?php if($key) echo 'style="display:none;"' ?> href="<?php if(isset($value['image'])) echo htmlspecialchars($value['image']); else echo htmlspecialchars($value['video']); ?>" data-lightbox-gallery="portfolio" class="zoom<?php if(!$key&&empty($slide['options']['url'])) echo ' item-no-url' ?>" title="<?php echo htmlspecialchars(get_the_title($slide['post']->ID)); ?>"><?php if(!$key): ?><img src="<?php echo htmlspecialchars(tesla_locate_uri('img/spacer.gif')); ?>" alt="" /><?php endif; ?></a>
                    <?php endforeach; ?>
                    <?php if(!empty($slide['options']['url'])): ?>
                    <a href="<?php echo $slide['options']['url']; ?>" class="link"<?php if(!empty($slide['options']['urltarget'])&&is_array($slide['options']['urltarget'])&&in_array('new-window', $slide['options']['urltarget'])) echo ' target="_blank"'; ?>><img src="<?php echo htmlspecialchars(tesla_locate_uri('img/spacer.gif')); ?>" alt="" /></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </li>
    </ul>
</div>
<?php else: ?>
<div class="container">
    <div class="portfolio">
        <?php $nr = $custom['pagination']?$custom['items_per_page']:count($slides); ?>
        <?php foreach($slides as $i => $slide): ?>
        <?php if($i>=$nr) break; ?>
        <div class="item">
            <img src="<?php echo htmlspecialchars($slide['options']['small_image']); ?>" alt="" />
            <div class="overlay">
                <span class="title"><?php echo get_the_title($slide['post']->ID); ?></span>
                <?php
                if(count($slide['categories'])){
                    echo '<span class="subtitle">';
                    foreach(array_values($slide['categories']) as $j => $category){
                        if($j) echo ' / ';
                        if(!($j%2))
                            echo $category;
                        else
                            echo '<em>'.$category.'</em>';
                    }
                    echo '</span>';
                }
                ?>
                <?php foreach($slide['options']['lightbox'] as $key => $value): ?>
                <a <?php if($key) echo 'style="display:none;"' ?> href="<?php if(isset($value['image'])) echo htmlspecialchars($value['image']); else echo htmlspecialchars($value['video']); ?>" data-lightbox-gallery="portfolio" class="zoom<?php if(!$key&&empty($slide['options']['url'])) echo ' item-no-url' ?>" title="<?php echo htmlspecialchars(get_the_title($slide['post']->ID)); ?>"><?php if(!$key): ?><img src="<?php echo htmlspecialchars(tesla_locate_uri('img/spacer.gif')); ?>" alt="" /><?php endif; ?></a>
                <?php endforeach; ?>
                <?php if(!empty($slide['options']['url'])): ?>
                <a href="<?php echo $slide['options']['url']; ?>" class="link"<?php if(!empty($slide['options']['urltarget'])&&is_array($slide['options']['urltarget'])&&in_array('new-window', $slide['options']['urltarget'])) echo ' target="_blank"'; ?>><img src="<?php echo htmlspecialchars(tesla_locate_uri('img/spacer.gif')); ?>" alt="" /></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="clear"></div>
    </div>
    <?php if($custom['pagination']&&$custom['pages']>1): ?>
    <ul class="page-numbers">
        <li><span class="prev page-numbers clickable">Prev</span></li>
        <li><span class="page-numbers page-number current">1</span></li>
        <?php for($j=2;$j<=$custom['pages'];$j++): ?>
        <li><span class="page-numbers page-number clickable"><?php echo $j; ?></span></li>
        <?php endfor; ?>
        <li><span class="next page-numbers clickable">Next</span></li>
    </ul>
    <?php endif; ?>
</div>
<?php endif; ?>