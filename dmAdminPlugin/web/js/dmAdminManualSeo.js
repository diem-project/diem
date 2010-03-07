(function($)
{

  var $table = $('#dm_page_meta_table');

  $table.dataTable({
    "oLanguage": {
      "sUrl": $table.metadata().translation_url
    },
    "bJQueryUI": true,
    "sPaginationType": "full_numbers"
  });

  var editCallback = function( sValue, y ) {
    var aPos = $table.fnGetPosition( this );
    $table.fnUpdate( sValue, aPos[0], aPos[1] );
  };

  var submitdata = function ( value, settings ) {
    return {
      page_id: this.parentNode.getAttribute('id'),
      field: this.getAttribute('rel')
    };
  };

  $table.find('td.editable').one('mouseover', function()
  {
    $(this).editable($table.metadata().edition_url, {
      type: $(this).hasClass('edit_textarea') ? 'textarea' : 'text',
      height: $(this).hasClass('edit_textarea') ? 'auto' : '20px',
      width: '80%',
      submit: 'OK',
      callback: editCallback,
      submitdata: submitdata,
      placeholder: ''
    });
  });

  // toggle booleans
  $table.find('span.boolean').click(function() {
    $(this).toggleClass('s16_tick s16_cross');
    $.ajax({
      url:      $table.metadata().toggle_url,
      data:     {
        field:  this.getAttribute('rel'),
        page_id: this.parentNode.parentNode.getAttribute('id')
      },
      success:  function(data) {
        $(this).toggleClass('s16_tick', '1' == data).toggleClass('s16_cross', '0' == data);
      }
    });
  });
  
})(jQuery);