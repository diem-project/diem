<?php // Vars: $dmTagPager

echo $dmTagPager->renderNavigationTop();

echo _open('ul.elements');

foreach ($dmTagPager as $dmTag)
{
  echo _open('li.element');

    echo _link($dmTag);

  echo _close('li');
}

echo _close('ul');

echo $dmTagPager->renderNavigationBottom();