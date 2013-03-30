<?php
/**
 * Front End Accounts
 *
 * @category    WordPress
 * @package     FrontEndAccounts
 * @since       0.1
 * @author      Christopher Davis <http://christopherdavis.me>
 * @copyright   2013 Christopher Davis
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace Chrisguitarguy\FrontEndAccounts;

!defined('ABSPATH') && exit;

abstract class AccountBase
{
    const ACCOUNT_VAR       = 'fe_account';
    const ADDITIONAL_VAR    = 'fe_account_add';

    private static $reg = array();

    public static function instance()
    {
        $cls = get_called_class();

        if (!isset(self::$reg[$cls])) {
            self::$reg[$cls] = new $cls;
        }

        return self::$reg[$cls];
    }

    public static function init()
    {
        add_action('plugins_loaded', array(static::instance(), '_setup'), 10);
    }

    abstract public function _setup();

    protected function getRole()
    {
        return apply_filters('frontend_accounts_role', FE_ACCOUNTS_ROLE);
    }

    protected static function url($area, $additional=null)
    {
        global $wp_rewrite;

        // maybe I should deal with trailingslash/non trailingslashhere?
        if ($wp_rewrite->using_permalinks()) {
            $path = "/account/{$area}";

            if ($additional) {
                $path .= '/' . $additional;
            }

            if ('/' === $wp_rewrite->permalink_structure[count($wp_rewrite->permalink_structure) - 1]) {
                $path = trailingslashit($path);
            }
        } else {
            $q = array(
                static::ACCOUNT_VAR => $area,
            );

            if ($additional) {
                $q[static::ADDITIONAL_VAR] = $additional;
            }

            $path = http_build_query($q);
        }

        return apply_filters('frontend_accounts_url', home_url($path), $area, $additional);
    }
}
