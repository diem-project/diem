<?php

class dmWidgetNavigationMenuView extends dmWidgetPluginView
{
  protected
  $isIndexable = false;

  public function configure()
  {
    parent::configure();

    $this->addRequiredVar(array('items', 'ulClass', 'liClass'));
  }

  protected function filterViewVars(array $vars = array())
  {
    $vars = parent::filterViewVars($vars);

    $menuClass = dmArray::get($vars, 'menuClass');
    $menuName = dmArray::get($vars, 'menuName');

    $vars['menu'] = $this->getService('menu', $menuClass ? $menuClass : null)
    ->ulClass($vars['ulClass']);
    if (!is_null($menuName)) {
	    $vars['menu']->name($menuName);
    }

    foreach($vars['items'] as $index => $item)
    {
      $menuItem = $vars['menu']
      ->addChild($index.'-'.dmString::slugify($item['text']), $item['link'])
      ->label($item['text'])
      ->secure(!empty($item['secure']))
      ->liClass($vars['liClass'])
      ->addRecursiveChildren(dmArray::get($item, 'depth', 0));

      if(!empty($item['nofollow']) && $menuItem->getLink())
      {
        $menuItem->getLink()->set('rel', 'nofollow');
      }

      if(!empty($item['target']) && $menuItem->getLink())
      {
        $menuItem->getLink()->target($item['target']);
      }
    }

    unset($vars['items'], $vars['ulClass'], $vars['liClass']);

    return $vars;
  }

  protected function doRender()
  {
    if ($this->isCachable() && $cache = $this->getCache())
    {
      return $cache;
    }

    $vars = $this->getViewVars();

    $html = $vars['menu']->render();

    if ($this->isCachable())
    {
      $this->setCache($html);
    }

    return $html;
  }

}