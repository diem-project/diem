<?php

class dmPageI18nBuilder extends dmConfigurable
{
  protected
  $dispatcher,
  $isConnected;
  
  public function __construct(sfEventDispatcher $dispatcher, array $options = array())
  {
    $this->dispatcher = $dispatcher;
    
    $this->initialize($options);
  }
  
  public function getDefaultOptions()
  {
    return array(
      'activate_new_translations' => true
    );
  }
  
  protected function initialize(array $options)
  {
    if (!isset($options['cultures']) || !is_array($options['cultures']))
    {
      throw new dmException('You must provide an array of cultures');
    }
    
    $this->configure($options);

    $this->setOption('cultures', array_unique(array_filter($this->getOption('cultures'))));
  }
  
  public function connect()
  {
    $this->dispatcher->connect('dm.page.post_save', array($this, 'listenToPagePostSaveEvent'));
    
    $this->isConnected = true;
  }
  
  public function isConnected()
  {
    return $this->isConnected;
  }
  
  public function listenToPagePostSaveEvent(sfEvent $event)
  {
    $this->createPageTranslations($event->getSubject()->get('id'));
  }
  
  public function createAllPagesTranslations()
  {
    $cultures = $this->getOption('cultures');
    
    $pageIds = dmDb::table('DmPage')->createQuery('p')
    ->select('p.id')
    ->fetchFlat();

    $existingTranslations = array_flip(dmDb::query('DmPageTranslation p')
    ->select('CONCAT(p.id, p.lang)')
    ->fetchFlat());
    
    foreach($pageIds as $pageId)
    {
      foreach($cultures as $culture)
      {
        if(!isset($existingTranslations[$pageId.$culture]))
        {
          $this->createPageTranslations($pageId);
        }
      }
    }
  }
  
  protected function createPageTranslations($pageId)
  {
    $cultures = $this->getOption('cultures');
    
    $translationTable = dmDb::table('DmPageTranslation');
    
    $existingCultures = $translationTable->createQuery('t')
    ->where('t.id = ? ', $pageId)
    ->andWhereIn('t.lang', $cultures)
    ->select('t.lang')
    ->fetchFlat();
    
    // can not generate translations from nothing
    if (empty($existingCultures))
    {
      return;
    }
    
    // calculate missing cultures for this page
    $missingCultures = array_diff($cultures, $existingCultures);
    
    // all translations exist
    if(empty($missingCultures))
    {
      return;
    }
    
    if (in_array(sfConfig::get('sf_default_culture'), $existingCultures))
    {
      $mainCulture = sfConfig::get('sf_default_culture');
    }
    elseif(in_array(myDoctrineRecord::getDefaultCulture(), $existingCultures))
    {
      $mainCulture = myDoctrineRecord::getDefaultCulture();
    }
    else
    {
      $mainCulture = dmArray::first($existingCultures);
    }
    
    $mainTranslationArray = $translationTable->createQuery('t')
    ->select('t.slug, t.name, t.title, t.h1, t.description, t.keywords, t.is_active')
    ->where('t.id = ?', $pageId)
    ->andWhere('t.lang = ?', $mainCulture)
    ->limit(1)
    ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
    
    $missingTranslations = new myDoctrineCollection(dmDb::table('DmPageTranslation'));

    if($this->getOption('activate_new_translations'))
    {
      $isActive = $mainTranslationArray['is_active'];
    }
    else
    {
      $isActive = false;
    }

    foreach($missingCultures as $culture)
    {
      $missingTranslations->add($translationTable->create(array_merge($mainTranslationArray, array(
        'lang'      => $culture,
        'is_active' => $isActive
      ))));
    }
    
    $missingTranslations->save();
  }
}