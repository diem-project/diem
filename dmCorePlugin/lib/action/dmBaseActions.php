<?php

abstract class dmBaseActions extends sfActions
{
  protected function forwardSecureUnless($condition, $message = null)
  {
    if (!$condition)
    {
      return $this->forwardSecure($message);
    }
  }

  protected function forwardSecure()
  {
    return $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
  }

  
  /**
   * Appends the given json to the response content and bypasses the built-in view system.
   *
   * This method must be called as with a return:
   *
   * <code>return $this->renderJson(array('key'=>'value'))</code>
   *
   * @param string $json Json to append to the response
   *
   * @return sfView::NONE
   */
  public function renderJson($json)
  {
    $this->response->clearJavascripts();
    $this->response->clearStylesheets();
    $this->response->setContentType('application/json');
    $this->setLayout(false);
    sfConfig::set('sf_web_debug', false);
    
    $this->response->setContent(json_encode($json));

    return sfView::NONE;
  }  
  
  protected function redirectBack()
  {
    $refererUrl = $this->request->getReferer();

    if (!$refererUrl || $refererUrl == $this->request->getUri())
    {
      $refererUrl = '@homepage';
    }
    
    return $this->redirect($refererUrl);
  }
  
  /*
   * To download a file using its absolute path or raw data
   */
  protected function download($pathOrData, array $options = array())
  {
    if (is_readable($pathOrData))
    {
      $data = file_get_contents($pathOrData);

      if(empty($options['fileName']))
      {
        $options['fileName'] = dmProject::getKey().'-'.basename($path);
      }
    }
    else
    {
      $data = $pathOrData;

      if(empty($options['fileName']))
      {
        $options['fileName'] = dmProject::getKey().'-'.dmString::random(8);
      }
    }

    //Gather relevent info about file
    $fileLenght = strlen($data);
    $fileType = dmArray::get($options, 'type', dmOs::getFileMime($options['filename']));

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");

    //Use the switch-generated Content-Type
    header("Content-Type: $fileType");

    //Force the download
    header("Content-Disposition: attachment; filename=\"".$options['filename']."\";");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$fileLenght);
    print $data;
    exit;
  }

}