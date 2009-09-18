<?php

abstract class dmRecursivePageList
{
  protected
  $tree,
  $options,
  $culture,
  $html,
  $level,
  $lastLevel;

  public function __construct($culture = null)
  {
    $this->culture  = null === $this->culture ? dm::getUser()->getCulture() : $culture;
    $this->tree     = $this->getTree();
  }

  protected function getTree()
  {
    $pageTable = dmDb::table('DmPage');

    $q = $pageTable->createQuery('p')
    ->withI18n($this->culture)
    ->select('p.id, p.action, translation.name, translation.slug');

    $treeObject = $pageTable->getTree();
    $treeObject->setBaseQuery($q);
    $tree = $treeObject->fetchTree(array(), Doctrine::HYDRATE_NONE);
    $treeObject->resetBaseQuery();

    return $tree;
  }

  public function render($options = array())
  {
    $this->options = dmString::toArray($options, true);

    $this->html = isset($this->options['class'])
    ? '<ul class="'.dmArray::get($this->options, 'class').'">'
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
    $id = $page[0];

    /*
     * First time, don't insert nothing
     */
    if ($this->lastLevel === false)
    {
      $html = '';
    }
    else
    {
      if ($this->level === $this->lastLevel)
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
    }

    $html .= '<li id="dmp'.$id.'">';

    $html .= $this->getPageLink($page);

    return $html;
  }

}