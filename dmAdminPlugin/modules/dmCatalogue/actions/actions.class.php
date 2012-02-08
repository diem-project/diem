<?php

require_once dirname(__FILE__).'/../lib/dmCatalogueGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/dmCatalogueGeneratorHelper.class.php';

/**
 * dmCatalogue actions.
 *
 * @package    diem
 * @subpackage dmCatalogue
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class dmCatalogueActions extends autoDmCatalogueActions
{
  public function executeExportSentences(dmWebRequest $request)
  {
    $catalogue = $this->getObjectOrForward404($request);

    $units = dmDb::query('DmTransUnit t')->select('t.source, t.target')->where('t.dm_catalogue_id = ?', $catalogue->getId())->fetchArray();
    
    $data = array();
    
    if(count($units) > 0)
    {
      foreach($units as $unit)
      {
        $data[$unit['source']] = $unit['target'];
      }
    }
    
    $data = sfYaml::dump($data);
    
    $name = $catalogue->getName();
    
    if(($pos = strrpos($name, '.'.$catalogue->getTargetLang())) !== false)
    {
        $name = substr($name, 0, $pos);
    }
    
    $this->download($data, array(
      'file_name' => sprintf('%s.%s_%s.yml',
          $name,
          $catalogue->getSourceLang(),
          $catalogue->getTargetLang()
        ),
      'mime_type' => 'application/octet-stream'
    ));
  }
  
  public function executeImportSentences(dmWebRequest $request)
  {
    $catalogue = $this->getObjectOrForward404($request);
    
    $form = new DmCatalogueImportForm();
    
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
    
    if($request->isMethod('post') && $form->bindAndValid($request))
    {
      $file = $form->getValue('file');
      $override = $form->getValue('override');
      
      $dataFile = $file->getTempName();
      
      $table = dmDb::table('DmTransUnit');

      $existQuery = $table->createQuery('t')
      ->select('t.id, t.source, t.target, t.created_at, t.updated_at')
      ->where('t.dm_catalogue_id = ? AND t.source = ?');
      $catalogueId = $catalogue->get('id');
      
      $nbAdded = 0;
      $nbUpdated = 0; 
      
      try
      {
        if(!is_array($data = sfYaml::load(file_get_contents($dataFile))))
        {
          $this->getUser()->logError($this->getI18n()->__('Could not load file: %file%', array('%file%'=>$file->getOriginalName())));
          return $this->renderPartial('dmInterface/flash');
        } 
      }
      catch(Exception $e)
      {
          $this->getUser()->logError($this->getI18n()->__('Unable to parse file: %file%', array('%file%'=>$file->getOriginalName())));
          return $this->renderPartial('dmInterface/flash');
      }
      
      $addedTranslations = new Doctrine_Collection($table);
      $line = 0;
      foreach($data as $source => $target)
      {
        ++$line;

        if (!is_string($source) || !is_string($target))
        {
          $this->getUser()->logError($this->getI18n()->__('Error line %line%: %file%', array('%line%'=>$line, '%file%'=>$file->getOriginalName())));
          return $this->renderPartial('dmInterface/flash');
        }
        else
        {
          $existing = $existQuery->fetchOneArray(array($catalogueId, $source));
          if (!empty($existing) && $existing['source'] === $source)
          {
            if ($existing['target'] !== $target)
            {
              if ($override || $existing['created_at'] === $existing['updated_at'])
              {
                $table->createQuery()
                ->update('DmTransUnit')
                ->set('target', '?', array($target))
                ->where('id = ?', $existing['id'])
                ->execute();

                ++$nbUpdated;
              }
            }
          }
          elseif(empty($existing))
          {
            $addedTranslations->add(dmDb::create('DmTransUnit', array(
              'dm_catalogue_id' => $catalogue->get('id'),
              'source' => $source,
              'target' => $target
            )));

            ++$nbAdded;
          }
        }
      }

      $addedTranslations->save(); 
      
      if($nbAdded)
      {
        $this->getUser()->logInfo($this->getI18n()->__('%catalogue%: added %count% translation(s)', array('%catalogue%'=>$catalogue->get('name'), '%count%'=>$nbAdded)));
      }
      if($nbUpdated)
      {
        $this->getUser()->logInfo($this->getI18n()->__('%catalogue%: updated %count% translation(s)', array('%catalogue%'=>$catalogue->get('name'), '%count%'=>$nbUpdated)));
      }
      if(!$nbAdded && !$nbUpdated)
      {
        $this->getUser()->logInfo($this->getI18n()->__('%catalogue%: nothing to add and update', array('%catalogue%'=>$catalogue->get('name'))));
      }
      
      return $this->renderText(url_for1($this->getRouteArrayForAction('index')));
    }
    
    $action = url_for1($this->getRouteArrayForAction('importSentences', $catalogue));
    return $this->renderText($form->render('.dm_form.list.little action="'.$action.'"'));
  }
}
