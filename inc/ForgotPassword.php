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

class ForgotPassword extends SectionBase
{
    private $form = null;

    public function save($postdata, $additional)
    {
        do_action('lostpassword_post'); // XXX wp-login.php compat

        $form = $this->getForm();

        $form->bind($postdata);

        list($valid, $errors) = $form->validate();

        if (!empty($errors)) {
            foreach ($errors as $k => $err) {
                $this->addError("vaidation_{$k}", $err);
            }

            return $this->dispatchFailed($postdata, $additional);
        }

        $user = $this->getUser($valid['username']);

        if (!$user) {
            $this->addError('bad_combo', __('Invalid username or email.', FE_ACCOUNTS_TD));

            return $this->dispatchFailed($postdata, $additional);
        }

        do_action('retreive_password', $user->user_login); // XXX wp-login.php compat
        do_action('retrieve_password', $user->user_login); // XXX wp-login.php compat

        $allow = apply_filters('allow_password_reset', true, $user->ID); // XXX wp-login.php compat

        if (!$allow) {
            $this->addError('no_password_reset', __('Password reset is not allowed for this user.', FE_ACCOUNTS_TD));

            return $this->dispatchFailed($postdata, $additional);
        }

        if ($this->doForgotPassword($user)) {
            do_action('frontend_accounts_forgot_password_success', $user, $postdata, $this);
        } else {
            $this->dispatchFailed($postdata, $additional);
        }
    }

    public function getTitle()
    {
        return esc_html__('Forgot Password', FE_ACCOUNTS_TD);
    }

    protected function showContent()
    {
        $this->getForm()->render();
        echo $this->submit(__('Reset Password', FE_ACCOUNTS_TD));
    }

    protected function getName()
    {
        return 'forgot_password';
    }

    protected function getForm()
    {
        if ($this->form) {
            return $this->form;
        }

        $this->form = Form\Form::create();

        $this->form->addField('username', array(
            'label'         => __('Username or Email', FE_ACCOUNTS_TD),
            'required'      => true,
            'validators'    => array(
                new Validator\NotEmpty(__('Please enter a username or email.', FE_ACCOUNTS_TD)),
            ),
        ));

        do_action('frontend_accounts_alter_forgot_password_form', $this->form);

        return $this->form;
    }

    private function getUser($login_or_email)
    {
        if (strpos($login_or_email, '@') !== false) {
            $user = get_user_by('email', $login_or_email);
        } else {
            $user = get_user_by('login', $login_or_email);
        }

        return apply_filters('frontend_accounts_forgot_password_user', $user, $login_or_email, $this);
    }

    private function doForgotPassword($user)
    {
        do {
            $rk = $this->generateResetKey($user);
        } while ($this->keyExists($rk));

        $this->saveKey($rk, $user);

        if ($this->sendResetEmail($user, $rk)) {
            $this->addError('error_sending_email', __('Error sending email. Please contact the site administrator.', FE_ACCOUNTS_TD));

            return false;
        }

        $this->addError('success', __('Password reset sent.', FE_ACCOUNTS_TD));

        return true;
    }

    private function generateResetKey($user, $len=100)
    {
        $bytes = $this->getHasher()->get_random_bytes($len);

        return apply_filters('frontend_accounts_password_reset_key', sha1($bytes), $user);
    }

    private function getHasher()
    {
        global $wp_hasher;

        if (!$wp_hasher) {
            require_once ABSPATH . 'wp-includes/class-phpass.php';
            $wp_hasher = new \PasswordHash(12, true); // XXX args are irrelevant, we're just generating random bytes
        }

        return $wp_hasher;
    }

    private function keyExists($key)
    {
        global $wpdb;

        return $wpdb->get_var($wpdb->prepare(
            "SELECT 1 FROM {$wpdb->users} WHERE user_activation_key = %s",
            $key
        ));
    }

    private function saveKey($key, $user)
    {
        global $wpdb;

        return $wpdb->update($wpdb->users, array(
            'user_activation_key'   => $key,
        ), array(
            'user_login'            => $user->user_login,
        ));
    }

    private function sendResetEmail($user, $key)
    {
        $message = __('Someone requested that the password be reset for the following account:', FE_ACCOUNTS_TD) . "\r\n\r\n";
        $message .= network_home_url('/') . "\r\n\r\n";
        $message .= sprintf(__('Username: %s', FE_ACCOUNTS_TD), $user->user_login) . "\r\n\r\n";
        $message .= __('If this was a mistake, just ignore this email and nothing will happen.', FE_ACCOUNTS_TD) . "\r\n\r\n";
        $message .= __('To reset your password, visit the following address:', FE_ACCOUNTS_TD) . "\r\n\r\n";
        $message .= '<' . static::url('reset_password', $key) . ">\r\n";


        $title = sprintf(__('[%s] Password Reset', FE_ACCOUNTS_TD), $this->getBlogName());

        $title = apply_filters('retrieve_password_title', $title); // XXX wp-login.php compat
        $message = apply_filters('retrieve_password_message', $message, $key); // XXX wp-login.php compat

        return $message && !wp_mail($user->user_email, $title, $message);
    }

    private function getBlogName()
    {
        if (is_multisite()) {
            $blogname = $GLOBALS['current_site']->site_name;
        } else {
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        }

        return apply_filters('frontend_accounts_blogname', $blogname);
    }
}
