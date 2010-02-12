<?php

abstract class dmPageTreeView extends dmConfigurable
{
  protected
  $tree,
  $culture,
  $html,
  $level,
  $lastLevel;

  public function __construct($culture, array $options)
  {
    $this->initialize($options);
    
    $this->culture  = $culture;
    $this->tree     = $this->getTree();
  }

  protected function initialize(array $options)
  {
    $this->configure($options);
  }

  abstract protected function getPageLink(array $page);

  protected function getTree()
  {
    $pageTable = dmDb::table('DmPage');

    $q = $pageTable->createQuery('page')
    ->withI18n($this->culture, null, 'page')
    ->select('page.id, page.action, pageTranslation.name, pageTranslation.slug');

    $pageTable->getTree()->setBaseQuery($q);

    $tree = $pageTable->getTree()->fetchTree(array(), Doctrine_Core::HYDRATE_NONE);
    
    $pageTable->getTree()->resetBaseQuery();

    return $tree;
  }

  public function render($options = array())
  {
    $this->options = array_merge(dmString::toArray($options, true), $this->options);

    $this->html = isset($this->options['class'])
    ? '<ul class="'.$this->options['class'].'">'
    : '<ul>';

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

    $html .= '<li id="dmp'.$page[0].'">';

    $html .= $this->getPageLink($page);

    return $html;
  }

}