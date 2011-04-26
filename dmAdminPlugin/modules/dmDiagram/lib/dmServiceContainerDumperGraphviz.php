<?php

class dmServiceContainerDumperGraphviz extends sfServiceContainerDumperGraphviz
{
  protected
  $dispatcherLinksEnabled = true;
  
  public function enableDispatcherLinks($val)
  {
    $this->dispatcherLinksEnabled = (bool) $val;
  }
  
  protected function addEdges()
  {
    $code = '';
    foreach ($this->edges as $id => $edges)
    {
      foreach ($edges as $edge)
      {
        if (!$this->dispatcherLinksEnabled && $edge['to']->__toString() == 'dispatcher')
        {
          continue;
        }
        $code .= sprintf("  node_%s -> node_%s [label=\"%s\" style=\"%s\"];\n", $this->dotize($id), $this->dotize($edge['to']), $edge['name'], $edge['required'] ? 'filled' : 'dashed');
      }
    }

    return $code;
  }
}