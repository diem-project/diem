<?php

class sfWidgetFormDmFilterInput extends sfWidgetFormFilterInput
{
  /**
   * @see sfWidgetFormFilterInput
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure();

    $this->setOption('template', '%input%<div class="dm_filter_empty">%empty_checkbox% %empty_label%</div>');
  }
}
