<?php

abstract class dmToolBarView
{
  protected
  $dispatcher,
  $user,
  $helper,
  $i18n,
  $container;

  public function __construct(sfEventDispatcher $dispatcher, dmCoreUser $user, dmHelper $helper, dmI18n $i18n, sfServiceContainer $container)
  {
    $this->dispatcher = $dispatcher;
    $this->user       = $user;
    $this->helper     = $helper;
    $this->i18n       = $i18n;
    $this->container  = $container;
  }

  abstract public function render();

  protected function getCultureSelect()
  {
    if ($this->i18n->hasManyCultures())
    {
      $cultures = array();

      foreach($this->i18n->getCultures() as $key)
      {
        try
        {
          $cultures[$key] = sfCultureInfo::getInstance($key)->getLanguage($key);
        }
        catch(sfException $e)
        {
          $cultures[$key] = $key;
        }
      }

      return new sfWidgetFormSelect(array('choices' => $cultures));
    }
  }
}