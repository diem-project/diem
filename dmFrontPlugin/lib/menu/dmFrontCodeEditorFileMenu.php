<?php

class dmFrontCodeEditorFileMenu extends dmMenu
{

  public function build()
  {
    $this
    ->name('Front code editor add menu')
    ->ulClass('clearfix level0')
    ->addFiles();

    $this->serviceContainer->get('dispatcher')->notify(new sfEvent($this, 'dm.front.code_editor_file_menu', array()));

    return $this;
  }

  protected function addFiles()
  {
    $controllers  = $this->addChild('Controllers')->ulClass('level1')->liClass('type');
    $templates    = $this->addChild('Templates')->ulClass('level1')->liClass('type');

    $moduleDirs = sfFinder::type('dir')->maxDepth(0)->in(sfConfig::get('sf_app_module_dir'));
    natcasesort($moduleDirs);
    
    foreach($moduleDirs as $moduleDir)
    {
      if(count($found = sfFinder::type('file')->in($moduleDir.'/actions')))
      {
        $moduleControllers = $controllers->addChild(basename($moduleDir))->ulClass('level2');

        natcasesort($found);
        foreach($found as $path)
        {
          $moduleControllers->addChild(basename($path), '#'.dmProject::unRootify($path));
        }
      }

      if(count($found = sfFinder::type('file')->in($moduleDir.'/templates')))
      {
        $moduleTemplates = $templates->addChild(basename($moduleDir))->ulClass('level2');

        natcasesort($found);
        foreach($found as $path)
        {
          $moduleTemplates->addChild(str_replace($moduleDir.'/templates/', '', $path), '#'.dmProject::unRootify($path));
        }
      }
    }

    $stylesheets = $this->addChild('Stylesheets')->ulClass('level1')->liClass('type')
    ->addChild($this->user->getTheme()->getName())->ulClass('level2');

    $cssDir = $this->user->getTheme()->getFullPath('css');
    $cssFiles = sfFinder::type('file')->name('*.css')->in($cssDir);
    natcasesort($cssFiles);
    
    foreach($cssFiles as $cssFile)
    {
      if (dmProject::isInProject($cssFile))
      {
        $stylesheets->addChild(str_replace($cssDir.'/', '', $cssFile), '#'.dmProject::unRootify($cssFile));
      }
    }
    
    return $this;
  }

  public function renderLabel()
  {
    return '<a>'.parent::renderLabel().'</a>';
  }
}