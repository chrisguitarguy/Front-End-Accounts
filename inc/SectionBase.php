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
 * Base class for account "sections" -- eg. login, account edit, forgot password
 *
 * @since   0.1
 */
abstract class SectionBase extends AccountBase
{
    private $errors = array();

    public function _setup()
    {
        $s = $this->getName();

        add_action("frontend_accounts_init_{$s}", array($this, 'initSection'));
        add_action("frontend_accounts_save_{$s}", array($this, 'save'), 10, 2);
        add_action("frontend_accounts_content_{$s}", array($this, 'content'));
        add_filter('frontend_accounts_registered_sections', array($this, 'addSection'));
    }

    public function initSection($additional)
    {
        // do nothing by default
    }

    public function save($data, $additional)
    {
        // do nothing by default
    }

    public function content($additional)
    {
        $s = $this->getName();

        do_action("frontend_accounts_before_wrap_{$s}", $additional);
        ?>
        <div class="frontend-accounts-wrap">

            <?php
            do_action("frontend_accounts_before_title_{$s}", $additional);

            if (apply_filters("frontend_accouts_show_title_{$s}", true, $additional)) {
                echo '<h2 class="frontend-accounts-title">',
                    apply_filters("frontend_accounts_title_{$s}", $this->getTitle(), $additional),
                    '<h2>';
            }

            do_action("frontend_accounts_after_title_{$s}", $additional);

            if (apply_filters("frontend_accounts_show_errors_{$s}", true, $additional)) {
                foreach ($this->getErrors() as $key => $errmsg) {
                    echo '<div class="frontend-accounts-error ', esc_attr($key), '">', $errmsg, '</div>';
                }
            }

            do_action("frontend_accounts_before_form_{$s}", $additional);
            ?>

            <form class="frontend-accounts-form" method="post">

                <?php
                do_action("frontend_accounts_before_fields_{$s}", $additional);
                $this->showContent($additional);
                do_action("frontend_accounts_after_fields_{$s}", $additional);
                ?>

            </form>

            <?php do_action("frontend_accounts_after_form_{$s}", $additional); ?>

        </div>
        <?php
        do_action("frontend_accounts_after_wrap_{$s}", $additional);
    }

    public function addSection($sections)
    {
        $sections[] = $this->getName();
        return $sections;
    }

    protected function addError($key, $err)
    {
        $this->errors[$key] = $err;
    }

    protected function removeError($key)
    {
        if (isset($this->errors[$key])) {
            unset($this->errors[$key]);
            return true;
        }

        return false;
    }

    protected function getErrors()
    {
        return apply_filters('frontend_accounts_errors_' . $this->getName(), $this->errors);
    }

    abstract protected function getName();

    abstract protected function showContent();

    abstract protected function getTitle();
}
