<?php

class dmMimeTypeResolver
{
  protected
  $mimeTypes;
  
  public function getByFilename($file, $default = null)
  {
    return $this->getByExtension(pathinfo($file,PATHINFO_EXTENSION), $default);
  }
  
  public function getGroupByFilename($file)
  {
    return $this->getMimeTypeGroup($this->getByFilename($file));
  }
  
  public function getByExtension($extension, $default = null)
  {
    if(!$extension)
    {
      return $default;
    }
    
    $extension  = strtolower(trim($extension, '.'));
    $mimeTypes  = $this->getMimeTypes();
    
    if (isset($mimeTypes[$extension]))
    {
      $mimeType = $mimeTypes[$extension];
    }
    else
    {
      $mimeType = $default;
    }
    
    unset($mimeTypes);
    
    return $mimeType;
  }
  
  public function getGroupByExtension($extension)
  {
    return $this->getMimeTypeGroup($this->getByExtension($extension));
  }
  
  public function getMimeTypeGroup($mimeType)
  {
    return substr($mimeType, 0, strpos($mimeType, '/'));
  }
  
  protected function getMimeTypes()
  {
    if (null === $this->mimeTypes)
    {
      $this->mimeTypes = include(dirname(__FILE__).'/data/dmMimeTypes.php');
    }
    
    return $this->mimeTypes;
  }
  
}