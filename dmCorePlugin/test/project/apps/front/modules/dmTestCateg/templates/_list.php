<?php
// Dm test categ : List
// Vars : $dmTestCategPager

echo _open('div.dm_test_categ.list');

 echo $dmTestCategPager->renderNavigationTop();

  echo _open('ul.elements');

  foreach ($dmTestCategPager as $dmTestCateg)
  {
    echo _open('li.element');
    
      echo _link($dmTestCateg);
      
    echo _close('li');
  }

  echo _close('ul');

 echo $dmTestCategPager->renderNavigationBottom();

echo _close('div');