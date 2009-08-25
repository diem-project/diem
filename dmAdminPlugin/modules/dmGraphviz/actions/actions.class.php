<?php
/**
 * dmMedia actions.
 *
 * @package    diem
 * @subpackage dmMedia
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class dmGraphvizActions extends dmAdminBaseActions
{

  public function executeIndex(sfWebRequest $request)
  {
    $service = new dmGraphvizService($this->dispatcher);
    $service->execute(true);
    $this->download(sfConfig::get('sf_cache_dir').'/dm/graph/uml-schema.png');
  }

}
