;(function($) {
    $.dm.behaviorsManager = {
        /**
         * The list of behaviors that are loaded
         */
        behaviors:      null,      
        /**
         * The current state of the behaviors manager
         * nothing = 0 || constructed = 1 || initialized = 2 || started = 3 || stoped = 2 || destroyed = 1 
         */        
        state:          0,
        /**
         * Initializes the behaviors manager, loads behaviors settings
         */
        initializeManager: function() { 
            var self = this;
            self.behaviors = new Array();
            var $beahviors_settings = $('div.dm_behaviors');      
            if ($beahviors_settings.length == 0) {
                self.state = 1;
                return;            
            };
            var behaviors = $beahviors_settings.metadata().behaviors;
            $('div.dm_behaviors').remove();
            $.each(behaviors, function(){                
                 self.behaviors.push(this);
            });            
            self.state = 1;
        },
        /**
         * Initializes all the behaviors by calling init(behaviorSettings) method
         */
        init: function() {  
            var self = this;
            if (self.state < 1) self.initializeManager();
            $.each(self.behaviors, function(){
                try {
                    $.dm.behaviors[this.dm_behavior_key].init(this); // Init this behavior
                } catch(e) {
                    self.reportError({
                        javascript_exception    :   e.toString(),
                        method                  :   'init',
                        behavior_settings       :   this
                    });
                };
            });
            self.state = 2;
        },
        /**
         * Starts all behaviors by calling start() method
         */
        start: function() {
            var self = this;
            if (self.state < 2) self.init();
            $.each(self.behaviors, function(){
                try {
                    $.dm.behaviors[this.dm_behavior_key].start(this); // Start this behavior
                }catch(e) {
                    self.reportError({
                        javascript_exception    :   e.toString(),
                        method                  :   'start',
                        behavior_settings       :   this
                    });
                };
            });
            self.state = 3;
        },
        /**
         * Utility function - reports the error on client side
         * the error is saved as DmError object and can be viewed in admin/system/log/errors
         * later on for debug
         */
        reportError: function(data) {
            if (dm_configuration.debug) {
                alert(data.javascript_exception + '@' + data.method);
            };
            $.ajax(dm_configuration.script_name + '+/dmBehaviors/logBehaviorException', {
                data        :           $.extend({}, { 
                    dm_behavior_error   :   true,
                    page_module         :   dm_configuration.module,
                    page_action         :   dm_configuration.action,
                    page_culture        :   dm_configuration.culture,
                    page_id             :   dm_configuration.page_id,
                    script_name         :   dm_configuration.script_name
                }, data),
                type        :           'post',
                error       :      function(xhr, textStatus, errorThrown) {return;},
                success: function(html) {return;}
            });
        },
        /**
         * Utility function - gets the CSS selector based on the settings of the behavior
         * @param behaviorSettings JSON {
         *      dm_behavior_id: int
         *      dm_behavior_key: string      
         *      dm_behavior_attached_to: string (page|area|zone|widget)
         *      dm_behavior_attached_to_id: int
         *      dm_behavior_attached_to_content: boolean
         *      dm_behavior_sequence: int
         *      dm_behavior_attached_to_selector: string or null
         *      dm_behavior_enabled: boolean
         *      dm_behavior_valid: boolean
         * }
         * @param withInnerContainer boolean wheter to include container 
         * @return string
         */
        getCssXPath: function(behaviorSettings, withInnerContainer) {
            var path = '.dm_' + behaviorSettings.dm_behavior_attached_to + '_' + behaviorSettings.dm_behavior_attached_to_id;
            if (withInnerContainer && !behaviorSettings.dm_behavior_attached_to_content) {
                switch(behaviorSettings.dm_behavior_attached_to) {
                    case 'area': path += ' ' + '.dm_zones'; break;
                    case 'zone': path += ' ' + '.dm_widgets'; break;
                    case 'widget': path += ' ' + '.dm_widget_inner'; break;
                }
            };
            if (withInnerContainer && behaviorSettings.dm_behavior_attached_to_content) path += ' ' + '.behaviorable_container';
            if (behaviorSettings.dm_behavior_attached_to_content) 
                path += ' ' + behaviorSettings.dm_behavior_attached_to_selector;
            return path;
        }
    };
 })(jQuery);