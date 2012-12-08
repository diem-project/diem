;(function($) {

    var methods = {
        init: function(behavior) {
            var $this = $(this), data = $this.data('dmTipsyBehavior'); // Same can be attached if they listend different events
            if (data && behavior.dm_behavior_id != data.dm_behavior_id) { // There is attached the same, so we must report it
                alert('You can not attach the behavior with same settings to same content'); // TODO TheCelavi - adminsitration mechanizm for this? Reporting error
            };
            $this.data('dmTipsyBehavior', behavior);
        },
        start: function(behavior) {
            switch(behavior.gravity) {
                case 'ans': behavior.gravity = $.fn.tipsy.autoNS; break;
                case 'awe': behavior.gravity = $.fn.tipsy.autoWE; break;
            };
            $(this).tipsy(behavior);
        },
        stop: function(behavior) {
            $('body > div.tipsy').remove();
        },
        destroy: function(behavior) {
            $(this).data('dmTipsyBehavior', null);
        }
    };

    $.fn.dmTipsyBehavior = function(method, behavior){

        return this.each(function() {
            if ( methods[method] ) {
                return methods[ method ].apply( this, [behavior]);
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, [method] );
            } else {
                $.error( 'Method ' +  method + ' does not exist on jQuery.dmTipsyBehavior' );
            };
        });
    };


    $.extend($.dm.behaviors, {
        dmTipsyBehavior: {
            init: function(behavior) {
                if (behavior.inner_target) {
                    $.each(behavior.inner_target, function(){
                        $($.dm.behaviorsManager.getCssXPath(behavior) + ' ' + this).dmTipsyBehavior('init', behavior);
                    });
                } else $($.dm.behaviorsManager.getCssXPath(behavior)).dmTipsyBehavior('init', behavior);
            },
            start: function(behavior) {
                if (behavior.inner_target) {
                    $.each(behavior.inner_target, function(){
                        $($.dm.behaviorsManager.getCssXPath(behavior) + ' ' + this).dmTipsyBehavior('start', behavior);
                    });
                } else $($.dm.behaviorsManager.getCssXPath(behavior)).dmTipsyBehavior('start', behavior);
            },
            stop: function(behavior) {
                if (behavior.inner_target) {
                    $.each(behavior.inner_target, function(){
                        $($.dm.behaviorsManager.getCssXPath(behavior) + ' ' + this).dmTipsyBehavior('stop', behavior);
                    });
                } else $($.dm.behaviorsManager.getCssXPath(behavior)).dmTipsyBehavior('stop', behavior);
            },
            destroy: function(behavior) {
                if (behavior.inner_target) {
                    $.each(behavior.inner_target, function(){
                        $($.dm.behaviorsManager.getCssXPath(behavior) + ' ' + this).dmTipsyBehavior('destroy', behavior);
                    });
                } else $($.dm.behaviorsManager.getCssXPath(behavior)).dmTipsyBehavior('destroy', behavior);
            }
        }
    });

})(jQuery);