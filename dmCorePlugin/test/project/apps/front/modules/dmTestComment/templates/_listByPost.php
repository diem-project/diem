<?php
// Dm test comment : List by post
// Vars : $dmTestCommentPager

echo £o('div.dm_test_comment.list_by_post');

 echo $dmTestCommentPager->renderNavigationTop();

  echo £o('ul.elements');

  foreach ($dmTestCommentPager as $dmTestComment)
  {
    echo £o('li.element');
    
      echo
      £('p.author', $dmTestComment->author).
      £('p.body', $dmTestComment->body);
      
    echo £c('li');
  }

  echo £c('ul');

 echo $dmTestCommentPager->renderNavigationBottom();

echo £c('div');