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

class Register extends SectionBase
{
    private $form = null;

    public function getTitle()
    {
        return esc_html__('Register', FE_ACCOUNTS_TD);
    }

    public function initSection($additional)
    {
        $this->redirectMultisite();
    }

    public function save($postdata, $additional)
    {
        $this->redirectMultisite();

        $form = $this->getForm();

        $form->bind($postdata);

        list($valid, $errors) = $form->validate();

        if (!empty($errors)) {
            foreach ($errors as $k => $err) {
                $this->addError("validation_{$k}", $err);
            }

            return $this->dispatchFailed($postdata, $additional);
        }

        $result = $this->registerUser($valid['username'], $valid['email']);

        if (is_wp_error($result)) {
            foreach ($result->get_error_codes() as $code) {
                $this->addError("validation_{$code}", $result->get_error_message($code));
            }

            return $this->dispatchFailed($postdata, $additional);
        }

        wp_safe_redirect(
            apply_filters('frontend_accounts_register_successful_redirect', static::url('login', 'registration_complete')),
            303
        );

        exit;
    }

    public function switchLoginUrl($url, $redirect)
    {
        $url = static::url('login');

        if ($redirect) {
            $url = add_query_arg('redirect_to', urlencode($redirect), $url);
        }

        return $url;
    }

    protected function showContent()
    {
        echo '<p>', esc_html__('A password will be sent to you via email.', FE_ACCOUNTS_TD), '</p>';

        $this->getForm()->render();

        echo '<p>', $this->submit(__('Reset Password', FE_ACCOUNTS_TD)), '</p>';
    }

    protected function getName()
    {
        return 'register';
    }

    private function getForm()
    {
        if ($this->form) {
            return $this->form;
        }

        $this->form = Form\Form::create(array(
            'redirect_to' => isset($_GET['redirect_to']) ? $_GET['redirect_to'] : static::url('login', 'registered'),
        ));

        $this->form->addField('email', array(
            'type'          => 'email',
            'label'         => __('Email', FE_ACCOUNTS_TD),
            'validators'    => array(
                new Validator\Email(__('Please enter a valid email', FE_ACCOUNTS_TD)),
            ),
        ));

        // NOTE: this does not check for invalid character. WP removes them
        // via the `sanitize_user` function.
        $this->form->addField('username', array(
            'type'          => 'text',
            'label'         => __('Username', FE_ACCOUNTS_TD),
            'validators'    => array(
                new Validator\NotEmpty(__('Please enter a username', FE_ACCOUNTS_TD)),
            ),
        ));

        $this->form->addField('redirect_to', array(
            'type'          => 'hidden',
        ));

        do_action('frontend_accounts_alter_register_form', $this->form);

        return $this->form;
    }

    private function redirectMultisite()
    {
        // XXX wp-login.php compat
        if (is_multisite()) {
            wp_redirect(apply_filters(
                'wp_signup_location',
                network_site_url('wp-signup.php')
            ));

            exit;
        }
    }

    /**
     * TODO need to look into wp_insert_user and see how much of the validation
     * here gets repeated.
     *
     * @see wp-login.php - `register_new_user`
     */
    function registerUser($user_login, $user_email)
    {
        $errors = new \WP_Error();

        $sanitized_user_login = sanitize_user($user_login);
        $user_email = apply_filters('user_registration_email', $user_email );

        // Check the username
        if (!$sanitized_user_login) {
            $errors->add('empty_username', __('Please enter a username.', FE_ACCOUNTS_TD));
        } elseif ($sanitized_user_login != $user_login) {
            $errors->add('invalid_username', __('This username is invalid because it uses illegal characters. Please enter a valid username.', FE_ACCOUNTS_TD));
            $sanitized_user_login = '';
        } elseif (username_exists($sanitized_user_login)) {
            $errors->add('username_exists', __('This username is already registered. Please choose another one.', FE_ACCOUNTS_TD));
        }

        // Check the e-mail address
        if (!$user_email) {
            $errors->add('empty_email', __('Please type your e-mail address.', FE_ACCOUNTS_TD));
        } elseif (!is_email($user_email)) {
            $errors->add('invalid_email', __('The email address isn&#8217;t correct.', FE_ACCOUNTS_TD));
            $user_email = '';
        } elseif (email_exists($user_email)) {
            $errors->add('email_exists', __('This email is already registered, please choose another one.', FE_ACCOUNTS_TD));
        }

        // XXX wp-login.php compat
        do_action('register_post', $sanitized_user_login, $user_email, $errors);

        // XXX wp-login.php compat
        $errors = apply_filters('registration_errors', $errors, $sanitized_user_login, $user_email);

        if ($errors->get_error_code()) {
            return $errors;
        }

        $user_pass = wp_generate_password(20, false);

        $user_id = wp_create_user($sanitized_user_login, $user_pass, $user_email);

        if (!$user_id) {
            $errors->add('registerfail', sprintf(
                __('Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a>!', FE_ACCOUNTS_TD),
                get_option('admin_email')
            ));

            return $errors;
        }

        update_user_option($user_id, 'default_password_nag', true, true);

        // switch the login url for the new user notification
        add_filter('login_url', array($this, 'switchLoginUrl'), 10, 2);

        wp_new_user_notification($user_id, $user_pass);

        // back to normal on the login url
        remove_filter('login_url', array($this, 'switchLoginUrl'), 10, 2);

        return $user_id;
    }
}
