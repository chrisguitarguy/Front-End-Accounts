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

class TextInput extends InputBase
{
    /**
     * {@inheritdoc}
     */
    protected function getType()
    {
        return 'text';
    }
}
