<?php
/**
 */
class PluginDmSentMailTable extends myDoctrineTable
{

  /**
   * Creates a DmSentMail from a Swift_Message
   * @param Swift_Message $message
   * @return DmSentMail
   */
  public function createFromSwiftMessage(Swift_Message $message)
  {
    $debug = $message->toString();

    if($attachementPosition = strpos($debug, 'attachment; filename='))
    {
      $debug = substr($debug, 0, $attachementPosition);
    }

    return $this->create(array(
      'subject'         => $message->getSubject(),
      'body'            => $message->getBody(),
      'from_email'      => implode(', ', array_keys((array)$message->getFrom())),
      'to_email'        => implode(', ', array_keys((array)$message->getTo())),
      'cc_email'        => implode(', ', array_keys((array)$message->getCC())),
      'bcc_email'       => implode(', ', array_keys((array)$message->getBCC())),
      'reply_to_email'  => implode(', ', array_keys((array)$message->getReplyTo())),
      'sender_email'    => implode(', ', array_keys((array)$message->getSender())),
      'debug_string'    => $debug
    ));
  }
}