<?php // Vars: $dmTestDomainPager

echo $dmTestDomainPager->renderNavigationTop();

echo _open('ul.elements');

foreach ($dmTestDomainPager as $dmTestDomain)
{
  echo _open('li.element');

    echo _link($dmTestDomain);

  echo _close('li');
}

echo _close('ul');

echo $dmTestDomainPager->renderNavigationBottom();