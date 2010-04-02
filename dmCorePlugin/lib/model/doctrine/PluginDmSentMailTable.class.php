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
    return $this->create(array(
      'subject'         => $message->getSubject(),
      'body'            => $message->getBody(),
      'from_email'      => implode(', ', array_keys((array)$message->getFrom())),
      'to_email'        => implode(', ', array_keys((array)$message->getTo())),
      'cc_email'        => implode(', ', array_keys((array)$message->getCC())),
      'bcc_email'       => implode(', ', array_keys((array)$message->getBCC())),
      'reply_to_email'  => $message->getReplyTo(),
      'sender_email'    => $message->getSender(),
      'debug_string'    => $message->toString()
    ));
  }
}