(function($) {
  
$.dm.galleryCtrl = {

  init: function()
  {
    this.$ = $("div.dm_gallery_big");
    
    this.metadata = this.$.metadata();
    
		this.form();
		
		this.sort();
  },
	
	form: function()
	{
		var self = this;
		
		self.$.find('a.open_form').click(function() {
			self.$.find('form.dm_add_media').toggle();
		});
	},
	
	sort: function()
	{
		var self = this, $list = self.$.find('ul.list');
		
		$list.sortable({
			tolerance:              'pointer',
      opacity:                0.5,
      placeholder:            'ui-state-highlight',
      revert:                 true,
      scroll:                 true,
			distance:               10,
      start:                  function(e, ui) {
        ui.placeholder.html(ui.item.html());
      },
			stop: function() {
				$.ajax({
					url: $.dm.ctrl.getHref('+/dmMedia/sortGallery?model='+self.metadata.model+'&pk='+self.metadata.pk+'&'+$list.sortable('serialize'))
				});
			}
		});
	}

};

$.dm.ctrl.add($.dm.galleryCtrl);

})(jQuery);