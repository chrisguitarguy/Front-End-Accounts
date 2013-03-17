<?php
/**
 * Template for displaying account pages.
 *
 */

!defined('ABSPATH') && exit;

get_header('account');
?>
<div id="container">
    <div id="content" role="main">

        <?php the_account(); ?>

    </div><!-- #content -->
</div><!-- #container -->
<?php
get_sidebar('account');
get_footer('account');
