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

        $this->act('frontend_accounts_before_wrap', $s);
        ?>
        <div class="frontend-accounts-wrap entry entry-content">

            <?php
            $this->act('frontend_accounts_before_title', $s);

            if (apply_filters("frontend_accouts_show_title_{$s}", true, $additional)) {
                echo '<h2 class="frontend-accounts-title">',
                    apply_filters("frontend_accounts_title_{$s}", $this->getTitle(), $additional),
                    '</h2>';
            }

            $this->act('frontend_accounts_after_title', $s);

            if (apply_filters("frontend_accounts_show_errors_{$s}", true, $additional)) {
                echo '<div class="frontend-accounts-errors">';
                foreach ($this->getErrors() as $key => $errmsg) {
                    echo '<div class="frontend-accounts-error ', esc_attr($key), '">', $errmsg, '</div>';
                }
                echo '</div>';
            }

            $this->act('frontend_accounts_before_form', $s);
            ?>

            <form class="frontend-accounts-form <?php echo esc_attr($s); ?>" method="post">

                <?php
                $this->act('frontend_accounts_before_fields', $s);
                if (has_action("frontend_account_renderform_{$s}")) {
                    do_action("frontend_account_renderform_{$s}", $this->getForm(), $this);
                } else {
                    $this->showContent($additional);
                }
                $this->act('frontend_accounts_after_fields', $s);
                ?>

            </form>

            <?php $this->act('frontend_accounts_after_form', $s); ?>

        </div>
        <?php
        $this->act('frontend_accounts_after_wrap', $s);
    }

    public function addSection($sections)
    {
        $sections[] = $this->getName();
        return $sections;
    }

    public function addError($key, $err)
    {
        $this->errors[$key] = apply_filters(
            'frontend_accounts_' . $this->getName() . '_error_message',
            $err,
            $key,
            $this
        );
    }

    public function removeError($key)
    {
        if (isset($this->errors[$key])) {
            unset($this->errors[$key]);
            return true;
        }

        return false;
    }

    public function getErrors()
    {
        return apply_filters('frontend_accounts_errors_' . $this->getName(), $this->errors);
    }

    abstract public function getTitle();

    protected function submit($msg)
    {
        $section = $this->getName();
        return sprintf(
            '<%1$s class="%2$s"><button type="submit" class="frontend-accounts-submit %3$s">%4$s</button></%1$s>',
            tag_escape(apply_filters('frontend_accounts_submit_wraptag', 'p', $section) ?: 'p'),
            esc_attr(apply_filters('frontend_accounts_submit_wrapclass', 'frontend-accounts-submit-wrap', $section)),
            esc_attr(apply_filters('frontend_accounts_submit_buttonclass', '', $section)),
            esc_html($msg)
        );
    }

    protected function dispatchFailed($postdata, $additional)
    {
        do_action('frontend_accounts_' . $this->getName() . '_failed', $postdata, $additional, $this);
    }

    /**
     * Check whether or not users can set their own passwords on registration.
     *
     * @since   0.2
     * @return  boolean
     */
    protected function allowUserPasswords()
    {
        return apply_filters('frontend_accounts_allow_user_password', false);
    }

    abstract protected function getName();

    abstract protected function showContent();

    abstract protected function getForm();

    private function act($act, $section)
    {
        do_action($act, $section, $this);
        do_action("{$act}_{$section}", $this);
    }
}
