<?php

include_partial('dmAutoSeo/tabs', array('autoSeos' => $autoSeos, 'current' => $autoSeo));

echo £o('div.dm_auto_seo.clearfix');

echo £('div.dm_half1',
  £('h2', '1. Edit meta generation rules').
  
  £('div.clearfix',
  
    £('div.dm_variables_wrap.mt10', get_component('dmAutoSeo', 'variables', array('autoSeo' => $autoSeo))).
  
    £('div.dm_form_wrap.mt10',
      $form->open('.dm_form.list').
        '<ul class="dm_form_elements">'.
        $form->getFormFieldSchema()->render().
        $form->renderHiddenFields().
        £('li.dm_form_element',
          £('label for=preview', __('Preview')).
          $form->renderSubmitTag(__('Preview'), 'name=preview').
          £('div.dm_help_wrap', __('Preview modifications without applying changes to the site'))
        ).
        £('li.dm_form_element',
          £('label for=preview', __('Save')).
          $form->renderSubmitTag(__('Save'), 'name=save').
          £('div.dm_help_wrap', __('Save modifications and apply changes to the site'))
        ).
        '</ul>'.
      $form->close()
    )
  )
);

echo £o('div.dm_half2');

echo £('h2', '2. Preview generated metas');

echo £('div.mt10',
  get_component('dmAutoSeo', 'preview', array('autoSeo' => $autoSeo, 'form' => $form, 'rules' => $previewRules))
);

echo £c('div');

echo £c('div');

echo £c('div');