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
  public function formatFast($string, array $args = array(), $catalogue)
  {
    $this->loadCatalogue($catalogue);

    foreach ($this->messages[$catalogue] as $variant)
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
          return $this->postscript[0].$this->replaceArgs($string, $args).$this->postscript[1];
        }
        
        return empty($args) ? $target : $this->replaceArgs($target, $args);
      }
    }

    // well we did not find the translation string.
    $this->source->append($string);

    return $this->postscript[0].$this->replaceArgs($string, $args).$this->postscript[1];
  }
}