<?php
// Author : List
// Vars : $dmUserPager

echo _open('div.dm_user.list');

 echo $dmUserPager->renderNavigationTop();

  echo _open('ul.elements');

  foreach ($dmUserPager as $dmUser)
  {
    echo _open('li.element');
    
      echo _link($dmUser);
      
    echo _close('li');
  }

  echo _close('ul');

 echo $dmUserPager->renderNavigationBottom();

echo _close('div');