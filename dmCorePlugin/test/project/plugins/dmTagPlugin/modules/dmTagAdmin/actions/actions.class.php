<?php

require_once dirname(__FILE__).'/../lib/dmTagAdminGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/dmTagAdminGeneratorHelper.class.php';

/**
 * dmTagAdmin actions.
 *
 * @package    test
 * @subpackage dmTagAdmin
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class dmTagAdminActions extends autoDmTagAdminActions
{

  public function executeGetTagsForAutocomplete(sfWebRequest $request)
  {
    $tags = dmDb::query('DmTag t')
    ->select('t.name as value, t.name as caption')
    ->fetchArray();

    return $this->renderJson($tags);
  }

}
