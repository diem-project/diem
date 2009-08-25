<?php

abstract class dmDoctrineCollection extends Doctrine_Collection
{

  /*
   * Return array representation of this collection
   *
   * @return array An array representation of the collection
   */
  public function toDebug()
  {
    return array(
      'class' => $this->getTable()->getComponentName(),
      'data' => $this->toArray()
    );
  }
}