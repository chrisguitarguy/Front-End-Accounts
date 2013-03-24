<?php
/**
 * Front End Accounts
 *
 * Some utility functions for our "themes".
 *
 * @category    WordPress
 * @package     FrontEndAccounts
 * @since       0.1
 * @author      Christopher Davis <http://christopherdavis.me>
 * @copyright   2013 Christopher Davis
 * @license     http://opensource.org/licenses/MIT MIT
 */

!defined('ABSPATH') && exit;

function frontend_accounts_theme_enqueue()
{
    wp_enqueue_style(
        'fe-accounts-css',
        trailingslashit(get_stylesheet_directory_uri()) . 'style.css',
        array(),
        FE_ACCOUNTS_VER,
        'screen'
    );
}

/**
 * Since we can't really do `import` CSS stuff in this theme directory,
 * let's just switch our child them stylesheet with our parent themes'
 * stylesheet.
 *
 * @since   0.1
 * @param   string $uri
 * @uses    get_template_directory_uri
 * @uses    trailingslashit
 * @return  string
 */
function frontend_accounts_replace_stylesheet($uri)
{
    return trailingslashit(get_template_directory_uri()) . 'style.css';
}
