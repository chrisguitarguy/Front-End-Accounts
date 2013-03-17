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

/**
 * When we get a field we don't understand.
 *
 * @since   0.1
 */
class DummyField extends FieldBase implements FieldInterface
{
    public function render()
    {
        echo '<p>Invalid Field</p>';
    }

    public function validate()
    {
        return false;
    }
}
