<?php

/*
 * As Diem only runs on UTF-8,
 * let's gain some microseconds
 * bypassing utf8 conversions
 */
class dmMessageFormat extends sfMessageFormat
{
  /**
   * Do string translation.
   *
   * @param string  $string     the string to translate.
   * @param array   $args       a list of string to substitute.
   * @param string  $catalogue  get the translation from a particular message catalogue.
   * @return string translated string.
   */
  public function formatFast($string, array $args, $catalogue)
  {
    $translated = $this->formatFastOrFalse($string, $args, $catalogue);
    
    if(false === $translated)
    {
      $translated = $this->formatFastUntranslated($string, $args);
    }
    
    return $translated;
  }
  
  public function formatFastOrFalse($string, array $args, $catalogue)
  {
    // make sure that objects with __toString() are converted to strings
    $string = (string) $string;
    
    $this->loadCatalogue($catalogue);
    
    foreach($this->messages[$catalogue] as $variant)
    {
      // we found it, so return the target translation
      if (isset($variant[$string]))
      {
        $target = $variant[$string];

        // check if it contains only strings.
        if (is_array($target))
        {
          $target = empty($target) ? null : $target[0];
        }

        // found, but untranslated
        if (empty($target))
        {
          return false;
        }
        
        return empty($args) ? $target : $this->replaceArgs($target, $args);
      }
    }

    // well we did not find the translation string.
    $this->source->append($string);
    
    return false;
  }
  
  public function formatFastUntranslated($string, array $args)
  {
    return $this->postscript[0].$this->replaceArgs($string, $args).$this->postscript[1];
  }
  
  public function addTranslations($culture, array $translations, $catalogue, $culture)
  {
    $this->loadCatalogue($catalogue);
    
    foreach($translations as $source => $target)
    {
      $this->messages[$catalogue][$catalogue.$culture][$source] = $target;
    }
  }
}