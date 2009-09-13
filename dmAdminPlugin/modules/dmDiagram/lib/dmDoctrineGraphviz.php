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
  }

//  public function getMcdImage()
//  {
//    $digraph = $this->genMCD();
//
//    $diagramImage = 'mcd_diagram.png';
//
//    $imageFullPath = dmOs::join(sfConfig::get('sf_cache_dir'), 'web', $diagramImage);
//
//    $dotFile = dmOs::join(sys_get_temp_dir(), dmString::random(12).'.dot');
//
//    if (!$this->filesystem->mkdir(dirname($imageFullPath)))
//    {
//      throw new dmException(sprintf('Can not mkdir %s', $imageFullPath));
//    }
//
//    file_put_contents($dotFile, $digraph);
//
//    $return = $this->filesystem->exec(sprintf('dot -Tpng %s > %s', $dotFile, $imageFullPath));
//
//    if (!$return)
//    {
//      $this->getUser()->logError(sprintf('Diem can not generate the mcd diagram : %s', $this->filesystem->getLastExec('output')));
//    }
//
//    return '/cache/'.$diagramImage;
//  }
  
  public function getMldImage(array $options = array())
  {
    $options = array_merge(array(
      'type' => null,
      'size' => '30,10'
    ), $options);
    
    $digraph = $this->genMLD($options);

    $diagramImage = sprintf('mld_diagram_%s.png', $options['type']);

    $imageFullPath = dmOs::join(sfConfig::get('sf_cache_dir'), 'web', $diagramImage);

    $dotFile = dmOs::join(sys_get_temp_dir(), dmString::random(12).'.dot');

    if (!$this->filesystem->mkdir(dirname($imageFullPath)))
    {
      throw new dmException(sprintf('Can not mkdir %s', $imageFullPath));
    }

    file_put_contents($dotFile, $digraph);

    $return = $this->filesystem->exec(sprintf('dot -Tpng %s > %s', $dotFile, $imageFullPath));

    if (!$return)
    {
      $this->getUser()->logError(sprintf('Diem can not generate the mcd diagram : %s', $this->filesystem->getLastExec('output')));
    }

    return '/cache/'.$diagramImage;
  }

  protected function searchTables($type = null)
  {
    switch($type)
    {
      case null: $tables = $this->getAllTables(); break;
      case 'user':
        $tables = array();
        foreach($this->getAllTables() as $tableName)
        {
          if (strncmp($tableName, 'sfGuard', 7) === 0 || $tableName === 'DmProfile')
          {
            $tables[] = $tableName;
          }
        }
        break;
      case 'core':
        $tables = dmProject::getDmModels();
        break;
      case 'project':
        $tables = dmProject::getModels();
        break;
      default:
        throw new dmException('TODO');
    }
    return $tables;
  }
  
  protected function getAllTables()
  {
    if(null === $this->tables)
    {
      $this->tables = dmProject::getAllModels();
    }
    
    return $this->tables;
  }

  private function listColumns($table) {
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

  protected function genMCD()
  {
    $tables = $this->searchTables();

    $relations = array();
    foreach($tables as $tableName)
    {
      $table = dmDb::table($tableName);
      foreach ($table->getRelations() as $relation)
      {
        if ($relation->getType() === Doctrine_Relation::MANY && isset($relation['refTable']))
        {
          $relations[] = $relation['refTable']->getComponentName();
        }
      }
    }
    
    $entites = array();
    $assocs = array();
    $liens = array();
    $gens = array();

    foreach($tables as $tableName) {
      $table = dmDb::table($tableName);
      $entites[$tableName]=$this->listColumns($table);
    }

    $relations = array_unique($relations);
    foreach($relations as $relation)
    {
      unset($tables[array_search($relation, $tables)]);
    }

    foreach($relations as $tableName) {
      $table = dmDb::table($tableName);
      $assocs[$tableName]=array();
      $assocs[$tableName]=$this->listColumns($table);
    }

    foreach($relations as $tableName) {
      $table = dmDb::table($tableName);
      foreach ($table->getRelations() as $name => $relation) {
        if ($relation instanceof Doctrine_Relation_LocalKey) {
          $liens[]=Array($tableName, $relation->getTable()->name, '0,n', $relation->getAlias());
        }
      }
    }

    foreach($tables as $tableName) {
      $table = dmDb::table($tableName);
      foreach ($table->getRelations() as $name => $relation) {
        if (!in_array($relation->getTable()->name, $relations)){
          if ($relation instanceof Doctrine_Relation_LocalKey) {
            if (!$relation->getTable()->getRelationHolder()->getByClass($tableName)->isOneToOne()) {
              $assocName=$tableName.$relation->getTable()->name;
              $liens[]=Array($assocName, $relation->getTable()->name, '0,n', $relation->getAlias());
            }
          } elseif ($relation instanceof Doctrine_Relation_ForeignKey) {
            if ($relation->isOneToOne()) {
              $gens[]=Array($tableName, $relation->getTable()->name);
            } else {
              $assocName=$relation->getTable()->name.$tableName;
              $liens[]=Array($assocName, $relation->getTable()->name, '0,1', $relation->getAlias());
            }
          }
        }
      }
    }

    foreach($liens as $lien) {
      if (!@$assocs[$lien[0]]) {
        $assocs[$lien[0]]=Array();
      }
    }

    $digraph="graph G {\noverlap=false;splines=true\nratio=\"fill\" size=\"12,4\" bgcolor=\"#fbfbfb\"

  node [fontsize=\"22\" fontname=\"Arial\" shape=\"record\"];
  edge [fontsize=\"9\" fontname=\"Arial\" color=\"grey\" arrowhead=\"open\" arrowsize=\"0.5\"];";
    foreach($entites as $entite=>$champs) {
      $digraph.="node".$entite." [label=\"{<table>".$entite."|<cols>".implode("\l", $champs)."}\", shape=record];\n";
    }
    $digraph.="\n";
    foreach($assocs as $assoc=>$champs) {
      $digraph.="node".$assoc." [label=\"{<table>".$assoc."|<cols>".implode("\l", $champs)."}\", shape=Mrecord];\n";
    }
    $digraph.="\n";
    foreach($liens as $lien) {
      if ($lien[3]==$lien[1])
      $lien[3]='';
      else
      $lien[3]="($lien[3])";
      $digraph.="node$lien[0] -- node$lien[1] [headlabel=\"$lien[2]\",label=\"$lien[3]\",labeldistance=3];\n";
    }

    $digraph.="\n";
    foreach($gens as $gen) {
      $digraph.="node$gen[0] -- node$gen[1] [arrowhead=normal];\n";
    }

    $digraph.="}\n";
    return $digraph;
  }

  protected function genMLD(array $options)
  {
    $tables = $this->searchTables($options['type']);

    $digraph="digraph G {edge  [ len=2 labeldistance=2 ];overlap=false;splines=true;
bgcolor=\"transparent\";
node [fontsize=\"9\" fontname=\"Arial\"];
edge [fontsize=\"9\" fontname=\"Arial\"];";
    foreach($tables as $tableName) {
      $table = dmDb::table($tableName);
      $digraph.="node".$table->name." [label=\"{<table>".$table->tableName."|<cols>";
      foreach ($table->getColumns() as $name=>$column) {
        $digraph.="$name ($column[type])".(@$column['primary']?' [PK]':'')."\l";
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
          $rel[]="node".$table->name.":cols -> node".$relation->getTable()->name.":table [label=\"".$relation->getLocal()."=".$relation->getForeign()." \"];";
        }
      }
    }

    $rel = array_unique($rel);
    $digraph.=implode("\n", $rel)."}\n";
    return $digraph;
  }

}