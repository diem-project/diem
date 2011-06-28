<?php
/**
 */
class PluginDmMediaFolderTable extends myDoctrineTable
{

  public function checkRoot()
  {
    if (!$root = $this->getTree()->fetchRoot())
    {
      $root = $this->create(array(
        'name' => 'Root',
        'rel_path' => ''
      ))->saveGet();

      $this->getTree()->createRoot($root);
    }
    
    return $root;
  }

  public function findOneByRelPathOrCreate($relPath)
  {
    if (!$record = $this->findOneByRelPath($relPath))
    {
      $parent = $this->findOneByRelPathOrCreate(trim(dirname($relPath), '/.'));

      $record = $this->create(array(
        'name'     => trim(basename($relPath), '/'),
        'rel_path' => $relPath
      ));
      
      $record->getNode()->insertAsLastChildOf($parent);
    }
    
    return $record;
  }

  /**
   * Performance shortcuts
   */

  public function findOneByRelPath($relPath)
  {
    return $this->createQuery('f')->where('f.rel_path = ?', $relPath)->fetchRecord();
  }

}