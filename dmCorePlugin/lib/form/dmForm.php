<?php

/**
 * Diem form base class.
 * Extends the form component with diem-specific functionality.
 *
 * @package    diem
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormBaseTemplate.php 9304 2008-05-27 03:49:32Z dwhittle $
 */
class dmForm extends sfFormSymfony
{
  protected static
  $serviceContainer,
  $counter = 1;

  protected
  $key,
  $name;

  public function setup()
  {
    parent::setup();
    
    $this->widgetSchema->setFormFormatterName('dmList');

    $this->key = "dm_form_".self::$counter++;

    $this->setName(dmString::underscore(get_class($this)));
  }

  public function setName($name)
  {
    $this->name = $name;
    $this->widgetSchema->setNameFormat($name.'[%s]');

    return $this;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getKey()
  {
    return $this->key;
  }

  
  public function removeCsrfProtection()
  {
    $this->localCSRFSecret = false;
    
    if ($this->isCSRFProtected())
    {
      unset($this[self::$CSRFFieldName]);
    }
    
    return $this;
  }
  
  public function changeToHidden($fieldName)
  {
    $this->widgetSchema[$fieldName] = new sfWidgetFormInputHidden;
    return $this;
  }
  
  public function changeToEmail($fieldName)
  {
    $this->validatorSchema[$fieldName] = new sfValidatorEmail($this->validatorSchema[$fieldName]->getOptions());
  }
  
  /**
   * Renders the widget schema associated with this form.
   *
   * @param array $attributes An array of HTML attributes
   *
   * @return string The rendered widget schema
   */
  public function render($attributes = array())
  {
    $attributes = dmString::toArray($attributes, true);
    
    return
    $this->open($attributes).
    '<ul class="dm_form_elements">'.
    $this->getFormFieldSchema()->render($attributes).
    sprintf('<li class="dm_form_element"><label>%s</label>%s</li>',
    $this->__('Validate'),
    $this->renderSubmitTag($this->__('Validate'))
    ).
    '</ul>'.
    $this->close();
  }

  public function renderSubmitTag($value = 'submit', $attributes = array())
  {
    $attributes = array_merge(array(
      'value' => $value,
      'type' => 'submit'
    ), dmString::toArray($attributes));
    
    $attributes['class'] = dmArray::toHtmlCssClasses(array_merge(dmArray::get($attributes, 'class', array()), array('submit')));

    return sprintf('<input%s />', $this->getWidgetSchema()->attributesToHtml($attributes));
  }
  
  public function submit($value = 'submit', $attributes = array())
  {
    return $this->renderSubmitTag($value, $attributes);
  }
  
  /**
   * Binds the current form validate it in one step.
   *
   * @param  array      An array of tainted values to use to bind the form
   * @param  array      An array of uploaded files (in the $_FILES or $_GET format)
   * @param  Connection An optional Doctrine Connection object
   *
   * @return Boolean    true if the form is valid, false otherwise
   */
  public function bindAndValid(sfWebRequest $request)
  {
    return $this->bindRequest($request)->isValid();
  }
  
  public function bindRequest(sfWebRequest $request)
  {
    $this->bind($request->getParameter($this->name), $request->getFiles($this->name));
    
    return $this;
  }

  public function open($opt = array())
  {
    $opt = dmString::toArray($opt, true);

    $defaults = array(
      'class' => dmArray::get($opt, 'class'),
      'id' => $this->getKey(),
      'anchor' => false
    );

    if (isset($opt['class']))
    {
      unset($opt['class']);
    }

    $opt = array_merge($defaults, $opt);

    if ($action = dmArray::get($opt, 'action'))
    {
      $action = self::$serviceContainer->getService('helper')->£link($action)->getHref();
    }
    else
    {
      $action = self::$serviceContainer->getService('request')->getUri();
    }

    if ($opt['anchor'] && strpos($action, '#') === false)
    {
      $action .= '#'.(is_string($opt['anchor']) ? $opt['anchor'] : $this->getKey());
    }
    
    if (!isset($opt['method']))
    {
      $opt['method'] = 'post';
    }

    if (isset($opt['action'])) unset($opt['action']);
    
    return $this->renderFormTag($action, $opt).$this->renderHiddenFields();
  }
  
  public function close()
  {
    return '</form>';
  }

  /**
   * Sets the service container to be used by all forms.
   *
   * @param dmBaseServiceContainer $serviceContainer
   */
  public static function setServiceContainer(dmBaseServiceContainer $serviceContainer)
  {
    self::$serviceContainer = $serviceContainer;
  }

  public function getValueOrDefault($name)
  {
    if (!$return = $this->getValue($name))
    {
      $return = $this->getDefault($name);
    }

    return $return;
  }

  /*
   * Usefull for debugging : will throw the error exception
   */
  public function throwError()
  {
    throw $this->errorSchema;
  }

  /**
   * Returns the form field associated with the name (implements the ArrayAccess interface).
   *
   * @param  string $name  The offset of the value to get
   *
   * @return dmFormField   A form field instance
   */
  public function offsetGet($name)
  {
    if (!isset($this->formFields[$name]))
    {
      if (!$widget = $this->widgetSchema[$name])
      {
        throw new InvalidArgumentException(sprintf('Widget "%s" does not exist.', $name));
      }

      if ($this->isBound)
      {
        $value = isset($this->taintedValues[$name]) ? $this->taintedValues[$name] : null;
      }
      else if (isset($this->defaults[$name]))
      {
        $value = $this->defaults[$name];
      }
      else
      {
        $value = $widget instanceof sfWidgetFormSchema ? $widget->getDefaults() : $widget->getDefault();
      }

      $class = $widget instanceof sfWidgetFormSchema ? 'sfFormFieldSchema' : self::$serviceContainer->getParameter('form_field.class');

      $this->formFields[$name] = new $class($widget, $this->getFormFieldSchema(), $name, $value, $this->errorSchema[$name]);
    
      if ($this->formFields[$name] instanceof dmFormField && ($validator = $this->getValidatorSchema()->offsetGet($name)))
      {
        $this->formFields[$name]->setIsRequired($validator->getOption('required'));
      }
    }

    return $this->formFields[$name];
  }
  
  protected function __($text, $args = array(), $catalogue = null)
  {
    return self::$serviceContainer->getService('i18n')->__($text, $args, $catalogue);
  }
  
  protected function getHelper()
  {
    return self::$serviceContainer->getService('helper');
  }
}