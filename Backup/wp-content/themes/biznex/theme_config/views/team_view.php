<?php $shortcode['description'] = 'false' === strtolower($shortcode['description']) ? false : (bool) $shortcode['description']; ?>
<div class="row team<?php if($shortcode['description']) echo ' team-with-description'; ?>">
    <?php foreach($slides as $i => $slide): ?>
    <div class="col-lg-3 col-md-3 col-sm-6">
        <div class="item">
            <img src="<?php echo htmlspecialchars($slide['options']['image']); ?>" class="image" alt="" />
            <div class="name"><?php echo get_the_title($slide['post']->ID); ?></div>
            <div class="title"><?php echo $slide['options']['position']; ?></div>
            <ul class="social">
                <?php if($slide['options']['facebook']): ?>
                <li><a href="<?php echo htmlspecialchars($slide['options']['facebook']); ?>" class="facebook"><i class="icon-facebook"></i></a></li>
                <?php endif; ?>
                <?php if($slide['options']['twitter']): ?>
                <li><a href="<?php echo htmlspecialchars($slide['options']['twitter']); ?>" class="twitter"><i class="icon-twitter"></i></a></li>
                <?php endif; ?>
                <?php if($slide['options']['dribbble']): ?>
                <li><a href="<?php echo htmlspecialchars($slide['options']['dribbble']); ?>" class="dribbble"><i class="icon-dribbble"></i></a></li>
                <?php endif; ?>
                <?php if($slide['options']['linkedin']): ?>
                <li><a href="<?php echo htmlspecialchars($slide['options']['linkedin']); ?>" class="linkedin"><i class="icon-linkedin"></i></a></li>
                <?php endif; ?>
            </ul>
            <?php if($shortcode['description']): ?>
            <div class="team-member" style="display:none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="team-member-cover"><img src="<?php echo htmlspecialchars($slide['options']['image']); ?>" alt="member" /></div>
                    </div>
                    <div class="col-md-6">
                        <div class="team-member-details">
                            <h3><?php echo get_the_title($slide['post']->ID); ?></h3>
                            <h5><?php echo $slide['options']['position']; ?></h5>
                            <?php echo wpautop($slide['options']['description']); ?>
                            <ul class="social">
                                <?php if($slide['options']['facebook']): ?>
                                <li><a href="<?php echo htmlspecialchars($slide['options']['facebook']); ?>" class="facebook"><i class="icon-facebook"></i></a></li>
                                <?php endif; ?>
                                <?php if($slide['options']['twitter']): ?>
                                <li><a href="<?php echo htmlspecialchars($slide['options']['twitter']); ?>" class="twitter"><i class="icon-twitter"></i></a></li>
                                <?php endif; ?>
                                <?php if($slide['options']['dribbble']): ?>
                                <li><a href="<?php echo htmlspecialchars($slide['options']['dribbble']); ?>" class="dribbble"><i class="icon-dribbble"></i></a></li>
                                <?php endif; ?>
                                <?php if($slide['options']['linkedin']): ?>
                                <li><a href="<?php echo htmlspecialchars($slide['options']['linkedin']); ?>" class="linkedin"><i class="icon-linkedin"></i></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>