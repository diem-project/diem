<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

/**
 * Doctrine_Template_Sortable
 *
 * @package     Doctrine
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.phpdoctrine.org
 * @since       1.2
 * @version     $Revision$
 */
class Doctrine_Template_Sortable extends Doctrine_Template
{
  protected $_options = array('manyListsColumn' => null);

  public function setTableDefinition()
  {
    $this->hasColumn('position', 'integer');
    $this->addListener(new Doctrine_Template_Listener_Sortable($this->_options));
  }

  public function getPrevious()
  {
    return $this->getPreviousQuery()->fetchOne();
  }
  
  public function getPreviousQuery($rootAlias = 'r')
  {
    $many = $this->_options['manyListsColumn'];
    
    $q = $this->getInvoker()->getTable()->createQuery($rootAlias)
//    ->whereIsActive()
    ->addWhere($rootAlias.'.position < ?', $this->getInvoker()->get('position'))
    ->orderBy($rootAlias.'.position DESC');
    
    if (!empty($many))
    {
      $q->addWhere($many . ' = ?', $this->getInvoker()->get($many));
    }
    
    return $q;
  }

  public function getNext()
  {
    return $this->getNextQuery()->fetchOne();
  }
  
  public function getNextQuery($rootAlias = 'r')
  {
    $many = $this->_options['manyListsColumn'];
    
    $q = $this->getInvoker()->getTable()->createQuery($rootAlias)
//    ->whereIsActive()
    ->addWhere($rootAlias.'.position > ?', $this->getInvoker()->get('position'))
    ->orderBy($rootAlias.'.position ASC');
    
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