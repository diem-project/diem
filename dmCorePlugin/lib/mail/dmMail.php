<?php

class dmMail
{
  protected
  $mailer,
  $dispatcher,
  $message,
  $template,
  $values,
  $isRendered;
  
  public function __construct(sfContext $context, sfEventDispatcher $dispatcher)
  {
    $this->mailer     = $context->getMailer();
    $this->dispatcher = $dispatcher;
    
    $this->initialize();
  }
  
  protected function initialize()
  {
    dm::enableMailer();
    
    $this->values     = array();
    $this->isRendered = false;
    $this->message    = Swift_Message::newInstance();
  }

  /**
   * Set a template to the mail
   *
   * @param mixed $templateName the template name, or a DmMailTemplateInstance
   * @return dmMail $this
   */
  public function setTemplate($templateName)
  {
    if($templateName instanceof DmMailTemplate)
    {
      $this->template = $templateName;
    }
    elseif (!$this->template = dmDb::query('DmMailTemplate t')->where('t.name = ?', $templateName)->fetchRecord())
    {
      $this->template = dmDb::table('DmMailTemplate')->createDefault($templateName);
    }
    
    return $this;
  }

  /**
   * Get the template used to build the mail
   *
   * @return DmMailTemplate the template instance
   */
  public function getTemplate()
  {
    return $this->template;
  }

  /**
   * Set a message manually to the mail
   *
   * @param Swift_Message $message the Swift message
   * @return dmMail $this
   */
  public function setMessage(Swift_Message $message)
  {
    $this->message = $message;

    return $this;
  }

  /**
   * Get the Swift message that will be sent
   *
   * @return Swift_Message the message instance
   */
  public function getMessage()
  {
    return $this->message;
  }

  /**
   * Set a mailer manually
   *
   * @param sfMailer $mailer another mailer
   * @return dmMail $this
   */
  public function setMailer(sfMailer $mailer)
  {
    $this->mailer = $mailer;

    return $this;
  }

  /**
   * Get the mailer used
   *
   * @return sfMailer the mailer instance
   */
  public function getMailer()
  {
    return $this->mailer;
  }

  /**
   * Add values to the mail that will be available in the template
   *
   * @param   mixed   $data   a record, a form or an array
   * @param   string  $prefix a prefix for this data
   * @return  dmMail  $this
   */
  public function addValues($values, $prefix = null)
  {
    if ($values instanceof dmDoctrineRecord)
    {
      $values = $values->toArray();
    }
    elseif($values instanceof dmFormDoctrine)
    {
      $values = $values->getObject()->toArray();
    }
    elseif($values instanceof dmForm)
    {
      $values = $values->getValues();
    }

    if(!is_array($values))
    {
      throw new dmException('dmMail->setValues supports records, forms and arrays');
    }

    foreach($values as $key => $value)
    {
      if(is_array($value))
      {
        unset($values[$key]);
      }
      elseif(is_object($value))
      {
        try
        {
          $values[$key] = (string)$value;
        }
        catch(Exception $e)
        {
          unset($values[$key]);
        }
      }
      elseif($prefix)
      {
        $values[$prefix.$key] = $value;
        unset($values[$key]);
      }
    }

    $this->values = array_merge($this->values, $values);

    return $this;
  }

  /**
   * Return values that will be available in the template
   *
   * @return array $values
   */
  public function getValues()
  {
    return $this->values;
  }

  /**
   * Binds the mail with available data
   * Uses Swift to send it.
   *
   * @return dmMail $this
   */
  public function send()
  {
    if(!$this->getTemplate()->get('is_active'))
    {
      return $this;
    }
    
    if(!$this->isRendered())
    {
      $this->render();
    }

    $eventParams = array(
      'mailer'    => $this->getMailer(),
      'message'   => $this->getMessage(),
      'template'  => $this->getTemplate()
    );

    $this->dispatcher->notify(new sfEvent($this, 'dm.mail.pre_send', $eventParams));

    $this->getMailer()->send($this->getMessage());

    $this->dispatcher->notify(new sfEvent($this, 'dm.mail.post_send', $eventParams));
    
    return $this;
  }

  public function isRendered()
  {
    return $this->isRendered;
  }

  /**
   * Builds the Swift message inserting vars in templates
   *
   * @return dmMail $this
   */
  public function render()
  {
    if (!$this->getTemplate())
    {
      throw new dmMailException('You must call setTemplate() to set a mail template');
    }
    
    $this->updateTemplate();
    
    $template = $this->getTemplate();
    $replacements = $this->getReplacements();
    $message = $this->getMessage();
    
    $message
    ->setContentType($template->isHtml ? "text/html" : "text/plain")
    ->setSubject(strtr($template->subject, $replacements))
    ->setBody(strtr($template->body, $replacements))
    ->setFrom($this->emailListToArray(strtr($template->from_email, $replacements)))
    ->setTo($this->emailListToArray(strtr($template->to_email, $replacements)));

    foreach(array('cc', 'bcc', 'reply_to', 'sender') as $field)
    {
      if($value = $template->get($field.'_email'))
      {
        $processedValue = $this->emailListToArray(strtr($value, $replacements));
        
        $message->{'set'.dmString::camelize($field)}($processedValue);
      }
    }

    $headers = $message->getHeaders();

    if($headers->has('List-Unsubscribe'))
    {
      $headers->remove('List-Unsubscribe');
    }

    if($template->list_unsubscribe)
    {
      $headers->addTextHeader('List-Unsubscribe', strtr($template->list_unsubscribe, $replacements));
    }

    $this->isRendered = true;

    return $this;
  }

  protected function getReplacements()
  {
    $replacements = array();

    foreach($this->getValues() as $key => $value)
    {
      $replacements[$this->wrap($key)] = $value;
    }

    return $replacements;
  }

  protected function emailListToArray($emails)
  {
    $entries = array_unique(array_filter(array_map('trim', explode(',', str_replace("\n", ',', $emails)))));
    $emails = array();
    foreach($entries as $entry)
    {
      if(preg_match('/^.+\s<.+>$/', $entry))
      {
        $email  = preg_replace('/^.+\s<(.+)>$/', '$1', $entry);
        $name   = preg_replace('/^(.+)\s<.+$/', '$1', $entry);
        $emails[$email] = $name;
      }
      else
      {
        $emails[$entry] = null;
      }
    }

    return $emails;
  }
  
  protected function updateTemplate()
  {
    $template     = $this->getTemplate();
    $templateVars = array_keys($this->getValues());

    natsort($templateVars);

    if ($template->get('vars') != implode(', ', $templateVars))
    {
      $template->set('vars', implode(', ', $templateVars));
    }
    
    if (!$template->get('body'))
    {
      $body = array();
      foreach($this->getValues() as $key =>  $value)
      {
        $body[] = dmString::humanize($key).' : '.$this->wrap($key);
      }
      $template->set('body', implode("\n", $body));
    }
    
    if($template->isModified())
    {
      $template->save();
    }
  }
  
  protected function wrap($key)
  {
    return '%'.$key.'%';
  }
}