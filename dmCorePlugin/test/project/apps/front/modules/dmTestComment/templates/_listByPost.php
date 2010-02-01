<?php
// Dm test comment : List by post
// Vars : $dmTestCommentPager

echo _open('div.dm_test_comment.list_by_post');

 echo $dmTestCommentPager->renderNavigationTop();

  echo _open('ul.elements');

  foreach ($dmTestCommentPager as $dmTestComment)
  {
    echo _open('li.element');
    
      echo
      _tag('p.author', $dmTestComment->author).
      _tag('p.body', $dmTestComment->body);
      
    echo _close('li');
  }

  echo _close('ul');

 echo $dmTestCommentPager->renderNavigationBottom();

echo _close('div');