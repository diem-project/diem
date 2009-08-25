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
		return $this->createQuery('m, m.Folder f')->where('m.id = ?', $id)->fetchOne();
	}


	public function findOneByFileAndDmMediaFolderId($file, $id)
	{
		return dmDb::query('DmMedia m')
		->where('file = ? AND dm_media_folder_id = ?', array($file, $id))
		->fetchRecord();
	}
}