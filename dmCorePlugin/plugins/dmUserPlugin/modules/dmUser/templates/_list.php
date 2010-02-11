<?php // Vars: $articlePager

echo $dmUserPager->renderNavigationTop();

echo _open('ul.elements');

foreach ($dmUserPager as $dmUser)
{
  echo _open('li.element');

    echo $dmUser->username;

  echo _close('li');
}

echo _close('ul');

echo $dmUserPager->renderNavigationBottom();