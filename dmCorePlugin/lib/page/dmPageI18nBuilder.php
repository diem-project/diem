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
    return array_merge(parent::getDefaultOptions(), array(
      'activate_new_translations' => false
    ));
  }
  
  protected function initialize(array $options)
  {
    if (!isset($options['cultures']) || !is_array($options['cultures']))
    {
      throw new dmException('You must provide an array of cultures');
    }
    
    $this->configure($options);
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
    $this->createPageTranslations($event->getSubject());
  }
  
  public function createAllPagesTranslations()
  {
    $pages = dmDb::table('DmPage')->createQuery('p')
    ->leftJoin('p.Translation')
    ->fetchRecords();
    
    foreach($pages as $page)
    {
      $this->createPageTranslations($page);
    }
  }
  
  protected function createPageTranslations(DmPage $page)
  {
    $cultures = array_unique($this->getOption('cultures'));
    
    $translationTable = dmDb::table('DmPageTranslation');
    
    $existingCultures = $translationTable->createQuery('t')
    ->where('t.id = ? ', $page->get('id'))
    ->andWhereIn('t.lang', $cultures)
    ->select('t.lang')
    ->fetchFlat();
    
    // can not generate translations from nothing
    if (empty($existingCultures))
    {
      return;
    }
    
    // calculate missing cultures for this page
    $missingCultures = array();
    foreach($cultures as $culture)
    {
      if (!in_array($culture, $existingCultures))
      {
        $missingCultures[] = $culture;
      }
    }
    
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
    ->select('t.slug, t.name, t.title, t.h1, t.description, t.keywords')
    ->where('t.id = ?', $page->get('id'))
    ->andWhere('t.lang = ?', $mainCulture)
    ->limit(1)
    ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
    
    $missingTranslations = new myDoctrineCollection(dmDb::table('DmPageTranslation'));

    $changes = array(
      'lang' => $culture
    );

    if(!$this->getOption('activate_new_translations'))
    {
      $changes['is_active'] = false;
    }

    foreach($missingCultures as $culture)
    {
      $missingTranslations->add($translationTable->create(array_merge($mainTranslationArray, $changes)));
    }
    
    $missingTranslations->save();
  }
}