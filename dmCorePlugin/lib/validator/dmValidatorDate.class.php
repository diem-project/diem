<?php

class dmValidatorDate extends sfValidatorDate
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

    $i18n = dm::getI18n();

    $this->addMessage('bad_format', '"%value%" '.$i18n->__('does not match the date format').' (%date_format%).');
    $this->addMessage('max', $i18n->__('The date must be before').' %max%.');
    $this->addMessage('min', $i18n->__('The date must be after').' %min%.');

    //    $this->addOption('date_format', $this->getDateFormat(dm::getUser()->getCulture()));
    $this->addOption('context', dmContext::getInstance());
  }

  protected function doClean($value)
  {
    if (is_array($value)) {
      $clean = strtotime($this->convertDateArrayToString($value));
    } else if ($regex = $this->getOption('date_format')) {
      if (!preg_match($regex, $value, $match)) {
        throw new sfValidatorError($this, 'bad_format',
        array(
                        'value' => $value,
                        'date_format' => $this->getOption('date_format_error') ?
        $this->getOption('date_format_error') : $this->getOption('date_format'))
        );
      }
      $clean = strtotime($this->convertDateArrayToString($match));
    } else if (!ctype_digit($value)) {
      $context = $this->getOption('context');
      $i18n = $context->getI18N();
      list($d, $m, $y) = $i18n->getDateForCulture($value, $context->getUser()->getCulture());
      $clean = "$y-$m-$d";
      if ($this->getOption('with_time')) {
        list($h, $m) = $i18n->getTimeForCulture($value, $context->getUser()->getCulture());
        $clean = "$clean $h:$m";
      }
      $clean = strtotime($clean);
      if (false === $clean) {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }
    } else {
      $clean = (integer) $value;
    }
    if ($this->hasOption('max') && $clean > $this->getOption('max')) {
      throw new sfValidatorError($this, 'max',
      array(
                    'value' => $value,
                    'max' => date($this->getOption('date_format_range_error'), $this->getOption('max')))
      );
    }
    if ($this->hasOption('min') && $clean < $this->getOption('min')) {
      throw new sfValidatorError($this, 'min',
      array(
                    'value' => $value,
                    'min' => date($this->getOption('date_format_range_error'), $this->getOption('min')))
      );
    }
    return $clean === $this->getEmptyValue() ?
    $clean : date($this->getOption('with_time') ?
    $this->getOption('datetime_output') : $this->getOption('date_output'), $clean);
  }
}
