<?php
// Auteur : List
// Vars : $auteurPager

echo £o('div.auteur.list');

 echo $auteurPager->renderNavigationTop();

  echo £o('ul.elements');

  foreach ($auteurPager as $auteur)
  {
    echo £o('li.element');
    
      echo £link($auteur);
      
    echo £c('li');
  }

  echo £c('ul');

 echo $auteurPager->renderNavigationBottom();

echo £c('div');