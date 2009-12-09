<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$nbTests = 20;

$t = new lime_test(3);

$array = array(
  'text' => "

Interroger l’homme au travers des croyances, des gestes et des récits dont il enveloppe le subtil du monde pour y entrevoir sa propre forme.


En tout lieu retrouver les empreintes laissées par l’imaginaire sur la pierre, sur l’eau, sur la terre, dans le temps du calendrier, dans le souffle des mots, et dans l’ombre des gestes.


Reconstruire, fragment par fragment, ce sens que la parole du mythe, aux mille et une légendes chuchotées depuis des millénaires, pourrait nous faire saisir.


Et, inlassablement, reposer la question : qu’est-ce que l’homme ?",
  'tag' => 'h1',
  'css_class' => null,
  'other_info' => 'tagada test'
);


$array = array(
  'view' => 'big',
  'css_class' => null,
  'other_info' => 'tagada test'
);

$t->diag('json length : '.strlen(json_encode($array)));
$t->diag('yaml length : '.strlen(sfYaml::dump($array)));
$t->diag('serialize length : '.strlen(serialize($array)));

$t->diag(serialize($array));

$t->is(json_decode(json_encode($array), true), $array, 'json_works');
$t->is(sfYaml::load(sfYaml::dump($array)), $array, 'yaml works');
$t->is(unserialize(serialize($array)), $array, 'serialize works');

$json = dmDebug::timer('json');

for($it=1;$it<=$nbTests;$it++)
{
  json_decode(json_encode($array), true);
}

$t->diag('json time : '.$json->getElapsedTime());

$yaml = dmDebug::timer('yaml');

for($it=1;$it<=$nbTests;$it++)
{
  sfYaml::load(sfYaml::dump($array));
}

$t->diag('yaml time : '.$yaml->getElapsedTime());

$serialize = dmDebug::timer('serialize');

for($it=1;$it<=$nbTests;$it++)
{
  unserialize(serialize($array));
}

$t->diag('serialize time : '.$serialize->getElapsedTime());