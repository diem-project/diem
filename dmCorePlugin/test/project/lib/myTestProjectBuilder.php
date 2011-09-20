<?php

class myTestProjectBuilder
{
  protected
  $context;

  public function __construct(dmContext $context)
  {
    $this->context = $context;
  }

  public function execute()
  {
    $this->loremize();

    $this->addRecords();

    $this->context->get('page_tree_watcher')->synchronizePages();
    $this->context->get('page_tree_watcher')->synchronizeSeo();

    dmDb::table('DmPage')->clear();

    $this->changeHomeLayout();
    
    $this->addSigninForm();

    $this->addBreadCrumb();

    $this->addNavigation();

    $this->addSitemap();

    $this->addH1();

    $this->addManualPages();

    $this->addUsers();
  }

  protected function addUsers()
  {
    $writer = dmDb::table('DmUser')->create(array(
      'username' => 'writer',
      'email'    => 'writer.org',
      'is_active' => true,
      'is_super_admin' => false
    ));
    
    $writer->setPassword('writer');

    $writer->addGroupByName('writer');

    $writer->save();
  }

  /*
   * root
   *   page1
   *     page11
   *       page111
   *     page12
   *   page2
   *     page21
   */
  protected function addManualPages()
  {
    $table = dmDb::table('DmPage');

    $page1 = $table->create(array(
      'module' => 'main',
      'action' => 'page1',
      'name'   => 'Page 1',
      'slug'   => 'page1'
    ));
    $page11 = $table->create(array(
      'module' => 'main',
      'action' => 'page11',
      'name'   => 'Page 11',
      'slug'   => 'page11'
    ));
    $page111 = $table->create(array(
      'module' => 'main',
      'action' => 'page111',
      'name'   => 'Page 111',
      'slug'   => 'page111'
    ));
    $page12 = $table->create(array(
      'module' => 'main',
      'action' => 'page12',
      'name'   => 'Page 12',
      'slug'   => 'page12'
    ));
    $page2 = $table->create(array(
      'module' => 'main',
      'action' => 'page2',
      'name'   => 'Page 2',
      'slug'   => 'page2'
    ));
    $page21 = $table->create(array(
      'module' => 'main',
      'action' => 'page21',
      'name'   => 'Page 21',
      'slug'   => 'page21'
    ));

    assert($page1->Node->insertAsFirstChildOf($table->getTree()->fetchRoot()));
    assert($page12->Node->insertAsFirstChildOf($page1));
    assert($page11->Node->insertAsFirstChildOf($page1));
    assert($page111->Node->insertAsFirstChildOf($page11));
    assert($page2->Node->insertAsFirstChildOf($table->getTree()->fetchRoot()));
    assert($page21->Node->insertAsFirstChildOf($page2));
  }

  protected function addRecords()
  {
    dmDb::table('DmTestDomain')->create(array(
      'title' => 'Domain 1',
      'Categs' => array(
        array(
          'name' => 'Categ 1',
          'Posts' => array(
            array(
              'title' => 'Post 1',
              'user_id' => dmDb::table('DmUser')->findOne()->id,
              'date' => '2010-01-12',
              'url' => 'http://diem-project.org',
              'body' => 'Post 1 body',
              'excerpt' => 'Post 1 excerpt',
              'Tags' => array(
                array(
                  'name' => 'Tag 1',
                  'slug' => 'tag-1'
                )
              ),
              'Comments' => array(
                array(
                  'author' => 'Author 1',
                  'body' => 'Comment 1'
                )
              )
            )
          )
        )
      )
    ))->save();
  }

  protected function changeHomeLayout()
  {
    $globalLayout = dmDb::table('DmLayout')->findOneByName('Global');
    $globalLayout->cssClass = 'global_layout';
    $globalLayout->save();

    $root = dmDb::table('DmPage')->getTree()->fetchRoot();
    $root->PageView->Layout = $globalLayout;
    $root->PageView->save();
  }

  protected function addSitemap()
  {
    $page = dmDb::table('DmPage')->create(array(
      'module' => 'main',
      'action' => 'sitemap',
      'name' => 'Sitemap',
      'slug' => 'sitemap'
    ));
    $page->Node->insertAsLastChildOf(dmDb::table('DmPage')->getTree()->fetchRoot());
    
    $this->createWidget(
      'main/sitemap',
      array(),
      $page->PageView->getArea('content')->Zones[0]
    )->save();
  }

