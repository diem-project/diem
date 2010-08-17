<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

class DummyTestForm extends dmForm
{
  public function getStylesheets()
  {
    return array(
      'lib.ui-tabs',
      'lib.ui-core' => 'all'
    );
  }

  public function getJavascripts()
  {
    return array(
      'core.tabForm',
    );
  }
}

$t = new lime_test();

$form = new DummyTestForm();

dm::loadHelpers(array('Asset', 'Tag', 'Dm'));

$stylesheets = dm_get_stylesheets_for_form($form);

$t->like($stylesheets, '#'.preg_quote('/dmCorePlugin/lib/jquery-ui/css/jquery-ui-tabs.css', '#').'#');
$t->like($stylesheets, '#'.preg_quote('/fancyTheme/css/lib.ui-core.css', '#').'#');

$javascripts = dm_get_javascripts_for_form($form);

$t->like($javascripts, '#'.preg_quote('/dmCorePlugin/js/dmCoreTabForm.js', '#').'#');
