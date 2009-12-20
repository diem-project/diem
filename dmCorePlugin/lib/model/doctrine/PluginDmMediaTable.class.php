<?php
/**
 */
class PluginDmMediaTable extends myDoctrineTable
{

  /*
   * Performance shortcuts
   */
  public function findOneByIdWithFolder($id)
  {
    if ($spacePos = strpos($id, ' '))
    {
      $id = substr($id, 0, $spacePos);
    }
    
    return $this->createQuery('m')
    ->where('m.id = ?', $id)
    ->leftJoin('m.Folder f')
    ->fetchOne();
  }


  public function findOneByFileAndDmMediaFolderId($file, $id)
  {
    return dmDb::query('DmMedia m')
    ->where('file = ? AND dm_media_folder_id = ?', array($file, $id))
    ->fetchRecord();
  }
}