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

class ResetPassword extends SectionBase
{
    private $form = null;

    protected function showContent()
    {
        $this->getForm()->render();
        echo '<p>', $this->submit(__('Reset Password', FE_ACCOUNTS_TD)), '</p>';
    }

    protected function getTitle()
    {
        return esc_html__('Reset Password', FE_ACCOUNTS_TD);
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
            'type'          => 'password',
            'label'         => __('Password', FE_ACCOUNTS_TD),
            'validators'    => array(

            ),
        ))
        ->addField('password_again', array(
            'type'          => 'password',
            'label'         => __('Password Again', FE_ACCOUNTS_TD),
            'validators'    => array(

            ),
        ));

        do_action('frontend_accounts_alter_reset_password_form', $this->form);

        return $this->form;
    }
}
