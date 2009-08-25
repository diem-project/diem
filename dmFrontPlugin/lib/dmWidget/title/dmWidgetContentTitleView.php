<?php

class dmWidgetContentTitleView extends dmWidgetPluginView
{

	public function configure()
	{
    parent::configure();

    $this->addRequiredVar(array('text', 'tag'));
	}

}