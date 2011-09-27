<?php

/**
 * dmAutoSeo actions.
 *
 * @package    diem
 * @subpackage dmAutoSeo
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class dmAutoSeoActions extends dmAdminBaseActions
{
  
  public function executeIndex(dmWebRequest $request)
  {
    $autoSeos = $this->getDmAutoSeos();

    if($autoSeos->count())
    {
      $this->redirect($this->getHelper()->link($autoSeos->getFirst())->getHref());
    }
  }
  
  public function executeEdit(dmWebRequest $request)
  {
    $this->forward404Unless($this->autoSeo = dmDb::table('DmAutoSeo')->find($request->getParameter('pk')));

    if(!$this->autoSeo->getTargetDmModule() instanceof dmProjectModule)
    {
      throw new dmException($this->autoSeo->getTargetDmModule().' is not a project module');
    }

    $this->form = new DmAutoSeoForm($this->autoSeo);
    
    if ($request->isMethod('post'))
    {
      $this->form->setSeoSynchronizer($this->getService('seo_synchronizer'));
      
      if ($this->form->bindAndValid($request))
      {
        if ($request->getParameter('save'))
        {
          $this->form->save();
      
          $this->getUser()->logInfo('The item was updated successfully.');
      
          return $this->redirectBack();
        }
        else
        {
          $tryMode = true;
        }
      }
    }
    
    $this->previewRules = array();
    foreach($this->form->getRules() as $ruleKey)
    {
      $this->previewRules[$ruleKey] = isset($tryMode) ? $this->form->getValue($ruleKey) : $this->form->getDefault($ruleKey);
    }
    
    $this->dispatcher->notify(new sfEvent($this, 'admin.edit_object', array('object' => $this->autoSeo)));
    
    $this->autoSeos = $this->getDmAutoSeos();
  }
  
  protected function getDmAutoSeos()
  {
    return dmDb::table('DmAutoSeo')->findActives();
  }
  
}