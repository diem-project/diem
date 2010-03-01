<?php

echo _open('div.dm_auto_seo_manager.ui-tabs.ui-widget.ui-widget-content.ui-corner-all.mt10');

include_partial('dmAutoSeo/tabs', array('autoSeos' => $autoSeos, 'current' => $autoSeo));

echo _open('div.dm_auto_seo.clearfix');

echo _tag('div.dm_half1',
  _tag('h2', __('1. Edit meta generation rules')).
  
  _tag('div.clearfix',
  
    _tag('div.dm_variables_wrap.mt10', get_component('dmAutoSeo', 'variables', array('autoSeo' => $autoSeo))).
  
    _tag('div.dm_form_wrap.mt10',
      $form->open('.dm_form.list').
        '<ul class="dm_form_elements">'.
        $form->getFormFieldSchema()->render().
        $form->renderHiddenFields().
        _tag('li.dm_form_element',
          _tag('label for=preview', __('Preview')).
          $form->renderSubmitTag(__('Preview'), 'name=preview').
          _tag('div.dm_help_wrap', __('Preview modifications without applying changes to the site'))
        ).
        _tag('li.dm_form_element',
          _tag('label for=preview', __('Save')).
          $form->renderSubmitTag(__('Save'), 'name=save').
          _tag('div.dm_help_wrap', __('Save modifications and apply changes to the site'))
        ).
        '</ul>'.
      $form->close()
    )
  )
);

echo _open('div.dm_half2');

echo _tag('h2', __('2. Preview generated metas'));

echo _tag('div.mt10',
  get_component('dmAutoSeo', 'preview', array('autoSeo' => $autoSeo, 'form' => $form, 'rules' => $previewRules))
);

echo _close('div');

echo _close('div');

echo _close('div');