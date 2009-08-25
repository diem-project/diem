<?php

class dmWidgetContentLinkView extends dmWidgetPluginView
{

	public function configure()
	{
    parent::configure();

    $this->addRequiredVar(array('href'));
	}

}