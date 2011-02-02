<?php

class dmRecordLoremizer extends dmConfigurable
{
  protected
  $record,
  $table;
  
  public function __construct(array $options = array())
  {
    $this->initialize($options);
  }
  
  public function getDefaultOptions()
  {
    return array(
      'override' => false,
      'create_associations' => true
    );
  }
  
  protected function initialize(array $options)
  {
    $this->configure($options);
  }
  
  public function execute(dmDoctrineRecord $record)
  {
    $this->record = $record;
    $this->table  = $record->getTable();

    if($this->table->hasI18n())
    {
      $this->record->getCurrentTranslation();
    }

//    $this->record->clearRelated();
    
    foreach($this->getLoremizableColumns() as $columnName => $column)
    {
      $this->loremizeColumn($columnName, $column);
    }
    
    if ($this->getOption('create_associations'))
    {
      $this->loremizeAssociations();
    }

    return $this->record;
  }
  
  protected function loremizeColumn($columnName, array $column)
  {
    if ($this->table->isI18nColumn($columnName))
    {
      $defaultValue = $this->table->getI18nTable()->getDefaultValueOf($columnName);
    }
    else
    {
      $defaultValue = $this->table->getDefaultValueOf($columnName);
    }
    
    /*
     * Non override on existing records
     * pass if columns value is different than its default value
     */
    if (!$this->getOption('override') && (!$this->record->isNew() || $this->record->get($columnName) !== $defaultValue))
    {
      return;
    }
    
    // skip auto-generated slug
    if ($columnName === 'slug' && $this->table->hasTemplate('Sluggable'))
    {
      return;
    }
    
    // skip i18n lang
    if ($columnName === 'lang' && $this->table->hasI18n())
    {
      return;
    }
    
    // if the field can be null, set it to null sometimes
    if (!dmArray::get($column, 'notnull') && !dmArray::get($column, 'unique') && !mt_rand(0, 3))
    {
      $val = null;
    }
    // handle local keys
    elseif ($localRelation = $this->table->getRelationHolder()->getLocalByColumnName($columnName))
    {
      $val = $this->getRandomId($localRelation->getTable());
    }
    // handle i18n local keys
    elseif ($this->table->hasI18n() && ($localRelation = $this->table->getI18nTable()->getRelationHolder()->getLocalByColumnName($columnName)))
    {
      $val = $this->getRandomId($localRelation->getTable());
    }
    else
    {
      $val = $this->getRandomValue($columnName, $column);
    }

    $this->record->set($columnName, $val);
  }
  
  protected function loremizeAssociations()
  {
    foreach($this->table->getRelationHolder()->getAssociations() as $relation)
    {
      $this->record->clearRelated($relation->getAlias());
      
      if (mt_rand(0, 4))
      {
        $ids = (array) dmDb::query($relation->getClass().' t')
        ->select('t.id, RANDOM() AS rand')
        ->orderBy('rand')
        ->limit(mt_rand(1, 6))
        ->fetchFlat();
        
        $this->record->link($relation->getAlias(), array_unique($ids));
      }
    }
  }
  
  protected function getLoremizableColumns()
  {
    return $this->table->getHumanColumns();
  }

  protected function getRandomValue($columnName, array $column)
  {
    $column['name'] = $columnName;
    
    switch($column['type'])
    {
      case 'string':
        $val = $this->getStringValForColumn($column);
        break;
      case 'boolean':
        if('is_active' === $columnName)
        {
          $val = (bool)mt_rand(0, 2);
        }
        else
        {
          $val = (bool)mt_rand(0, 1);
        }
        break;
      case 'blob':
      case 'clob':
        if ($this->table->isMarkdownColumn($columnName))
        {
          $val = dmLorem::getMarkdownLorem(1);
        }
        else
        {
          $val = dmLorem::getBigLorem();
        }
        break;
      case 'time':
      case 'timestamp':
      case 'date':
        $val = date("Y-m-d H:i:s", mt_rand(strtotime('-10 year') , time()));
        break;
      case 'year':
        $val = date("Y", mt_rand(strtotime('-10 year') , time()));
        break;
      case 'enum':
        $val = $column['values'][array_rand($column['values'])];
        break;
      case 'integer':
      case 'int':
        $val = mt_rand(0, pow(10, $column['length']) - 1);
        break;
      case 'float':
      case 'decimal':
      case 'double':
        $val = mt_rand(0, pow(10, $column['length']) - 1)/pow(10, dmArray::get($column, 'scale', 2));
        break;
      default:
        throw new dmException(sprintf('Diem can not generate random content for %s column', $columnName));
    }

    return $val;
  }

  protected function getStringValForColumn($column)
  {
    if (dmArray::get($column, 'email'))
    {
      return dmString::random(mt_rand(4, 30)).'@localhost.com';
    }
    
    if ($this->table->isLinkColumn($column['name']))
    {
      return $this->getRandomLink();
    }

    if($column['length'] < 40)
    {
      $nbCarac = mt_rand(1, $column['length']);
    }
    else
    {
      $maxLen = $column['length'] > 80 ? $column['length'] / 2 : $column['length'];
      $nbCarac = mt_rand(min(4, $column['length']), max(40, $maxLen));
    }

    $val = trim(dmLorem::getLittleLorem($nbCarac));

    if (dmArray::get($column, 'unique'))
    {
      $val = substr(dmString::random(4).' '.$val, 0, $column['length']);
    }

    if (dmArray::get($column, 'nospace'))
    {
      $val = str_replace(' ', '-', $val);
    }

    return $val;
  }

  protected function getRandomLink()
  {
    if(mt_rand(0, 1))
    {
      return sprintf('http://'.dmString::random().'.random.com');
    }

    if(!class_exists('DmPageTranslation', false))
    {
      new DmPage();
    }
    
    $page = dmDb::query('DmPageTranslation p')
    ->select('p.id, p.name, p.lang, RANDOM() AS rand')
    ->where('p.lang = ?', dmDoctrineRecord::getDefaultCulture())
    ->orderBy('rand')
    ->limit(1)
    ->fetchPDO();
    
    return sprintf('page:%d %s', $page[0][0], $page[0][1]);
  }
  
  protected function getRandomId(dmDoctrineTable $table)
  {
    $id = $table->createQuery('t')
    ->select('t.id, RANDOM() AS rand')
    ->orderBy('rand')
    ->limit(1)
    ->fetchValue();
    
    if (!$id)
    {
      $recordLoremizer = new self($this->getOptions());
      $id = $recordLoremizer->execute($table->create())->saveGet()->get('id');
    }

    return $id;
  }
}
