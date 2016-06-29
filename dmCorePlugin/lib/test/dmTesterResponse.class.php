<?php

/**
 * Fix segfaults with PHP 5.3.2
 *
 * @see sfTesterResponse
 */
class dmTesterResponse extends sfTesterResponse
{
  /**
   * @see sfTesterResponse::checkElement()
   */
  public function checkElement($selector, $value = true, $options = array())
  {
    if (null === $this->dom)
    {
      throw new LogicException('The DOM is not accessible because the browser response content type is not HTML.');
    }

    if (is_object($selector))
    {
      $values = $selector->getValues();
    }
    else
    {
      $values = $this->domCssSelector->matchAll($selector)->getValues();
    }

    if (false === $value)
    {
      $this->tester->is(count($values), 0, sprintf('response selector "%s" does not exist', $selector));
    }
    else if (true === $value)
    {
      $this->tester->ok(count($values) > 0, sprintf('response selector "%s" exists', $selector));
    }
    else if (is_int($value))
    {
      $this->tester->is(count($values), $value, sprintf('response selector "%s" matches "%s" times', $selector, $value));
    }
    else if (preg_match('/^(!)?([^a-zA-Z0-9\\\\]).+?\\2[ims]?$/', $value, $match))
    {
      $position = isset($options['position']) ? $options['position'] : 0;
      if ($match[1] == '!')
      {
        $this->tester->unlike(@$values[$position], substr($value, 1), sprintf('response selector "%s" does not match regex "%s"', $selector, substr($value, 1)));
      }
      else
      {
        $this->tester->like(@$values[$position], $value, sprintf('response selector "%s" matches regex "%s"', $selector, $value));
      }
    }
    else
    {
      $position = isset($options['position']) ? $options['position'] : 0;
      $this->tester->is(@$values[$position], $value, sprintf('response selector "%s" matches "%s"', $selector, $value));
    }

    if (isset($options['count']))
    {
      $this->tester->is(count($values), $options['count'], sprintf('response selector "%s" matches "%s" times', $selector, $options['count']));
    }

    return $this->getObjectToReturn();
  }

  /**
   * Validates the response.
   *
   * @param mixed $checkDTD Either true to validate against the response DTD or
   *                        provide the path to a *.xsd, *.rng or *.rnc schema
   *
   * @return sfTestFunctionalBase|sfTester
   *
   * @throws LogicException If the response is neither XML nor (X)HTML
   */
  public function isValid($checkDTD = false)
  {
    if (preg_match('/(x|ht)ml/i', $this->response->getContentType()))
    {
      $revert = libxml_use_internal_errors(true);

      $dom = new DOMDocument('1.0', $this->response->getCharset());
      $content = $this->response->getContent();

      if (true === $checkDTD)
      {
        $cache = sfConfig::get('sf_cache_dir').'/sf_tester_response/w3';
        if ($cache[1] == ':')
        {
          // On Windows systems the path will be like c:\symfony\cache\xml.dtd
          // I did not manage to get DOMDocument loading a file protocol url including the drive letter
          // file://c:\symfony\cache\xml.dtd or file://c:/symfony/cache/xml.dtd
          // The first one simply doesnt work, the second one is treated as remote call.
          // However the following works. Unfortunatly this means we can only access the current disk
          // file:///symfony/cache/xml.dtd
          // Note that all work for file_get_contents so the bug is most likely in DOMDocument.
          $local = 'file://'.substr(str_replace(DIRECTORY_SEPARATOR, '/', $cache), 2);
        }
        else
        {
          $local = 'file://'.$cache;
        }

        if (!file_exists($cache.'/TR/xhtml11/DTD/xhtml11.dtd'))
        {
          $filesystem = new sfFilesystem();

          $finder = sfFinder::type('any')->discard('.sf');
          $filesystem->mirror(dirname(__FILE__).'/w3', $cache, $finder);

          $finder = sfFinder::type('file');
          $filesystem->replaceTokens($finder->in($cache), '##', '##', array('LOCAL_W3' => $local));
        }

        $content = preg_replace('#(<!DOCTYPE[^>]+")http://www.w3.org(.*")#i', '\\1'.$local.'\\2', $content);
        $dom->validateOnParse = $checkDTD;
      }

      // validation is cumbersome: html5 tags (eg. nav / header / aside) are not recognised by loadHTML
      // loadXML fails on most non numeric html entities (nbsp / bull / ..)
      // best solution is to change the html output settings in dm/config.yml to xhtml for the test environment
      if (preg_match('/(xht|x)ml/i', $this->response->getContentType())) {
        $dom->loadXML($content);
      } else {
        $dom->loadHTML($content);
      }

      switch (pathinfo($checkDTD, PATHINFO_EXTENSION))
      {
        case 'xsd':
          $dom->schemaValidate($checkDTD);
          $message = sprintf('response validates per XSD schema "%s"', basename($checkDTD));
          break;
        case 'rng':
        case 'rnc':
          $dom->relaxNGValidate($checkDTD);
          $message = sprintf('response validates per relaxNG schema "%s"', basename($checkDTD));
          break;
        default:
          $message = $dom->validateOnParse ? sprintf('response validates as "%s"', $dom->doctype->name) : 'response is well-formed "'.$dom->doctype->name.'"';
      }

      if (count($errors = libxml_get_errors()))
      {
        $lines = explode(PHP_EOL, $this->response->getContent());

        $this->tester->fail($message);
        foreach ($errors as $error)
        {
          $this->tester->diag('    '.trim($error->message));
          if (preg_match('/line (\d+)/', $error->message, $match) && $error->line != $match[1])
          {
            $this->tester->diag('      '.str_pad($match[1].':', 6).trim($lines[$match[1] - 1]));
          }
          $this->tester->diag('      '.str_pad($error->line.':', 6).trim($lines[$error->line - 1]));
        }
      }
      else
      {
        $this->tester->pass($message);
      }

      libxml_use_internal_errors($revert);
    }
    else
    {
      throw new LogicException(sprintf('Unable to validate responses of content type "%s"', $this->response->getContentType()));
    }

    return $this->getObjectToReturn();
  }


}
