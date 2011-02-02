<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('front');

$b = $helper->getBrowser();

$b->get('/')
->checks(array(
  'code' => 200,
  'module_action' => 'dmFront/page',
  'h1' => 'Home H1'
))
->has('.navigation_bread_crumb')
->has('body.global_layout')
->has('h1:eq(0)')
->has('h1:eq(1)', false)
->has('.dm_test_domain.list li.element a:eq(0)')
->has('.dm_test_domain.list li.element a:eq(2)')
->has('.dm_test_domain.list li.element a:eq(121)', false)
->click('Dm test domains')
->checks(array('h1' => 'Domains'))
->click('Domain 1')
->checks(array('h1' => 'Domain 1'))
->click('Categ 1')
->checks(array('h1' => 'Categ 1'))
->click('Post 1')
->checks(array('h1' => 'Post 1'))
->has('p.user', 'admin')
->has('p.excerpt', 'Post 1 excerpt')
->has('div.body', 'Post 1 body')
->has('p.url', 'http://diem-project.org')
->has('p.categ', 'Categ 1')
->has('p.date', '2010-01-12')
->has('.dm_test_tag.list_by_post', 'Tag 1')
->has('.dm_test_comment.list_by_post p.author', 'Author 1')
->has('.dm_test_comment.list_by_post p.body', 'Comment 1')
->click('Tag 1')
->checks(array('h1' => 'Tag 1'))
->click('Post 1')
->checks(array('h1' => 'Post 1'))
->click('input.submit', array(), array('_with_csrf' => true))
->checks(array('method' => 'post', 'code' => 200))
->has('.error_list li', 'Required.')
->setField('dm_test_comment_form[author]', 'Author 2')
->setField('dm_test_comment_form[body]', 'Comment 2')
->click('input.submit', array(), array('_with_csrf' => true))
->checks(array('method' => 'post', 'code' => 302))
->redirect()
->checks(array('method' => 'get'))
->has('.dm_test_comment.list_by_post li.element:last .author', 'Author 2')
->has('.dm_test_comment.list_by_post li.element:last .body', 'Comment 2')
->get('/sitemap')
->checks()
->has('.main_sitemap .link:first', 'Home')
;