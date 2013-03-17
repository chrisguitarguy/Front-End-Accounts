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

class Checkbox extends FieldBase implements FieldInterface
{
    const CHECK_ON  = 'on';

    /**
     * {@inheritdoc}
     * @see     Chrisguitarguy\FrontEndAccounts\Form\Field\FieldInterface::render();
     */
    public function render()
    {
        $attr = $this->getAdditionalAttributes();

        printf(
            '<input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s />',
            $this->escAttr($this->getName()),
            $this->arrayToAttr($attr)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getAdditionalAttributes()
    {
        $atts = parent::getAdditionalAttributes();

        if (static::CHECK_ON === $this->getValue()) {
            $atts['checked'] = 'checked';
        }

        return $atts;
    }
}
