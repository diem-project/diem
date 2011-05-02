<?php

class Doctrine_Template_Listener_DmSortable extends Doctrine_Record_Listener
{

  public function __construct($options = array())
  {
    $this->_options = array_merge(array('new' => 'first'), $this->_options, $options);
    
    if (!in_array($this->_options['new'], array('first', 'last')))
    {
      throw new dmException($this->_options['new'].' is not a valid new record insertion method ( first, last )');
    }
  }

  public function postInsert(Doctrine_Event $event)
  {
    if (is_null($event->getInvoker()->get('position')))
    {
      if ('first' === $this->_options['new'])
      {
        $position = $event->getInvoker()->getTable()->createQuery('r')
        ->select('MIN(r.position)')
        ->fetchValue()
        - 1;
      }
      else
      {
        $position = $event->getInvoker()->get('id');
      }
    }
    else
    {
      $position = $event->getInvoker()->get('position');
    }
    
    $event->getInvoker()->set('position', $position);
    $event->getInvoker()->save();
  }
}