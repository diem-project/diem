<?php
// Author : List
// Vars : $dmUserPager

echo £o('div.dm_user.list');

 echo $dmUserPager->renderNavigationTop();

  echo £o('ul.elements');

  foreach ($dmUserPager as $dmUser)
  {
    echo £o('li.element');
    
      echo £link($dmUser);
      
    echo £c('li');
  }

  echo £c('ul');

 echo $dmUserPager->renderNavigationBottom();

echo £c('div');