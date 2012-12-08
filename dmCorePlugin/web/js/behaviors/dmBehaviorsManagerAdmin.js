;(function($) {           
    $.dm.behaviorsManager = $.extend($.dm.behaviorsManager, {  
        contextMenuTemplate: null,
        contextMenu: null,
        settings: null,
        /*
         * Stops all behaviors
         */
        stop: function() {
            var self = this;
            if (self.state < 2) {
                self.init();
                return;
            }
            if (self.state == 2) return; 
            for(var i = self.behaviors.length - 1; i >= 0; --i){
                try {
                    $.dm.behaviors[self.behaviors[i].dm_behavior_key].stop(self.behaviors[i]); // Stop this behavior
                } catch(e) {
                    self.reportError({
                        javascript_exception    :   e.toString(),
                        method                  :   'stop',
                        behavior_settings       :   this
                    });
                };
            };
            self.state = 2;
        },
        /*
         * Destroys all behaviors
         */
        destroy: function() {
            var self = this;
            if (self.state == 1) return;
            if (self.state == 2) {
                for(var i = self.behaviors.length - 1; i >= 0; --i){
                    try {
                        $.dm.behaviors[self.behaviors[i].dm_behavior_key].destroy(self.behaviors[i]); // Destroy this behavior
                    } catch(e) {
                        self.reportError({
                            javascript_exception    :   e.toString(),
                            method                  :   'destroy',
                            behavior_settings       :   this
                        });
                    };
                };
                self.state = 1;
            };
            if (self.state > 2) {
                self.stop();
                self.destroy();
            };
        },
        /**
         * Initializes administration of the behaviors
         */
        initializeAdministration: function() {
            var self = this;
            self.initializeManager();
            self.init();
            self.loadSettings();
                       
            
            /*
             * Create required helper elements
             */
            self.contextMenuTemplate = $('<div class="dm dm_edit_behaviors_context_menu_template none"></div>');
            self.contextMenu = $('<div class="dm dm_edit_behaviors_context_menu none"><ul></ul></div>');
            
            /*
             * Append helper elements
             */
            $('body').append(self.contextMenuTemplate); // Appending context menu template
            $('body').append(self.contextMenu); // Appending actual context menu
            $('#dm_page').addClass('dm_page_' + dm_configuration.page_id); // Providing support for attaching behaviors onto page
            /*
             * Tracking events - behavior added, behavior deleted
             * Widget, zone added, deleted...
             * In order to enable drop behaviors, edit behaviors or remove behaviors from the manager
             */
            $('body').bind('behaviorAdded', function(){
                self.enableEditBehaviors();
            }).bind('behaviorDeleted', function(){
                self.enableEditBehaviors();
            }).bind('widgetAdded', function(){
                self.enableAddBehaviors();
            }).bind('zoneAdded', function(){
                self.enableAddBehaviors();
            }).bind('widgetDeleted', function(e, id){ // It is not required to fire destroy() - behaviors are removed
                var $widget = $('.dm_widget_' + id);
                var $inner = $widget.find('.dm_behaviors_attachable');
                $.each($inner, function(){
                    var meta = self.getAttachedToMetadata($(this));
                    self.cleanUpBehaviorManager(meta.dm_behavior_attached_to, meta.dm_behavior_attached_to_id);
                });
                self.cleanUpBehaviorManager('widget', id);
            }).bind('zoneDeleted', function(e, id){ // It is not required to fire destroy() - behaviors are removed
                var $zone = $('.dm_zone_' + id);
                var $inner = $zone.find('.dm_behaviors_attachable');
                $.each($inner, function(){
                    var meta = self.getAttachedToMetadata($(this));
                    self.cleanUpBehaviorManager(meta.dm_behavior_attached_to, meta.dm_behavior_attached_to_id);
                });
                self.cleanUpBehaviorManager('zone', id);
            });
            /*
             * Add some utilities to the helper elements, if required
             */
            $('.dm_edit_behaviors_icon').tipsy({gravity: $.fn.tipsy.autoNorth});            
            /*
             * Hide context menu, if required
             */
            $(document).click(function(evt){
                if (self.contextMenuTemplate == null) return;
                if (!$(evt.target).hasClass('dm_edit_behaviors_icon')) {
                    $('div.dm_edit_behaviors_context_menu').hide('fast');
                };
            });
            /*
             * Enable play-stop behaviors
             */
            $('body').bind('runBehaviors', function(e, doRun){
                if (doRun) {
                    $.dm.removeTipsy();
                    self.start();
                }
                else {
                    self.stop();
                    // If there is no good destroy() method for the used plugin...
                    self.enableAddBehaviors();
                    self.enableEditBehaviors(); 
                };
            });
        },
        /**
         * Utility function - when we remove zone or widget
         * It is required to remove all inner behaviors from the memory
         * They are deleted from database, but we can avoid refresh or another HTTP request
         * We do not need to call destroy()
         */
        cleanUpBehaviorManager: function(attachedTo, attachedToId) {
            var self = this;
            var search = -1;
            var id = -1;
            $.each(self.behaviors, function(index){
                if (this.dm_behavior_attached_to == attachedTo && this.dm_behavior_attached_to_id == attachedToId) {
                    id = this.dm_behavior_id;
                    search = index;
                    return false;
                };
            });                        
            if (search > -1){
                if (self.behaviors[search].dm_edit_dialog) self.behaviors[search].dm_edit_dialog.dialog('close');
                self.behaviors.splice(search, 1);
                $('body').trigger('behaviorDeleted',[id]);
            };            
        },
        /**
         * Utility function, loads the settings for the manager
         * privileges, translations, etc...
         * And initialize administration for the accepting behaviors management
         */
        loadSettings: function() {
            var self = this;
            $.ajax(dm_configuration.script_name + '+/dmBehaviors/getBehaviorsManagerSettings', {                
                type        :           'get',
                dataType    :           'json',
                error       :      function(xhr, textStatus, errorThrown) {                    
                    $.dm.ctrl.errorDialog('Error when getting behavior manager settings', xhr.responseText);
                },
                success: function(data) {
                    self.settings = data;                    
                    // Lets check if the page can accept the behaviors?
                    if (self.settings.page_attachable) {
                        $('#dm_page').addClass('dm_behaviors_attachable');
                        if (self.settings.privileges.del || self.settings.privileges.edit) {
                            $('#dm_page').prepend($('<a class="dm dm_edit_behaviors_icon dm_edit_behaviors_page_icon s16_gear s16"></a>'));
                        };
                        if (self.settings.privileges.add) {
                            $('#dm_page').prepend($('<a class="dm dm_page_edit dm_behaviors_droppable ui-droppable">Page</a>'));
                        };
                    };
                    // When settings are loaded, do the magic...                                       
                    self.reloadBehaviorsMenu();
                    self.initializeContextMenu();
                    self.enableAddBehaviors();
                    self.enableEditBehaviors();                    
                    // If page is currently being edited - behaviors should not run.
                    if (!$('#dm_page').hasClass('edit')) {
                        // In Chrome, something is broken for toggle view button
                        // THIS IS A HARD FIX!!!
                        $('a.tipable.edit_toggle').removeClass('s24_view_on').addClass('s24_view_off');
                        self.start();
                    }
                    
                }
            });
        },
        /**
         * Initializes the context menu for the editing, deleting and sorting behaviors
         */       
        initializeContextMenu: function() {
            var self = this;
            // Load context menu template
            $.ajax(dm_configuration.script_name + '+/dmBehaviors/buildContextMenuItems', {
                type        :           'post',
                error       :      function(xhr, textStatus, errorThrown) {                    
                    $.dm.ctrl.errorDialog('Error when loading context menu template', xhr.responseText);
                },
                success: function(html) {
                    if (html == '') { // User can not edit, delete or sort behaviors
                        $('div.dm.dm_edit_behaviors_context_menu_template').remove();
                        $('div.dm.dm_edit_behaviors_context_menu').remove();
                        self.contextMenuTemplate = null;
                        self.contextMenu = null;
                    } else $('div.dm_edit_behaviors_context_menu_template').html(html);
                }
            });
        },
        /**
         * Reloads behaviors menu in toolbar
         * This function is similar in 99% to the code of reloadAddMenu for widgets
         */
        reloadBehaviorsMenu: function(callback) {
            var self = this, $menu = $('div.dm_behaviors_menu');
            if(!$menu.length) return;
            $.ajax({
                url:      $menu.metadata().reload_url,
                success: function(html) {
                    $menu.html(html);
                    var $actions = $menu.find('li.dm_behaviors_menu_actions').prependTo($menu.find('ul.level1'));    
                    // Enable sorting of all behaviors of the content of this page
                    $actions.find('a.dm_sort_all_behaviors').click(function(){
                        if (!self.settings.privileges.sort) {
                            $.dm.ctrl.errorDialog('Unauthorized', 'You do not have permissions to sort behaviors.'); // TODO - TheCelavi translate
                            return;
                        };
                        if (self.behaviors.length) {
                            self.sortBehaviors({
                                dm_behavior_attached_to: 'page',
                                dm_behavior_attached_to_id: dm_configuration.page_id
                            });
                        };
                        $menu.dmMenu('close');
                    });
                    $menu.dmMenu({
                        hoverClass: 'ui-state-active'
                    })
                    .find('li.dm_droppable_behaviors').disableSelection();
                    $actions.find('input.dm_add_behaviors_search').hint();
                    $menu.find('a.tipable').tipsy({
                        gravity: 's'
                    });
                    $menu.find('input.dm_add_behaviors_search').bind('keyup', function() {
                        var term = new RegExp($.trim($(this).val()), 'i');
                        if(term == ''){
                            $menu.find(':hidden').show();
                            return;
                        }
                        $menu.find('li.dm_droppable_behaviors').each(function(){
                            $(this).show();            
                            if($(this).find('> a').text().match(term)) {
                                $(this).find('li:hidden').show();
                            }
                            else                            {
                                $(this).find('li').each(function()                                {
                                    $(this)[$(this).find('span.move').text().match(term) ? 'show' : 'hide']();
                                });
                                $(this)[$(this).find('li:visible').length ? 'show' : 'hide']();
                            }
                        });
                    });
                    $menu.find('span.behavior_add').draggable({
                        helper: function() {
                            return $('<div class="dm"><div class="dm_behavior_add_helper ui-corner-all">'+ $(this).html()+'</div></div>').maxZIndex();
                        },
                        appendTo: '#dm_page',
                        cursorAt: {
                            left: 30, 
                            top: 10
                        },
                        cursor: 'move',
                        start: function(){
                            $menu.dmMenu('close');
                            $('#dm_tool_bar').dmFrontToolBar('activateEdit', true);                            
                        }
                    });
                    callback && $.isFunction(callback) && callback();
                }
            });            
        },
        /**
         * Utility function, provides the information of the droppable that can accept the behaviors
         * Provided information:
         * - dm_behavior_attached_to, string: (page | area | zone | widget) - the container that accepted the behavior
         * - dm_behavior_attached_to_id, integer: (number) - the id of the container that accepted the behavior
         * - dm_behavior_attached_to_content, boolean: (true | false) - if the behavior is attached to content of the container
         * - dm_behavior_attached_to_selector, string: (css selector | null) - the css XPath to that content in the container
         * returns JSON object
         */
        getAttachedToMetadata: function($droppable) {  
            if (!$droppable.hasClass('dm_behaviors_attachable')) $droppable = $droppable.closest('.dm_behaviors_attachable');
            var typeAndId = /dm_(page|area|zone|widget)_([0-9]+)/m.exec($droppable.prop('class'));
            if (typeAndId) {
                return {
                    dm_behavior_attached_to             :       typeAndId[1],
                    dm_behavior_attached_to_id          :       typeAndId[2],
                    dm_behavior_attached_to_content     :       false,
                    dm_behavior_attached_to_selector    :       null
                };
            };
            // It is not the page element (page | area | zone | widget), find container and add selector for indetification
            var $tmp = $droppable.parent();
            do {
                typeAndId = /dm_(page|area|zone|widget)_([0-9]+)/m.exec($tmp.prop('class'));
                if (typeAndId) {                    
                    var selector = $tmp.prop('class').split(' ');
                    if ($tmp.prop('id')!='') selector.push('#' + $tmp.prop('id'));                    
                    return {
                        dm_behavior_attached_to             :       typeAndId[1],
                        dm_behavior_attached_to_id          :       typeAndId[2],
                        dm_behavior_attached_to_content     :       true,
                        dm_behavior_attached_to_selector    :       selector.reverse().join('.')                       
                    };
                };
            } while ($tmp.length != 0);
            // We can not find the attachable metadata, lets report it
            this.reportError({
                javascript_exception    :   'Error in behavior administration caused by $droppable with HTML code: ' + $($droppable).html(),
                method                  :   '$.dm.behaviorsManager.getAttachedTo()',
                behavior_settings       :   this,
                dm_behavior_error       :   true,
                page_module             :   dm_configuration.module,
                page_action             :   dm_configuration.action,
                page_culture            :   dm_configuration.culture,
                page_id                 :   dm_configuration.page_id,
                script_name             :   dm_configuration.script_name
            });
            throw 'Error: the attachable metadata can not be found';
        },
        /**
         * Shows the context menu when user clicks on the page element that has attached behavior (the gear icon)
         */
        showContextMenu: function($icon) {
            var self = this;
            if (!(self.settings.privileges.sort || self.settings.privileges.edit || self.settings.privileges.del) || self.contextMenuTemplate == null) return;            
            var attachedToMetadata = self.getAttachedToMetadata($icon.closest('.dm_behaviors_attachable'));
            if (!attachedToMetadata) return;
            
            self.contextMenu.empty().append($('<ul></ul>'));
            
            // Add edit/delete of behaviors possibility 
            if (self.settings.privileges.edit || self.settings.privileges.del) { 
                $.each(self.behaviors, function(){
                    if (self.getCssXPath(this) == self.getCssXPath(attachedToMetadata)) {
                        var $cmi = self.contextMenuTemplate.find('li#dm_behavior_cm_item_' + this.dm_behavior_key).clone();
                        if (!this.dm_behavior_enabled) $cmi.addClass('disabled');
                        if (!this.dm_behavior_valid) $cmi.addClass('invalid');
                        $cmi.prop('id', 'dm_behavior_cm_item_cloned_' + this.dm_behavior_id).click(function(){
                            self.editBehavior($cmi.prop('id').replace('dm_behavior_cm_item_cloned_',''));
                        });
                        self.contextMenu.find('ul').append($cmi);
                    };
                });
            };
            // Add sort behaviors possibility
            if (self.settings.privileges.sort) {
                var $cmsi = self.contextMenuTemplate.find('li#dm_behavior_cm_sort').clone();
                $cmsi.prop('id', 'dm_behavior_cm_sort_cloned').click(function(){
                    self.sortBehaviors(attachedToMetadata);
                });
                self.contextMenu.find('ul').append($cmsi);
            };            
            self.contextMenu.css('top', $icon.offset().top).css('left', $icon.offset().left).show('fast');            
        },
        /**
         * Adds the ability to the page elements to receive the behaviors
         */
        enableAddBehaviors: function() {
            var self = this;
            $('.dm_behaviors_droppable').droppable({
                accept      :       '.behavior_add',
                tolerance   :       'pointer',
                greedy      :       true,
                drop        :       function(event, ui) {
                    var $icon = $(ui.draggable, '.dm_droppable_behaviors'), $attachable = $(this);
                    if (!$attachable.hasClass('dm_behaviors_attachable')) $attachable = $attachable.closest('.dm_behaviors_attachable');
                    $attachable.removeClass('dm_behaviors_droppable_hover');
                    if ($icon.length == 0) return;
                    if ($icon.hasClass('clipboard')) {
                        self.pasteBehavior($.extend({},{
                            dm_behavior_id:    $icon.prop('id').replace('dmba_clipboard_behavior_id_', ''),
                            dm_behavior_clipboard_action:   ($icon.hasClass('copy')) ? 'copy' : 'cut'
                        }, self.getAttachedToMetadata($attachable)));
                    } else {
                        self.addBehavior($.extend({}, {dm_behavior_key: $icon.prop('id').replace('dmba_', '')}, self.getAttachedToMetadata($attachable)));
                    };
                },
                over: function(event, ui) {
                    var $attachable = $(this);
                    if (!$attachable.hasClass('dm_behaviors_attachable')) $attachable = $attachable.closest('.dm_behaviors_attachable');
                    $attachable.addClass('dm_behaviors_droppable_hover');
                },
                out: function(event, ui) {
                    var $attachable = $(this);
                    if (!$attachable.hasClass('dm_behaviors_attachable')) $attachable = $attachable.closest('.dm_behaviors_attachable');
                    $attachable.removeClass('dm_behaviors_droppable_hover');
                }
            });
        },
        /**
         * Enables the context menu for the behaviors
         * It will show the gear icon for the elements that have behaviors attached
         * Call this function after each delete/add behavior, or add/delete widget
         */
        enableEditBehaviors: function() {
            var self = this;
            $('a.dm_edit_behaviors_icon').removeClass('edit');
            $.each(self.behaviors, function(){
                var $gearIcon = $(self.getCssXPath(this) + ' a.dm_edit_behaviors_icon:first').addClass('edit');
                if (!$gearIcon.hasClass('configured')) {
                    $gearIcon.addClass('configured').click(function(){
                        self.showContextMenu($(this));
                    });
                };
            });
        },
        /**
         * Save the behavior to the database
         * Loads it and opens the edit form
         */
        addBehavior: function(options) {
            var self = this;
            if (!self.settings.privileges.add) {
                $.dm.ctrl.errorDialog('Unauthorized', 'You do not have permissions to add behaviors.'); // TODO - TheCelavi translate
                return;
            };
            $.ajax($.dm.ctrl.getHref('+/dmBehaviors/add'), {
                data        :           $.extend({}, {dm_action_add: true}, options),
                type        :           'post',
                dataType    :           'json',
                error       :      function(xhr, textStatus, errorThrown) {                    
                    $.dm.ctrl.errorDialog('Error when adding behavior', xhr.responseText);
                },
                success: function(data) {
                    if (data.error) {
                        $.dm.ctrl.errorDialog(data.error.title, data.error.message);
                        return;
                    }
                    else {
                        self.behaviors.push(data.dm_behavior_data); // Added has highest sequence number - so we do not need to do some sorting or something else
                        $.fn.dmExtractEncodedAssets(data); 
                        self.editBehavior(data.dm_behavior_data.dm_behavior_id);
                        $('body').trigger('behaviorAdded',[data.dm_behavior_data]); // Notify sort behaviors about change
                    };
                }
            });
        },
        /**
         * Paste behavior from the clipboard
         */
        pasteBehavior: function(options){
            var self = this;
            if (!self.settings.privileges.add) {
                $.dm.ctrl.errorDialog('Unauthorized', 'You do not have permissions to add behaviors.'); // TODO - TheCelavi translate
                return;
            };
            if (!self.settings.privileges.del && options.clipboard_action == 'cut') {
                $.dm.ctrl.errorDialog('Unauthorized', 'You do not have permissions to cut behaviors.'); // TODO - TheCelavi translate
                return;
            };
            $.ajax($.dm.ctrl.getHref('+/dmBehaviors/paste'), {
                data        :           $.extend({}, {dm_action_paste: true}, options),
                type        :           'post',
                dataType    :           'json',
                error       :      function(xhr, textStatus, errorThrown) {                    
                    $.dm.ctrl.errorDialog('Error when adding pasting behavior', xhr.responseText);
                },
                success: function(data) {
                    if (data.error) {
                        $.dm.ctrl.errorDialog(data.error.title, data.error.message);
                        return;
                    } else {
                        $.fn.dmExtractEncodedAssets(data); 
                        var addBehavior = true;
                        var behavior = null;
                        if (data.dm_behavior_clipboard_action == 'cut') {
                            $.each(self.behaviors, function(index){                                
                                if (this.dm_behavior_id == data.dm_behavior_data.dm_behavior_id) {                                   
                                    this.dm_behavior_attached_to = data.dm_behavior_data.dm_behavior_attached_to;
                                    this.dm_behavior_attached_to_id = data.dm_behavior_data.dm_behavior_attached_to_id;
                                    this.dm_behavior_attached_to_content = data.dm_behavior_data.dm_behavior_attached_to_content;
                                    this.dm_behavior_attached_to_selector = data.dm_behavior_data.dm_behavior_attached_to_selector;
                                    $('body').trigger('behaviorDeleted',[this.dm_behavior_id]); // Notify sort behaviors about change
                                    $('body').trigger('behaviorAdded',[this]); // Notify sort behaviors about change
                                    addBehavior = false;
                                    behavior = this;
                                    try {
                                        $.dm.behaviors[behavior.dm_behavior_key].destroy(behavior); // Destroy this behavior                                    
                                    } catch(e) {
                                        self.reportError({
                                            javascript_exception    :   e.toString(),                                        
                                            method                  :   'destroy',
                                            behavior_settings       :   behavior
                                        });
                                    };
                                    return false;
                                };
                            });
                        }; 
                        if (addBehavior){ // copy or it is cutted from different page...
                            behavior = data.dm_behavior_data;
                            self.behaviors.push(behavior);
                            self.behaviors.sort(function(a,b){
                                return a.dm_behavior_sequence - b.dm_behavior_sequence;
                            });   
                            $('body').trigger('behaviorAdded',[data.dm_behavior_data]); // Notify sort behaviors about change
                        };
                        try {
                            $.dm.behaviors[behavior.dm_behavior_key].destroy(behavior); // Initialize this behavior                                    
                        } catch(e) {
                            self.reportError({
                                javascript_exception    :   e.toString(),                                        
                                method                  :   'init',
                                behavior_settings       :   behavior
                            });
                        };
                    };
                }
            });
        },
        /**
         * Delete the behavior from the database
         * Destroy it from the memory and DOM
         * @param dm_behavior_id integer, id of the behavior
         */
        deleteBehavior: function(dm_behavior_id) {
            var self = this;
            if (!self.settings.privileges.del) {
                $.dm.ctrl.errorDialog('Unauthorized', 'You do not have permissions to delete behaviors.'); // TODO - TheCelavi translate
                return;
            };
            $.ajax($.dm.ctrl.getHref('+/dmBehaviors/delete'), {
                data        :           {
                    dm_action_delete    :   true,
                    dm_behavior_id      :   dm_behavior_id
                },
                type        :           'post',
                dataType    :           'json',
                error       :      function(xhr, textStatus, errorThrown) {                    
                    $.dm.ctrl.errorDialog('Error when deleting behavior', xhr.responseText);
                },
                success: function(data) {
                    if (data.error) {
                        $.dm.ctrl.errorDialog(data.error.title, data.error.message);
                        return;
                    }
                    else {
                        var search = -1;
                        $.each(self.behaviors, function(index){
                            if (this.dm_behavior_id == dm_behavior_id) {
                                search = index;
                                return false;
                            };
                        });                        
                        if (search > -1) {                            
                            try {
                                $.dm.behaviors[self.behaviors[search].dm_behavior_key].destroy(self.behaviors[search]); // Destroy this behavior
                            } catch(e) {
                                self.reportError({
                                    javascript_exception    :   e.toString(),
                                    method                  :   'destroy',
                                    behavior_settings       :   this
                                });
                            };
                            self.behaviors.splice(search, 1);
                        }
                        $('body').trigger('behaviorDeleted',[dm_behavior_id]); // Notify sort behaviors about change
                    };
                }
            });
        },
        /**
         * Edit behavior settings
         * @param dm_behavior_id integer, id of the behavior
         */
        editBehavior: function(dm_behavior_id) {
            var self = this;
            if (!self.settings.privileges.edit) {
                $.dm.ctrl.errorDialog('Unauthorized', 'You do not have permissions to edit behaviors.'); // TODO - TheCelavi translate
                return;
            };
            /*
             * Check if dialog already exists, if exists, bring it to top
             * It is stored in behaviors array, it can not be done as in widgets
             * so we will use this array
             */ 
            var behavior = null;
            $.each(self.behaviors, function(){
                if (this.dm_behavior_id == dm_behavior_id) {
                    behavior = this;
                    return false;
                };
            });
            if (behavior.dm_edit_dialog) {
                behavior.dm_edit_dialog.dialog('moveToTop');
                return;
            } else behavior.dm_edit_dialog = $.dm.ctrl.ajaxDialog({
                url         :           $.dm.ctrl.getHref('+/dmBehaviors/edit'),
                data        :           {
                    dm_behavior_id: dm_behavior_id,
                    dm_action_edit: true
                },
                type        :           'get',
                title       :           'Edit behavior', // TODO - TheCelavi translate
                width       :           600,
                'class'     :           'dm_widget_edit_dialog_wrap ',
                resizable   :           true,
                resize      :           function(event, ui) {
                    behavior.dm_edit_dialog.maximizeContent('textarea.markItUpEditor');
                },
                close       :           function(event, ui) {
                    if (behavior && behavior.dm_edit_dialog) behavior.dm_edit_dialog = false;
                }
            });
            var $dialog = behavior.dm_edit_dialog;
            
            $dialog.bind('dmAjaxResponse', function(event){                
                $dialog.prepare();                
                $('a.delete', $dialog).click(function() {
                    if (confirm($(this).tipsyTitle()+" ?")) {
                        $.dm.removeTipsy();
                        $.dm.behaviorsManager.deleteBehavior(dm_behavior_id);
                        $dialog.dialog('close');
                    };
                });
                var $form = $('div.dm.dm_behavior_edit.dm_behavior_edit_form', $dialog);
                if (!$form.length) return;
                
                if ($cutCopy = $form.find('div.dm_cut_copy_actions').orNot()) {
                    $cutCopy.appendTo($dialog.parent().find('div.ui-dialog-titlebar')).show().find('a').click(function() {
                        var $a = $(this).addClass('s16_gear');          
                        $.ajax({
                            url         :      $(this).attr('href'),
                            dataType    :           'json',
                            error       :      function(xhr, textStatus, errorThrown) {                    
                                $.dm.ctrl.errorDialog('Error when adding behavior to clipboard', xhr.responseText);
                                $a.removeClass('s16_gear');
                            },
                            success     :  function(data) {
                                if (data.error) {
                                    $.dm.ctrl.errorDialog(data.error.title, data.error.message);
                                    return;
                                }
                                self.reloadBehaviorsMenu(function() {
                                    $a.removeClass('s16_gear');
                                });
                            }
                        });
                        return false;
                    });
                }
                // enable tool tips
                $dialog.parent().find('a[title], input[title]').tipsy({
                    gravity: $.fn.tipsy.autoSouth
                });
                
                /*
                 * Apply generic front form abilities
                 */
                $form.dmFrontForm();
                /*
                 * Apply specific behavior form abilities
                 */
                if ((formClass = $form.metadata().form_class) && $.isFunction($form[formClass])) $form[formClass]($form);  // TODO check if this is the context to provide???
                
                $form.find('form').dmAjaxForm({
                    beforeSubmit: function(data) {
                        $dialog.block();
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        $dialog.unblock();
                        $.dm.ctrl.errorDialog('Error when updating the behavior', xhr.responseText);
                    },
                    success: function(html) {
                        $dialog.unblock();
                        if(/dm dm_behavior_edit dm_behavior_edit_form/m.exec(html)) {
                            $dialog.html(html).trigger('dmAjaxResponse'); // Form is not valid, it is rendered again
                            return;
                        };
                        try {
                            var $tmp = $(html);
                            var data = $.parseJSON($tmp.val());
                        } catch(e) { 
                            $.dm.ctrl.errorDialog('Error', 'Something went wrong when updating behavior');
                            return;
                        }
                        if (data.error){
                            $.dm.ctrl.errorDialog(data.error.title, data.error.message);
                            return;
                        }                        
                        $.each(self.behaviors, function(index){
                            if (this.dm_behavior_id == data.dm_behavior_data.dm_behavior_id) {
                                // Delete current behavior
                                try {
                                    $.dm.behaviors[this.dm_behavior_key].destroy(this); // Destroy this behavior
                                } catch(e) {
                                    self.reportError({
                                        javascript_exception    :   e.toString(),
                                        method                  :   'destroy',
                                        behavior_settings       :   this
                                    });
                                };
                                
                                self.behaviors[index] = data.dm_behavior_data;
                                // Reload behavior in memory
                                try {
                                    $.dm.behaviors[self.behaviors[index].dm_behavior_key].init(self.behaviors[index]); // Load it
                                } catch(e) {
                                    self.reportError({
                                        javascript_exception    :   e.toString(),
                                        method                  :   'init',
                                        behavior_settings       :   this
                                    });
                                };
                                
                                return false;
                            };
                        });
                        $.fn.dmExtractEncodedAssets(data);                      
                        $dialog.dialog('close');
                    }
                });
            });
        },
        sortBehaviors: function(options) {
            var self = this;
            if (!self.settings.privileges.sort) {
                $.dm.ctrl.errorDialog('Unauthorized', 'You do not have permissions to sort behaviors.'); // TODO - TheCelavi translate
                return;
            };
            
            var $sortingContainer = $(self.getCssXPath(options));
            var data = $sortingContainer.data('dm_behavior_sorting_dialog');
            if (data && data.target) {
                data.target.dialog('moveToTop');
                return;
            };
            var $dialog = $.dm.ctrl.ajaxDialog({
                url         :           $.dm.ctrl.getHref('+/dmBehaviors/sortBehaviors'),
                data        :           $.extend({}, {dm_action_sort: true}, options),
                type        :           'get',
                title       :           'Sort behaviors for ' + options.dm_behavior_attached_to, // TODO Translate
                width       :           300,
                'class'     :           'dm_widget_edit_dialog_wrap ',
                resizable   :           true,
                close       :           function(event, ui) {
                    $sortingContainer.data('dm_behavior_sorting_dialog', null);
                }
            });
            $sortingContainer.data('dm_behavior_sorting_dialog', {
                target: $dialog
            });
            $dialog.bind('dmAjaxResponse', function(event){                
                $dialog.prepare();                
                
                var $form = $('div.dm.dm_behavior_sort.dm_behavior_sort_form', $dialog);
                $dialog.parent().find('a[title]').tipsy({
                    gravity: $.fn.tipsy.autoSouth
                });
                
                var initialSequence = new Array();
                $.each($form.find('ul.dm_behaviors_sortable li'), function(){
                    initialSequence.push($(this).metadata().dm_behavior_sequence);
                });
                
                var serialize = function() {
                    var sorted = new Array();
                    $.each($form.find('ul.dm_behaviors_sortable li'), function(index){
                        var data = $(this).metadata();
                        data.dm_behavior_sequence = initialSequence[index];
                        sorted.push(data);
                    });
                    $dialog.find('#dm_behaviors_sort_form_behaviors').val($.toJSON(sorted));
                };
                
                $form.find('ul.dm_behaviors_sortable').sortable({
                    helper: 'clone',
                    update: function(){
                        serialize();
                    }
                }).disableSelection().find('li').hover(function(){
                    $(self.getCssXPath($(this).metadata())).addClass('dm_behaviors_droppable_hover');
                },function(){
                    $(self.getCssXPath($(this).metadata())).removeClass('dm_behaviors_droppable_hover');
                }).click(function(){
                    if (self.settings.privileges.edit) self.editBehavior($(this).metadata().dm_behavior_id);
                });
                
                /*
                 * If behavior is added, or deleted
                 * We should fix the sorting by doing the following
                 * - if new behavior is added and it is a same container, refresh dialog
                 * - deleted should be just removed
                 */
                
                $('body').bind('behaviorAdded', function(e, behavior){
                    if (options.dm_behavior_attached_to == behavior.dm_behavior_attached_to 
                        && options.dm_behavior_attached_to_id == behavior.dm_behavior_attached_to_id
                        && options.dm_behavior_attached_to_selector == behavior.dm_behavior_attached_to_selector) {
                        $dialog.dialog('close');
                        self.sortBehaviors(options);
                    };
                }).bind('behaviorDeleted', function(e, dm_behavior_id){
                    $.each($form.find('ul.dm_behaviors_sortable li'), function(){
                        if ($(this).metadata().dm_behavior_id == dm_behavior_id) {
                            $(this).remove();
                            serialize();
                            return false;
                        };
                    });
                    if ($form.find('ul.dm_behaviors_sortable li').length == 0) $dialog.dialog('close');
                });
                
                $form.find('form').dmAjaxForm({
                    beforeSubmit: function(data) {
                        $dialog.block();
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        $dialog.unblock();
                        $.dm.ctrl.errorDialog('Error when sorting behaviors', xhr.responseText);
                    },
                    success: function(html) {
                        $dialog.unblock();
                        if(/dm dm_behavior_sort dm_behavior_sort_form/m.exec(html)) {
                            $dialog.html(html).trigger('dmAjaxResponse'); // Form is rendered for the first time...
                            return;
                        };
                        try {
                            var $tmp = $(html);
                            var data = $.parseJSON($tmp.val());
                        } catch(e) { 
                            $.dm.ctrl.errorDialog('Error', 'Something went wrong when sorting behavior');
                            return;
                        };
                        if (data.error){
                            $.dm.ctrl.errorDialog(data.error.title, data.error.message);
                            return;
                        };
                        
                        for(var i = 0; i < $.dm.behaviorsManager.behaviors.length; i++){
                            for(var j = 0; j < data.dm_behavior_data.length; j++){
                                if ($.dm.behaviorsManager.behaviors[i].dm_behavior_id == parseInt(data.dm_behavior_data[j].dm_behavior_id)) {
                                    $.dm.behaviorsManager.behaviors[i].dm_behavior_sequence = parseInt(data.dm_behavior_data[j].dm_behavior_sequence);
                                };
                            };
                        };
                        $.dm.behaviorsManager.behaviors.sort(function(a,b){
                            return a.dm_behavior_sequence - b.dm_behavior_sequence;
                        });                                             
                        $dialog.dialog('close');
                    }
                });
            });
           
        }        
    });
    
    $.dm.behaviorsManager.initializeAdministration();

})(jQuery);