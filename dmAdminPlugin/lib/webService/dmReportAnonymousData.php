<?php

class dmReportAnonymousData extends dmConfigurable
{
  protected
  $serviceContainer;

  public function __construct(dmBaseServiceContainer $serviceContainer, array $options)
  {
    $this->serviceContainer = $serviceContainer;

    $this->initialize($options);
  }

  protected function initialize(array $options)
  {
    $this->configure($options);
  }

  public function shouldSend()
  {
    return !$this->serviceContainer->getService('user')->getAttribute('report_anonymous_data', false, 'dm');
  }

  public function send()
  {
    $this->serviceContainer->getService('user')
    ->setAttribute('report_anonymous_data', true, 'dm');
    
    $this->serviceContainer->getService('web_browser')
    ->post($this->getOption('url'), $this->getData());

//    print($this->serviceContainer->getService('web_browser')->getResponseText());
  }

  protected function getData()
  {
    return array(
      'hash'    => md5($this->getUniqueKey()),
      'version' => DIEM_VERSION,
      'plugins' => implode(',', $this->getPlugins())
    );
  }

  protected function getUniqueKey()
  {
    return var_export(array(
      $this->serviceContainer->getService('request')->getUriPrefix(),
      sfConfig::get('sf_root_dir')
    ), true);
  }

  protected function getPlugins()
  {
    return array_diff(
      $this->serviceContainer->getService('context')->getConfiguration()->getPlugins(),
      array('dmCorePlugin', 'dmUserPlugin', 'dmAdminPlugin', 'sfDoctrinePlugin', 'sfWebBrowserPlugin', 'sfImageTransformPlugin', 'sfFeed2Plugin', 'sfFormExtraPlugin')
    );
  }
}