<?php
/**
 * Front End Accounts
 *
 * @category    WordPress
 * @package     FrontEndAccounts
 * @since       1.0
 * @author      Christopher Davis <http://christopherdavis.me>
 * @copyright   2013 Christopher Davis
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace Chrisguitarguy\FrontEndAccounts;

/**
 * Handles some SEO stuff for front end account screens. Mainly making sure that
 * plugins like WordPress SEO and All in One SEO don't do anything wonky to the
 * meta information because of the custom rewrite.
 *
 * @since   1.0
 */
class Seo extends AccountBase
{
    private $section = null;

    public function _setup()
    {
        add_action('frontend_accounts_init', array($this, 'initSeo'));
    }

    public function initSeo($section)
    {
        global $wpseo_front, $aiosp;

        if (apply_filters('frontend_accounts_disable_wpseo', isset($wpseo_front))) {
            remove_action('wp_head', array($wpseo_front, 'head'), 1);
        }
        if (apply_filters('frontend_accounts_disable_aiosp', isset($aiosp))) {
            remove_action('wp_head', array($aiosp, 'wp_head'));
        }

        $this->section = $section;

        add_action('wp_head', array($this, 'robotsMeta'), 1);
        add_action('wp_title', array($this, 'accountTitle'), 100, 3);
    }

    public function robotsMeta()
    {
        printf(
            '<meta name="robots" content="%s" />',
            esc_attr(apply_filters('frontend_accounts_meta_robots', 'noindex,follow', $this->section))
        );
        echo "\n";
    }

    public function accountTitle($title, $sep='', $sepLocation='right')
    {
        $at = $this->getSectionTitle();
        if (apply_filters('frontend_accounts_disable_page_title', empty($at), $this->section)) {
            return $title;
        }

        return apply_filters('frontend_accounts_page_title', sprintf(
            '%s %s %s',
            esc_html(apply_filters("frontend_accounts_{$this->section}_page_title", $at, $this->section)),
            esc_html($sep ?: '|'),
            get_bloginfo('name')
        ), $this->section, $sep, $sepLocation);
    }

    private function getSectionTitle()
    {
        switch ($this->section) {
            case 'login':
                return __('Login', FE_ACCOUNTS_TD);
            case 'forgot_password':
                return __('Forgot Password', FE_ACCOUNTS_TD);
            case 'register':
                return __('Register', FE_ACCOUNTS_TD);
            case 'reset_password':
                return __('Reset Password', FE_ACCOUNTS_TD);
            case 'edit':
                return __('Edit Account', FE_ACCOUNTS_TD);
            default:
                return null;
        }
    }
}
