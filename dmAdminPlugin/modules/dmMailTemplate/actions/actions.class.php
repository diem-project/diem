<?php

require_once dirname(__FILE__).'/../lib/dmMailTemplateGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/dmMailTemplateGeneratorHelper.class.php';

/**
 * dmMailTemplate actions.
 *
 * @package    diem-commerce
 * @subpackage dmMailTemplate
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class dmMailTemplateActions extends autoDmMailTemplateActions
{
  
  public function preExecute()
  {
    $this->getUser()->logAlert('Diem mail support is <strong>-NOT-</strong> completed. Please use the symfony 1.4 mail service instead', false);
    
    parent::preExecute();
  }
}
