/* 
 * Numbers with HTML sorting by Allan Jardine <http://www.sprymedia.co.uk/>
 */
jQuery.fn.dataTableExt.oSort['num-html-asc']  = function(a,b) {
  var x = a.replace( /<.*?>/g, "" );
  var y = b.replace( /<.*?>/g, "" );
  x = parseFloat( x );
  y = parseFloat( y );
  return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

jQuery.fn.dataTableExt.oSort['num-html-desc'] = function(a,b) {
  var x = a.replace( /<.*?>/g, "" );
  var y = b.replace( /<.*?>/g, "" );
  x = parseFloat( x );
  y = parseFloat( y );
  return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

jQuery.fn.dataTableExt.aTypes.push( function ( sData )
{
  sData = typeof sData.replace == 'function' ? sData.replace( /<.*?>/g, "" ) : sData;

  var sValidFirstChars = "0123456789-";
  var sValidChars = "0123456789.";
  var Char;
  var bDecimal = false;

  /* Check for a valid first char (no period and allow negatives) */
  Char = sData.charAt(0); 
  if (sValidFirstChars.indexOf(Char) == -1) 
  {
    return null;
  }

  /* Check all the other characters are valid */
  for (var i=1; i<sData.length; i++) 
  {
    Char = sData.charAt(i); 
    if (sValidChars.indexOf(Char) == -1) 
    {
      return null;
    }
  
    /* Only allowed one decimal place... */
    if (Char == "." )
    {
      if (bDecimal)
      {
        return null;
      }
      bDecimal = true;
    }
  }

  return 'num-html';
});

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