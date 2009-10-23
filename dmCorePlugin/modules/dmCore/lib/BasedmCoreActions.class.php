<?php

class BasedmCoreActions extends dmBaseActions
{

  public function executeW3cValidateHtml()
  {
    $this->doctype = sfConfig::get('dm_w3c_doctype', 'XHTML');

    $this->validator = new dmHtmlValidator($this->context->getCacheManager()->getCache("dm/view/html/validate")->get(session_id()));
  }

  public function executeSelectCulture(dmWebRequest $request)
  {
    $this->forward404Unless(
    $culture = $request->getParameter('culture'),
      'No culture specified'
    );

    $this->forward404Unless(
    $this->context->getI18n()->cultureExists($culture),
    "Culture $culture does not exist"
    );

    $this->getUser()->setCulture($culture);

    return $this->redirectBack();
  }

  public function executeRefresh(dmWebRequest $request)
  {
    $this->context->get('cache_manager')->clearAll();
     
    if ($this->getUser()->can('system'))
    {
      $this->context->get('filesystem')->sf('dmFront:generate');

      dmFileCache::clearAll();
    }

    $this->context->get('page_synchronizer')->execute();

    $this->context->get('seo_synchronizer')->execute();

    $this->context->getEventDispatcher()->notify(new sfEvent($this, 'dm.refresh', array()));
    
    return $this->redirectBack();
  }

}