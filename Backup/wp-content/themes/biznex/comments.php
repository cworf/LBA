<?php

if ( post_password_required() )
    return;
?>

<?php if ( have_comments() || comments_open() ) : ?>

<div class="comments-area">

    <?php

    $user = wp_get_current_user();
    $user_identity = $user->exists() ? $user->display_name : '';

    comment_form(array(
        'fields'               => array(
                                      'author' => '<div class="row"><div class="col-md-6"><input class="comments-line" name="author" type="text" placeholder="Name"></div>',
                                      'email'  => '<div class="col-md-6"><input class="comments-line" name="email" type="text" placeholder="E-mail"></div></div>',
                                      'url'    => '<input class="comments-line" name="url" type="text" placeholder="Website">',
                                  ),
        'comment_field'        => '<textarea class="comments-area" name="comment" placeholder="Comment"></textarea',
        'title_reply'          => _x('Leave us a reply', 'comments', 'biznex'),
        'title_reply_to'       => _x('Leave a reply to %s', 'comments', 'biznex'),
        'comment_notes_before' => '',
        'comment_notes_after'  => '',
        'logged_in_as'         => '',
        'label_submit'         => _x('Write', 'comments submit', 'biznex'),
    ));

    ?>

    <div class="comment-all-box">
        <h3><?php _ex('Comments','comments','biznex'); ?> (<?php echo get_comments_number(); ?>)</h3>
        <ul class="commentlist">
            <?php wp_list_comments(array('callback' => 'biznex_comment', 'end-callback' => 'biznex_comment_end')); ?>
        </ul>
    </div>

    <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
    <nav class="navigation comment-navigation" role="navigation">
        <h1 class="screen-reader-text section-heading"><?php _ex( 'Comment navigation', 'comments', 'biznex' ); ?></h1>
        <div class="nav-previous"><?php previous_comments_link( _x( '&larr; Older Comments', 'comments', 'biznex' ) ); ?></div>
        <div class="nav-next"><?php next_comments_link( _x( 'Newer Comments &rarr;', 'comments', 'biznex' ) ); ?></div>
    </nav>
    <?php endif; ?>

    <?php if ( ! comments_open() && get_comments_number() ) : ?>
    <p class="no-comments"><?php _ex( 'Comments are closed.', 'comments' , 'biznex' ); ?></p>
    <?php endif; ?>

</div>

<?php endif; ?>