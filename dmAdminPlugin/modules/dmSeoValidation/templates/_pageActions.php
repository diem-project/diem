<?php

$module = $page->getDmModule();

echo £('p.type', sprintf('%s : %s',
  $page->getIsAutomatic() ? __('Automatic page') : __('Manual page'),
  $page->getModuleAction()
));

echo £o('ul');

if ($page->getIsAutomatic())
{
	$record = $page->getRecord();
  echo £('li', £link($record)->text(sprintf('%s "%s" (%s)',
    __('Modify record'),
    $record,
    $module->getName()
  )));

  if ($autoSeo = $page->getDmAutoSeo())
  {
    echo £('li', £link(array('sf_route' => 'dm_auto_seo_edit', 'sf_subject' => $autoSeo))->text(__('Configure automatic seo for %1% pages', array('%1%' => $page->getModuleAction()))));
  }

  echo £('li', £link($page)->text(__('View page on website')));
}

echo £c('ul');