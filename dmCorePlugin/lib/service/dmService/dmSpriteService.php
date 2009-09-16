<?php

class dmSpriteService extends dmService
{

	protected
	  $classes = array(
	    '16' => array(
'signout'
	    )
	  );

	public function execute()
  {
  	$sprite_classes = $this->getClasses();

    $sizes = array();
    $css_dir = dmOs::join(sfConfig::get('sf_web_dir'), sfConfig::get('dm_core_asset'), 'css');

    foreach($this->getClasses() as $size => $classes)
    {
      $this->log('generate sprite '.$size);

      $generator = new dmSpriteGenerator($this->dispatcher);
      $generator->execute(
        $size,
        dmOs::join($css_dir, 'sprite'.$size.'.css'),
        $classes
      );
    }
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

}