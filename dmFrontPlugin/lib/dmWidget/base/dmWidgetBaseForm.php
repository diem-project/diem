<?php

abstract class dmWidgetBaseForm extends dmForm
{
	protected
	  $widget;

	protected
	  $firstDefaults = array();

  /**
   * Constructor.
   *
   * @param dmWidget $widget    A widget
   * @param array  $options     An array of options
   * @param string $CSRFSecret  A CSRF secret (false to disable CSRF protection, null to use the global CSRF secret)
   */
  public function __construct($widget = array(), $options = array(), $CSRFSecret = null)
  {
  	if (!$widget instanceof DmWidget)
  	{
  		throw new dmException(sprintf('%s must be initialized with a DmWidget, not a %s', get_class($this), gettype($widget)));
  	}

  	$this->dmWidget = $widget;

  	parent::__construct($widget->values, $options, $CSRFSecret);
  }

  public function configure()
  {
    parent::configure();

    $this->widgetSchema['cssClass']     = new sfWidgetFormInputText();
    $this->validatorSchema['cssClass']  = new sfValidatorString(array('required' => false));

    $this->setDefault('cssClass', $this->dmWidget->cssClass);
  }

	/*
	 * Overload this method to alter form values
	 * when form has been validated
	 */
  public function getWidgetValues()
  {
    $values = $this->getValues();

    unset($values['cssClass']);

    return $values;
  }

  public function render($attributes = array())
  {
    $attributes = dmString::toArray($attributes, true);

    return
    $this->open($attributes).
    '<ul class="dm_form_elements">'.$this->renderContent($attributes).'</ul>'.
    $this->renderActions().
    $this->close();
  }

  protected function renderContent($attributes)
  {
  	return $this->getFormFieldSchema()->render($attributes);
  }

  protected function renderActions()
  {
  	return sprintf(
      '<div class="actions">
        <div class="actions_part clearfix">
          %s%s
        </div>
        <div class="actions_part clearfix">
          %s%s
        </div>
      </div>',
  	  sprintf('<a class="dm cancel close_dialog button fleft">%s</a>', dm::getI18n()->__('Cancel')),
  	  sprintf('<input type="submit" class="submit try blue fright" name="try" value="%s" />', dm::getI18n()->__('Try')),
      sprintf('<a class="dm delete button red fleft" title="%s">%s</a>', dm::getI18n()->__('Delete this widget'), dm::getI18n()->__('Delete')),
      sprintf('<input type="submit" class="submit and_save green fright" name="and_save" value="%s" />', dm::getI18n()->__('Save and close'))
    );
  }

  /*
   * Try to guess default values
   * from last updated widget with same module.action
   * @return array default values
   */
  protected function getDefaultsFromLastUpdated(array $fields = array())
  {
  	if ($this->dmWidget->value)
    {
      return array_merge($this->dmWidget->values, array('cssClass' => $this->dmWidget->cssClass));
    }

    $lastWidgetValue = dmDb::query('DmWidget w')
    ->where('w.module = ? AND w.action = ?', array($this->dmWidget->module, $this->dmWidget->action))
    ->orderBy('w.updated_at desc')
    ->limit(1)
    ->select('w.value')
    ->fetchValue();

    $defaults = $this->getFirstDefaults();

    if (!$lastWidgetValue)
    {
      return $defaults;
    }

    $values = (array) json_decode($lastWidgetValue, true);

    foreach($fields as $field)
    {
    	$defaults[$field] = dmArray::get($values, $field, dmArray::get($defaults, $field));
    }
    
    return $defaults;
  }

  protected function getFirstDefaults()
  {
    return $this->firstDefaults;
  }

  /*
   * Static methods
   */
}