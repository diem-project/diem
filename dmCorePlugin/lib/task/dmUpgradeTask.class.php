<?php

/**
 * Install Diem
 */
class dmUpgradeTask extends dmContextTask
{
  protected
  $changes = array(
    'multipleAreas',
    'userUid'
  );
  
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();
    
    $this->namespace = 'dm';
    $this->name = 'upgrade';
    $this->briefDescription = 'Safely upgrade a project to the current Diem version. Can be run several times without side effect.';

    $this->detailedDescription = $this->briefDescription;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->withDatabase();
    
    $this->logSection('diem', 'Upgrade '.dmProject::getKey());
    
    foreach($this->changes as $change)
    {
      $upgradeMethod = 'upgradeTo'.ucfirst($change);
      
      try
      {
        $this->$upgradeMethod();
      }
      catch(Exception $e)
      {
        $this->logBlock('Can not upgrade to change '.$change.' : '.$e->getMessage(), 'ERROR');
      }
    }
  }

  /**
   * Populate the user uid if missing
   */
  protected function upgradeToUserUid()
  {
    $users = dmDb::query('DmUser u')
    ->where('u.uid IS NULL')
    ->fetchRecords();

    if(count($users))
    {
      $this->logSection('diem', 'Populate UID for '.count($users).' users.');
      foreach($users as $user)
      {
        $user->uid = dmString::random(8);
      }
      $users->save();
    }
  }

  /**
   * Allow multiple layout and page areas
   * Will upgrade templates in apps/front/modules/dmFront/templates/***Success.php
   */
  protected function upgradeToMultipleAreas()
  {
    $areaNameChanges = array(
      'top'     => 'layout.top',
      'left'    => 'layout.left',
      'right'   => 'layout.right',
      'bottom'  => 'layout.bottom',
      'content' => 'page.content'
    );

    $templates = array_unique(dmDb::query('DmLayout l')
    ->select('l.template')
    ->fetchFlat());
    
    foreach($templates as $template)
    {
      $file = dmOs::join(sfConfig::get('sf_apps_dir'), 'front/modules/dmFront/templates', $template.'Success.php');
      if(!$code = @file_get_contents($file))
      {
        continue;
      }
      $newCode = $code;
      foreach($areaNameChanges as $old => $new)
      {
        $newCode = str_replace("\$helper->renderArea('{$old}'", "\$helper->renderArea('{$new}'", $newCode);
      }
      if($newCode != $code)
      {
        if(is_writable($file))
        {
          $this->logSection('diem', 'Upgrade '.$file);
          file_put_contents($file, $newCode);
        }
        else
        {
          $this->logBlock('Can not upgrade '.$file.', unsuficient permissions', 'ERROR');
        }
      }
    }
  }
}