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
    $transliterationMap = array(
      '¥' => 'Y', 'µ' => 'u', 'À' => 'A', 'Á' => 'A',
      'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
      'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
      'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I',
      'Î' => 'I', 'Ï' => 'I', 'Ð' => 'D', 'Ñ' => 'N',
      'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
      'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
      'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'ß' => 'ss',
      'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
      'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
      'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
      'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
      'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
      'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
      'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
      'ý' => 'y', 'ÿ' => 'y', 'œ' => 'oe'
    );

    switch($this->getCulture())
    {
      case 'ru':
        $transliterationMap = array_merge($transliterationMap, array(
          'Г'=>'G','Ё'=>'YO','Е'=>'E','Й'=>'YI','И'=>'I',
          'и'=>'i','г'=>'g','ё'=>'yo','№'=>'#','е'=>'e',
          'й'=>'yi','А'=>'A','Б'=>'B','В'=>'V','Г'=>'G',
          'Д'=>'D','Е'=>'E','Ж'=>'ZH','З'=>'Z','И'=>'I',
          'Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N',
          'О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T',
          'У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'TS','Ч'=>'CH',
          'Ш'=>'SH','Щ'=>'SCH','Ъ'=>'','Ы'=>'YI','Ь'=>'',
          'Э'=>'E','Ю'=>'YU','Я'=>'YA','а'=>'a','б'=>'b',
          'в'=>'v','г'=>'g','д'=>'d','е'=>'e','ж'=>'zh',
          'з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l',
          'м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r',
          'с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h',
          'ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'',
          'ы'=>'yi','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya'
        ));
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