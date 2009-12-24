<?php
// Dm test domain : List
// Vars : $dmTestDomainPager

echo £o('div.dm_test_domain.list');

 echo $dmTestDomainPager->renderNavigationTop();

  echo £o('ul.elements');

  foreach ($dmTestDomainPager as $dmTestDomain)
  {
    echo £o('li.element');
    
      echo £link($dmTestDomain);
      
    echo £c('li');
  }

  echo £c('ul');

 echo $dmTestDomainPager->renderNavigationBottom();

echo £c('div');