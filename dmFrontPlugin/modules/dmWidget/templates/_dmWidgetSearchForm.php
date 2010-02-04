<?php

echo
$form->open('action=main/search method=get'),

$form['query']->label(__('Query'))->field('.query'),

$form->submit(__('Search')),

$form->close();