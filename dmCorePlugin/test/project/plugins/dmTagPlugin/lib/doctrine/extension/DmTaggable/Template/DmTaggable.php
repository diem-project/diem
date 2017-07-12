<?php

class Doctrine_Template_DmTaggable extends Doctrine_Template
{
  protected $_options = array(
    'tagClass'      => 'DmTag',
    'tagAlias'      => 'Tags',
    'className'     => '%CLASS%DmTag',
    'generateFiles' => false,
    'table'         => false,
    'pluginTable'   => false,
    'children'      => array(),
    'cascadeDelete' => true,
    'appLevelDelete' => false,
    'cascadeUpdate' => false
  );

  public function __construct(array $options = array())
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);

    $this->_plugin = new Doctrine_DmTaggable($this->_options);
  }

  public function setUp()
  {
    $this->_plugin->initialize($this->_table);

    $className = $this->_table->getComponentName();

    dmDb::table($this->_options['tagClass'])->bind(array($className.' as '.$className.'s', array(
      'class'    => $className,
      'local'    => 'dm_tag_id',
      'foreign'  => 'id',
      'refClass' => $this->_plugin->getTable()->getOption('name')
    )), Doctrine_Relation::MANY);
  }

  public function getNbTags()
  {
    return $this->getInvoker()->get('Tags')->count();
  }

  public function hasTags()
  {
    return $this->getNbTags() > 0;
  }

  public function getTagNames()
  {
    $tagNames = array();
    foreach ($this->getInvoker()->get('Tags') as $tag)
    {
      $tagNames[] = $tag->get('name');
    }
    
    return $tagNames;
  }

  public function getTagsString($sep = ', ')
  {
    return implode($sep, $this->getTagNames());
  }

  public function getTagsAsString ($sep = ', ')
  {
    return $this->getTagsString($sep);
  }

  public function setTags($tags)
  {
    if(empty($tags))
    {
      $tags = array();
    }
    
    $tagIds = $this->getTagIds($tags);
    $this->getInvoker()->unlink('Tags');
    $this->getInvoker()->link('Tags', $tagIds);

    return $this->getInvoker();
  }

  public function addTags($tags)
  {
    $this->getInvoker()->link('Tags', $this->getTagIds($tags));

    return $this->getInvoker();
  }

  public function removeTags($tags)
  {
    $this->getInvoker()->unlink('Tags', $this->getTagIds($tags));

    return $this->getInvoker();
  }

  public function removeAllTags()
  {
    $this->getInvoker()->unlink('Tags');

    return $this->getInvoker();
  }

  public function getRelatedRecords($hydrationMode = Doctrine::HYDRATE_RECORD)
  {
    return $this->getRelatedRecordsQuery()
    ->execute(array(), $hydrationMode);
  }

  public function hasRelatedRecords($hydrationMode = Doctrine::HYDRATE_RECORD)
  {
    return $this->getRelatedRecordsQuery()
    ->count();
  }

  public function getRelatedRecordsQuery()
  {
    return $this->getInvoker()->getTable()
    ->createQuery('a')
    ->leftJoin('a.Tags t')
    ->whereIn('t.id', $this->getCurrentTagIds())
    ->andWhere('a.id != ?', $this->getInvoker()->get('id'));
  }

  public function getCurrentTagIds()
  {
    $tagIds = array();
    foreach ($this->getInvoker()->get('Tags') as $tag)
    {
      $tagIds[] = $tag->get('id');
    }
    
    return $tagIds;
  }

  public function getTagIds($tags)
  {
    if (is_string($tags))
    {
      $tagNames = array_unique(array_filter(array_map('trim', explode(',', $tags))));

      $tagsList = array();
      if (!empty($tagNames))
      {
        $existingTagQuery = dmDb::table($this->_options['tagClass'])
        ->createQuery('t')
        ->select('t.id')
        ->where('t.name = ?')
        ->limit(1);
        
        foreach ($tagNames as $tagName)
        {
          //check if tag is existing in db
          $_existingTag = $existingTagQuery->fetchPDO(array($tagName));

          //if tag is not in db, insert tag 
          if (empty($_existingTag))
          {
            $tag = new $this->_options['tagClass']();
            $tag->set('name', $tagName);
            $tag->save();
            $tagsList[] = $tag->get('id');
          }
          else
          {
            $tagsList[] = $_existingTag[0][0];
          }
        }
      }

      return $tagsList;
    }
    elseif (is_array($tags))
    {
      if (is_numeric(current($tags)))
      {
        return $tags;
      }
      else
      {
        return $this->getTagIds(implode(',', $tags));
      }
    }
    elseif ($tags instanceof Doctrine_Collection)
    {
      return $tags->getPrimaryKeys();
    }
    else
    {
      throw new Doctrine_Exception('Invalid $tags data provided. Must be a string of tags, an array of tag ids, or a Doctrine_Collection of tag records.');
    }
  }

  public function getDmTagRelClass()
  {
    return $this->_plugin->getTable()->getOption('name');
  }
}