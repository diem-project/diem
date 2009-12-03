<?php

abstract class dmBaseActions extends sfActions
{
  protected function forwardSecureUnless($condition)
  {
    if (!$condition)
    {
      return $this->forwardSecure();
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
   * Important : due to a limitation of the jquery form plugin (http://jquery.malsup.com/form/#file-upload)
   * when a file have been uploaded, the contentType is set to text/html
   * and the json response is wrapped into a textarea
   *
   * @param string $json Json to append to the response
   *
   * @return sfView::NONE
   */
  public function renderJson($json)
  {
    $this->response->clearJavascripts();
    $this->response->clearStylesheets();
    $this->setLayout(false);
    sfConfig::set('sf_web_debug', false);
    
    $encodedJson = json_encode($json);
    
    if ($this->request->isMethod('post') && $this->request->isXmlHttpRequest() && !in_array('application/json', $this->request->getAcceptableContentTypes()))
    {
      $this->response->setContentType('text/html');
      $this->response->setContent('<textarea>'.$encodedJson.'</textarea>');
    }
    else
    {
      $this->response->setContentType('application/json');
      $this->response->setContent($encodedJson);
    }

    return sfView::NONE;
  }  
  
  protected function redirectBack()
  {    
    return $this->redirect($this->getBackUrl());
  }
  
  protected function getBackUrl()
  {
    $backUrl = $this->request->getReferer();

    if (!$backUrl || ($backUrl == $this->request->getUri() && $this->request->isMethod('get')))
    {
      $backUrl = '@homepage';
    }
    
    return $backUrl;
  }
  
  
  protected function getRouting()
  {
    return $this->context->getRouting();
  }
  
  protected function getHelper()
  {
    return $this->context->getHelper();
  }
  
  protected function getServiceContainer()
  {
    return $this->context->getServiceContainer();
  }
  
  /*
   * To download a file using its absolute path or raw data
   */
  protected function download($pathOrData, array $options = array())
  {
    if (is_readable($pathOrData))
    {
      $data = file_get_contents($pathOrData);

      if(empty($options['file_name']))
      {
        $options['file_name'] = dmProject::getKey().'-'.basename($path);
      }
    }
    else
    {
      $data = $pathOrData;

      if(empty($options['file_name']))
      {
        $options['file_name'] = dmProject::getKey().'-'.dmString::random(8);
      }
    }

    //Gather relevent info about file
    $fileLenght = strlen($data);
    $fileType = dmArray::get($options, 'type', dmOs::getFileMime($options['file_name']));

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");

    //Use the switch-generated Content-Type
    header("Content-Type: $fileType");

    //Force the download
    header("Content-Disposition: attachment; filename=\"".$options['file_name']."\";");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$fileLenght);
    print $data;
    exit;
  }

}