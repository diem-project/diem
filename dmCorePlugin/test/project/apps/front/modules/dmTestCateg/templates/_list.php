<?php
// Dm test categ : List
// Vars : $dmTestCategPager

echo £o('div.dm_test_categ.list');

 echo $dmTestCategPager->renderNavigationTop();

  echo £o('ul.elements');

  foreach ($dmTestCategPager as $dmTestCateg)
  {
    echo £o('li.element');
    
      echo £link($dmTestCateg);
      
    echo £c('li');
  }

  echo £c('ul');

 echo $dmTestCategPager->renderNavigationBottom();

echo £c('div');