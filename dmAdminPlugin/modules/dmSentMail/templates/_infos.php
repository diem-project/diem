<?php
use_stylesheet('admin.dataTable');
use_helper('Date');

$mail = $dmSentMail;

echo _tag('div.dm_data',
  _table()->useStrip(true)
  ->body('From', $mail->from_email)
  ->body('To', $mail->to_email)
  ->body('CC', $mail->cc_email)
  ->body('BCC', $mail->bcc_email)
  ->body('Sender', $mail->sender_email)
  ->body('Reply to', $mail->reply_to_email)
  ->body('Date', format_date($mail->created_at, 'f'))
  ->body('Language', format_language($mail->culture))
  ->body('Template', _link($mail->Template))
  ->body('Strategy', $mail->strategy)
  ->body('Transport', $mail->transport)
);