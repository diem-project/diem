<?php

$module = $page->getDmModule();

echo _tag('p.type', sprintf('%s : %s',
  $page->getIsAutomatic() ? __('Automatic page') : __('Manual page'),
  $page->getModuleAction()
));

echo _open('ul');

if ($page->getIsAutomatic())
{
  $record = $page->getRecord();
  echo _tag('li', _link($record)->text(sprintf('%s "%s" (%s)',
    __('Modify record'),
    $record,
    $module->getName()
  )));

  if ($autoSeo = $page->getDmAutoSeo())
  {
    echo _tag('li', _link(array('sf_route' => 'dm_auto_seo_edit', 'sf_subject' => $autoSeo))->text(__('Configure automatic seo for %1% pages', array('%1%' => $page->getModuleAction()))));
  }

  echo _tag('li', _link($page)->text(__('View page on website')));
}

echo _close('ul');