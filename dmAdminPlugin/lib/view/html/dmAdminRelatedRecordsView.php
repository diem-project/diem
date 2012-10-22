<?php

class dmAdminRelatedRecordsView extends dmConfigurable
{
  protected
  $moduleManager,
  $helper,
  $routing,
  $i18n,
  $user,
  $record,
  $alias,
  $module,
  $foreignModule,
  $foreignRecords;

  public function __construct(dmModuleManager $moduleManager, dmHelper $helper, sfRouting $routing, dmI18n $i18n, $user, array $options)
  {
    $this->moduleManager  = $moduleManager;
    $this->helper         = $helper;
    $this->routing        = $routing;
    $this->i18n           = $i18n;
    $this->user 					= $user;

    $this->initialize($options);
  }

  public function getDefaultOptions()
  {
    return array(
      'max'   => 5
    );
  }

  protected function initialize(array $options)
  {
    $this->configure($options);

    $this->record         = $this->getOption('record');
    $this->alias          = $this->getOption('alias');
    $this->module         = $this->record->getDmModule();
    $this->relation       = $this->record->getTable()->getRelation($this->alias);
    $this->foreignModule  = $this->moduleManager->getModuleByModel($this->relation->getClass());
    $this->foreignRecords = $this->record->get($this->alias);

    /*
     * One to one relations give only one object instead of a collection
     * transform it to an array
     */
    if ($this->foreignRecords instanceof dmDoctrineRecord)
    {
      $this->foreignRecords = array($this->foreignRecords);
    }

    $this->setOption('foreign_has_route', $this->foreignModule && $this->routing->hasRouteName($this->foreignModule->getUnderscore()));
  }

  public function render()
  {
    if($this->record->isNew())
    {
      return '';
    }
    
    $html = '<div class="dm_related_records">';

    if (count($this->foreignRecords))
    {
      $html .= $this->renderList();
    }

    if($this->getOption('foreign_has_route'))
    {
      $html .= $this->renderActions();
    }

    $html .= '</div>';

    return $html;
  }

  public function renderList()
  {
    $html = '<ul>';

    $count = 0;

    foreach($this->foreignRecords as $foreignRecord)
    {
      if((1+$count) > $this->getOption('max'))
      {
        $html .= '<li>'.$this->renderMoreLink(count($this->foreignRecords) - $count).'</li>';
        break;
      }
      
      $html .= '<li>';

      if($this->getOption('foreign_has_route'))
      {
        $html .= $this->helper->link($foreignRecord)
        ->text($foreignRecord->__toString())
        ->set('.associated_record.s16right.s16_arrow_up_right_medium');
      }
      else
      {
        $html .= $this->helper->tag('span.associated_record', $foreignRecord->__toString());
      }

      $html .= '</li>';

      ++$count;
    }

    $html .= '</ul>';

    return $html;
  }

  protected function renderMoreLink($number)
  {
    $text = $this->i18n->formatNumberChoice('[1] And one more...|(1,+Inf] And %1% more...', array('%1%' => $number), $number);

    return $this->helper->link('dmAdminGenerator/showMoreRelatedRecords')
    ->text($text)
    ->set('.show_more_related_records')
    ->params(array(
      'model' => $this->module->getModel(),
      'pk'    => $this->record->get('id'),
      'alias' => $this->alias
    ));
  }

  protected function renderActions()
  {
    $html = '<ul>';

    if($this->getOption('new') && $this->record->getTable()->getRelationHolder()->get($this->alias)->getTable()->getDmModule()->getSecurityManager()->userHasCredentials('new', $this->record))
    {
      $html .= '<li>'.$this->renderNewLink().'</li>';
    }

    if(
    $this->getOption('sort')
    && $this->relation instanceof Doctrine_Relation_ForeignKey
    && $this->foreignModule->getTable()->isSortable()
    )
    {
      $html .= '<li>'.$this->renderSortLink().'</li>';
    }

    $html .= '</ul>';

    return $html;
  }

  protected function renderNewLink()
  {
    $link = $this->helper->link('@'.$this->foreignModule->getUnderscore().'?action=new')
    ->text($this->i18n->__('New'))
    ->set('.s16.s16_add_little');

    if ($this->relation instanceof Doctrine_Relation_ForeignKey)
    {
      $link->param('defaults['.$this->relation->getForeign().']', $this->record->get('id'));
    }
    elseif($this->relation instanceof Doctrine_Relation_Association)
    {
      $opposite = $this->relation['localTable']->getAssociationOppositeRelation($this->relation);
      if($opposite){
        $link->param('defaults['.dmString::tableize($opposite->getAlias()) . '_list][]', $this->record->get('id'));
      }
    }

    return $link->render();
  }
  
  protected function renderSortLink()
  {
    return $this->helper->link(array(
      'sf_route'      => $this->module->getUnderscore(),
      'id'            => $this->record->get('id'),
      'action'        => 'sortReferers',
      'refererModule' => $this->foreignModule->getKey()
    ))
    ->text($this->i18n->__('Sort'))
    ->set('.s16.s16_right_little')
    ->render();
  }
}