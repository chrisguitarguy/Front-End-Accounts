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
 * Better multiple select (with checkboxes)
 *
 * @since   0.1
 */
class Multiple extends FieldBase implements FieldInterface
{
    /**
     * {@inheritdoc}
     * @see     Chrisguitarguy\FrontEndAccounts\Form\Field\FieldInterface::render();
     */
    public function render()
    {
        $name = $this->getName();
        $attr = $this->arrrayToAttr($this->getAdditionalAttributes());

        foreach ($this->getArg('choices', array()) as $key => $label) {
            printf(
                '<label for="%1$s[%2$s]"><input type="checkbox" name="%1$s[]" id="%1$s[%2$s]" value="%2$s" %3$s /> %4$s</label>',
                $this->escAttr($name),
                $this->escAttr($key),
                $attr,
                $this->escHtml($label)
            );
        }
    }
}
