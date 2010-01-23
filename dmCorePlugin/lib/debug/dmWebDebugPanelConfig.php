<?php

class dmWebDebugPanelConfig extends sfWebDebugPanelConfig
{
  
  public function getPanelContent()
  {
    $html = parent::getPanelContent();
    
    $html .= $this->formatArrayAsHtml('Diem', array(
      'version' => DIEM_VERSION,
      'path'    => dm::getDir(),
    ));

    return $html;
  }

  /**
   * Converts an array to HTML.
   *
   * @param string $id     The identifier to use
   * @param array  $values The array of values
   *
   * @return string An HTML string
   */
  protected function formatArrayAsHtml($id, $values)
  {
    $id = ucfirst(strtolower($id));

    return '
    <h2>'.$id.' '.$this->getToggler('sfWebDebug'.$id).'</h2>
    <div id="sfWebDebug'.$id.'" style="display: none"><pre>'.htmlspecialchars($this->dumpConfig(sfDebug::removeObjects($values)), ENT_QUOTES, sfConfig::get('sf_charset')).'</pre></div>
    ';
  }
  
  protected function dumpConfig(array $array)
  {
    if (sfConfig::get('dm_web_debug_config_fast_dump', true))
    {
      // generate html with fast print_r function
      $dumped = print_r($array, true);
      // reduce indentation
      $dumped = str_replace(array(str_repeat(' ', 8), str_repeat(' ', 4)), array('  ', ' '), $dumped);
      // replace "[key] =>" by "key:"
      // replace "[key] => Array" by "key:"
      $dumped = preg_replace('|\[(.+)\]\s=>(?:\sArray)?|', '$1:', $dumped);
      // remove empty arrays
      $dumped = preg_replace('|\n\s+\(\n\s+\)\n$|m', '', $dumped);
      // remove lines containing only an opening brace
      $dumped = preg_replace('|\n\s*\($|m', '', $dumped);
      // remove lines containing only a closing brace
      $dumped = preg_replace('|\n\s*\)\n$|m', '', $dumped);
      // remove first line containing only Array
      $dumped = substr($dumped, strpos($dumped, "\n"));
    }
    else
    {
      $dumped = sfYaml::dump($array);
    }
    
    return $dumped;
  }
}