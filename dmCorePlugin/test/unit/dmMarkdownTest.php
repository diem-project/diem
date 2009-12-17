<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(8);

$markdown = $helper->get('markdown');

$t->comment('Basic features');
$markdownText = 'this is a **markdown** text with *some basic* features';

$html = '<p class="dm_first_p">this is a <strong>markdown</strong> text with <em>some basic</em> features</p>';
$t->is($markdown->toHtml($markdownText), $html, $markdown->toHtml($markdownText));

$text = 'this is a markdown text with some basic features';
$t->is($markdown->toText($markdownText), $text, $markdown->toText($markdownText));

$text = 'this is a markdown text with some basic features';
$t->is($markdown->brutalToText($markdownText), $text, $markdown->brutalToText($markdownText));
$t->is($markdown->toHtml($markdownText), $html, $markdown->toHtml($markdownText));

$t->comment('Standart image inclusion');
$markdownText = 'this is a **markdown** ![image alt text](/uploads/image.png) [with](http://diem-project.org "link title") *advanced* features';

$text = 'this is a markdown  with advanced features';
$t->is($markdown->toText($markdownText), $text, $markdown->toText($markdownText));

$text = 'this is a markdown image alt text with advanced features';
$t->is($markdown->brutalToText($markdownText), $text, $markdown->brutalToText($markdownText));

$text = 'this is a markdown image alt text with advanced features';
$t->is($markdown->brutalToText($markdownText), $text, $markdown->brutalToText($markdownText));

$text = <<<EOF
##Replace a Diem service

Let's suppose we want to change the way Diem detect browsers. This is done with the *browser* service. We will tell Diem to use another class for the browser service in
**config/dm/services.yml**
~~~
parameters:

  browser.class:      myBrowser
~~~

Then we create the myBrowser class in
**lib/myBrowser.php**
EOF;
$expectedHtml = <<<EOF
<h2 id="replace-a-diem-service">Replace a Diem service</h2>

<p class="dm_first_p">Let's suppose we want to change the way Diem detect browsers. This is done with the <em>browser</em> service. We will tell Diem to use another class for the browser service in<br />

<strong>config/dm/services.yml</strong></p>

<pre><code>parameters:  

  browser.class:      myBrowser  
</code></pre>

<p>Then we create the myBrowser class in<br />
<strong>lib/myBrowser.php</strong></p>
EOF;

$expectedHtml = str_replace("\n", '', $expectedHtml);
$html = str_replace("\n" ,'', $markdown->toHtml($text));

$t->is($html, $expectedHtml, 'Successfully transformed text h2, strong and code tags');