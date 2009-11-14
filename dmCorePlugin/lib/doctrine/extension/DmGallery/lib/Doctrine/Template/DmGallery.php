<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

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
  
  /*
   * $this->_plugin->getTable() = ProdDmMedia
   * $this->_options['mediaClass'] = DmMedia
   * $this->_table = Prod
   */

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
    $this->getDmGallery->add($media);
    
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