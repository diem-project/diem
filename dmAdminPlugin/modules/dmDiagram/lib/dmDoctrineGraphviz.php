<?php

class dmDoctrineGraphviz
{
  protected
  $tables,
  $filesystem,
  $configuration;

  public function __construct(dmFilesystem $filesystem, sfApplicationConfiguration $configuration)
  {
    $this->filesystem = $filesystem;
    $this->configuration = $configuration;
    
    // ensure DmSetting is loaded
    new DmSetting;
  }

  
  public function getMldImage(array $options = array())
  {
    $options = array_merge(array(
      'type' => null,
      'size' => '30,10'
    ), $options);
    
    $digraph = $this->genMLD($options);

    $diagramImage = sprintf('mld_diagram_%s_%s.png', $options['type'], time());

    $imageFullPath = dmOs::join(sfConfig::get('sf_web_dir'), 'cache', $diagramImage);

    $dotFile = tempnam(sys_get_temp_dir(), 'dm_mld_');

    if (!$this->filesystem->mkdir(dirname($imageFullPath)))
    {
      throw new dmException(sprintf('Can not mkdir %s', $imageFullPath));
    }

    file_put_contents($dotFile, $digraph);

    $return = $this->filesystem->exec(sprintf('dot -Tpng %s > %s', $dotFile, $imageFullPath));

    unlink($dotFile);

    if (!$return)
    {
      throw new dmException(sprintf('Diem can not generate the mld diagram : %s', $this->filesystem->getLastExec('output')));
    }
    
    return '/cache/'.$diagramImage;
  }

  protected function searchTables($type = null)
  {
    switch($type)
    {
      case null: $tables = $this->getAllTables(); break;
      case 'user':
        $tables = array('DmUser', 'DmPermission', 'DmGroup', 'DmGroupPermission', 'DmUserPermission', 'DmUserGroup', 'DmRememberKey');
        break;
      case 'core':
        $tables = $this->fixModels(dmProject::getDmModels());
        break;
      case 'project':
        $tables = $this->fixModels(dmProject::getModels());
        $tables[] = 'DmMedia';
        break;
      default:
        throw new dmException('TODO');
    }
    return $tables;
  }
  
  protected function fixModels($models)
  {
    foreach($models as $model)
    {
      if (dmDb::table($model)->hasI18n())
      {
        new $model;
        $models[] = dmDb::table($model)->getI18nTable()->getComponentName();
      }
    }
    
    return $models;
  }
  
  protected function getAllTables()
  {
    if(null === $this->tables)
    {
      $this->tables = dmProject::getAllModels();
      
      foreach($this->tables as $table)
      {
        if (dmDb::table($table)->hasI18n())
        {
          new $table;
          $this->tables[] = dmDb::table($table)->getI18nTable();
        }
      }
    }
    
    return $this->tables;
  }

  protected function listColumns($table) {
    $ret=Array();
    foreach ($table->getColumns() as $name=>$column) {
      if (!@$column['primary']) {
        $ajoute=True;
        foreach ($table->getRelations() as $relation) {
          if ($relation instanceof Doctrine_Relation_LocalKey && $relation->getLocal()==$name) {
            $ajoute=False;
            break;
          }
        }
        if ($ajoute)
        $ret[]=$name.' ('.$column['type'].')';
      }
    }
    return $ret;
  }


  protected function genMLD(array $options)
  {
    $tables = $this->searchTables($options['type']);

    $digraph="digraph G {edge  [ len=2 labeldistance=2 ];overlap=false;splines=true;
bgcolor=\"transparent\";
node [fontsize=\"9\" fontname=\"Arial\"];
edge [fontsize=\"9\" fontname=\"Arial\" arrowhead=\"odiamond\"];";
    foreach($tables as $tableName) {
      $table = dmDb::table($tableName);
      $digraph.=$table->getTableName()." [label=\"{<table>".$table->getTableName()."|<cols>";
      foreach ($table->getColumns() as $name=>$column) {
        $digraph.=$name.' ('.$column['type'].')'.(!empty($column['primary'])?' [PK]':'')."\l";
      }
      $digraph.="}\", shape=record];\n";
    }

    $digraph.="\n";

    $rel=Array();
    foreach($tables as $tableName) {
      $table = dmDb::table($tableName);
      foreach ($table->getRelations() as $name => $relation)
      {
        if ($relation instanceof Doctrine_Relation_LocalKey)
        {
          $rel[]=$table->getTableName().":cols -> ".$relation->getTable()->getTableName().":table [label=\"".$relation->getLocal()."=".$relation->getForeign()." \"];";
        }
      }
    }

    $rel = array_unique($rel);
    $digraph.=implode("\n", $rel)."}\n";
    return $digraph;
  }

}