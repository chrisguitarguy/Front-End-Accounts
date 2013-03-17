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

class Login extends SectionBase
{
    private $form = null;

    protected function showContent()
    {
        $this->getForm()->render();
        echo '<p>', $this->submit(__('Login', FE_ACCOUNTS_TD)), '</p>';
    }

    protected function getTitle()
    {
        return esc_html__('Login', FE_ACCOUNTS_TD);
    }

    protected function getName()
    {
        return 'login';
    }

    private function getForm()
    {
        if ($this->form) {
            return $this->form;
        }

        $this->form = Form\Form::create(array(
            'redirect_to' => isset($_GET['redirect_to']) ? $_GET['redirect_to'] : static::url('edit'),
        ));

        $this->form->addField('username', array(
            'label'         => __('Username', FE_ACCOUNTS_TD),
            'validators'    => array(

            ),
        ))
        ->addField('password', array(
            'type'          => 'password',
            'label'         => __('Password', FE_ACCOUNTS_TD),
            'validators'    => array(

            ),
        ))
        ->addField('redirect_to', array(
            'type'          => 'hidden',
        ));

        do_action('frontend_accounts_alter_login_form', $this->form);

        return $this->form;
    }
}
