<?php
// Dm test fruit : List
// Vars : $dmTestFruitPager

echo _open('div.dm_test_fruit.list');

 echo $dmTestFruitPager->renderNavigationTop();

  echo _open('ul.elements');

  foreach ($dmTestFruitPager as $dmTestFruit)
  {
    echo _open('li.element');
    
      echo $dmTestFruit;
      
    echo _close('li');
  }

  echo _close('ul');

 echo $dmTestFruitPager->renderNavigationBottom();

echo _close('div');