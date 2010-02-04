<?php

abstract class dmWidgetPluginView extends dmWidgetBaseView
{

  protected function getPartialModuleAction()
  {
    return array('dmWidget', $this->widgetType->getFullKey());
  }
}