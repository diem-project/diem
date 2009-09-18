<?php

abstract class dmRecursivePageListOrig extends RecursiveIteratorIterator
{

  protected
  $lastDepth = -1,
  $options,
  $html;

  protected abstract function getPageLink(DmPage $page, $class);

  public function render($options = array())
  {
    $this->options = dmString::toArray($options, true);

    $this->html = '<ul class="'.dmArray::get($this->options, 'class').'">';

    foreach ($this as $item)
    {
      $this->html .= $item;
    }

    $this->html .= "</li>".str_repeat("</ul>\n</li>\n", $this->getLastDepth())."</ul>\n";

    return $this->html;
  }

  public function getLastDepth()
  {
    return $this->lastDepth;
  }

  public function beginChildren()
  {
    $this->html .= "<ul>";
  }

  public function endChildren()
  {
    //echo "\n</li></ul>";
  }

  public function nextElement()
  {
  }

  public function current()
  {
    $current = '';
    $page = parent::current();

    if ($this->getDepth() === $this->lastDepth)
    {
      $current .= "</li>";
    }
    elseif($this->getDepth() < $this->lastDepth)
    {
      $current .= "</li>";
      $current .= str_repeat("</ul></li>", max(0, $this->lastDepth - $this->getDepth() ));
    }
//    else
//    {
//      $current .= '<ul>';
//    }

    $class = sprintf(
      '{ id: %s }',
      $page->getId()
    );

    if ($this->lastDepth === -1)
    {
      $class .= " ".dmArray::get($this->options, 'root_class');
    }

    $current .= $this->getPageLink($page, $class);

    $this->lastDepth = $this->getDepth();

    return $current;
  }
}