<?php
// Post : List
// Vars : $postPager

echo £o('div.post.list');

 echo $postPager->renderNavigationTop();

  echo £o('ul.elements');

  foreach ($postPager as $post)
  {
    echo £o('li.element');
    
      echo £link($post);
      
    echo £c('li');
  }

  echo £c('ul');

 echo $postPager->renderNavigationBottom();

echo £c('div');