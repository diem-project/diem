<?php

$config = getcwd().'/config/ProjectConfiguration.class.php';

require_once $config;
require_once(dm::getDir().'/dmCorePlugin/test/unit/helper/dmUnitTestHelper.php');

$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test(50);

dm::loadHelpers(array('Dm', 'I18N'));

$menu = $helper->get('menu')->name('Test Menu');

$root1 = $menu->getChild('Root 1');
$root1->addChild('Child 1');
$last = $root1->addChild('Child 2');

$root2 = $menu->getChild('Root 2');
$child1 = $root2->addChild('Child 1');
$child2 = $child1->addChild('Child 2');

$t->is($root1->getLevel(), 0, 'Test root level is 0');
$t->is($root2->getLevel(), 0, 'Test root level is 0');
$t->is($child1->getLevel(), 1, 'Test level is 1');
$t->is($child2->getLevel(), 2, 'Test level is 2');
$t->is($child2->getPathAsString(), 'Test Menu > Root 2 > Child 1 > Child 2', 'Test getPathAsString()');
$t->is(get_class($root1), get_class($menu), 'Test children are created as same class as parent');

// array access
$t->is($menu['Root 1']['Child 1']->getName(), 'Child 1', 'Test name()');

// countable
$t->is(count($menu), $menu->count(), 'Test sfSympalMenu Countable interface');
$t->is(count($root1), 2, 'Test sfSympalMenu Countable interface');

$count = 0;
foreach ($root1 as $key => $value)
{
  $count++;
  $t->is($key, 'Child '.$count, 'Test iteratable');
  $t->is($value->getLabel(), 'Child '.$count, 'Test iteratable');
}

$new = $menu['Root 2'];
$t->is(get_class($new), get_class($menu), 'Test child is correct class type');
$new2 = $new['Root 3']['Child 1'];
$t->is((string) $new, '<ul><li class="first">Child 1<ul><li class="first last">Child 2</li></ul></li><li class="last">Root 3<ul><li class="first last">Child 1</li></ul></li></ul>', 'Test __toString()');

$menu['Test']['With Route']->link('http://www.google.com');
$t->is((string) $menu['Test'], '<ul><li class="first last"><a class="link" href="http://www.google.com">With Route</a></li></ul>', 'Test __toString()');
$menu['Test']['With Route']->getLink()->set('target', '_BLANK');
$t->is((string) $menu['Test'], '<ul><li class="first last"><a class="link" href="http://www.google.com" target="_blank">With Route</a></li></ul>', 'Test __toString()');

$menu['Test']->showId(true);
$menu['Test']['With Route']->secure(true)->showId(true);
$t->is((string) $menu['Test'], '', 'Test secure()');
$user = $helper->get('user');
$user->setAuthenticated(true);
$t->is($user->isAuthenticated(), true, 'Test isAuthenticated()');
$t->is($menu['Test']['With Route']->checkUserAccess($user), true, 'Test checkUserAccess()');
$t->is((string) $menu['Test'], '<ul id="test-menu"><li id="menu-test-menu-with-route" class="first last"><a class="link" href="http://www.google.com" target="_blank">With Route</a></li></ul>', 'Test authentication');

$t->comment('test notAuthenticated');
$menu['Test']['With Route']->secure(false)->notAuthenticated(true);
$t->is($menu['Test']['With Route']->checkUserAccess($user), false, 'Test checkUserAccess()');
$user->setAuthenticated(false);
$t->is($menu['Test']['With Route']->checkUserAccess($user), true, 'Test checkUserAccess()');

$t->comment('misc tests');
$t->is($menu->getLevel(), -1, 'Test getLevel()');
$t->is($menu['Test']['With Route']->getParent()->getLabel(), $menu['Test']->getLabel(), 'Test getLabel()');

$menu['Root 4']['Test'];
$t->is($menu['Root 4']->toArray(), array(
  'name' => 'Root 4',
  'level' => 0,
  'options' => array(
    'ul_class' => NULL,
    'li_class' => NULL,
    'show_id' => false,
    'show_children' => true,
    'translate' => true
  ),
  'children' => array(
    'Test' => array(
      'name' => 'Test',
      'level' => 1,
      'options' => array(
        'ul_class' => NULL,
        'li_class' => NULL,
        'show_id' => false,
        'show_children' => true,
        'translate' => true
      ),
      'children' => array()
    )
  )
), 'Test toArray()');

