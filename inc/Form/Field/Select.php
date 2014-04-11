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

namespace Chrisguitarguy\FrontEndAccounts\Form\Field;

class Select extends FieldBase implements FieldInterface
{
    /**
     * {@inheritdoc}
     * @see     Chrisguitarguy\FrontEndAccounts\Form\Field\FieldInterface::render();
     */
    public function render()
    {
        printf(
            '<select id="%1$s" name="%1$s" %2$s>',
            $this->escAttr($this->getName()),
            $this->arrayToAttr($this->getAdditionalAttributes())
        );

        if ($emp = $this->getArg('empty_first')) {
            printf('<option value="">%s</option>', $this->escAttr($emp));
        }

        foreach ($this->getArg('choices', array()) as $val => $label) {
            printf(
                '<option value="%1$s" %2$s>%3$s</option>',
                $this->escAttr($val),
                selected($this->getValue(), $val, false), // XXX WordPress specific
                $this->escHtml($label)
            );
        }

        echo '</select>';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAdditionalAttributes()
    {
        $atts = parent::getAdditionalAttributes();

        if (!empty($this->args['multi'])) {
            $atts['multiple'] = 'multiple';
        }

        return $atts;
    }
}
