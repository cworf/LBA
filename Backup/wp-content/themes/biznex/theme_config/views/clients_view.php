<div class="align-center">
<?php foreach($slides as $i => $slide): ?>
    <?php if(''!==$slide['options']['url']): ?>
    <a href="<?php echo htmlspecialchars($slide['options']['url']); ?>"><img src="<?php echo htmlspecialchars($slide['options']['image']); ?>" alt="" /></a>
    <?php else: ?>
    <img src="<?php echo htmlspecialchars($slide['options']['image']); ?>" alt="" />
    <?php endif; ?>
<?php endforeach; ?>
</div>