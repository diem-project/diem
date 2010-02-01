<?php
// Dm test tag : List
// Vars : $dmTestTagPager

echo _open('div.dm_test_tag.list');

 echo $dmTestTagPager->renderNavigationTop();

  echo _open('ul.elements');

  foreach ($dmTestTagPager as $dmTestTag)
  {
    echo _open('li.element');
    
      echo _link($dmTestTag);
      
    echo _close('li');
  }

  echo _close('ul');

 echo $dmTestTagPager->renderNavigationBottom();

echo _close('div');