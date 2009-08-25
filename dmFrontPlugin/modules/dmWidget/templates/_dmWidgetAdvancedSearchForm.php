<?php

echo
$form->open('action=main/search method=get'),

$form['query']->renderRow(),

$form->renderSubmitTag(__('Search')),

$form->close();