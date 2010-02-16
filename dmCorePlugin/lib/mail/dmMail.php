<?php

class dmMail
{
  protected
  $serviceContainer,
  $template,
  $data,
  $attributes;

  protected static
  $swiftLoaded = false;
  
  public function __construct(dmBaseServiceContainer $serviceContainer)
  {
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize();
  }
  
  protected function initialize()
  {
    $this->data = array();
    $this->attributes = array(
      'title' => null,
      'body'  => null,
      'from'  => array(),
      'to'    => array()
    );
  }

  public function setTemplate($templateName)
  {
    if (!$this->template = dmDb::query('DmMailTemplate t')->where('t.name = ?', $templateName)->fetchRecord())
    {
      $this->template = dmDb::create('DmMailTemplate', array('name' => $templateName));
    }
    
    return $this;
  }

  public function set($data)
  {
    if ($data instanceof dmDoctrinRecord)
    {
      $data = $data->toArray();
    }
    elseif($data instanceof dmFormDoctrine)
    {
      $data = $data->getObject()->toArray();
    }
    elseif($data instanceof dmForm)
    {
      $data = $data->getValues();
    }
    else
    {
      $data = (array) $data;
    }

    $this->data = array_merge($this->data, $data);

    return $this;
  }

  public function send()
  {
    $this->bind();
    
    throw new dmException('TODO');

    //Create a message
    $message = Swift_Message::newInstance($this->get('title'))
    ->setFrom($this->get('from'))
    ->setTo($this->get('to'))
    ->setBody($this->get('body'));

    //Send the message
    $result = $mailer->send($message);
    
    return $this;
  }

  public function toDebug()
  {
    $this->bind();

    return $this->attributes;
  }
  
  protected function get($name)
  {
    return $this->attributes[$name];
  }

  protected function bind()
  {
    if (!$this->template instanceof DmMailTemplate)
    {
      throw new dmException('You must call setTemplate() to set a mail template');
    }
    
    $this->updateTemplate();
    
    $this->attributes = array(
      'title' => null,
      'body'  => null,
      'from'  => array('email' => 'name'),
      'to'    => array('email' => 'name')
    );
    
    dmDebug::kill($this->attributes, $this->data);
  }
  
  protected function updateTemplate()
  {
    if ($this->template->get('vars') != implode(', ', array_keys($this->data)))
    {
      $this->template->set('vars', implode(', ', array_keys($this->data)));
    }
    
    if (!$this->template->get('body'))
    {
      $body = array();
      foreach($this->data as $key =>  $value)
      {
        $body[] = dmString::humanize($key).' : '.$this->wrap($key);
      }
      $this->template->set('body', implode("\n", $body));
    }
    
    if($this->template->isModified())
    {
      $this->template->save();
    }
  }
  
  public function wrap($key)
  {
    return '%'.$key.'%';
  }
}