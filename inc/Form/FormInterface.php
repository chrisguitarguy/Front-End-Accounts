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

/**
 * Form are a collection of fields and their associated validations hidden
 * behind an interface.
 *
 * @since 0.1
 */
interface FormInterface
{
    /**
     * Render the form (spit out HTML)
     *
     * @since   0.1
     * @access  public
     * @return  void
     */
    public function render();

    /**
     * Render a single field (spit out HTML)
     *
     * @since   0.2
     * @param   string $field
     * @return  void
     */
    public function renderField($field);

    /**
     * Validate the field and return the validated data and and array of errors
     *
     * @since   0.1
     * @access  public
     * @return  array (array(), array()) Data, Errors
     */
    public function validate();

    /**
     * Bind data to the form for validation or display.
     *
     * @since   0.1
     * @access  public
     * @param   array $formdata The data to bind
     * @return  void
     */
    public function bind(array $data);

    /**
     * Add a field to the form.
     *
     * @since   0.1
     * @access  public
     * @param   string $field_id The field ID/name
     * @param   array $args The field's display arguments
     * @return  Field\FieldInterface The field that was added
     */
    public function addField($field_id, array $args=array());

    /**
     * Remove a field from the form.
     *
     * @since   0.1
     * @access  public
     * @param   string $field_id
     * @return  boolean True if the field was present and remove, false otherwise
     */
    public function removeField($field_id);

    /**
     * Get a field from the form.
     *
     * @since   0.2
     * @param   string $field_id
     * @return  FieldInterface|null
     */
    public function getField($field_id);

    /**
     * Get all the fields.
     *
     * @since   0.1
     * @access  public
     * @return  array
     */
    public function getFields();
}
