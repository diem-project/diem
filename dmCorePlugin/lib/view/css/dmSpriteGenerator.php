<?php

class dmSpriteGenerator
{

	protected
	  $dispatcher,
	  $filesystem;

	public function __construct(sfEventDispatcher $dispatcher)
	{
		$this->filesystem = dmContext::getInstance()->getFilesystem();
	}

	public function execute($size, $css_file, $classes)
	{
    if(!$this->filesystem->touch($css_file))
    {
    	throw new dmException("$css_file is not writable");
    }
    
    $css = array();
    $pos = 0;
    foreach($classes as $class)
    {
    	$css[] = '.s'.$size.'_'.$class.' { background-position: 0 -'.$pos.'px; }';
      $pos += $size;
    }

    file_put_contents($css_file, implode("\n", $css));
	}

}