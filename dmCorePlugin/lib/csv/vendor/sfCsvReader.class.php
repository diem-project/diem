<?php
/**
 * sfCsvReader
 * by Carlos Escribano <carlos@markhaus.com>
 *
 * $csv = new sfCsvReader("/path/to/a/csv/file.csv");
 * $csv->open();
 * $csv->setSelectColumns('field_A, field_B'); // or array('field_A', 'field_B')
 * while ($data = $csv->read())
 * {
 *   // do something with $data['field_A'] and $data['field_B']
 * }
 * $csv->close();
 *
 * --- Alternative ---
 * $csv = new sfCsvReader("/path/to/a/csv/file.csv");
 * $csv->setSelectColumns('field_A, field_B'); // or array('field_A', 'field_B')
 * $csv->open();
 * ...
 * --- Alternative: NUMERICAL INDEXES ---
 * $csv = new sfCsvReader("/path/to/a/csv/file.csv");
 * $csv->open();
 * while ($data = $csv->read())
 * {
 *   // do something with $data[0] and $data[1]
 * }
 * $csv->close();
 * 
 *
 * --- CHARSET ---
 * $to = 'ISO-8859-1';
 * $from = 'UTF-8';
 * $csv->setCharset($to);
 * $csv->setCharset($to, $from);   
 */
class sfCsvReader
{
  private
    $header;
    
  private
    $file,
    $path,
    $initialized;
    
  private
    $length,
    $delimiter,
    $enclosure,
    $to,
    $from;
  
  /**
   * @param string  $path      Path to the file to read
   * @param char    $delimiter Character delimiting fields    (Optional)
   * @param char    $enclosure Character enclosing fields     (Optional)
   * @param integer $length    PHP's fgetcsv length parameter (Optional)
   */
  function __construct($path, $delimiter = ',', $enclosure = '"', $length = null)
  {
    $this->path      = $path;
    $this->length    = $length;
    $this->delimiter = $delimiter;
    $this->enclosure = $enclosure;
    
    $this->header = array(
      'map'      => null,
      'selected' => array()
    );
    
    $this->initialized = false;
  }
  
  public function close()
  {
    if ($this->file !== null)
    {
      fclose($this->file);
      $this->file = null;
      $this->initialized = false;
    }
  }
  
  public function open($path = null)
  {
    $this->close();
    
    if ($path !== null)
    {
      $this->path = $path;
    }
    
    if (!($this->file = fopen($this->path, "r")))
    {
      throw new Exception("File can not be opened (".$this->path.").");
    }
    
    $this->initialized = true;
    $this->map();
  }
  
  public function setSelectColumns($selected = array())
  {
    $this->test = true;
    
    if (!is_array($selected))
    {
      $selected = explode(',', preg_replace('/\s+/', '', $selected));
    }
    
    $this->header['selected'] = $selected;
    $this->map();
  }
  
  public function clearSelectColumns()
  {
    $this->header['selected'] = array();
    $this->map();
  }
  
  private function map()
  {
    if ($this->initialized)
    {
      $this->all = false;
      
      $x = count($this->header['selected']); // N. of selected columns
      $y = 0;                                // N. of real columns
      $z = 0;                                // N. of matching columns
      
      if ($x == 0)
      { // No selection. All fields.
        $this->header['map'] = null;
      }
      else
      {
        $this->header['map'] = array();
        
        fseek($this->file, 0, SEEK_SET);
        
        if ($line = fgetcsv($this->file, $this->length, $this->delimiter, $this->enclosure))
        {
          $y = count($line);
          
          if ($y > 0)
          {
            $common = array_intersect($line, $this->header['selected']);
            
            $z = count($common);
            
            if (($y < $x) || (($x > $z) && ($z > 0)))
            { // More columns in selection than in file or less common columns than selection
              throw new Exception("Too much columns or non existing columns in selection (LINE: $y, SEL: $x, COMMON: $z).");
            }
            
            if ($z == 0)
            { // Relaxed Mapping: 0 matches found, numerical.
              foreach ($this->header['selected'] as $i => $name)
              {
                $this->header['map'][$name] = $i;
              }
              
              fseek($this->file, 0, SEEK_SET);
            }
            else if ($z == $x)
            { // Absolute Mapping: First line is header.
              foreach ($line as $i => $name)
              {
                $this->header['map'][$name] = $i;
              }
            }
          } // Has columns
        } // Read line
      } // N columns selected
    } // Initialized
  }
  
  public function read()
  {
    if (!$this->initialized)
    {
      throw new Exception('sfCsvReader is not ready.');
    }
    
    if ($line = fgetcsv($this->file, $this->length, $this->delimiter, $this->enclosure))
    {
      if (is_array($this->header['map']))
      {
        $res = array();
        foreach ($this->header['selected'] as $name)
        {
          if ($this->to !== null)
          {
            $res[$name] = $this->encode($line[$this->header['map'][$name]], $this->to, $this->from);
          }
          else
          {
            $res[$name] = $line[$this->header['map'][$name]];
          }
        }
        return $res;
      }
      else
      {
        return $line;
      }
    }
    else
    {
      return null;
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
  
  public function setCharset($to, $from = null)
  {
    $this->to = $to;
    $this->from = $from;
  }
  
  function __destruct()
  {
    $this->close();
  }
}
