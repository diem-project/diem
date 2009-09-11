<?php use_helper('Form');

echo $form->renderGlobalErrors();

echo $form->render('.dm_form.list.little action=dmMediaLibrary/saveFolder');

//echo
//$form->open('.dm_list_form.little').
//£('ul',
//  £('li', $form['name']->)