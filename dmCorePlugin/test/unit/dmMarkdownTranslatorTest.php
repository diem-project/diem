<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test();

$helper->get('user')->setCulture('fr');

$msgs = $helper->get('markdown_translator')->execute();

$t->ok(isset($msgs['Link']), 'Link is translated');

$t->is($msgs['Link'], 'Lien', 'Link is translated to Lien');

$t->ok(isset($msgs['Heading 3']), 'Heading 3 is translated');

$t->is($msgs['Heading 3'], 'Titre 3', 'Heading 3 is translated to Titre 3');