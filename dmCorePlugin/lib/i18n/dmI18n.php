<?php

class dmI18n extends sfI18N
{

	protected static
  $default_culture;

  public function translateArray(array $array, $args = array(), $catalogue = 'messages')
  {
  	foreach($array as $key => $value)
  	{
      $array[$key] = $this->__($value, $args, $catalogue);
  	}

  	return $array;
  }

  /**
   * Gets the message format.
   *
   * @return sfMessageFormat A sfMessageFormat object
   */
  public function getMessageFormat()
  {
    if (!isset($this->messageFormat))
    {
      $this->messageFormat = new sfMessageFormat($this->getMessageSource(), sfConfig::get('sf_charset'));

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

    if (!$this->cultureExists($this->culture))
    {
    	$this->culture = sfConfig::get('sf_default_culture');
    }
  }

  public function getCultures()
  {
    return sfConfig::get('dm_i18n_cultures');
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