<?php

class dmI18n extends sfI18N
{
  protected
  $useInternalCatalogue = false,
  $cultures = array();
  
  public function setCultures(array $cultures)
  {
    $this->cultures = array_unique($cultures);
  }
  
  public function setUseInternalCatalogue($v)
  {
    $this->useInternalCatalogue = (bool) $v;
  }

  public function translateArray(array $array, $args = array(), $catalogue = 'messages')
  {
    foreach($array as $key => $value)
    {
      $array[$key] = $this->__($value, $args, $catalogue);
    }

    return $array;
  }
  
  /**
   * Gets the translation for the given string
   *
   * @param  string $string     The string to translate
   * @param  array  $args       An array of arguments for the translation
   * @param  string $catalogue  The catalogue name
   *
   * @return string The translated string
   */
  public function __($string, $args = array(), $catalogue = 'messages')
  {
    if(empty($catalogue))
    {
      $catalogue = 'messages';
    }
    
    return $this->getMessageFormat()->formatFast($string, $args, $catalogue);
  }
  
  /**
   * Gets the translation for the given string, or returns false if untranslated
   *
   * @param  string $string     The string to translate
   * @param  array  $args       An array of arguments for the translation
   * @param  string $catalogue  The catalogue name
   *
   * @return string The translated string or false
   */
  public function __orFalse($string, $args = array(), $catalogue = 'messages')
  {
    return $this->getMessageFormat()->formatFastOrFalse($string, $args, $catalogue);
  }
  
  protected function handleNotFound($string, $args = array(), $catalogue)
  {
    // well we did not find the translation string.
    
    $event = new sfEvent($this, 'dm.i18n.not_found', array(
      'source'    => $string,
      'args'      => $args,
      'catalogue' => $catalogue
    ));
    
    $this->configuration->getEventDispatcher()->notifyUntil($event);
    
    // event returned a translation !
    if ($event->isProcessed())
    {
      $translated = $event->getReturnValue();
      
      return empty($args) ? $translated : $this->replaceArgs($translated, $args);
    }
    
    // format untranslated string
    return $this->getMessageFormat()->formatFastUntranslated($string, $args);
  }
  
  public function addTranslations($culture, array $translations, $catalogue = 'messages')
  {
    return $this->getMessageFormat()->addTranslations($culture, $translations, $catalogue, $this->getCulture());
  }

  /**
   * Gets the message format.
   *
   * @return sfMessageFormat A sfMessageFormat object
   */
  public function getMessageFormat()
  {
    if (null === $this->messageFormat)
    {
      $this->messageFormat = new dmMessageFormat($this->getMessageSource(), 'UTF-8');

      if ($this->options['debug'])
      {
        $this->messageFormat->setUntranslatedPS(array($this->options['untranslated_prefix'], $this->options['untranslated_suffix']));
      }

      $this->messageFormat->catalogue = sfConfig::get('dm_i18n_catalogue');
    }

    return $this->messageFormat;
  }

  /**
   * Initializes this class.
   *
   * Available options:
   *
   *  * culture:             The culture
   *  * source:              The i18n source (XLIFF by default)
   *  * debug:               Whether to enable debug or not (false by default)
   *  * database:            The database name (default by default)
   *  * untranslated_prefix: The prefix to use when a message is not translated
   *  * untranslated_suffix: The suffix to use when a message is not translated
   *
   * @param sfApplicationConfiguration $configuration   A sfApplicationConfiguration instance
   * @param sfCache                    $cache           A sfCache instance
   * @param array                      $options         An array of options
   */
  public function initialize(sfApplicationConfiguration $configuration, sfCache $cache = null, $options = array())
  {
    if (!isset($options['culture']))
    {
      $this->culture = sfConfig::get('sf_default_culture');
    }

    parent::initialize($configuration, $cache, $options);
    
    $this->setCultures((array) sfConfig::get('dm_i18n_cultures'));

    if (!$this->cultureExists($this->culture))
    {
      $this->culture = sfConfig::get('sf_default_culture');
    }
  }

  /**
   * Listens to the user.change_culture event.
   *
   * @param sfEvent $event  An sfEvent instance
   *
   */
  public function listenToChangeCultureEvent(sfEvent $event)
  {
    parent::listenToChangeCultureEvent($event);
    
    $this->loadTransliterationStrings();
  }

  protected function loadTransliterationStrings()
  {
    $filePattern = dmOs::join(sfConfig::get('dm_core_dir'), 'data/dm/transliteration/%s.php');
    
    $transliterationMap = include(sprintf($filePattern, 'default'));

    if(file_exists(sprintf($filePattern, $this->getCulture())))
    {
      $transliterationMap = array_merge($transliterationMap, include(sprintf($filePattern, $this->getCulture())));
    }

    sfConfig::set('dm_string_transliteration', $transliterationMap);
  }

  public function getCultures()
  {
    return $this->cultures;
  }

  public function cultureExists($c)
  {
    return in_array($c, $this->getCultures());
  }

  public function hasManyCultures()
  {
    return count($this->getCultures()) > 1;
  }
}