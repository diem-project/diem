<?php

class sfWidgetFormDmDbSelectDoubleList extends sfWidgetFormDmSelectDoubleList
{
  /**
   * @see sfWidget
   */
  public function __construct($options = array(), $attributes = array())
  {
    $options['choices'] = new sfCallable(array($this, 'getChoices'));

    parent::__construct($options, $attributes);
  }

  /**
   * Constructor.
   *
   * Available options:
   *
   *  * model:       The model class (required)
   *  * method:      The method to use to display object values (__toString by default)
   *  * key_method:  The method to use to display the object keys (getPrimaryKey by default)
   *  * order_by:    An array composed of two fields:
   *                   * The column to order by the results (must be in the PhpName format)
   *                   * asc or desc
   *  * connection:  The Propel connection to use (null by default)
   *  * multiple:    true if the select tag must allow multiple selections
   *  * find_method: The finder method to use to fetch objects
   *
   * @see sfWidgetFormSelect
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('model');
    $this->addOption('method', '__toString');
    $this->addOption('key_method', 'getPrimaryKey');
    $this->addOption('order_by', null);
    $this->addOption('connection', null);
    $this->addOption('find_method', 'find');

    parent::configure($options, $attributes);
  }

  /**
   * Returns the choices associated to the model.
   *
   * @return array An array of choices
   */
  public function getChoices()
  {
    $choices = array();

    $peerClass = constant($this->getOption('model').'::PEER');

    $finder = dm::db($this->getOption('model'));

    if ($order = $this->getOption('order_by'))
    {
      $finder->orderBy(
        call_user_func(array($peerClass, 'translateFieldName'), $order[0], BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME),
        strtolower($order[1])
      );
    }

    if($connection = $this->getOption('connection'))
    {
      $finder->setConnection($connection);
    }

    $findMethod = $this->getOption('find_method');

    $objects = $finder->$findMethod($this->getOption('connection'));

    $methodKey = $this->getOption('key_method');
    if (!method_exists($this->getOption('model'), $methodKey))
    {
      throw new RuntimeException(sprintf('Class "%s" must implement a "%s" method to be rendered in a "%s" widget', $this->getOption('model'), $methodKey, __CLASS__));
    }

    $methodValue = $this->getOption('method');
    if (!method_exists($this->getOption('model'), $methodValue))
    {
      throw new RuntimeException(sprintf('Class "%s" must implement a "%s" method to be rendered in a "%s" widget', $this->getOption('model'), $methodValue, __CLASS__));
    }

    foreach ($objects as $object)
    {
      $choices[$object->$methodKey()] = $object->$methodValue();
    }

    return $choices;
  }
}