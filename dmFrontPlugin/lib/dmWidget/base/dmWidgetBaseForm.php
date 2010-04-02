<?php

abstract class dmWidgetBaseForm extends dmForm
{
  protected
  $dmWidget,
  $stylesheets = array(),
  $javascripts = array();

  /**
   * Constructor.
   *
   * @param dmWidget $widget    A widget
   * @param array  $options     An array of options
   * @param string $CSRFSecret  A CSRF secret (false to disable CSRF protection, null to use the global CSRF secret)
   */
  public function __construct($widget, $options = array(), $CSRFSecret = null)
  {
    if (!$widget instanceof DmWidget)
    {
      throw new dmException(sprintf('%s must be initialized with a DmWidget, not a %s', get_class($this), gettype($widget)));
    }
    
    $this->dmWidget = $widget;

    // disable CSRF protection
    parent::__construct($widget->values, $options, false);
  }
  
  public function setup()
  {
    parent::setup();
    
    $this->setName($this->name.'_'.$this->dmWidget->get('id'));
  }

  public function configure()
  {
    parent::configure();

    $this->widgetSchema['cssClass']     = new sfWidgetFormInputText(array('label' => 'CSS class'));
    $this->validatorSchema['cssClass']  = new dmValidatorCssClasses(array('required' => false));
    
    $this->setDefault('cssClass', $this->dmWidget->get('css_class'));

    /*
     * if the user can not edit widgets (but only fast edit them)
     * remove the CSS class field
     */
    if(($user = $this->getService('user')) && !$user->can('widget_edit'))
    {
      $this->changeToHidden('cssClass');
      $this->widgetSchema['cssClass']->setAttribute('readonly', true);
      //unset($this['cssClass']);
    }
  }

  public function getDmWidget()
  {
    return $this->dmWidget;
  }
  
  /**
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
    $this->renderContent($attributes).
    $this->renderActions().
    $this->close();
  }

  protected function renderContent($attributes)
  {
    return '<ul class="dm_form_elements">'.$this->getFormFieldSchema()->render($attributes).'</ul>';
  }

  protected function renderActions()
  {
    return sprintf(
      '<div class="actions">
        <div class="actions_part clearfix">%s%s</div>
        <div class="actions_part clearfix">%s%s</div>
      </div>',
      sprintf('<a class="dm cancel close_dialog button fleft">%s</a>', $this->__('Cancel')),
      sprintf('<input type="submit" class="submit try blue fright" name="try" value="%s" />', $this->__('Try')),
      $this->getService('user')->can('widget_delete')
      ? sprintf('<a class="dm delete button red fleft" title="%s">%s</a>', $this->__('Delete this widget'), $this->__('Delete'))
      : '',
      sprintf('<input type="submit" class="submit and_save green fright" name="and_save" value="%s" />', $this->__('Save and close'))
    );
  }

  /**
   * Try to guess default values
   * from last updated widget with same module.action
   * @return array default values
   */
  protected function getDefaultsFromLastUpdated(array $fields = array())
  {
    if ($this->dmWidget->get('value'))
    {
      return array_merge($this->dmWidget->getValues(), array('cssClass' => $this->dmWidget->get('css_class')));
    }

    $lastWidgetValue = dmDb::query('DmWidget w')
    ->withI18n(null, null , 'w')
    ->where('w.module = ? AND w.action = ?', array($this->dmWidget->get('module'), $this->dmWidget->get('action')))
    ->orderBy('w.updated_at desc')
    ->limit(1)
    ->select('w.id, wTranslation.value as value')
    ->fetchOneArray();
    
    $defaults = $this->getFirstDefaults();

    if (!$lastWidgetValue)
    {
      return $defaults;
    }

    $values = json_decode((string) $lastWidgetValue['value'], true);

    foreach($fields as $field)
    {
      $defaults[$field] = dmArray::get($values, $field, dmArray::get($defaults, $field));
    }
    
    return $defaults;
  }

  protected function getFirstDefaults()
  {
    return array();
  }
  
  protected function getFirstDefault($key)
  {
    return dmArray::get($this->getFirstDefaults(), $key);
  }
  
  public function updateWidget()
  {
    $this->dmWidget->setValues($this->getWidgetValues());

    if(isset($this['cssClass']))
    {
      $this->dmWidget->set('css_class', $this->getValue('cssClass'));
    }
    
    return $this->dmWidget;
  }
}