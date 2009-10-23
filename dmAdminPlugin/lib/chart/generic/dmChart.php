<?php

require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/pChart/pChart/pChart.class.php'));

abstract class dmChart extends pChart
{
  protected static
  $colors = array(
    'grey1' => array(220, 220, 220),
    'grey2' => array(140, 140, 140),
    'grey3' => array(80, 80, 80),
    'blue' => array(180, 220, 250)
  );

  protected
  $serviceContainer,
  $cacheKey = '',
  $data,
  $name,
  $credentials;

  function dmChart(dmBaseServiceContainer $serviceContainer, array $options = array())
  {
    $this->serviceContainer = $serviceContainer;

    $this->options = array_merge($this->getDefaultOptions(), $options);
    
    parent::pChart($this->getWidth(), $this->getHeight());

    $this->setup();
  }

  protected function setup()
  {
    $this->addToCacheKey($this->options);
    
    $this->setFontProperties("Fonts/tahoma.ttf", 10);
    
    if (sfConfig::get('sf_debug'))
    {
      $reflection = new ReflectionClass(get_class($this));
      $this->addToCacheKey(filemtime($reflection->getFilename()));
    }
    
    $this->configure();
  }
  
  protected function configure()
  {
    // override me
  }
  
  protected function getCache($name)
  {
    return $this->serviceContainer->getService('cache_manager')->getCache('chart/'.$this->getKey())->get($name);
  }
  
  protected function setCache($name, $value)
  {
    return $this->serviceContainer->getService('cache_manager')->getCache('chart/'.$this->getKey())->set($name, $value, $this->options['lifetime']);
  }
  
  protected function choosePalette($number)
  {
    return $this->loadColorPalette(dmOs::join(sfConfig::get('dm_admin_dir'), 'lib/view/chart/palettes/tones-'.$number.'.txt'));
  }

  protected function addToCacheKey($data)
  {
    $this->cacheKey .= serialize($data);
  }

  public function getImage()
  {
    $this->data = $this->getData();
    
    $this->addToCacheKey($this->data);
    
    $cacheKey = md5($this->cacheKey);

    $image = sprintf('%s_%s.png', get_class($this), $cacheKey);

    $imageFullPath = dmOs::join(sfConfig::get('sf_cache_dir'), 'web', $image);

    if (!file_exists($imageFullPath))
    {
      if (!$this->serviceContainer->getService('filesystem')->mkdir(dirname($imageFullPath)))
      {
        throw new dmException(sprintf('Can not mkdir %s', dirname($imageFullPath)));
      }
      
      $this->serviceContainer->getService('logger')->notice('Refresh chart '.get_class($this));

      $this->draw();

      $this->render($imageFullPath);
    }

    return dmMediaTag::build('/cache/'.$image);
  }

  protected function getDefaultOptions()
  {
    return array(
      'width' => 500,
      'height' => 300,
      'name' => get_class($this),
      'key' => preg_replace('|(\w+)Chart|', '$1', get_class($this)),
      'credentials' => 'see_chart',
      'lifetime' => 60 * 60 * 24
    );
  }
  
  public function getKey()
  {
    return $this->options['key'];
  }
  
  public function getName()
  {
    return $this->options['name'];
  }
  
  public function setName($v)
  {
    $this->options['name'] = $v;
  }
  
  public function getCredentials()
  {
    return $this->options['credentials'];
  }
  
  public function setCredentials($v)
  {
    $this->options['credentials'] = $v;
  }

  abstract protected function draw();

  abstract protected function getData();

  /* Set the font properties */
  function setFontProperties($FontName,$FontSize)
  {
    return parent::setFontProperties(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/pChart/', $FontName), $FontSize);
  }

  /*
   * @return array ( ex: array(700, 320) )
   */
  public function getSize()
  {
    return array($this->options['width'], $this->options['height']);
  }
  
  public function getWidth()
  {
    return $this->options['width'];
  }
  
  public function getHeight()
  {
    return $this->options['height'];
  }
}