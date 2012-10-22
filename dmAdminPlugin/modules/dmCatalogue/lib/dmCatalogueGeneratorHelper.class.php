<?php

/**
 * dmCatalogue module helper.
 *
 * @package    diem
 * @subpackage dmCatalogue
 * @author     Your name here
 * @version    SVN: $Id: configuration.php 12474 2008-10-31 10:41:27Z fabien $
 */
class dmCatalogueGeneratorHelper extends BaseDmCatalogueGeneratorHelper
{
  public function linkToExportSentences($object, $params)
  {
    if($this->module->getSecurityManager()->userHasCredentials('edit', $object))
    {
      $title = __(isset($params['title']) ? $params['title'] : $params['label'], array('%1%' => dmString::strtolower(__($this->getModule()->getName()))), 'dm');
      return '<li class="sf_admin_action_export_sentences">'.link_to1(__($params['label'], array(), $this->getI18nCatalogue()), $this->getRouteArrayForAction('exportSentences', $object),
      array(
      'class' => 's16 s16_export dm_export_link sf_admin_action',
      'title' => $title
      )).'</li>';
    }
    return '';
  }
  
  public function linkToImportSentences($object, $params)
  {
    if($this->module->getSecurityManager()->userHasCredentials('edit', $object))
    {
      $title = __(isset($params['title']) ? $params['title'] : $params['label'], array('%1%' => dmString::strtolower(__($this->getModule()->getName()))), 'dm');
      return '<li class="sf_admin_action_import_sentences">'.link_to1(__($params['label'], array(), $this->getI18nCatalogue()), $this->getRouteArrayForAction('importSentences', $object),
      array(
      'class' => 's16 s16_save dm_import_link sf_admin_action',
      'title' => $title
      )).'</li>';
    }
    return '';
  }
}
