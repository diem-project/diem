<?php

class dmTestProjectBuilder
{
  protected
  $context;

  public function __construct(dmContext $context)
  {
    $this->context = $context;
  }

  public function execute()
  {
    $this->loremize(10);
    
    $this->addLoginForm();

    $this->addBreadCrumb();

    $this->addH1();
  }

  protected function loremize($nb)
  {
    $task = new dmLoremizeTask($this->context->getEventDispatcher(), new sfFormatter());
    $task->run(array(), array('nb' => $nb));

    $this->context->get('page_tree_watcher')->synchronizePages();
    $this->context->get('page_tree_watcher')->synchronizeSeo();
  }

  protected function addBreadCrumb()
  {
    $topZone = dmDb::table('DmPage')->findOneByModuleAndAction('main', 'login')->PageView->Layout->getArea('top')->Zones[0];

    dmDb::table('DmWidget')->create(array(
      'module' => 'dmWidgetNavigation',
      'action' => 'breadCrumb'
    ))
    ->set('Zone', $topZone)
    ->save();
  }

  protected function addH1()
  {
    $topZone = dmDb::table('DmPage')->findOneByModuleAndAction('main', 'login')->PageView->Layout->getArea('top')->Zones[0];

    dmDb::table('DmWidget')->create(array(
      'module' => 'dmWidgetContent',
      'action' => 'title',
      'tag'    => 'h1'
    ))
    ->set('Zone', $topZone)
    ->save();
  }

  protected function addLoginForm()
  {
    dmDb::table('DmWidget')->create(array(
      'module' => 'main',
      'action' => 'loginForm'
    ))
    ->set('Zone', dmDb::table('DmPage')->findOneByModuleAndAction('main', 'login')->PageView->Area->Zones[0])
    ->save();
  }

}