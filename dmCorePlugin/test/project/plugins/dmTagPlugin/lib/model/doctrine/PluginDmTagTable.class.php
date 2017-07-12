<?php
/**
 */
class PluginDmTagTable extends myDoctrineTable
{
  protected
  $taggableModelsLoaded = false;

  public function getAdminListQuery(dmDoctrineQuery $query)
  {
    $this->loadTaggableModels();

    return parent::getAdminListQuery($query);
  }

  public function getTagNames()
  {
    return $this->createQuery('t')
    ->select('t.name')
    ->fetchFlat();
  }

  public function getPopularTagsQuery($relations = null, $limit = null, dmDoctrineQuery $q = null)
  {
    if (empty($relations))
    {
      $this->loadTaggableModels();
      
      $relations = array_keys($this->getRelationHolder()->getAssociations());

      if(empty($relations))
      {
        throw new dmException('There is no taggable model');
      }
    }
    else
    {
      $relations = (array) $relations;
    }

    $q = $q ? $q : $this->createQuery('t')->select('t.*');

    $rootAlias = $q->getRootAlias();

    $counts = array();
    foreach ($relations as $relation)
    {
      $countAlias = 'num_' . Doctrine_Inflector::tableize($relation);

      $q->leftJoin($rootAlias.'.' . $relation . ' '.$relation);
      $q->addSelect('COUNT(DISTINCT ' . $relation . '.id) AS ' . $countAlias);
      $counts[] = 'COUNT(DISTINCT ' . $relation .'.id)';
    }

    $q->addSelect('(' . implode(' + ', $counts) . ') as total_num');
    $q->orderBy('total_num DESC');
    $q->groupBy($rootAlias.'.id');
    $q->addHaving('total_num > 0');

    if(null !== $limit)
    {
      $q->limit($limit);
    }
    
    return $q;
  }

  public function getPopularTags($relations = null, $limit = null, $hydrationMode = Doctrine::HYDRATE_RECORD)
  {
    return $this->getPopularTagsQuery($relations, $limit)->execute(array(), $hydrationMode);
  }

  public function loadTaggableModels()
  {
    if(!$this->taggableModelsLoaded)
    {
      $taggableModels = $this->getTaggableModels();

      $taggableModels = $this->getEventDispatcher()->filter(new sfEvent(
        $this, 'dm_tag.taggable_models.filter', array()
      ), $taggableModels)->getReturnValue();

      foreach($taggableModels as $model)
      {
        dmDb::table($model);
      }

      $this->taggableModelsLoaded = true;
    }
  }

  /**
   * @return array models that act as DmTaggable
   */
  public function getTaggableModels()
  {
    $cacheManager = $this->getService('cache_manager');
    
    if($cacheManager && $cacheManager->getCache('dm_tag')->has('taggable_models'))
    {
      return $cacheManager->getCache('dm_tag')->get('taggable_models');
    }

    $modelBaseFiles = array_merge(
      glob(dmOs::join(sfConfig::get('sf_lib_dir'), 'model/doctrine/base/Base*.class.php')),
      glob(dmOs::join(sfConfig::get('sf_lib_dir'), 'model/doctrine/*Plugin/base/Base*.class.php'))
    );

    $taggableModels = array();
    foreach($modelBaseFiles as $modelBaseFile)
    {
      if(strpos(file_get_contents($modelBaseFile), 'new Doctrine_Template_DmTaggable('))
      {
        $taggableModels[] = preg_replace('|^Base(\w+).class.php$|', '$1', basename($modelBaseFile));
      }
    }

    if($cacheManager)
    {
      $cacheManager->getCache('dm_tag')->set('taggable_models', $taggableModels);
    }

    return $taggableModels;
  }

  /**
   * Find a tag by its name
   * @param   string  $name
   * @return  DmTag   the founded DmTag, or null
   */
  public function findOneByName($name)
  {
    return $this->createQuery('r')
    ->where('r.name = ?', $name)
    ->fetchRecord();
  }
}