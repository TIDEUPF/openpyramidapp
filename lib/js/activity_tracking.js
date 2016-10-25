var pyramid_status = {
    "render" : {
        "elements" : {
            "group" : ["label", "n_users", "started", "completed"],
            "pyramid" : ["label", "n_users", "started", "completed"],
            "chat" : function() {},
            "student" : function() {},
            "rating" : function() {}
        },
        "insert" : function(params) {},
        "update" : function(params) {}
    },
    "init" : {
        "start" : function() {
            $flow_element = $('#flow-frame');


            for(var pyramid_item_key in current_flow_status.pyramid_data) {
                var pyramid_item = current_flow_status.pyramid_data[pyramid_item_key];
                var pyramid_id = 'flow-' + pyramid_item_key;
                var $pyramid_element = $('#'+pyramid_id).length ? $('#'+pyramid_id) : $(pyramid_template['global']['pyramid']);

                $pyramid_element.prop('id', pyramid_id);
                
                for(var level_item_key in pyramid_item.groups_activity.level) {
                    var level_item = pyramid_item.groups_activity.level[level_item_key];
                    var level_id = pyramid_id + '-' + level_item_key;
                    var $level = $('#'+level_id).length ? $('#'+level_id) : $(pyramid_template['global']['level']);
                    $level.prop('id', level_id);

                    for(var group_item_key in level_item.group) {
                        var group_item = level_item.group[group_item_key];
                        var group_id = level_id + '-' + group_item_key;
                        var $group = $('#'+group_id).length ? $('#'+group_id).length : $(pyramid_template['global']['group']);
                        $group.prop('id', group_id);

                        $('#'+group_id).length ? null : $level.append($group);
                    }

                    $('#'+level_id).length ? null: $pyramid_element.append($level);
                    //$pyramid_element.append($level);
                }

                $('#'+pyramid_id).length ? null: $flow_element.append($pyramid_element);
            }
        }
    }
};