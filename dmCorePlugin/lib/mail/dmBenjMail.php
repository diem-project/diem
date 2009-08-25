<?php

class dmBenjMail
{
  private $subject;
  private $to;
  private $from;
  private $fromName;
  private $txt;
  private $html;
  
  private $boundary;
  
  public function __construct(
      $subject = '',
      $to = '',
      $from = '',
      $txt = '',
      $html = '',
      $fromName = '')
  {
    $this->subject = $subject;
    $this->to = $to;
    $this->from = $from;
    $this->txt = $txt;
    $this->html = $html;
    $this->fromName = $fromName;
    $this->boundary = "-----=" . md5( uniqid ( rand() ) ); 
  }
  public function setTo($value)
  {
    $this->to = $value;
  }
  public function setFromName($value)
  {
    $this->fromName = $value;
  }
  public function setFromMail($value)
  {
    $this->from = $value;
  }
  public function setSubject($value)
  {
    $this->subject = $value;
  }
  public function setTextBody($value)
  {
    $this->txt = $value;
  }
  public function setHTMLBody($value)
  {
    $this->html = $value;
  }
  public function send()
  {
    
    $message  = "This is a multi-part message in MIME format.\n\n";
    $message .= "--" . $this->boundary . "\n";
    $message .= "Content-Type: Text/Plain; charset=\"iso-8859-1\"\n";
    $message .= "Content-Transfer-Encoding: quoted-printable\n\n";
    $message .= eregi_replace("\\\'","'",$this->txt);
    $message .= "\n\n";
    
    if($this->html != '') {
      $message .= "--" . $this->boundary . "\n";
      $message .= "Content-Type: Text/HTML; charset=\"iso-8859-1\"\n";
      $message .= "Content-Transfer-Encoding: quoted-printable\n\n";
      $message .= "<html>\n";
      $message .= "<body>\n";
  
      $this->html = eregi_replace("\\\'","'",$this->html);
      $message .= str_replace("=\"","=3D\"",$this->html);
  
      $message .= "</body>\n";
      $message .= "</html>\n";
      $message .= "\n\n";
      $message .= "--" . $this->boundary . "--\n";
    }
    
    return mail($this->to, $this->subject, $message, $this->headers());
  }
  private function headers()
  {
    $headers = "Return-Path: <".$this->from.">\n"; 
    //NOTE: l"adresse email indiquée dans le header From doit etre l"adresse absolue du serveur qui envoie les messages, et peut etre differente de votre adresse de contact si vous etes par exemple sur un serveur dedié partagé. dans mon cas l"adresse specifiee ici est <webusers@mail.nomduserveur.com> 
    $headers .= "MIME-Version: 1.0\n"; 
    $headers .= ($this->html != '') ? "Content-Type: Text/Plain;" : "Content-Type: multipart/alternative;";
    $headers .= " boundary=\"".$this->boundary."\"\n";
    $headers .= "Content-transfer-encoding: 8bit\n";
    $headers .= "Reply-to: \"".$this->fromName."\" <".$this->from.">\n"; 
    $headers .= "From: \"".$this->fromName."\" <".$this->from.">\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "X-auth-smtp-user: ".$this->from."\n";
    $headers .= "X-abuse-contact: ".$this->from."\n"; 
    
    return $headers;
  }
}