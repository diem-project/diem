<?php

abstract class dmPageTreeView extends dmConfigurable
{
  protected
  $helper,
  $culture,
  $tree,
  $html,
  $level,
  $lastLevel;

  public function __construct(dmHelper $helper, $culture, array $options)
  {
    $this->helper   = $helper;
    $this->culture  = $culture;
    $this->tree     = $this->getRecordTree();
    
    $this->initialize($options);
  }

  protected function initialize(array $options)
  {
    $this->configure($options);
  }

  abstract protected function renderPageLink(array $page);

  protected function getRecordTree()
  {
    $pageTableTree = dmDb::table('DmPage')->getTree();

    $pageTableTree->setBaseQuery($this->getRecordTreeQuery());

    $tree = $pageTableTree->fetchTree(array(), Doctrine_Core::HYDRATE_NONE);
    
    $pageTableTree->resetBaseQuery();

    return $tree;
  }

  protected function getRecordTreeQuery()
  {
    return dmDb::table('DmPage')->createQuery('page')
    ->withI18n($this->culture, null, 'page')
    ->select('page.id, page.action, pageTranslation.name');
  }

  public function render($options = array())
  {
    $this->options = array_merge(dmString::toArray($options, true), $this->options);

    $this->html = $this->helper->open('ul', $this->options);

    $this->lastLevel = false;
    foreach($this->tree as $node)
    {
      $this->level = $node[4];
      $this->html .= $this->renderNode($node);
      $this->lastLevel = $this->level;
    }

    $this->html .= str_repeat('</li></ul>', $this->lastLevel+1);

    return $this->html;
  }

  protected function renderNode(array $page)
  {
    /*
     * First time, don't insert nothing
     */
    if ($this->lastLevel === false)
    {
      $html = '';
    }
    elseif ($this->level === $this->lastLevel)
    {
      $html = '</li>';
    }
    elseif ($this->level > $this->lastLevel)
    {
      $html = '<ul>';
    }
    else // $this->level < $this->lastLevel
    {
      $html = str_repeat('</li></ul>', $this->lastLevel - $this->level).'</li>';
    }

    $html .= $this->renderOpenLi($page);

    $html .= $this->renderPageLink($page);

    return $html;
  }

  protected function renderOpenLi(array $page)
  {
    return '<li id="dmp'.$page[0].'" rel="'.($page[1] === 'show' ? 'auto' : 'manual').'">';
  }

}