  protected function addNavigation()
  {
    // domain left menu
    $this->createWidget(
      'dmTestDomain/list',
      array(
        'orderField'  => 'position',
        'orderType'   => 'asc',
        'maxPerPage'  => 0,
        'navTop'      => false,
        'navBottom'   => false
      ),
      dmDb::table('DmLayout')->findOneByName('Global')->getArea('left')->Zones[0]
    )->save();

    // domains link
    $this->createWidget(
      'dmWidgetContent/link',
      array('href' => 'page:'.dmDb::table('DmPage')->findOneByModuleAndAction('dmTestDomain', 'list')->id),
      dmDb::table('DmLayout')->findOneByName('Global')->getArea('left')->Zones[0]
    )->save();

    // tags link
    $this->createWidget(
      'dmWidgetContent/link',
      array('href' => 'page:'.dmDb::table('DmPage')->findOneByModuleAndAction('dmTestTag', 'list')->id),
      dmDb::table('DmLayout')->findOneByName('Global')->getArea('left')->Zones[0]
    )->save();

    // domains list
    $this->createWidget(
      'dmTestDomain/list',
      array(
        'orderField'  => 'position',
        'orderType'   => 'asc',
        'maxPerPage'  => 0,
        'navTop'      => true,
        'navBottom'   => true
      ),
      dmDb::table('DmPage')->findOneByModuleAndAction('dmTestDomain', 'list')->PageView->getArea('content')->Zones[0]
    )->save();

    $this->createWidget(
      'dmWidgetContent/title',
      array('text' => 'Domains', 'tag' => 'h1'),
      dmDb::table('DmPage')->findOneByModuleAndAction('dmTestDomain', 'list')->PageView->getArea('content')->Zones[0]
    )->save();

    $this->context->setPage($page = dmDb::table('DmPage')->findOneByModuleAndAction('dmTestDomain', 'show'));

    // categ list
    $this->createWidget(
      'dmTestCateg/listByDomain',
      array(
        'orderField'  => 'position',
        'orderType'   => 'asc',
        'maxPerPage'  => 0,
        'navTop'      => true,
        'navBottom'   => true
      ),
      $page->PageView->getArea('content')->Zones[0]
    )->save();
    
    // domain show
    $this->createWidget(
      'dmTestDomain/show',
      array(),
      $page->PageView->getArea('content')->Zones[0]
    )->save();

    $this->context->setPage($page = dmDb::table('DmPage')->findOneByModuleAndAction('dmTestCateg', 'show'));

    // post list
    $this->createWidget(
      'dmTestPost/listByCateg',
      array(
        'orderField'  => 'position',
        'orderType'   => 'asc',
        'maxPerPage'  => 0,
        'navTop'      => true,
        'navBottom'   => true
      ),
      $page->PageView->getArea('content')->Zones[0]
    )->save();
    
    // categ show
    $this->createWidget(
      'dmTestCateg/show',
      array(),
      $page->PageView->getArea('content')->Zones[0]
    )->save();

    $this->context->setPage($page = dmDb::table('DmPage')->findOneByModuleAndAction('dmTestPost', 'show'));

    // comment list
    $this->createWidget(
      'dmTestComment/listByPost',
      array(
        'orderField'  => 'created_at',
        'orderType'   => 'asc',
        'maxPerPage'  => 0,
        'navTop'      => true,
        'navBottom'   => true
      ),
      $page->PageView->getArea('content')->Zones[0]
    )->save();

    // comment form
    $this->createWidget(
      'dmTestComment/form',
      array(),
      $page->PageView->getArea('content')->Zones[0]
    )->save();

    // tag list
    $this->createWidget(
      'dmTestTag/listByPost',
      array(
        'orderField'  => 'created_at',
        'orderType'   => 'asc',
        'maxPerPage'  => 0,
        'navTop'      => true,
        'navBottom'   => true
      ),
      $page->PageView->getArea('content')->Zones[0]
    )->save();

    // post show
    $this->createWidget(
      'dmTestPost/show',
      array(),
      $page->PageView->getArea('content')->Zones[0]
    )->save();

    // tag list
    $this->createWidget(
      'dmTestTag/list',
      array(
        'orderField'  => 'created_at',
        'orderType'   => 'asc',
        'maxPerPage'  => 0,
        'navTop'      => true,
        'navBottom'   => true
      ),
      dmDb::table('DmPage')->findOneByModuleAndAction('dmTestTag', 'list')->PageView->getArea('content')->Zones[0]
    )->save();

    $this->context->setPage($page = dmDb::table('DmPage')->findOneByModuleAndAction('dmTestTag', 'show'));

    // post list
    $this->createWidget(
      'dmTestPost/listByTag',
      array(
        'orderField'  => 'position',
        'orderType'   => 'asc',
        'maxPerPage'  => 0,
        'navTop'      => true,
        'navBottom'   => true
      ),
      $page->PageView->getArea('content')->Zones[0]
    )->save();

    // tag show
    $this->createWidget(
      'dmTestTag/show',
      array(),
      $page->PageView->getArea('content')->Zones[0]
    )->save();

    // user list
    $this->createWidget(
      'dmUser/list',
      array(
        'orderField'  => 'created_at',
        'orderType'   => 'asc',
        'maxPerPage'  => 0,
        'navTop'      => true,
        'navBottom'   => true
      ),
      dmDb::table('DmPage')->findOneByModuleAndAction('dmUser', 'list')->PageView->getArea('content')->Zones[0]
    )->save();

    $this->context->setPage($page = dmDb::table('DmPage')->findOneByModuleAndAction('dmUser', 'show'));

    // user show
    $this->createWidget(
      'dmUser/show',
      array(),
      $page->PageView->getArea('content')->Zones[0]
    )->save();
    
    // dmTag list
    $this->createWidget(
      'dmTag/list',
      array(
        'orderField'  => 'name',
        'orderType'   => 'asc',
        'maxPerPage'  => 0,
        'navTop'      => true,
        'navBottom'   => true
      ),
      dmDb::table('DmPage')->findOneByModuleAndAction('dmTag', 'list')->PageView->getArea('content')->Zones[0]
    )->save();

    $this->context->setPage($page = dmDb::table('DmPage')->findOneByModuleAndAction('dmTag', 'show'));

    // dmTag show
    $this->createWidget(
      'dmTag/show',
      array(),
      $page->PageView->getArea('content')->Zones[0]
    )->save();

    // domain list
    $this->createWidget(
      'dmTestDomain/listByTag',
      array(
        'orderField'  => 'position',
        'orderType'   => 'asc',
        'maxPerPage'  => 0,
        'navTop'      => true,
        'navBottom'   => true
      ),
      $page->PageView->getArea('content')->Zones[0]
    )->save();
  }

