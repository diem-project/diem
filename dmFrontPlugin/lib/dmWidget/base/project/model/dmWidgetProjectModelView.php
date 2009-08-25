<?php

abstract class dmWidgetProjectModelView extends dmWidgetProjectView
{

  public function configure()
  {
  	parent::configure();

    $this->addRequiredVar('view');
  }

	/*
	 * Will transform string view in dmView
	 * @return array viewVars
	 */
  public function getViewVars($vars = array())
  {
    $viewVars = parent::getViewVars($vars);

    $viewVars['view'] = $this->dmModule->getView($viewVars['view']);

    return $viewVars;
  }

}