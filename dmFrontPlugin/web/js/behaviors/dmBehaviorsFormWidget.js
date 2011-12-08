(function($) {
    var counter = 0;
    var methods = {
        init: function() {
            var self = $(this);
            $(this).data('behaviors', {}).droppable({
                accept      :       '.dm_behavior_draggable_helper',
                activeClass :       'droppable_active',
                hoverClass  :       'droppable_hover',
                tolerance   :       'touch',
                drop        :       function(event, ui) {
                    var $icon = $('.dm_behavior_draggable', $(ui.draggable));
                    methods['openForm'].apply(self, [$icon.prop('id'), 'add']);
                }
            });
        },
        addBehavior: function(behaviorData) {
            var self = $(this);
            counter++;
            behaviorData.dmBehaviorTempID = 'behavior_' + counter;
            var $newBehavior = $('\
                <div class="dm_behavior_draggable_droped" id="' + behaviorData.dmBehaviorTempID + '" behavior="' + behaviorData.dmBehaviorKey + '"> \
                    <div class="dm_remove_behavior_thick"></div> \
                    <div class="dm_behavior_draggable"> \
                        <div class="dm_behavior_draggable_icon"> \
                            <img src="' + behaviorData.dmBehaviorIcon + '"> \
                        </div> \
                        <div class="dm_behavior_draggable_title">' + behaviorData.dmBehaviorName + '</div> \
                    </div> \
                </div>');
            self.append($newBehavior);
            
            $newBehavior.data('behavior', behaviorData).hover(function(){
                $(this).addClass('dm_behavior_draggable_hover');
            }, function(){
                $(this).removeClass('dm_behavior_draggable_hover');
            }).click(function(){
                var behaviorData = $(this).data('behavior');
                var behaviorDataCloned = $.extend(true, {}, behaviorData);
                methods['openForm'].apply(self, [behaviorDataCloned.dmBehaviorKey, 'edit', behaviorDataCloned]);
            });
            self.dmBehaviorsFormWidget('serializeBehaviors');
            $('.dm_remove_behavior_thick', $newBehavior).click(function(event){
                event.stopPropagation();
                if (confirm('Do you realy want to remove this behavior?')) { // TODO
                    methods['removeBehavior'].apply(self, [$newBehavior.data('behavior')]);
                }
            });        
        },
        removeBehavior: function(behaviorData) {
            var self = $(this);
            var $curr = self.find('#' + behaviorData.dmBehaviorTempID);
            $curr.remove();
            self.dmBehaviorsFormWidget('serializeBehaviors');
        },
        editBehavior: function(iconId, behaviorData) {
            var self = $(this);
            var $curr = self.find('#' + iconId);
            behaviorData.dmBehaviorTempID = iconId;
            $curr.data('behavior', behaviorData);
            self.dmBehaviorsFormWidget('serializeBehaviors');
        },
        serializeBehaviors: function() {
            var result = new Array();
            var $behavors = $(this).find('.dm_behavior_draggable_droped');
            $.each($behavors, function(){
                var data = $(this).data('behavior');
                result.push(data);
            });
            $input = $(this).find('input');
            if (result.length) $input.attr('value',$.toJSON(result));              
            else $input.attr('value',''); 
        },
        unserializeBehaviors: function() {
            var self = $(this);
            var behaviors = $(this).find('input').val();
            if (behaviors == "") return;
            behaviors = $.parseJSON(behaviors);
            $.each(behaviors, function(){
                methods['addBehavior'].apply(self, [this]);
            });
        },
        openForm: function(dmBehaviorKey, action, data) {            
            var self = $(this);
            var dmBehaviorTempID = null;            
            if (data == undefined) data = {};
            else { // IT IS EDIT MODE
                dmBehaviorTempID = data.dmBehaviorTempID;
                delete data.dmBehaviorTempID;
                delete data.dmBehaviorKey;
                delete data.dmBehaviorName;
                delete data.dmBehaviorIcon;
                data = {
                    dmBehaviorData : data
                };
            };
            
            
            var $dialog = $.dm.ctrl.ajaxDialog({
                url         :           $.dm.ctrl.getHref('+/dmBehaviorsFramework/form?dmBehaviorKey=' + dmBehaviorKey + '&dmBehaviorFormAction=' + action),
                data        :           data,
                type        :           'post',
                title       :           'Add behavior ', // TODO translate
                width       :           600,
                'class'     :           'dm_widget_edit_dialog_wrap ',
                resizable   :           true,
                resize      :           function(event, ui) {
                    $dialog.maximizeContent('textarea.markItUpEditor');
                }
            }).bind('dmAjaxResponse', function(event){
                var $save = $(this).find('input.submit.and_save');
                if ($save.length) {
                    $save.click(function() {
                        $dialog.block();
                        $.ajax({
                            url         :           $.dm.ctrl.getHref('+/dmBehaviorsFramework/form?dmBehaviorKey=' + dmBehaviorKey + '&dmBehaviorFormAction=save'),
                            data        :           $(this).closest('form').serializeArray(),
                            type        :           'post',
                            success     :           function(data) {
                                $dialog.html(data).dmExtractEncodedAssets().trigger('dmAjaxResponse');
                            },
                            error: function(xhr) {
                                $dialog.unblock();
                                $.dm.ctrl.errorDialog('Error in ' + xhr.responseText);
                            }
                        });
                        return false;
                    });
                } else {
                    var behaviorData = $.parseJSON($(event.target).text());           
                    $dialog.dialog('close');                    
                    switch(action) {
                        case 'add' : {
                                methods['addBehavior'].apply(self, [behaviorData]);
                        }break;
                        case 'edit' : {
                                methods['editBehavior'].apply(self, [dmBehaviorTempID, behaviorData]);
                        }break;
                    };                    
                };
            });
        }
    };
    
    $.fn.dmBehaviorsFormWidget = function(method) {
        
        return this.each(function(){
            var self = $(this);
                
            if ( methods[method] ) {
                return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, arguments );
            } else {
                $.error( 'Method ' +  method + ' does not exist on jQuery.dmBehaviorsFormWidget' );
            }       
                
        });
    }
    
    
    $('div.dm.dm_widget_edit_dialog_wrap').live('dmAjaxResponse', function() {
        var $widget = $( "div.dm_widget_form_behaviors_droppable", $(this)).dmBehaviorsFormWidget()
        $widget.dmBehaviorsFormWidget('unserializeBehaviors');        
    });
    
    
})(jQuery);