<?php

class sfValidatorDmDate extends sfValidatorDate
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * date_format:             A regular expression that dates must match
   *  * with_time:               true if the validator must return a time, false otherwise
   *  * date_output:             The format to use when returning a date (default to Y-m-d)
   *  * datetime_output:         The format to use when returning a date with time (default to Y-m-d H:i:s)
   *  * date_format_error:       The date format to use when displaying an error for a bad_format error (use date_format if not provided)
   *  * max:                     The maximum date allowed (as a timestamp)
   *  * min:                     The minimum date allowed (as a timestamp)
   *  * date_format_range_error: The date format to use when displaying an error for min/max (default to d/m/Y H:i:s)
   *
   * Available error codes:
   *
   *  * bad_format
   *  * min
   *  * max
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
  	parent::configure($options, $messages);

  	$i18n = sfContext::getInstance()->getI18n();

    $this->addMessage('bad_format', '"%value%" '.$i18n->__('does not match the date format').' (%date_format%).');
    $this->addMessage('max', $i18n->__('The date must be before').' %max%.');
    $this->addMessage('min', $i18n->__('The date must be after').' %min%.');
  	/*
  	 * Let's use a date_format regex that matches user culture
  	 */
  	if( $culture_dateI18n = sfConfig::get('dm_dateI18n_'.dm::getUser()->getCulture()))
  	{
      $date_format = $culture_dateI18n['regex'];
  	}
  	else
  	{
  		$date_format = '|(?P<year>\d{4})/(?P<month>\d{2})/(?P<day>\d{2})|';
  	}
    $this->addOption('date_format', $date_format);
  }
}