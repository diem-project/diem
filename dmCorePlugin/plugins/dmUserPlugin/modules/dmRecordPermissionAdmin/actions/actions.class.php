<?php

require_once dirname(__FILE__).'/../lib/dmRecordPermissionAdminGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/dmRecordPermissionAdminGeneratorHelper.class.php';

/**
 * dmRecordPermissionAdmin actions.
 *
 * @package    sf-1.4.9-DEV
 * @subpackage dmRecordPermissionAdmin
 * @author     Stéphane Erard
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class dmRecordPermissionAdminActions extends autoDmRecordPermissionAdminActions
{
  public function executeEditRecord(sfWebRequest $request)
  {
    $do = "";
  }
  
  public function executeShowRecordInFront(sfWebRequest $request)
  {
    $recordPermission = Doctrine_Core::getTable('DmRecordPermission')->find($request->getParameter('id'));
    if($recordPermission)
    {
      $record = $recordPermission->getRecord();
    }
    $this->redirect($record->getDmPage());
  }
}
