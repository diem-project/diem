<?php

/**
 * dmSite module configuration.
 *
 * @package    diem_test
 * @subpackage dmSite
 * @author     thibault d
 * @version    SVN: $Id: form.php 12474 2008-10-31 10:41:27Z fabien $
 */
class DmSiteAdminForm extends BaseDmSiteForm
{
	
  /*
   * Create current i18n form
   */
  protected function createCurrentI18nForm()
  {
  	$i18nForm = parent::createCurrentI18nForm();
  	
  	unset($i18nForm['app_urls']);
  	
  	return $i18nForm;
  }
}