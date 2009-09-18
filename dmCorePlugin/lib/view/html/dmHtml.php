<?php

class dmHtml
{

  const HTML_TAGS = "span|a|p|div|em|strong|li|h1|h2|h3|h4|h5|h6|th|td";
  const TEXT_ONLY_TAGS = "option";
  const ENABLE = true;

  public static
    $enabled,
    $current_lang,
    $current_title;

  public static function isEnabled()
  {
    if(self::$enabled === null)
    {
      self::$enabled = self::ENABLE && sfConfig::get('dm_tidy_enabled', true) && extension_loaded("tidy");
    }
    return self::$enabled;
  }

  public static function repair($html, $params = array())
  {
    $timer = dmDebug::timerOrNull("dmHtml::repair()");

    // suppression des ul vides
    $html = preg_replace(
      "|<ul[^>]*>[\n\s]*</ul>|ui", '', $html
    );

    // ajout d'un espace dans les <option> vides
    $html = preg_replace(
      "|<option([^>]*)></option>|ui", '<option$1>&nbsp;</option>', $html
    );

    $params = array_merge(array(
      'indent' => true,
      'indent-spaces' => 2,
      'tab-size' => 1,
      'output-xhtml' => true,
      'wrap' => 160,
      'merge-divs' => false,
      'accessibility-check' => 0,
      'char-encoding' => 'utf8',
      'input-encoding' => 'utf8',
      'output-encoding' => 'utf8',
    ), $params);

    $tidy = new tidy;
    $tidy->parseString($html, $params, 'UTF8');

    sfConfig::set("dm_tidy_output", self::getErrors($tidy));

    $html = (string) $tidy;

    $timer && $timer->addTime();

    return $html;
  }

  protected static function getErrors(tidy $tidy)
  {
    $errors = explode("\n", $tidy->errorBuffer);
    foreach($errors as $key => $error)
    {
      if (strpos($error, "Warning: trimming empty <div>"))
      {
        unset($errors[$key]);
      }
//      elseif (strpos($error, "style sheets require testing"))
//      {
//        unset($errors[$key]);
//      }
    }
    return implode("\n", $errors);
  }

  public static function improve($html)
  {
    $timer = dmDebug::timerOrNull("dmHtml::improve()");

    $site_start = "<!-- DM_SITE_START -->";
    $site_end =   "<!-- DM_SITE_END -->";

    $site_html = substr($html,
      strpos($html, $site_start) + strlen($site_start),
      strpos($html, $site_end) - strpos($html, $site_start) - strlen($site_end) -2
    );

    $site_html = self::removeBadTags(
      self::addAbbr(
        self::addSpanLang(
          $site_html
        )
      )
    );

    $html =
      substr($html, 0, strpos($html, $site_start)).
      $site_start.$site_html.$site_end.
      substr($html, strpos($html, $site_end) + strlen($site_end)
    );

    $timer && $timer->addTime();
    return $html;
  }

  public static function addSpanLang($html, $langs = null)
  {
    $timer = aze::timer("dmsHtml::addSpanLang");
    $langs = is_array($langs) ? $langs : DmsLangPeer::getLangs();
    if (!empty($langs))
    {
      foreach($langs as $word => $lang)
      {
        if (stripos($html, $word) !== false)
        {
          self::$current_lang = $lang;
          $html = self::wrapWith(
            $word,
            '\'<span lang="\'.dmsHtml::$current_lang.\'">\'.$matches[2].\'</span>\'',
            $html
          );
        }
      }
    }
    $timer->addTime();
    return $html;
  }

  public static function addAbbr($html, $abbrs = null)
  {
    $timer = aze::timer("dmsHtml::addAbbr");
    $abbrs = is_array($abbrs) ? $abbrs : DmsAbbrPeer::getAbbrs();
    if (!empty($abbrs))
    {
      foreach($abbrs as $word => $title)
      {
        if (stripos($html, $word) !== false)
        {
          self::$current_title = $title;
          $html = self::wrapWith(
            $word,
            '\'<abbr title="\'.dmsHtml::$current_title.\'">\'.$matches[2].\'</abbr>\'',
            $html
          );
        }
      }
    }
    $timer->addTime();
    return $html;
  }

  protected static function wrapWith($word, $replacement, $html)
  {
    return preg_replace_callback(
      '#
        (
          (?:
            >
            [^<]*
            [\W\s]
          |
            >
          )
        )
        (
          '.str_replace(" ", "\s", preg_quote($word)).'
        )
        (
          [\W\s]
        )
      #imx',
      create_function(
        '$matches',
        'return str_ireplace($matches[2], '.$replacement.', $matches[0]);'
      ),
      $html
    );
  }

  public static function removeBadTags($html)
  {
    $timer = aze::timer("dmsHtml::removeBadTags");
    // suppression des tags dans les TEXT_ONLY_TAGS
    $html = preg_replace_callback(
      '#
        (
          <(?:'.self::TEXT_ONLY_TAGS.') [^>]* >
          \n
        )
        (
          .*
        )
      #imx',
      create_function(
        '$matches',
        'return $matches[1].strip_tags($matches[2]);'
      ),
      $html
    );
    $timer->addTime();
    return $html;
  }

}