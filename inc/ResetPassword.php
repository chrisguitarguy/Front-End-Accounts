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

use Chrisguitarguy\FrontEndAccounts\Form\Validator;

class ResetPassword extends SectionBase
{
    private $form = null;

    private $user = null;

    public function initSection($reset_key)
    {
        if (!$this->getUser($reset_key)) {
            return $this->abort();
        }
    }

    public function save($postdata, $reset_key)
    {
        $user = $this->getUser($reset_key);

        if (!$user) {
            return $this->abort();
        }

        $form = $this->getForm();

        $form->bind($postdata);

        list($valid, $errors) = $form->validate();

        if (!empty($errors)) {
            foreach ($error as $k => $err) {
                $this->addError("validiation_{$k}", apply_filters(
                    'frontend_accounts_reset_password_error_message',
                    $err,
                    $k
                ));
            }

            return $this->dispatchFailed($postdata, $reset_key);
        }

        do_action( 'validate_password_reset', new \WP_Error(), $user); // XXX wp-login.php compat, not 100% compat??

        if ($valid['password'] != $valid['password_again']) {
            $this->addError('password_match', apply_filters(
                'frontend_accounts_reset_password_password_match_error_message',
                __('Password do not match.', FE_ACCOUNTS_TD)
            ));

            return $this->dispatchFailed($postdata, $reset_key);
        }

        // not really a way to check of this actually worked...
        $this->setPassword($user, $valid['password']);

        $this->addError('success', apply_filters(
            'frontend_accounts_reset_password_success_message',
            __('Your password has been reset.', FE_ACCOUNTS_TD)
        ));

        do_action('frontend_accounts_reset_password_success', $postdata, $reset_key, $user, $this);
    }

    public function getTitle()
    {
        return esc_html__('Reset Password', FE_ACCOUNTS_TD);
    }

    public function removeTemplate()
    {
        remove_filter('template_include', array(Rewrite::instance(), 'changeTemplate'), 10);
    }

    protected function showContent()
    {
        $this->getForm()->render();
        echo '<p>', $this->submit(__('Reset Password', FE_ACCOUNTS_TD)), '</p>';
    }

    protected function getName()
    {
        return 'reset_password';
    }

    private function getForm()
    {
        if ($this->form) {
            return $this->form;
        }

        $this->form = Form\Form::create();

        $this->form->addField('password', array(
            'label'         => __('Password', FE_ACCOUNTS_TD),
            'type'          => 'password',
            'required'      => true,
            'validators'    => array(
                new Validator\NotEmpty(__('Please enter a new password.', FE_ACCOUNTS_TD)),
            ),
        ))
        ->addField('password_again', array(
            'type'          => 'password',
            'label'         => __('Password Again', FE_ACCOUNTS_TD),
            'validators'    => array(
                new Validator\NotEmpty(__('Please enter your new password again.', FE_ACCOUNTS_TD)),
            ),
        ));

        do_action('frontend_accounts_alter_reset_password_form', $this->form);

        return $this->form;
    }

    private function getUser($reset_key)
    {
        global $wpdb;

        if (!is_null($this->user)) {
            return $this->user;
        }

        if ($reset_key) {
            $this->user = $wpdb->get_row($wpdb->prepare(
                "SELECT * from {$wpdb->users} WHERE user_activation_key = %s LIMIT 1", // XXX select * is probably terrible...
                $reset_key
            ));
        } else {
            $this->user = false;
        }

        return $this->user;
    }

    private function abort()
    {
        global $wp_query;
        add_filter('template_redirect', array($this, 'removeTemplate'), 11);
        return $wp_query->set_404();
    }

    private function dispatchFailed($postdata, $reset_key)
    {
        do_action('frontend_accounts_reset_password_failed', $postdata, $reset_key, $this);
    }

    private function setPassword($user, $new_pass)
    {
        do_action('password_reset', $user, $new_pass); // XXX wp-login.php compat

        wp_set_password($new_pass, $user->ID);

        wp_password_change_notification($user);
    }
}
