<?php

class sfValidatorDmTagsAutocomplete extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addOption('min');
    $this->addOption('max');

    $this->addMessage('min', 'At least %min% tags must be selected (%count% tags selected).');
    $this->addMessage('max', 'At most %max% tags must be selected (%count% tags selected).');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    if (!is_array($value))
    {
      $value = array($value);
    }

    $value = array_unique(array_filter(array_map('trim', $value)));

    $count = count($value);

    if ($this->hasOption('min') && $count < $this->getOption('min'))
    {
      throw new sfValidatorError($this, 'min', array('count' => $count, 'min' => $this->getOption('min')));
    }

    if ($this->hasOption('max') && $count > $this->getOption('max'))
    {
      throw new sfValidatorError($this, 'max', array('count' => $count, 'max' => $this->getOption('max')));
    }

    return $value;
  }
}