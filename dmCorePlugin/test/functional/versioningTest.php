<?php

require_once(getcwd().'/test/bootstrap/unit.php');

$browser = new sfTestFunctional(new sfBrowser());

$browser->post('/security/signin', array(
  'signin' => array(
    'username' => 'admin',
    'password' => 'admin'
  )
));

$browser->
  get('/test/ask_confirmation')->
  click('Yes')->
  with('request')->begin()->
    isParameter('sympal_ask_confirmation', 1)->
    isParameter('yes', 'Yes')->
  end()->
  with('response')->begin()->
    matches('/Ok!/')->
  end()->
  get('/admin/dashboard')->
  get('/test/ask_confirmation')->
  click('No')->
  with('response')->begin()->
    isRedirected()->
    followRedirect()->
  end()->
  with('request')->begin()->
    isParameter('module', 'sympal_dashboard')->
    isParameter('action', 'index')->
  end()
;