<?php
// Comment : List by post
// Vars : $commentPager

echo £o('div.comment.list_by_post');

 echo $commentPager->renderNavigationTop();

  echo £o('ul.elements');

  foreach ($commentPager as $comment)
  {
    echo £o('li.element');
    
      echo $comment;
      
    echo £c('li');
  }

  echo £c('ul');

 echo $commentPager->renderNavigationBottom();

echo £c('div');