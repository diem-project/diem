<?php
// Dm test domain : List
// Vars : $dmTestDomainPager

echo _open('div.dm_test_domain.list');

 echo $dmTestDomainPager->renderNavigationTop();

  echo _open('ul.elements');

  foreach ($dmTestDomainPager as $dmTestDomain)
  {
    echo _open('li.element');
    
      echo _link($dmTestDomain);
      
    echo _close('li');
  }

  echo _close('ul');

 echo $dmTestDomainPager->renderNavigationBottom();

echo _close('div');