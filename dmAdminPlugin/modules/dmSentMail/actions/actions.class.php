<?php

require_once dirname(__FILE__).'/../lib/dmSentMailGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/dmSentMailGeneratorHelper.class.php';

/**
 * dmSentMail actions.
 *
 * @package    diem
 * @subpackage dmSentMail
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class dmSentMailActions extends autoDmSentMailActions
{
  public function preExecute()
  {
    $this->getUser()->logAlert('Diem mail support is <strong>-NOT-</strong> completed. Please use the symfony 1.4 mail service instead', false);
    
    parent::preExecute();
  }
}
