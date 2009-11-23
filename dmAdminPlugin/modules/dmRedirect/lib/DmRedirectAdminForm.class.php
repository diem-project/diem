<?php

/**
 * dmRedirect module configuration.
 *
 * @package    blancerf2
 * @subpackage dmRedirect
 * @author     thibault d
 * @version    SVN: $Id: form.php 12474 2008-10-31 10:41:27Z fabien $
 */
class DmRedirectAdminForm extends BaseDmRedirectForm
{
  public function configure()
  {
    parent::configure();
    
    $this->widgetSchema['dest']->setAttribute('class', 'dm_link_droppable');
    
    $this->unsetAutoFields();
  }
}