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
  $i18n,
  $cacheKey = '',
  $data,
  $available = true;

  function __construct(dmBaseServiceContainer $serviceContainer, array $options = array())
  {
    $this->serviceContainer = $serviceContainer;

    $this->options = array_merge($this->getDefaultOptions(), $options);

    parent::pChart($this->getWidth(), $this->getHeight());

    $this->initialize($options);
  }
  
  public function isAvailable()
  {
    return $this->available;
  }

  protected function initialize(array $options)
  {
    $this->configure($options);

    $this->i18n = $this->serviceContainer->getService('i18n');
    
    $this->addToCacheKey($this->options);

    $this->addToCacheKey($this->serviceContainer->getParameter('user.culture'));

    $this->setFontProperties("Fonts/tahoma.ttf", 10);

    if (sfConfig::get('sf_debug'))
    {
      $reflection = new ReflectionClass(get_class($this));
      $this->addToCacheKey(filemtime($reflection->getFilename()));
    }
  }

  protected function configure(array $options)
  {
    $this->options = array_merge($this->getDefaultOptions(), $options);
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

    $imageFullPath = dmOs::join(sfConfig::get('sf_web_dir'), 'cache', $image);

    if (!$this->options['use_cache'] || !file_exists($imageFullPath))
    {
      if (!$this->serviceContainer->getService('filesystem')->mkdir(dirname($imageFullPath)))
      {
        throw new dmException(sprintf('Can not mkdir %s', dirname($imageFullPath)));
      }

      $this->serviceContainer->getService('logger')->notice('Refresh chart '.get_class($this));

      @$this->draw();

      $this->render($imageFullPath);
      
      $this->serviceContainer->getService('filesystem')->chmod($imageFullPath, 0777);
    }

    return $this->serviceContainer->getService('helper')->media('/cache/'.$image);
  }

  public function getDefaultOptions()
  {
    return array(
      'width'       => 500,
      'height'      => 300,
      'name'        => get_class($this),
      'key'         => preg_replace('|(\w+)Chart|', '$1', get_class($this)),
      'credentials' => 'see_chart',
      'lifetime'    => 60 * 60 * 24,
      'use_cache'   => true
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

  /* This function write the values of the specified series */
  function writeValuesOptions($Data,$DataDescription,$Series, array $options = array())
  {
    $options = array_merge(array(
        '>' => null,
        '<' => null
    ), $options);

    /* Validate the Data and DataDescription array */
    $this->validateDataDescription("writeValues",$DataDescription);
    $this->validateData("writeValues",$Data);

    if ( !is_array($Series) ) { $Series = array($Series); }

    foreach($Series as $Key => $Serie)
    {
      $ID = 0;
      foreach ( $DataDescription["Description"] as $keyI => $ValueI )
      { if ( $keyI == $Serie ) { $ColorID = $ID; }; $ID++; }

      $XPos  = $this->GArea_X1 + $this->GAreaXOffset;
      $XLast = -1;
      foreach ( $Data as $Key => $Values )
      {

        if ( isset($Data[$Key][$Serie]) && is_numeric($Data[$Key][$Serie]))
        {
          $Value = $Data[$Key][$Serie];
           
          if ((null === $options['<'] || $Value < $options['<']) && (null === $options['>'] || $Value > $options['>']))
          {
            $YPos = $this->GArea_Y2 - (($Value-$this->VMin) * $this->DivisionRatio);

            $Positions = imagettfbbox($this->FontSize,0,$this->FontName,$Value);
            $Width  = $Positions[2] - $Positions[6]; $XOffset = $XPos - ($Width/2);
            $Height = $Positions[3] - $Positions[7]; $YOffset = $YPos - 4;

            $C_TextColor =$this->AllocateColor($this->Picture,$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"]);
            imagettftext($this->Picture,$this->FontSize,0,$XOffset,$YOffset,$C_TextColor,$this->FontName,$Value);
          }
        }
        $XPos = $XPos + $this->DivisionWidth;
      }

    }
  }

  protected function getI18n()
  {
    return $this->i18n;
  }
}