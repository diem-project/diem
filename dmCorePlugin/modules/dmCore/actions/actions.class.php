<?php

class dmCoreActions extends dmBaseActions
{

	/*
	 * Gateway to handle amf requests @see sfAmfPlugin
	 */
	public function executeAmfGateway()
	{
    $this->setLayout(false);
    sfAmfGateway::getInstance()->handleRequest();
    return sfView::NONE;
	}

  public function executeW3cValidateHtml()
  {
  	$this->doctype = sfConfig::get('dm_w3c_doctype', 'XHTML');

    $this->validator = new dmHtmlValidator($this->dmContext->getCache("dm/view/html/validate")->get(session_id()));
  }


  public function executeSelectTheme(sfWebRequest $request)
  {
    $this->forward404Unless(
      $theme = dmTheme::getTheme($request->getParameter('theme')),
      sprintf('%s is not a valid theme.',
        $request->getParameter('theme')
      )
    );
    
    $this->getUser()->setThemeKey($theme->getKey());

    return $this->redirectBack();
  }
  
  public function executeSelectCulture(sfWebRequest $request)
  {
    $this->forward404Unless(
      $culture = $request->getParameter('culture'),
      'No culture specified'
    );
    
    $this->forward404Unless(
      dm::getI18n()->cultureExists($culture),
      "Culture $culture does not exist"
    );

    $this->getUser()->setCulture($culture);

    return $this->redirectBack();
  }

  public function executeLorem(sfWebRequest $request)
  {
    $type = $request->getParameter("type", "little");
    $size = $request->getParameter("size", null);
    $getter = "get".dmString::camelize($type)."Lorem";

    return $this->renderText(dmLorem::$getter($size));
  }


}