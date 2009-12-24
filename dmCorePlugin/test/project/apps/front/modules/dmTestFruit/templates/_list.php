<?php
// Dm test fruit : List
// Vars : $dmTestFruitPager

echo £o('div.dm_test_fruit.list');

 echo $dmTestFruitPager->renderNavigationTop();

  echo £o('ul.elements');

  foreach ($dmTestFruitPager as $dmTestFruit)
  {
    echo £o('li.element');
    
      echo $dmTestFruit;
      
    echo £c('li');
  }

  echo £c('ul');

 echo $dmTestFruitPager->renderNavigationBottom();

echo £c('div');