<?php
/**
 */
class PluginDmRedirectTable extends myDoctrineTable
{

  public function findOneForSlug($slug)
  {
    $exactRedirection = $this->createQuery('r')
    ->where('r.source = ?', $slug)
    ->fetchRecord();

    if($exactRedirection)
    {
      return $exactRedirection;
    }

    $wildCardRedirections = $this->createQuery('r')
    ->select('r.id, r.source')
    ->where('r.source LIKE ?', '%*%')
    ->fetchArray();

    foreach($wildCardRedirections as $array)
    {
      if(preg_match(sfGlobToRegex::glob_to_regex($array['source']), $slug))
      {
        return $this->find($array['id']);
      }
    }
  }

  public function getIdentifierColumnName()
  {
    return 'source';
  }
  
}