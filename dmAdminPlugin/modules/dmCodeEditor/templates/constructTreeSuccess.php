<?php 
if($isRootUrl)
{
  echo '['."\n";
  echo "\t".'{ attributes: { id : "_-SLASH-_" }, state: "closed", data: { title : "/" ,  attributes : {"class" : "dir root" } } },'."\n";
  echo ']'."\n";
}
else
{
  echo '['."\n";
  foreach( $arrayDir as $dir )
  {
      $class_dir = "dir ";
      if(is_readable($dir))
      {
        $class_dir .= "readable_dir ";
        $state = "closed";
      }
      else
      {
        $class_dir .= "not_readable_dir ";
        $state = ""; 
      }
      if(is_writable($dir))
      {
        $class_dir .= "writable_dir ";
      }
      else
      {
        $class_dir .= "not_writable_dir ";
      }
      echo "\t".'{ attributes: { id : "'.dmCodeEditorTools::encodeUrlTree($dir).'" }, state: "'.$state.'", data: { title : "'.basename($dir).'",  attributes : {"class" : "'.$class_dir.'"} } },'."\n";
  }
  foreach( $arrayFile as $file )
  {
      $class_file = "file ";
      if(is_readable($file))
      {
        $class_file .= "readable_file ";
      }
      if(is_writable($file))
      {
        $class_file .= "writable_file ";
      }
      echo "\t".'{ attributes: { id : "'.dmCodeEditorTools::encodeUrlTree($file).'" }, state: "", data: { title : "'.basename($file).'", attributes : {"class" : "'.$class_file.' file_'.strtolower(dmOs::getFileExtension($file, false)).'" } } },'."\n";
  }
  echo ']'."\n";
}