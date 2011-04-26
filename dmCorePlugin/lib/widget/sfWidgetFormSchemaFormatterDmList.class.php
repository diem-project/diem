<?php

class sfWidgetFormSchemaFormatterDmList extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<li class=\"dm_form_element clearfix\">\n  %error%%label%\n  %field%%help%\n%hidden_fields%</li>\n",
    $errorRowFormat  = "<li>\n%errors%</li>\n",
    $helpFormat      = '<div class="dm_help_wrap">%help%</div>',
    $decoratorFormat = "<ul class=\"dm_form_elements\">\n  %content%</ul>";
}
