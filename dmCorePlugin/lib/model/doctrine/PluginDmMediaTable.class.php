<?php
/**
 */
class PluginDmMediaTable extends myDoctrineTable
{

  public function getIdentifierColumnName()
  {
    return 'file';
  }

  /**
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
    return $this->createQuery('m')
    ->where('m.file = ?', $file)
    ->andWhere('m.dm_media_folder_id = ?', $id)
    ->leftJoin('m.Folder f')
    ->fetchOne();
  }

  public function findOneByRelPath($relPath)
  {
    $dirName = dirname($relPath);
    $fileName = basename($relPath);

    return $this->createQuery('m')
    ->innerJoin('m.Folder f')
    ->where('m.file = ?', $fileName)
    ->andWhere('f.rel_path = ?', $dirName)
    ->fetchOne();
  }

  public function findByFolderRelPath($folderRelPath)
  {
    return $this->createQuery('m')
    ->innerJoin('m.Folder f')
    ->where('f.rel_path = ?', $folderRelPath)
    ->fetchRecords();
  }
}