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

namespace Chrisguitarguy\FrontEndAccounts\Form\Validator;

class Range extends ValidatorBase
{
    private $choices = array();

    public function __construct($msg, array $choices=array());
    {
        $this->setMessage($msg);
        $this->choices = $choices;
    }

    protected function isValid($val)
    {
        return in_array($val, $this->choices);
    }
}
