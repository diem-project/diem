<?php

/**
 * Install Diem
 */
class dmSpriteTask extends dmContextTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();
    $this->namespace = 'dm';
    $this->name = 'sprite';
    $this->briefDescription = 'Build css sprites';

    $this->detailedDescription = <<<EOF
Will build css sprites for diem core
EOF;
  }

  protected function getClasses()
  {
    $classes = array();
    $files = sfFinder::type('file')
    ->name('[0-9]*')
    ->in(dmOs::join(sfConfig::get('dm_core_dir'), 'data/sprites'));
    foreach($files as $file)
    {
      $file_classes = file($file);
      array_walk($file_classes, create_function('&$a', '$a = str_replace("\n", "", $a);'));
      $classes[basename($file)] = $file_classes;
    }
    return $classes;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $sizes = array();
    $cssDir = dmOs::join(sfConfig::get('sf_web_dir'), sfConfig::get('dm_core_asset'), 'css');

    foreach($this->getClasses() as $size => $classes)
    {
      $this->log('generate sprite '.$size);

      $this->writeSprite($size, dmOs::join($cssDir, 'sprite'.$size.'.css'), $classes);
    }
  }
  
  protected function writeSprite($size, $cssFile, $classes)
  {
    if(!$this->get('filesystem')->touch($cssFile))
    {
      throw new dmException("$cssFile is not writable");
    }
    
    $css = array();
    $pos = 0;
    foreach($classes as $class)
    {
      $css[] = '.s'.$size.'_'.$class.'{background-position:0 -'.$pos.'px;}';
      $pos += $size;
    }

    file_put_contents($cssFile, implode("\n", $css));
  }
}
