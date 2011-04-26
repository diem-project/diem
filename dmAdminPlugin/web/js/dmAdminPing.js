(function($)
{

  $.dm.ping = $.extend($.dm.corePing, {

    users: '',
    locks: '',

    init: function(options)
    {
      this.initCore(options);
    },

    pong: function(data)
    {
      var self = $.dm.ping;

      if(data && data.users != self.users)
      {
        self.renderUsers(data.users.split('|'));
        self.users = data.users;
      }

      if(data && data.locks != self.locks)
      {
        self.renderLocks(data.locks.split('|'));
        self.locks = data.locks;
      }
      
      setTimeout(self.ping, self.options.delay);
    },

    renderUsers: function(users)
    {
      $('#dm_tool_bar div.dm_active_users').html('<span>'+users.join('</span><span>')+'</span>');
    },

    renderLocks: function(locks)
    {
      if($locks = $('#dm_admin_content div.dm_active_locks').orNot())
      {
        $locks.html(locks[0] ? '<span>'+locks.join('</span><span>')+'</span>' : '');
      }
    }
  });
  
})(jQuery);
