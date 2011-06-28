<?php

/**
 * dmServiceContainerLoaderConfiguration loads configuration from dmConfig class
 */
class dmServiceContainerLoaderConfiguration implements sfServiceContainerLoaderInterface
{
  protected
  $container,
  $dispatcher,
  $config;

  /**
   * Constructor.
   *
   * @param sfServiceContainerBuilder $container A sfServiceContainerBuilder instance
   * @param string|array              $paths     A path or an array of paths where to look for resources
   */
  public function __construct(sfServiceContainerBuilder $container, sfEventDispatcher $dispatcher)
  {
    $this->container = $container;
    $this->dispatcher = $dispatcher;
  }

  /**
   * Loads a resource.
   *
   * A resource is an array of key=>value
   */
  public function load($config)
  {
    $this->config = $config;
    
    $this->add('media_tag_image', 'resize_method',  'image_resize_method');
    $this->add('media_tag_image', 'resize_quality', 'image_resize_quality');
    
    $this->add('link_tag_record', 'current_span',   'link_current_span');
    $this->add('link_tag_page',   'current_span',   'link_current_span');
    
    $this->add('link_tag_record', 'use_page_title', 'link_use_page_title');
    $this->add('link_tag_page',   'use_page_title', 'link_use_page_title');

    $this->add('link_tag_uri',    'external_blank', 'link_external_blank');
    $this->add('link_tag',        'external_blank', 'link_external_blank');
    
    $this->dispatcher->notify(new sfEvent($this, 'dm.service_container.configuration', array(
      'container' => $this->container,
      'config'    => $config
    )));
    
    unset($this->config);
  }
  
  protected function add($service, $key, $configKey)
  {
    if (array_key_exists($configKey, $this->config) && $this->container->hasParameter($service.'.options'))
    {
      $this->container->setParameter(
        $service.'.options',
        array_merge($this->container->getParameter($service.'.options'), array($key => $this->config[$configKey]))
      );
    }
  }
}
