<?php

abstract class dmModelTreeView extends dmConfigurable
{
  protected
  $options,
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

    $this->initialize($options);

    $this->tree     = $this->getRecordTree();

  }

  public function getTree() {
    return dmDb::table($this->options['model'])->getTree();
  }

  protected function initialize(array $options)
  {
    if (!(dmDb::table($options['model']) instanceof dmDoctrineTable && dmDb::table($options['model'])->isNestedSet())) {
      unset ($options['model']);
    }
    $this->configure($options);
  }

  abstract protected function renderModelLink(myDoctrineRecord $model);

  protected function getRecordTree()
  {
    $modelTableTree = dmDb::table($this->options['model'])->getTree();

    $modelTableTree->setBaseQuery($this->getRecordTreeQuery());

    $tree = $modelTableTree->fetchTree();

    $modelTableTree->resetBaseQuery();

    return $tree;
  }

  protected function getRecordTreeQuery()
  {
    $select = 'model.*';
    $query = dmDb::table($this->options['model'])->createQuery('model');
    if (dmDb::table($this->options['model'])->hasI18n()) {
      $query->withI18n($this->culture, null, 'model');
      $select .= ', modelTranslation.*';
    }
    return $query->select($select);
  }

  public function render($options = array(), $treeOptions = array())
  {
    $this->options = array_merge(dmString::toArray($options, true), $this->options);

    $rootColumnName = $this->getTree()->getAttribute('rootColumnName');

    $this->html = '';

    if ($rootColumnName) {
      foreach ($this->getTree()->fetchRoots() as $root) {
        $treeOptions = array_merge(
          $treeOptions,
          array('root_id' => $root->$rootColumnName)
        );

        $this->renderTree($options, $treeOptions);

      }
    } else {
      $this->renderTree($options, $treeOptions);
    }

    return $this->html;
  }

  public function renderTree($options = array(), $treeOptions = array())
  {

    $this->html .= $this->helper->open('div', array('json' => array(
      'move_url' => $this->helper->link('dmAdminGenerator/move?dm_module='.$this->options['module'])->getHref()
    )));
    $this->html .= $this->helper->open('ul', $this->options);

    $this->lastLevel = false;

    foreach($this->getTree()->fetchTree($treeOptions) as $node)
    {
      $this->level = $node->level;
      $this->html .= $this->renderNode($node);
      $this->lastLevel = $this->level;
    }

    $this->html .= str_repeat('</li></ul>', $this->lastLevel+1);
    $this->html .= $this->helper->close('div');

  }

  protected function renderNode(myDoctrineRecord $model)
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

    $html .= $this->renderOpenLi($model);

    $html .= $this->renderModelLink($model);

    return $html;
  }

  protected function renderOpenLi(myDoctrineRecord $model)
  {
    return '<li id="dmm'.$model->id.'" rel="manual">';
  }

}