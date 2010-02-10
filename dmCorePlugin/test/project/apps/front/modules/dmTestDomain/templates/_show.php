<?php
// Dm test domain : Show
// Vars : $dmTestDomain

echo _open('div.dm_test_domain.show');

  echo _tag('h1', $dmTestDomain);

  echo _open('ul');
  foreach($dmTestDomain->getTags() as $tag)
  {
    echo _tag('li', _link($tag));
  }
  echo _close('ul');

  echo _open('ul');
  foreach($dmTestDomain->getRelatedRecords() as $domain)
  {
    echo _tag('li', _link($domain));
  }
  echo _close('ul');
  
echo _close('div');