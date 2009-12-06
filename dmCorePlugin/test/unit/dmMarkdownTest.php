<?php

require_once(dirname(__FILE__).'/helper/dmTestHelper.php');
$helper = new dmTestHelper();
$helper->boot();

$t = new lime_test(3);

$markdown = $helper->get('markdown');

$markdownText = 'this is a **markdown** text with *some basic* features';

$html = '<p class="first_p">this is a <strong>markdown</strong> text with <em>some basic</em> features</p>';
$t->is($markdown->toHtml($markdownText), $html, $markdown->toHtml($markdownText));

$text = 'this is a markdown text with some basic features';
$t->is($markdown->toText($markdownText), $text, $markdown->toText($markdownText));

$text = 'this is a markdown text with some basic features';
$t->is($markdown->brutalToText($markdownText), $text, $markdown->brutalToText($markdownText));