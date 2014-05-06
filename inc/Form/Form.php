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

namespace Chrisguitarguy\FrontEndAccounts\Form;

class Form implements FormInterface
{
    private $fields = array();

    private $initial = array();

    private $bound = array();

    public static function create(array $initial=array())
    {
        return new self($initial);
    }

    public function __construct(array $initial=array())
    {
        $this->initial = $initial;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        foreach ($this->getFields() as $field) {
            $this->renderRow($field);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function renderField($field)
    {
        $fields = $this->getFields();
        if (!isset($fields[$field])) {
            throw new \InvalidArgumentException(sprintf('Field "%s" does not exist', $field));
        }

        $this->renderRow($this->fields[$field]);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $values = $errors = array();

        foreach ($this->getFields() as $id => $field) {
            // replace value with bound data if we have it.
            if (isset($this->bound[$id])) {
                $field->setValue($this->bound[$id]);
            }

            try {
                $values[$id] = $field->validate();
            } catch (Validator\ValidationException $e) {
                $errors[$id] = $e->getMessage();
            }
        }

        return array($values, $errors);
    }

    /**
     * {@inheritdoc}
     */
    public function bind(array $data)
    {
        $this->bound = $data;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @todo    Maybe lazy load classes here?
     */
    public function addField($field_id, array $args=array())
    {
        $this->fields[$field_id] = $this->getFieldObject($field_id, $args);

        if (isset($this->initial[$field_id])) {
            $this->fields[$field_id]->setValue($this->initial[$field_id]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeField($field_id)
    {
        if (isset($this->fields[$field_id])) {
            unset($this->fields[$field_id]);
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getField($field_id)
    {
        if (isset($this->fields[$field_id])) {
            return $this->fields[$field_id];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->fields;
    }

    protected function renderRow(Field\FieldInterface $f)
    {
        if ($f instanceof Field\HiddenInput) {
            return $f->render();
        }

        $tag = apply_filters('frontend_accounts_field_wraptag', 'p', $f);
        $cls = apply_filters('frontend_accounts_field_wrapclass', sprintf('fe-accounts-field-wrap %s', $f->getName()), $f);

        printf('<%s class="%s">', tag_escape($tag), esc_attr($cls));
        do_action('frontend_accounts_field_before_label', $f);
        $f->label();
        do_action('frontend_accounts_field_before_input', $f);
        $f->render();
        do_action('frontend_accounts_field_after_input', $f);
        echo "</{$tag}>";
    }

    protected function getFieldObject($name, array $args)
    {
        $type = isset($args['type']) ? $args['type'] : 'text';

        $cls = 'DummyField';

        switch ($type) {
        case 'text':
            $cls = 'TextInput';
            break;
        case 'password':
            $cls = 'PasswordInput';
            break;
        case 'hidden':
            $cls = 'HiddenInput';
            break;
        case 'color':
            $cls = 'ColorInput';
            break;
        case 'date':
            $cls = 'DateInput';
            break;
        case 'datetime':
            $cls = 'DateTimeInput';
            break;
        case 'datetime-local':
            $cls = 'DateTimeLocalInput';
            break;
        case 'email':
            $cls = 'EmailInput';
            break;
        case 'month':
            $cls = 'MonthInput';
            break;
        case 'number':
            $cls = 'NumberInput';
            break;
        case 'search':
            $cls = 'SearchInput';
            break;
        case 'time':
            $cls = 'TimeInput';
            break;
        case 'url':
            $cls = 'UrlInput';
            break;
        case 'week':
            $cls = 'WeekInput';
            break;
        case 'multiple':
            $cls = 'Multiple';
            break;
        case 'radio':
            $cls = 'Radio';
            break;
        case 'select':
            $cls = 'Select';
            break;
        case 'textarea':
            $cls = 'Textarea';
            break;
        case 'checkbox':
            $cls = 'Checkbox';
            break;
        }

        $cls = "Chrisguitarguy\\FrontEndAccounts\\Form\\Field\\{$cls}";

        return new $cls($name, $args);
    }
}
