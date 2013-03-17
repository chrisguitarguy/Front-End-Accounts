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

class ForgotPassword extends SectionBase
{
    private $form = null;

    protected function showContent()
    {
        $this->getForm()->render();
        echo '<p>', $this->submit(__('Reset Password', FE_ACCOUNTS_TD)), '</p>';
    }

    protected function getTitle()
    {
        return esc_html__('Forgot Password', FE_ACCOUNTS_TD);
    }

    protected function getName()
    {
        return 'forgot_password';
    }

    private function getForm()
    {
        if ($this->form) {
            return $this->form;
        }

        $this->form = Form\Form::create();

        $this->form->addField('username', array(
            'label'         => __('Username or Email', FE_ACCOUNTS_TD),
            'validators'    => array(

            ),
        ));

        do_action('frontend_accounts_alter_forgot_password_form', $this->form);

        return $this->form;
    }
}
