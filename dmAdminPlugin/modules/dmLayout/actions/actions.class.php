<?php

require_once dirname(__FILE__).'/../lib/dmLayoutGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/dmLayoutGeneratorHelper.class.php';

/**
 * dmLayout actions.
 *
 * @package    diem
 * @subpackage dmLayout
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class dmLayoutActions extends autoDmLayoutActions
{
  
  public function executeDuplicate(dmWebRequest $request)
  {
    $this->forward404Unless(
      $layout = dmDb::query('DmLayout l')
      ->where('l.id = ?', $request->getParameter('id'))
      ->fetchOne()
    );
    
    $duplicatedLayout = $layout->duplicate()->saveGet();
    
    return $this->redirect($this->getHelper()->link($duplicatedLayout)->getHref());
  }
}