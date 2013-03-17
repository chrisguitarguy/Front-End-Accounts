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

/**
 * Validator object's check values and make sure they're okay. Validators can be
 * safely re-used.
 *
 * @since   0.1
 */
interface ValidatorInterface
{
    /**
     * Set the error message of the validator.
     *
     * @since   0.1
     * @access  public
     * @param   string $errmsg
     * return   void
     */
    public function setMessage($errmsg);

    /**
     * Get the error message of the validator
     *
     * @since   0.1
     * @access  public
     * @return  string
     */
    public function getMessage();

    /**
     * Checkout a value.
     *
     * @since   0.1
     * @access  public
     * @param   mixed $value
     * @throws  ValidationException on failure
     * @return  mixed the passed in $value if it was okay.
     */
    public function valid($value);
}
