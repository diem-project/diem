<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test();

$values = array(
  'name' => 'Jo',
  'email' => 'jo@mail.com'
);

/**
 * @var dmMail
 */
$mail = $helper
->get('mail')
->setTemplate('test')
->addValues($values);
$template = $mail->getTemplate();
/**
 * @var Swift_Message
 */
$message = $mail->getMessage();

$t->ok(!$mail->isRendered(), 'Mail is not rendered');
$t->is_deeply($mail->getValues(), $values, 'Mail values are '.print_r($values, true));

$t->ok(!$template->exists(), 'The mail template does not exist');

$mail->render();

$t->ok($mail->isRendered(), 'Mail is rendered');

$t->ok($template->exists(), 'The mail template exists');

$expected = array('noreply@nomail.com' => null);
$t->is($message->getFrom(), $expected, 'Message from is '.print_r($expected, true));

$template->from_email = 'webmaster@site.org';
$template->save();

/**
 * @var dmMail
 */
$mail = $helper
->get('mail')
->setTemplate('test')
->addValues($values)
->render();
/**
 * @var Swift_Message
 */
$message = $mail->getMessage();

$expected = array('webmaster@site.org' => null);
$t->is($message->getFrom(), $expected, 'Message from is '.print_r($expected, true));

$template->from_email = 'Jack <webmaster@site.org>';
$template->save();

/**
 * @var dmMail
 */
$mail = $helper
->get('mail')
->setTemplate('test')
->addValues($values)
->render();
/**
 * @var Swift_Message
 */
$message = $mail->getMessage();

$expected = array('webmaster@site.org' => 'Jack');
$t->is($message->getFrom(), $expected, 'Message from is '.print_r($expected, true));