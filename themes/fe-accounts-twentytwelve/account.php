<?php
/**
 * Template for displaying account pages.
 *
 */

!defined('ABSPATH') && exit;

get_header('account');
?>

<div id="primary" class="site-content">
    <div id="content" role="main">

        <?php the_account(); ?>

    </div><!-- #content -->
</div><!-- #primary -->

<?php
get_sidebar('account');
get_footer('account');
