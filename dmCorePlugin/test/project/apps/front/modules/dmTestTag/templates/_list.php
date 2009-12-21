<?php
// Dm test tag : List
// Vars : $dmTestTagPager

echo £o('div.dm_test_tag.list');

 echo $dmTestTagPager->renderNavigationTop();

  echo £o('ul.elements');

  foreach ($dmTestTagPager as $dmTestTag)
  {
    echo £o('li.element');
    
      echo £link($dmTestTag);
      
    echo £c('li');
  }

  echo £c('ul');

 echo $dmTestTagPager->renderNavigationBottom();

echo £c('div');