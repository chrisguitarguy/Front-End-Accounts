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

use Chrisguitarguy\FrontEndAccounts\Form\Validator\ValidatorInterface;
use Chrisguitarguy\FrontEndAccounts\Form\Validator\ValidationException;

/**
 * Implement some common methods for fields.
 *
 * @since   0.1
 */
abstract class FieldBase
{
    protected $args = array();

    private $value = '';

    private $name = '';

    public function __construct($name, array $args=array())
    {
        $this->setName($name);
        $this->args = $this->setDefaults($args, array(
            'validators'    => array(),
            'label'         => '',
            'type'          => '',
            'errmsg'        => false,
            'class'         => 'frontend-accounts-field',
        ));
    }

    /**
     * @see     Chrisguitarguy\FrontEndAccounts\Form\Field\FieldInterface::setValue()
     */
    public function setValue($val)
    {
        $this->value = $val;
        return $this;
    }

    /**
     * @see     Chrisguitarguy\FrontEndAccounts\Form\Field\FieldInterface::getValue()
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @see     Chrisguitarguy\FrontEndAccounts\Form\Field\FieldInterface::setName()
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @see     Chrisguitarguy\FrontEndAccounts\Form\Field\FieldInterface::getName();
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @see     Chrisguitarguy\FrontEndAccounts\Form\Field\FieldInterface::validate()
     */
    public function validate()
    {
        $res = $this->getValue();

        foreach ($this->args['validators'] as $validator) {
            try {
                if ($validator instanceof ValidatorInterface) {
                    if ($this->args['errmsg']) {
                        $validator->setMessage($this->args['errmsg']);
                    }

                    $res = $validator->valid($res);
                } elseif (is_callable($validator)) {
                    // it's up to the callable to throw exceptions...
                    $res = call_user_func($validator, $res);
                }
            } catch (\Exception $e) {
                $this->args['class'] .= ' error';
                throw $e;
            }
        }

        return $res;
    }

    /**
     * @see     Chrisguitarguy\FrontEndAccounts\Form\Field\FieldInterface::validate()
     */
    public function label()
    {
        printf(
            '<label for="%1$s" class="field-type-%2$s">%3$s%4$s%5$s</label>',
            $this->escAttr($this->getName()),
            $this->escAttr($this->getArg('type', 'unknown')),
            apply_filters('frontend_accounts_before_field_label', '', $this->getName()),
            $this->escHtml($this->getArg('label', '')),
            apply_filters('frontend_accounts_after_field_label', '', $this->getName())
        );
    }

    public function offsetGet($key)
    {
        return $this->args[$key];
    }

    public function offsetSet($key, $val)
    {
        $this->args[$key] = $val;
    }

    public function offsetUnset($key)
    {
        unset($this->args[$key]);
    }

    public function offsetExists($key)
    {
        return isset($this->args[$key]);
    }

    /**
     * Make sure an array has required values.
     *
     * @since   0.1
     * @access  protected
     * @return  array
     */
    protected function setDefaults(array $args, array $default)
    {
        return array_replace($default, $args);
    }

    /**
     * Escape the value passed in for use in HTML attributes.
     *
     * @since   0.1
     * @access  protected
     * @param   scalar $attr The attribute to escape
     * @return  scalar The escaped value
     */
    protected function escAttr($attr)
    {
        // return filter_var($attr, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // XXX WP Specific
        return esc_attr($attr);
    }

    /**
     * Escape the value passed for use in HTML
     *
     * @since   0.1
     * @access  protected
     * @param   scalar $html The value to escape
     * @return  scalar The escaped value
     */
    protected function escHtml($html)
    {
        // return filter_var($html, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // XXX WP Specific
        return esc_html($html);
    }

    /**
     * Fetch a value from our args array or return the default.
     *
     * @since   0.1
     * @access  protected
     * @param   string $key The argument key to fetch
     * @param   mixed $default The default to return otherwise
     * @return  mixed
     */
    protected function getArg($key, $default=null)
    {
        return array_key_exists($key, $this->args) ? $this->args[$key] : $default;
    }

    /**
     * Put together additional attributes for used in the input.
     *
     * @since   0.1
     * @access  protected
     * @return  array
     */
    protected function getAdditionalAttributes()
    {
        $attr = array();

        if (!empty($this->args['required'])) {
            $attr['required'] = 'required';
        }

        $supportedAttributes = apply_filters('frontend_accounts_supported_fields_attr', array(
            'class',
            'placeholder',
            'autocomplete',
        ), $this);

        foreach ($supportedAttributes as $attrName) {
            if ($attrVal = $this->getArg($attrName)) {
                $attr[$attrName] = $attrVal;
            }
        }

        return $attr;
    }

    /**
     * Take an associtive array and turn it into attribute="value" pairs.
     *
     * @since   0.1
     * @access  protected
     * @return  string
     */
    protected function arrayToAttr(array $attr)
    {
        $out = '';

        $attr = apply_filters('frontend_accounts_field_attr', $attr, $this);
        foreach ($attr as $key => $val) {
            $out .= sprintf(' %1$s="%2$s"', $key, $this->escAttr($val));
        }

        return $out;
    }
}
