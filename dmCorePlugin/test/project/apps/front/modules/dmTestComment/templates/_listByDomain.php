<?php
// Dm test comment : List by domain
// Vars : $dmTestCommentPager

echo £o('div.dm_test_comment.list_by_domain');

 echo $dmTestCommentPager->renderNavigationTop();

  echo £o('ul.elements');

  foreach ($dmTestCommentPager as $dmTestComment)
  {
    echo £o('li.element');
    
      echo $dmTestComment;
      
    echo £c('li');
  }

  echo £c('ul');

 echo $dmTestCommentPager->renderNavigationBottom();

echo £c('div');