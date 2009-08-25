<?php

/**
 * sfWidgetFormDate represents a date widget.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormDate.class.php 16259 2009-03-12 11:42:00Z fabien $
 */
class sfWidgetFormDmDate extends sfWidgetFormDate
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
  	parent::configure($options, $attributes);
    /*
     * Let's use a date format that matches user culture
     */
    if( $culture_dateI18n = sfConfig::get('dm_dateI18n_'.dm::getUser()->getCulture()))
    {
      $format = $culture_dateI18n['format'];
    }
    else
    {
      $format = '%month%/%day%/%year%';
    }
    $this->addOption('format', $format);
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
        $value = array('year' => date('Y', $value), 'month' => date('n', $value), 'day' => date('j', $value));
      }

	    $formatted_value = strtr(
	          $this->getOption('format'),
	          array(
	            '%year%' => sprintf('%04d', $value['year']),
	            '%month%' => sprintf('%02d', $value['month']),
	            '%day%' => sprintf('%02d', $value['day']),
	          )
	        );
  	}
  	else
  	{
      $formatted_value = $value;
  	}

    //$formatted_value = dm::getI18n()->getDateForCulture(strtotime($value));

    return $this->renderTag(
	    'input',
	    array(
	      'name' => $name,
	      'size' => 10,
	      'id' => $this->generateId($name),
	      'class' => 'datepicker_me',
	      'value' => $formatted_value
	    )
    );
  }
}
