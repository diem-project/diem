<?php

class dmPixlr
{
  
  public function getPostUrl()
  {
    $query_vars = array("file"=>$file, "target"=>$target);
    foreach(array('referrer','title','exit','loc','app','target_vars','save_to') as $key)
    {
      if(isset($options[$key]))
      {
        $query_vars[$key] = $options[$key];
      }
    }
  
    if(!isset($options['skip_default']) || $options['skip_default']==FALSE)
    {
      $query_vars['target'] = url_for("@sf_pixlr_save?options=".base64_encode(serialize($query_vars)), true);
    }
  
    return url_for("@sf_pixlr_post?".http_build_query($query_vars, '', '&'));
  }

  public function save(sfWebRequest $request)
  {
    $options = base64_decode($request->getParameter("options"));
    $options = unserialize($options);

    $options = array_merge(array(
      'save_to' => null,
      'target_vars' => true,
    ), $options);

    $state = $request->getParameter("state");
    $url = $request->getParameter("image");
    $extension = $request->getParameter("type");

    if($state!="fetched")
    {
      throw new sfException("Unknown pixlr state: {$state}");
    }

    if(substr($url, 0, strlen(sfPixlrTools::PIXLR_URL))!=sfPixlrTools::PIXLR_URL)
    {
      throw new sfException("Unrecognized url: {$url}");
    }

    if($options['target_vars'])
    {
      $target_parts = explode("#", $options['target'], 2);
      $options['target'] = $target_parts[0].(strpos($options['target'], "?")===FALSE?"?":"&").http_build_query($request->getGetParameters(), '', '&');
      if(isset($target_parts[1]))
      {
        $options['target'] .= "#".$target_parts[1];
      }
    }

    if($options['save_to'])
    {

      $full_path = sfConfig::get('app_pixlr_upload_dir', sfConfig::get('sf_upload_dir'));
      if(is_string($options['save_to']))
      {
        $options['save_to'] = preg_replace('/(^|[\/\\\\]??)([\\.\\s]+)($|[\/\\\\])/', '/', $options['save_to']);
        $full_path .= "/".$options['save_to'];
      }
      $name = $this->getUniqueFilename($request->getParameter("title").".".$extension, $full_path);

      $this->copyFromUrl($url, "{$full_path}/{$name}");
    }

    //$this->redirect($options['target']);


  }

  private function getUniqueFilename($name, $full_path)
  {
    return $name;

//    if(!file_exists("{$full_path}/{$name}"))
//    {
//      return $name;
//    }
//    $pathinfo = pathinfo($name);
//    return $pathinfo['filename']."-".md5(uniqid()).".".$pathinfo['extension'];
  }

  private function copyFromUrl($url, $full_path)
  {
//    dmDebug::log("aaaaaaaaaaaaaa {$url}->{$full_path}");

    $file = file_get_contents($url);
    if($file===FALSE)
    {
      throw new sfException("File {$url} could not be retrieved.");
    }

    $success = file_put_contents($full_path, $file);
    if($success===FALSE)
    {
      throw new sfException("File {$full_path} could not be saved.");
    }

    $permission = sfConfig::get('app_pixlr_upload_permissions');
    if($permission===null)
    {
      $permission = fileperms(sfConfig::get('app_pixlr_upload_dir', sfConfig::get('sf_upload_dir')));
    }

    chmod($full_path, $permission);

    return $success;
  }

}