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

class Textarea extends FieldBase implements FieldInterface
{
    /**
     * {@inheritdoc}
     * @see     Chrisguitarguy\FrontEndAccounts\Form\Field\FieldInterface::render();
     */
    public function render()
    {
        $attr = $this->getAdditionalAttributes();

        printf(
            '<textarea name="%1$s" id="%1$s" %2$s>%3$s</textarea>',
            $this->escAttr($this->getName()),
            $this->arrayToAttr($attr),
            $this->escHtml($this->getValue())
        );
    }

    protected function getAdditionalAttributes()
    {
        $attr = parent::getAdditionalAttributes();

        if ($rows = $this->getArg('rows')) {
            $atts['rows'] = $rows;
        }

        if ($cols = $this->getArg('cols')) {
            $atts['cols'] = $cols;
        }

        return $attr;
    }
}
