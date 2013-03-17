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

class NumberInput extends InputBase
{
    /**
     * {@inheritdoc}
     */
    protected function getType()
    {
        return 'number';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAdditionalAttributes()
    {
        $atts = parent::getAdditionalAttributes();

        if ($min = $this->getArg('min')) {
            $atts['min'] = $min;
        }

        if ($max = $this->getArg('max')) {
            $atts['max'] = $max;
        }

        if ($step = $this->getArg('step')) {
            $atts['step'] = $step;
        }

        return $atts;
    }
}
