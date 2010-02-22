<?php

class Doctrine_Template_DmSortable extends Doctrine_Template
{
  protected $_options = array('manyListsColumn' => null);

  public function setTableDefinition()
  {
    $this->hasColumn('position', 'integer');
    $this->addListener(new Doctrine_Template_Listener_DmSortable($this->_options));
  }

  public function getPrevious($onlyActive = true)
  {
    return $this->getPreviousQuery('r', $onlyActive)->fetchOne();
  }
  
  public function getPreviousQuery($rootAlias = 'r', $onlyActive = true)
  {
    $many = $this->_options['manyListsColumn'];
    
    $q = $this->getInvoker()->getTable()->createQuery($rootAlias)
    ->addWhere($rootAlias.'.position < ?', $this->getInvoker()->get('position'))
    ->orderBy($rootAlias.'.position DESC');
    
    if ($onlyActive)
    {
      $q->whereIsActive(true, $this->getInvoker()->getTable()->getComponentName());
    }
    
    if (!empty($many))
    {
      $q->addWhere($many . ' = ?', $this->getInvoker()->get($many));
    }
    
    return $q;
  }

  public function getNext($onlyActive = true)
  {
    return $this->getNextQuery('r', $onlyActive)->fetchOne();
  }
  
  public function getNextQuery($rootAlias = 'r', $onlyActive = true)
  {
    $many = $this->_options['manyListsColumn'];
    
    $q = $this->getInvoker()->getTable()->createQuery($rootAlias)
    ->addWhere($rootAlias.'.position > ?', $this->getInvoker()->get('position'))
    ->orderBy($rootAlias.'.position ASC');
    
    if ($onlyActive)
    {
      $q->whereIsActive(true, $this->getInvoker()->getTable()->getComponentName());
    }
    
    if (!empty($many))
    {
      $q->addWhere($many . ' = ?', $this->getInvoker()->get($many));
    }
    
    return $q;
  }

  public function swapWith(Doctrine_Record $record2)
  {
    $record1 = $this->getInvoker();

    $many = $this->_options['manyListsColumn'];
    if (!empty($many)) {
      if ($record1->$many != $record2->$many) {
        throw new Doctrine_Record_Exception('Cannot swap items from different lists.');
      }
    }

    $conn = $this->getTable()->getConnection();
    $conn->beginTransaction();

    $pos1 = $record1->position;
    $pos2 = $record2->position;
    $record1->position = $pos2;
    $record2->position = $pos1;
    $record1->save();
    $record2->save();

    $conn->commit();
  }

  public function moveUp()
  {
    $prev = $this->getInvoker()->getPrevious();
    if ($prev) {
      $this->getInvoker()->swapWith($prev);
    }
  }

  public function moveDown()
  {
    $next = $this->getInvoker()->getNext();
    if ($next) {
      $this->getInvoker()->swapWith($next);
    }
  }
}