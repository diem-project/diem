<?php

/**
 * The Doctrine builder to generate xfDocument's from Doctrine objects.
 *
 * @package dm
 * @subpackage Builder
 * @author Thibault Duplessis
 */
class dmSearchDoctrineBuilder implements xfBuilder
{
  /**
   * The fields to build on.
   *
   * @var array
   */
  private $fields = array();

  /**
   * Constructor to store fields
   *
   * @param array $fields The fields to index
   */
  public function __construct(array $fields = array())
  {
    $this->addFields($fields);
  }

  /**
   * @see xfBuilder
   */
  public function build($input, xfDocument $doc)
  {
    if (!($input instanceof myDoctrineRecord))
    {
      throw new xfException("Input must be a myDoctrineRecord instance.");
    }

    foreach ($this->fields as $fieldName => $field)
    {
      $doc->addField(new xfFieldValue($field, $input->get($fieldName)));
    }

    $doc->addField(new xfFieldValue(new xfField('_model', xfField::STORED), get_class($input)));

    return $doc;
  }

  /**
   * Adds a field to be indexed.
   *
   * If no getter is passed, it will derivied from the field name.
   *
   * @param xfField $field The field
   * @param string $getter The getter for this field (optional)
   */
  public function addField(xfField $field)
  {
    $this->fields[$field->getName()] = $field;
  }

  /**
   * Adds multiple fields.
   *
   * The index is the getter. If the index is an integer, then the getter is derivied.
   *
   * @param array $fields
   */
  public function addFields(array $fields)
  {
    foreach ($fields as $field)
    {
      $this->addField($field);
    }
  }
}