  protected function loremize()
  {
    $this->context->get('project_loremizer')->execute(5);

    foreach(array('DmTestDomain' => 9, 'DmTestCateg' => 9, 'DmTestPost' => 19, 'DmTestTag' => 39, 'DmTestComment' => 39, 'DmTag' => 20) as $model => $nb)
    {
      $this->context->get('table_loremizer')->execute(dmDb::table($model), $nb);

      if(dmDb::table($model)->hasField('is_active'))
      {
        foreach(dmDb::table($model)->createQuery('r')->limit(ceil($nb/2))->fetchRecords() as $record)
        {
          if(!$record->isActive)
          {
            $record->isActive = true;
            $record->save();
          }
        }
      }
    }
  }

  protected function addBreadCrumb()
  {
    $this->createWidget(
      'dmWidgetNavigation/breadCrumb',
      array('includeCurrent' => true),
      dmDb::table('DmPage')->findOneByModuleAndAction('main', 'signin')->PageView->Layout->getArea('top')->Zones[0]
    )->save();
  }

  protected function addH1()
  {
    $this->createWidget(
      'dmWidgetContent/link',
      array('href' => 'page:1'),
      dmDb::table('DmPage')->findOneByModuleAndAction('main', 'signin')->PageView->Layout->getArea('top')->Zones[0]
    )->save();

    $this->createWidget(
      'dmWidgetContent/title',
      array('text' => 'Home H1', 'tag' => 'h1'),
      dmDb::table('DmPage')->findOneByModuleAndAction('main', 'root')->PageView->getArea('content')->Zones[0]
    )->save();
  }

  protected function addSigninForm()
  {
    $this->createWidget(
      'dmUser/signin',
      array(),
      dmDb::table('DmPage')->findOneByModuleAndAction('main', 'signin')->PageView->getArea('content')->Zones[0]
    )->save();
    
    $this->createWidget(
      'dmUser/form',
      array(),
      dmDb::table('DmPage')->findOneByModuleAndAction('main', 'signin')->PageView->getArea('content')->Zones[0]
    )->save();
  }

  protected function createWidget($moduleAction, array $data, DmZone $zone)
  {
    list($module, $action) = explode('/', $moduleAction);
    
    $widgetType = $this->context->get('widget_type_manager')->getWidgetType($module, $action);

    $formClass = $widgetType->getOption('form_class');
    $form = new $formClass(dmDb::create('DmWidget', array(
      'module' => $module,
      'action' => $action,
      'value'  => '[]',
      'dm_zone_id' => $zone->id
    )));
    $form->removeCsrfProtection();

    $form->bind(array_merge($form->getDefaults(), $data), array());

    if(!$form->isValid())
    {
      throw $form->getErrorSchema();
    }

    return $form->updateWidget();
  }

}