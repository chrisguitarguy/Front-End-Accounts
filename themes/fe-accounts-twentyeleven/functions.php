<?php
!defined('ABSPATH') && exit;

require_once dirname(__DIR__) . '/common.php';

add_action('wp_enqueue_scripts', 'frontend_accounts_theme_enqueue', 100);
add_action('stylesheet_uri', 'frontend_accounts_replace_stylesheet');

add_action('frontend_accounts_before_wrap', 'fe_accounts_twentyeleven_before', 10, 2);
function fe_accounts_twentyeleven_before($section, $acct)
{
    add_filter("frontend_accouts_show_title_{$section}", '__return_false');
    ?>
    <article class="singular page">
        <div class="hentry">
            <header class="entry-header">
                <h1 class="entry-title"><?php echo esc_html($acct->getTitle()); ?></h1>
            </header>
    <?php
}

add_action('frontend_accounts_after_wrap', 'fe_accounts_twentyeleven_after');
function fe_accounts_twentyeleven_after()
{
    echo '</div></article>';
}
