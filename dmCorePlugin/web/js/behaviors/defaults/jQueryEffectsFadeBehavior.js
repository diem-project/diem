;(function($) {

    var methods = {
        init: function(behavior) {
            var dataNamespace = 'jQueryEffectsFadeBehavior' + behavior.event;
            var $this = $(this), data = $this.data(dataNamespace); // Same can be attached if they listend different events
            if (data && behavior.dm_behavior_id != data.dm_behavior_id) { // There is attached the same, so we must report it
                alert('You can not attach the behavior with same settings to same content'); // TODO TheCelavi - adminsitration mechanizm for this? Reporting error
            };
            // Remember the begining opacity
            if (!$this.data('jQueryEffectsFadeBehaviorStartOpacity')) $this.data('jQueryEffectsFadeBehaviorStartOpacity', $this.css('opacity'));
            $this.data(dataNamespace, behavior);
        },
        start: function(behavior) {
            var $this = $(this);
            if (behavior.event == 'load') {
                methods.animate.apply($this, [behavior]);
            } else {
                $this.bind(behavior.event + '.jQueryEffectsFadeBehavior' , function(){ // Use namespace for binding events for stop to easily unbind
                    methods.animate.apply($this, [behavior]);
                });
            };
        },
        stop: function(behavior) {
            var $this = $(this);
            if (behavior.event != 'load') {
                $this.unbind(behavior.event + '.jQueryEffectsFadeBehavior');
            };
            $this.css('opacity', $this.data('jQueryEffectsFadeBehaviorStartOpacity'));
        },
        destroy: function(behavior) {
            var dataNamespace = 'jQueryEffectsFadeBehavior' + behavior.event;
            var $this = $(this);
            $this.data(dataNamespace, null);
        },
        animate: function(behavior) {
            $(this).animate({
                opacity: behavior.opacity
            }, behavior.duration, behavior.easing);
        }
    };

    $.fn.jQueryEffectsFadeBehavior = function(method, behavior){

        return this.each(function() {
            if ( methods[method] ) {
                return methods[ method ].apply( this, [behavior]);
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, [method] );
            } else {
                $.error( 'Method ' +  method + ' does not exist on jQuery.jQueryEffectsFadeBehavior' );
            };
        });
    };

    // Entry point for initialization of behavior
    // This one is tricky, since same behavior can be attached several times with different settings
    // The difference is event
    // So we will use that to figure out are several attached behaviors are legitime
    $.extend($.dm.behaviors, {
        jQueryEffectsFadeBehavior: {
            init: function(behavior) {
                $($.dm.behaviorsManager.getCssXPath(behavior) + ' ' + behavior.inner_target).jQueryEffectsFadeBehavior('init', behavior);
            },
            start: function(behavior) {
                $($.dm.behaviorsManager.getCssXPath(behavior) + ' ' + behavior.inner_target).jQueryEffectsFadeBehavior('start', behavior);
            },
            stop: function(behavior) {
                $($.dm.behaviorsManager.getCssXPath(behavior) + ' ' + behavior.inner_target).jQueryEffectsFadeBehavior('stop', behavior);
            },
            destroy: function(behavior) {
                $($.dm.behaviorsManager.getCssXPath(behavior) + ' ' + behavior.inner_target).jQueryEffectsFadeBehavior('destroy', behavior);
            }
        }
    });

})(jQuery);