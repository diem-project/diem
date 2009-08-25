<?php
/**
 * sfCsvWriter
 * by Carlos Escribano <carlos@markhaus.com>
 *
 * Examples:
 *
 * $myExampleData = array(
 *   array('carlos', 'escribano', 'carlos@markhaus.com'),
 *   array('carlos2', 'escribano2', 'carlos@markhaus.com2')
 * );
 *
 * $csv = new sfCsvWriter();
 * foreach ($myExampleData as $row)
 * {
 *   $line = $csv->write($row); // "carlos","escribano","carlos@markhaus.com"
 *   ... // Do something like writing to an opened file.
 * }
 *
 * Also:
 * $delimiter = ';';
 * $enclosure = '"';
 * $csv = new sfCsvWriter($delimiter, $enclosure);
 *
 * CHARSET:
 * $to = 'ISO-8859-1';
 * $from = 'UTF-8';
 * $csv->setCharset($to);
 * $csv->setCharset($to, $from);
 */
class sfCsvWriter
{
  private
    $delimiter,
    $enclosure;
  
  private
    $limits,
    $limitCount;
  
  private
    $to,
    $from;
  
  function __construct($delimiter = ',', $enclosure = '"')
  {
    $this->delimiter = $delimiter;
    $this->enclosure = $enclosure;
    
    mb_detect_order('UTF-8,ISO-8859-1');
  }
  
  public function setColumnLimits($limits)
  {
    if (!is_array($limits))
    {
      $limits = (array) $limits;
    }
    
    $this->limits = $limits;
    $this->limitCount = count($limits);
  }
  
  public function write($row)
  {
    if (!is_array($row))
    {
      $row = (array) $row;
    }
    
    if ($this->limitCount)
    {
      $i = 0;
      foreach ($row as $k => $value)
      {
        if ($i <= $this->limitCount)
        {
          $row[$k] = str_pad(substr($value, 0, $this->limits[$i]), $this->limits[$i], STR_PAD_RIGHT);
          $i++;
        }
      }
    }
    
    $row = implode($this->enclosure.$this->delimiter.$this->enclosure, $row);
    $row = preg_replace("/\n|\r/", " ", $row);
    $row = preg_replace("/\s\s+/", " ", $row);
    if ($this->to !== null)
    {
      return $this->encode($this->enclosure.$row.$this->enclosure, $this->to, $this->from);
    }
    else
    {
      return $this->enclosure.$row.$this->enclosure;
    }
  }
  
  private function encode($str, $to, $from = null)
  {
    if ($from === null)
    {
      $from = mb_detect_encoding($str);
    }
    
    if (function_exists('iconv'))
    {
      return iconv($from, $to, $str);
    }
    else
    {
      return mb_convert_encoding($str, $to, $from);
    }
  }
  
  public function setDelimiter($chr = ',')
  {
    $this->delimiter = $chr;
  }
  
  public function setEnclosure($chr = '"')
  {
    $this->enclosure = $chr;
  }
  
  public function setCharset($to, $from = null)
  {
    $this->to = $to;
    $this->from = $from;
  }
}
