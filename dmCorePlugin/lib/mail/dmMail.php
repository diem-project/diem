<?php

class dmMail
{
  protected
  $mailTemplate,
  $data,
  $attributes = array(
    'title' => null,
    'body'  => null,
    'from'  => array(),
    'to'    => array()
  );

  protected static
  $swiftLoaded = false;

  public static function build($mailTemplateName)
  {
    if (!$mailTemplate = dmDb::query('DmMailTemplate t')->where('t.name = ?', $mailTemplateName))
    {
      throw new dmException('Can not build a dmMail because '.$mailTemplateName.' is not a valid mail template name');
    }

    $mail = new self($mailTemplate);
  }

  public function __construct(DmMailTemplate $mailTemplate)
  {
    $this->mailTemplate = $mailTemplate;
  }

  public function set($data)
  {
    if ($data instanceof myDoctrinRecord)
    {
      $data = $data->toArray();
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
    $this->attributes = array(
    'title' => null,
    'body'  => null,
    'from'  => array('email' => 'name'),
    'to'    => array('email' => 'name')
    );
  }
}