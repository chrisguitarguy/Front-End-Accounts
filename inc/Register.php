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

class Register extends SectionBase
{
    private $form = null;

    public function getTitle()
    {
        return esc_html__('Register', FE_ACCOUNTS_TD);
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

            ),
        ))
        ->addField('username', array(
            'type'          => 'text',
            'label'         => __('Username', FE_ACCOUNTS_TD),
            'validators'    => array(

            ),
        ))
        ->addField('redirect_to', array(
            'type'          => 'hidden',
        ));

        do_action('frontend_accounts_alter_register_form', $this->form);

        return $this->form;
    }
}
