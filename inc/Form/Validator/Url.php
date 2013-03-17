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

namespace Chrisguitarguy\FrontEndAccounts\Form\Validator;

class Email extends ValidatorBase
{
    public function isValid($val)
    {
        // XXX no asci domain names will fail
        return filter_var($val, FILTER_VALIDATE_URL);
    }
}
