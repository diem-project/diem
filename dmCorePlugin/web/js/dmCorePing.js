(function($)
{

  $.dm.corePing = {

    options: {},

    initCore: function(options)
    {
      var self = $.dm.ping;

      self.options = $.extend({
        delay:    4000,
        url:      $.dm.ctrl.getHref('+/dmCore/ping')
      }, options || {});

      setTimeout(self.ping, 200+self.random());
    },

    random: function(max)
    {
      return Math.round(Math.random()*(max||500));
    },

    ping: function()
    {
      var self = $.dm.ping;

      $.ajax({
        dataType: 'json',
        url:      self.options.url,
        data:     self.getPingData(),
        success:  self.pong,
        error:    self.pong
      });
    },

    getPingData: function()
    {
      var self = $.dm.ping;

      return {
        sf_module:  self.options.module,
        sf_action:  self.options.action,
        record_id:  self.options.record_id,
        dm_nolog:   1
      };
    },

    pong: function(data)
    {
      var self = $.dm.ping;

      setTimeout(self.ping, self.options.delay+self.random());
    }
  };
  
})(jQuery);