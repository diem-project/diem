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

    $vars['menu'] = $this->getService('menu')
    ->ulClass($vars['ulClass']);

    foreach($vars['items'] as $index => $item)
    {
      $vars['menu']
      ->addChild($index.'-'.dmString::slugify($item['text']), $item['link'])
      ->label($item['text'])
      ->liClass($vars['liClass'])
      ->addRecursiveChildren(dmArray::get($item, 'depth', 0));
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