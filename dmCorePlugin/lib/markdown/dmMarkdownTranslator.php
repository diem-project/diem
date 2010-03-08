<?php

class dmMarkdownTranslator extends dmConfigurable
{
  protected
  $i18n;

  public function __construct(dmI18n $i18n, array $options = array())
  {
    $this->i18n = $i18n;

    $this->initialize($options);
  }

  public function getDefaultOptions()
  {
    return array(
      'messages' => array()
    );
  }

  protected function initialize(array $options)
  {
    $this->configure($options);
  }

  public function execute()
  {
    $translatedMessages = array();

    foreach($this->getOption('messages') as $message)
    {
      $translatedMessages[$message] = $this->i18n->__($message);
    }

    return $translatedMessages;
  }
}