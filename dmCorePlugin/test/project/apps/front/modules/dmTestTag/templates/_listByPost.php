<?php
// Dm test tag : List by post
// Vars : $dmTestTagPager

echo £o('div.dm_test_tag.list_by_post');

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