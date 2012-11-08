<?php

/**
 * sfWidgetFormDate represents a date widget.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormDate.class.php 16259 2009-03-12 11:42:00Z fabien $
 */
class sfWidgetFormDmDate extends sfWidgetFormI18nDate
{

  /**
   * @param  string $name        The element name
   * @param  string $value       The date displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */

  protected function configure($options = array(), $attributes = array())
  {
    $options['culture'] = isset($options['culture']) ? $options['culture'] : sfDoctrineRecord::getDefaultCulture();
    
    parent::configure($options, $attributes);
    
    $this->setOption('culture', $options['culture']);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if($value && strtotime($value))
    {
      // convert value to an array
      $default = array('year' => null, 'month' => null, 'day' => null);

      $value = (string) $value == (string) (integer) $value ? (integer) $value : strtotime($value);
      if (false === $value)
      {
        $value = $default;
      }
      else
      {
        $value = array('year' => date('y', $value), 'month' => date('n', $value), 'day' => date('j', $value));
      }

      $formattedValue = strtr(
        $this->getOption('format'),
        array(
          '%year%' => sprintf('%02d', $value['year']),
          '%month%' => sprintf('%02d', $value['month']),
          '%day%' => sprintf('%02d', $value['day']),
        )
      );
    }
    else
    {
      $formattedValue = $value;
    }

    return $this->renderTag(
      'input',
      array(
        'type' => 'text',
        'name' => $name,
        'size' => 10,
        'id' => $this->generateId($name),
        'class' => 'datepicker_me',
        'value' => $formattedValue
      )
    );
  }

  public function getJavascripts()
  {
    $javascripts = array('lib.ui-datepicker');

    if('en' !== $this->getOption('culture'))
    {
      $javascripts[] = 'lib.ui-i18n';
    }
    
    return array_merge(parent::getJavascripts(), $javascripts);
  }

  public function getStylesheets()
  {
    return array_merge(parent::getStylesheets(), array(
      'lib.ui-datepicker' => null
    ));
  }
}
