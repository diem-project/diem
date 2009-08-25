<?php

class dmHtmlValidator
{
  protected
    $html,
    $xmlResult;

  protected static
    $validator_url = "http://validator.w3.org/check";

  public function __construct($html)
  {
    $this->html = $html;
  }

  public function getHtmlLines($num, $nb)
  {
    $start = $num < 2 ? 0 : $num-2;
    $lines = array();

    foreach(array_slice(explode("\n", $this->html), $start, $nb) as $line)
    {
      $lines[] = $this->purifyLine($line);
    }

    return $lines;
  }
  public function getHtmlLine($num)
  {
    $line = aze::getArrayKey(explode("\n", $this->html), $num);
    return $this->purifyLine($line);
  }

  protected function purifyLine($line)
  {
    return htmlentities(
      $line, //preg_replace("|^(\s)+|", "", $line),
      null,
      "UTF-8"
    );
  }

  // Retourne un objet XML
  public function validate()
  {
    if ($this->xmlResult === null)
    {
      $browser = new dmWebBrowser("Html Validation");
      $browser->post(self::$validator_url, array(
        "output" => "xml",
        "fragment" => $this->html
      ));
      $this->xmlResult = $browser->getResponseXml();
    }
    return $this->xmlResult;
  }

  public function getErrors()
  {
    $errors = array();
    foreach($this->validate()->messages->msg as $element)
    {
      $errors[] = new dmHtmlValidatorError($element);
    }
    return $errors;
  }

  public function getNbErrors()
  {
    return $this->validate()->meta->errors;
  }

  public function isValid()
  {
    return $this->getNbErrors() == 0;
  }

}

class dmHtmlValidatorError
{

  protected
    $message,
    $line,
    $col;

  public function __construct(SimpleXMLElement $element)
  {
    $attributes = $element->attributes();
    $this->line = intval($attributes["line"]);
    $this->col = intval($attributes["col"]);
    $this->message = (string) $element;
  }

  public function getMessage()
  {
    return $this->message;
  }

  public function getLine()
  {
    return $this->line;
  }

  public function getCol()
  {
    return $this->col;
  }

}