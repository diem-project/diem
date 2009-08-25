<?php
/**
 * sfCsvPropelWriter
 * by Carlos Escribano <carlos@markhaus.com>
 *
 * Examples:
 *
 * $c = new Criteria();
 * $c->clearSelectColumns()->addSelectColumn(PersonPeer::ID)->addSelectColumn(PersonPeer::NAME);
 *
 * $csv = new sfCsvPropelWriter('Person', $c);
 * $csv->getWriter()->setCharset('ISO-8859-1', 'UTF-8'); // TO <- FROM
 *
 * $header = $csv->getHeader(); // "id","name"
 * while ($data = $csv->write())
 * {
 *   ... // $data -> "1","carlos"
 * }
 *
 */
class sfCsvPropelWriter
{
  private
    $className,
    $peerName,
    $criteria,
    $selectMethod,
    $writeMethod;
  
  private
    $fields,
    $columns,
    $attributes;
  
  private
    $rs,
    $hd,
    $writer;
  
  function __construct($className, $criteria = null, $delimiter = ',', $enclosure = '"')
  {
    $this->writer = new sfCsvWriter($delimiter, $enclosure);
    
    $this->className  = sfInflector::camelize($className);
    $this->criteria   = $criteria;
    $this->peerName   = $this->className."Peer";
    
    $this->fields     = call_user_func(array($this->peerName, 'getFieldNames'), BasePeer::TYPE_FIELDNAME); // id, nombre
    $this->columns    = call_user_func(array($this->peerName, 'getFieldNames'), BasePeer::TYPE_COLNAME);   // tabla.ID, tabla.NOMBRE
    
    if (is_callable(array($this->peerName, 'doSelectRS')))
    {
      $this->selectMethod = 'doSelectRS';
      $this->writeMethod  = 'writeRS';
    }
    else if (is_callable(array($this->peerName, 'doSelectStmt')))
    {
      $this->selectMethod = 'doSelectStmt';
      $this->writeMethod  = 'writeStmt';
    }
    else
    {
      throw new Exception("Propel selection method could not be found!!!");
    }
    
    if (!($this->criteria instanceof Criteria))
    {
      $this->criteria = new Criteria();
    }
    
    $this->rs = call_user_func(array($this->peerName, $this->selectMethod), $this->criteria);
    
    if ($this->selectMethod == 'doSelectRS')
    {
      $this->rs->setFetchMode(ResultSet::FETCHMODE_ASSOC);
    }
    
    if (count($cols = $this->criteria->getSelectColumns()))
    {
      $this->hd = array();
      foreach ($cols as $i => $colName)
      {
        $this->hd[] = $this->fields[array_search($colName, $this->columns)];
      }
    }
    else
    {
      $this->hd = $this->fields;
    }
  }
  
  public function getHeader()
  {
    return $this->writer->write($this->hd);
  }
  
  private function writeRS($to, $from)
  {
    if ($this->rs->next())
    {
      return $this->writer->write($this->rs->getRow());
    }
    else
    {
      return null;
    }
  }
  
  private function writeStmt($to, $from)
  {
    if ($row = $this->rs->fetch(PDO::FETCH_NUM))
    {
      return $this->writer->write(array_values($row));
    }
    else
    {
      return null;
    }
  }
  
  public function write($to = null, $from = null)
  {
    $m = $this->writeMethod;
    return $this->$m($to, $from);
  }
  
  public function getWriter()
  {
    return $this->writer;
  }
}
