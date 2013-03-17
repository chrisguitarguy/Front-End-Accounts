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
                foreach ($this->getErrors() as $key => $errmsg) {
                    echo '<div class="frontend-accounts-error ', esc_attr($key), '">', $errmsg, '</div>';
                }
            }

            $this->act('frontend_accounts_before_form', $s);
            ?>

            <form class="frontend-accounts-form <?php echo esc_attr($s); ?>" method="post">

                <?php
                $this->act('frontend_accounts_before_fields', $s);
                $this->showContent($additional);
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

    abstract public function getTitle();

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

    protected function submit($msg)
    {
        return sprintf('<button type="submit" class="frontend-accounts-submit">%1$s</button>', esc_html($msg));
    }

    abstract protected function getName();

    abstract protected function showContent();

    private function act($act, $section)
    {
        do_action($act, $section, $this);
        do_action("{$act}_{$section}", $this);
    }
}
