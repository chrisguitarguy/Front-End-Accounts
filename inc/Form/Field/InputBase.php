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

namespace Chrisguitarguy\FrontEndAccounts\Form\Field;

!defined('ABSPATH') && exit;

/**
 * Base class for anything that starts with '<input'
 *
 * @since   0.1
 */
abstract class InputBase extends FieldBase implements FieldInterface
{
    /**
     * {@inheritdoc}
     * @see     Chrisguitarguy\FrontEndAccounts\Form\Field\FieldInterface::render();
     */
    public function render()
    {
        $t = $this->getType();

        $attr = $this->getAdditionalAttributes();

        printf(
            '<input type="%1%s" id="%2$s" name="%2$s" value="%3$s" %4$s />',
            $this->escAttr($t),
            $this->escAttr($this->getName()),
            'password' === $t ? '' : $this->escAttr($this->getValue()),
            $this->arrayToAttr($attr)
        );
    }

    /**
     * Get the "type" of the field (eg. text, password, etc)
     *
     * @since   0.1
     * @access  protected
     * @return  string
     */
    abstract protected function getType();
}
