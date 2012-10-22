<?php

/**
 * Add gallery capabilities to your models
 */
class Doctrine_Template_DmGallery extends Doctrine_Template
{
  protected $_options = array(
    'mediaClass'    => 'DmMedia',
    'mediaAlias'    => 'Medias',
    'formClass'     => 'DmMediaForm',
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
      'foreign'  => 'dm_record_id',
      'refClass' => $this->_plugin->getTable()->getOption('name')
    )), Doctrine_Relation::MANY);
  }

  public function hasMedias()
  {
    return $this->getNbMedias() > 0;
  }

  public function hasMedia(DmMedia $media)
  {
    return dmDb::table($this->getGalleryRelClass())
    ->createQuery('r')
    ->where('r.dm_record_id = ?', $this->getInvoker()->get('id'))
    ->andWhere('r.dm_media_id = ?', $media->get('id'))
    ->exists();
  }
  
  public function getNbMedias()
  {
    return $this->getDmGallery()->count();
  }
  
  public function addMedia(DmMedia $media)
  {
    if(!$this->hasMedia($media))
    {
      $rel = dmDb::table($this->getGalleryRelClass())->create(array(
        'dm_media_id' => $media->get('id'),
        'dm_record_id' => $this->getInvoker()->get('id')
      ));

      $rel->save();
    }
    
    return $this->getInvoker();
  }
  
  public function addMedias($medias)
  {
    foreach($medias as $media)
    {
      $this->addMedia($media);
    }

    return $this->getInvoker();
  }

  public function removeMedias()
  {
    dmDb::table($this->getGalleryRelClass())
    ->createQuery()
    ->delete()
    ->where('dm_record_id = ?', $this->_invoker->get('id'))
    ->execute();

    return $this->getInvoker();
  }

  public function getDmGallery($reload = false)
  {
    if ($reload || (!$medias = $this->_invoker->reference($this->_options['mediaAlias'])))
    {
      $medias = dmDb::query($this->_options['mediaClass'].' m, m.Folder f, m.'.$this->getGalleryRelClass().' rel')
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
    
    return dmDb::query($this->_options['mediaClass'].' m, m.Folder f, m.'.$this->getGalleryRelClass().' rel')
    ->where('rel.dm_record_id = ?', $this->_invoker->get('id'))
    ->orderBy('rel.position ASC')
    ->select('m.*, f.*')
    ->fetchOne();
  }
  
  public function getGalleryRelClass()
  {
    return $this->_plugin->getTable()->getOption('name');
  }

  public function getGalleryFormClass()
  {
    return $this->getOption('formClass');
  }

  public function getGalleryMediaClass()
  {
    return $this->getOption('mediaClass');
  }
}
