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
    ->where('m.id = ?', (int)$id)
    ->leftJoin('m.Folder f')
    ->fetchOne();
  }


  public function findOneByFileAndDmMediaFolderId($file, $id)
  {
    return dmDb::query('DmMedia m')
    ->where('file = ?', $file)
    ->andWhere('dm_media_folder_id = ?', $id)
    ->leftJoin('m.Folder f')
    ->fetchOne();
  }
}