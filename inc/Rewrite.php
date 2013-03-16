<?php
/**
 * Front End Accounts
 *
 * @category    WordPress
 * @package     FrontEndAcounts
 * @since       0.1
 * @author      Christopher Davis <http://christopherdavis.me>
 * @copyright   2013 Christopher Davis
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace Chrisguitarguy\FrontEndAccounts;

!defined('ABSPATH') && exit;

/**
 * Create the "pretty url" rewrites for the accounts. Also creates some
 * subactions that get fired when accounts, save, and such.
 *
 * @since   0.1
 */
class Rewrite extends AccountBase
{
    const ACCOUNT_VAR       = 'fe_account';
    const ADDITIONAL_VAR    = 'fe_account_add';

    private $section = null;

    private $additional = null;

    public function _setup()
    {
        add_action('init', array($this, 'addRule'));
        add_filter('query_vars', array($this, 'addVars'));
        add_action('template_redirect', array($this, 'catchAccount'));
    }

    public function addRule()
    {
        add_rewrite_rule(
            '^account/([A-Za-z0-9_-]+)(/[^/]+)?/?$',
            'index.php?' . static::ACCOUNT_VAR . '=$matches[1]&' . static::ADDITIONAL_VAR . '=$matches[2]',
            'top'
        );
    }

    public function addVars($vars)
    {
        $vars[] = static::ACCOUNT_VAR;
        $vars[] = static::ADDITIONAL_VAR;
        return $vars;
    }

    public function catchAccount()
    {
        global $wp_query;

        $this->section = get_query_var(static::ACCOUNT_VAR);

        // are we on an accounts page?
        if (!$this->section) {
            return;
        }

        // make sure we're on out of our whitelisted sections or 404
        if (!in_array($this->section, $this->getRegisteredSections())) {
            $wp_query->set_404();
            return;
        }

        $this->additional = trim(get_query_var(static::ADDITIONAL_VAR), '/');

        $this->dispatchSave($_POST);
        $this->dispatchInit();

        add_action('frontend_accounts_content', array($this, 'contentSubAction'));
        add_filter('template_include', array($this, 'changeTemplate'));
    }

    public function contentSubAction()
    {
        do_action("frontend_accounts_content_{$this->section}", $this->additional);
    }

    public function changeTemplate($tmp)
    {
        $found = locate_template(apply_filters('frontend_accounts_templates', array(
            "account-{$this->section}.php",
            'account.php',
        ), $this->section, $this->additional));

        return $found ?: $tmp;
    }

    private function dispatchSave($postdata)
    {
        if ('post' === strtolower($_SERVER['REQUEST_METHOD'])) {
            do_action(
                "frontend_accounts_save_{$this->section}",
                $postdata,
                $this->additional
            );
        }
    }

    private function dispatchInit()
    {
        do_action("frontend_accounts_init_{$this->section}", $this->additional);
    }

    private function getRegisteredSections()
    {
        return apply_filters('frontend_accounts_registered_sections', array());
    }
}
