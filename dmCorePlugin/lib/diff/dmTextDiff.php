<?php

/*
 * Inspired by sfSympalTextDiff
 */
class dmTextDiff
{

  public function __construct()
  {
    @$this->loadVendorLib();
  }
  
  public function generateHtml($from, $to)
  {
    return $this->renderHtml($this->generate($from, $to));
  }
  
  public function generate($from, $to)
  {
    return @new Text_Diff('auto', array(explode("\n", $from), explode("\n", $to)));
  }

  protected function renderHtml(Text_Diff $diff)
  {
    $renderer = new Text_Diff_Renderer_inline();
    
    return (string) @$renderer->render($diff);
  }
  
  protected function loadVendorLib()
  {
    $dir = dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/Text_Diff');
    
    set_include_path(get_include_path().PATH_SEPARATOR.$dir);
    
    require_once(dmOs::join($dir, 'Text/Diff.php'));
    
    require_once(dmOs::join($dir, 'Text/Diff/Renderer/inline.php'));
  }
}