[?php

/**
 * <?php echo $this->getModuleName() ?> module configuration.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getModuleName()."\n" ?>
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: helper.php 12482 2008-10-31 11:13:22Z fabien $
 */
abstract class Base<?php echo ucfirst($this->getModuleName()) ?>GeneratorHelper extends myAdminModelGeneratorHelper
{

  protected function getModuleName()
  {
    return '<?php echo $this->getModuleName() ?>';
  }

  public function getRouteArrayForAction($action, $object = null, $module = '<?php echo $this->params['route_prefix'] ?>', $key = 'pk', $value = null)
  {
    $route = array('sf_route' => $module);

    if ('list' !== $action)
    {
      $route['action'] = $action;
    }

    if (null !== $object && !$object->isNew())
    {
      $route[$key] = $value === null ? $object->getPrimaryKey() : $object->get($value);
    }

    if(sfConfig::get('dm_admin_embedded'))
    {
      $route['dm_embed'] = 1;
    }

    return $route;
  }

  public function getUrlForAction($action)
  {
    return '<?php echo $this->params['route_prefix'] ?>?action='.$action;
  }
}
