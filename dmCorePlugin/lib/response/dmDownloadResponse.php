<?php

class dmDownloadResponse extends dmConfigurable
{
  protected
  $response,
  $mimeTypeResolver;

  public function __construct(sfWebResponse $response, dmMimeTypeResolver $mimeTypeResolver, array $options)
  {
    $this->response         = $response;
    $this->mimeTypeResolver = $mimeTypeResolver;

    $this->initialize($options);
  }

  protected function initialize(array $options)
  {
    $this->configure($options);
  }

  public function getDefaultOptions()
  {
    return array(
      'file_name' => null,
      'file_size' => null,
      'mime_type' => 'application/download'
    );
  }

  public function execute($fileOrData)
  {
    if (is_readable($fileOrData))
    {
      $this->configureFromFile($file = $fileOrData);
    }
    else
    {
      $this->configureFromData($data = $fileOrData);
    }

    $this->writeHeaders();

    if(isset($file))
    {
      readfile($file);
    }
    else
    {
      print $data;
    }

    exit;
  }

  protected function writeHeaders()
  {
    $this->response->clearHttpHeaders();

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");

    //Use the switch-generated Content-Type
    header('Content-Type: '.$this->getOption('mime_type'));

    //Force the download
    header("Content-Disposition: attachment; filename=\"".$this->getOption('file_name')."\";");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$this->getOption('file_size'));
  }

  protected function configureFromFile($file)
  {
    if(!$this->getOption('file_name'))
    {
      $this->setOption('file_name', basename($file));
    }

    if(!$this->getOption('file_size'))
    {
      $this->setOption('file_size', filesize($file));
    }

    if(!$this->getOption('mime_type'))
    {
      $this->setOption('mime_type', $this->mimeTypeResolver->getByFilename($options['file_name']));
    }
  }

  protected function configureFromData($data)
  {
    if(!$this->getOption('file_name'))
    {
      $this->setOption('file_name', dmString::slugify(dmConfig::get('site_name')).'-'.dmString::random(8));
    }

    if(!$this->getOption('file_size'))
    {
      $this->setOption('file_size', strlen($data));
    }
  }
}