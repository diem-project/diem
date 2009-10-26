[?php

/**
 * <?php echo $this->getModuleName() ?> module configuration.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getModuleName()."\n" ?>
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: helper.php 12482 2008-10-31 11:13:22Z fabien $
 */
class Base<?php echo ucfirst($this->getModuleName()) ?>GeneratorHelper extends dmAdminModelGeneratorHelper
{

  protected function getModuleName()
  {
    return '<?php echo $this->getModuleName() ?>';
  }

  public function getRouteArrayForAction($action, $object = null)
  {
    $route = array('sf_route' => '<?php echo $this->params['route_prefix'] ?>');
    
    if ('list' !== $action)
    {
      $route['action'] = $action;
    }
    
    if (null !== $object)
    {
      $route['pk'] = $object->getPrimaryKey();
    }
    
    return $route;
  }

  public function getUrlForAction($action)
  {
    return '<?php echo $this->params['route_prefix'] ?>?action='.$action;
  }
}