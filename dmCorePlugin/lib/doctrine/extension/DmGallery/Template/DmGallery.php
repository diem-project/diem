<?php

/**
 * Add gallery capabilities to your models
 */
class Doctrine_Template_DmGallery extends Doctrine_Template
{
  protected $_options = array(
    'mediaClass'    => 'DmMedia',
    'mediaAlias'    => 'Medias',
    'className'     => '%CLASS%DmMedia',
    'generateFiles' => false,
    'table'         => false,
    'pluginTable'   => false,
    'children'      => array(),
    'cascadeDelete' => true,
    'cascadeUpdate' => false
  );

  public function __construct(array $options = array())
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
    
    $this->_plugin = new Doctrine_DmGallery($this->_options);
  }
  

  public function setUp()
  {
    $this->_plugin->initialize($this->_table);

    dmDb::table($this->_options['mediaClass'])->bind(array($this->_table->getComponentName(), array(
      'local'    => 'dm_media_id',
      'foreign'  => 'id',
      'refClass' => $this->_plugin->getTable()->getOption('name')
    )), Doctrine_Relation::MANY);
  }
  
  public function hasMedias()
  {
    return $this->getNbMedias() > 0;
  }
  
  public function getNbMedias()
  {
    return $this->getDmGallery()->count();
  }
  
  public function addMedia(DmMedia $media)
  {
    $this->getDmGallery()->add($media);
    
    return $this->_invoker;
  }
  
  public function addMedias($medias)
  {
    $currentMedias = $this->getDmGallery();
    
    foreach($medias as $media)
    {
      $currentMedias->add($media);
    }
    
    return $this->_invoker;
  }

  public function getDmGallery()
  {
    if (!$medias = $this->_invoker->reference($this->_options['mediaAlias']))
    {
      $medias = dmDb::query('DmMedia m, m.Folder f, m.'.$this->getGalleryRelClass().' rel')
      ->where('rel.dm_record_id = ?', $this->_invoker->get('id'))
      ->orderBy('rel.position ASC')
      ->select('m.*, f.*')
      ->fetchRecords();
      
      $this->_invoker->setRelated($this->_options['mediaAlias'], $medias);
    }
        
    return $medias;
  }
  
  public function getFirstMedia()
  {
    if ($this->_invoker->contains($this->_options['mediaAlias']))
    {
      return $this->_invoker->reference($this->_options['mediaAlias'])->getFirst();
    }
    
    return dmDb::query('DmMedia m, m.Folder f, m.'.$this->getGalleryRelClass().' rel')
    ->where('rel.dm_record_id = ?', $this->_invoker->get('id'))
    ->orderBy('rel.position ASC')
    ->select('m.*, f.*')
    ->fetchOne();
  }
  
  public function getGalleryRelClass()
  {
    return $this->_plugin->getTable()->getOption('name');
  }
}