$test = $helper->get('menu')->name('Test');
$test->fromArray($menu['Root 4']->toArray());
$t->is($test->toArray(), $menu['Root 4']->toArray(), 'Test fromArray()');
$t->is($menu['Root 4']['Test']->getPathAsString(), 'Test Menu > Root 4 > Test', 'Test getPathAsString()');
$t->is($menu->getFirstChild()->getName(), 'Root 1', 'Test getFirstChild()');
$t->is($menu->getLastChild()->getName(), 'Root 4', 'Test getLastChild()');

$menu = $helper->get('menu')->name('Test Menu');
$root1 = $menu->getChild('Root 1');
$first = $root1->addChild('Child 1');
$middle = $root1->addChild('Child 2');
$last = $root1->addChild('Child 3');

$t->is($first->isFirst(), true, 'Test isFirst()');
$t->is($last->isLast(), true, 'Test isLast()');
$t->is($middle->isFirst(), false, 'Test isFirst()');
$t->is($middle->isLast(), false, 'Test isLast()');
$t->is($first->getNum(), 1, 'Test getNum()');
$t->is($middle->getNum(), 2, 'Test getNum()');
$t->is($last->getNum(), 3, 'Test getNum()');

class dmMyMenu extends dmMenu
{
  
}

$menu = $helper->get('menu', 'dmMyMenu')->name('My menu');

$t->isa_ok($menu, 'dmMyMenu', 'Got a dmMyMenu instance');

$menu->addChild('Home', '@homepage')->end()
->addChild('Sites')->ulClass('my_ul_class')
->addChild('Diem', 'http://diem-project.org')->showId(true)->end()
->addChild('Symfony', 'http://symfony-project.org')->end();

$html = _tag('ul',
  _tag('li.first', _link('@homepage')->text($helper->get('i18n')->__('Home'))).
  _tag('li.last',
    'Sites'.
    _tag('ul.my_ul_class',
      _tag('li#menu-my-menu-diem.first', _link('http://diem-project.org')->text('Diem')).
      _tag('li.last', _link('http://symfony-project.org')->text('Symfony'))
    )
  )
);

$t->is($menu->render(), $html, $html);

$t->comment('Test getRoot');

$t->is($menu['Home']->getRoot(), $menu, 'Home root is $menu');
$t->is($menu['Sites']['Diem']->getRoot(), $menu, 'Diem root is menu');

$sitemap = $helper->get('sitemap_menu')->build();

$t->isa_ok($sitemap, 'dmSitemapMenu', 'Got a dmSitemapMenu');

$t->is($sitemap->getFirstChild()->renderLink(), (string)_link(), 'Sitemap first child is Home');

$t->like((string)$sitemap, '|^'.preg_quote('<ul><li class="first last"><a class="link" href="', '|').'.*|', 'Sitemap html is valid');

$t->comment('Test current page');

$homePage = dmDb::table('DmPage')->getTree()->fetchRoot();

$helper->getContext()->setPage($homePage);

$menu = $helper->get('menu')->addChild('Home', '@homepage')->end();
$html = _tag('ul', _tag('li.first.last.dm_current', _link()->text($helper->get('i18n')->__('Home'))));

$t->is($menu->render(), $html, 'Current li has the dm_current class');

$helper->getContext()->setPage(dmDb::table('DmPage')->findOneByModuleAndAction('main', 'signin'));

$menu = $helper->get('menu')->addChild('Home', '@homepage')->end();
$html = _tag('ul', _tag('li.first.last.dm_parent', _link()->text($helper->get('i18n')->__('Home'))));

$t->is($menu->render(), $html, 'Parent li has the dm_parent class');

$menu = $helper->get('menu')
->addChild('Home')->end()
->addChild('Sites')
  ->addChild('Diem')->end()
  ->addChild('Symfony')->end()
->end();

$html = _tag('ul',
  _tag('li.first', 'Home').
  _tag('li.last',
    'Sites'.
    _tag('ul',
      _tag('li.first', 'Diem').
      _tag('li.last', 'Symfony')
    )
  )
);

$t->is($menu->render(), $html, $html);

$t->comment('->getSiblings()');

$t->is_deeply($menu['Home']->getSiblings(), array('Sites' => $menu['Sites']), '->getSiblings() works');

$t->is_deeply($menu['Home']->getSiblings(true), array('Home' => $menu['Home'], 'Sites' => $menu['Sites']), '->getSiblings(true) works');

$t->comment('Move menus');

$menu['Home']->moveToLast();

$menu['Sites']['Symfony']->moveToFirst();

$html = _tag('ul',
  _tag('li.first',
    'Sites'.
    _tag('ul',
      _tag('li.first', 'Symfony').
      _tag('li.last', 'Diem')
    )
  ).
  _tag('li.last', 'Home')
);

$t->is($menu->render(), $html, $html);