<?php

require_once dirname(__FILE__).'/../lib/dmPageGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/dmPageGeneratorHelper.class.php';

/**
 * dmPage actions.
 *
 * @package    diem
 * @subpackage dmPage
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class dmPageActions extends autoDmPageActions
{

  public function executeViewTree(sfWebRequest $request)
  {
    $this->tree = dmDb::table('DmPage')->getTree()->fetchTree();
  }

}