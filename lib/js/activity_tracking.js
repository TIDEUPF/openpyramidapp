var pyramid_status = {
    "utility" : {
        "clear_classes" : function($elem) {
            var type = $elem.prop('type');
            for(var class_item in pyramid_status.render.resources.status[type]) {
                $elem.removeClass(pyramid_status.render.resources.status[type][class_item]);
            }
        }
    },
    "render" : {
        "resources" : {
            "labels" :{
                "group" : [],
                "pyramid" : []
            },
            "status" : {
                "global-group" : ["started", "not-started", "complete"]
            }
        },
        "elements" : {
            //"group" : ["label", "n_users", "started", "completed"],
            "pyramid" : ["label", "n_users", "started", "completed"],
            "group" : function(params) {


                params.$elem.removeClass();
            },
            "chat" : function() {},
            "student" : function() {},
            "rating" : function() {}
        },
        "create" : function(params) {
            var $item = $('#'+params.item_id).length ? $('#'+params.item_id) : $(pyramid_template[params.section][params.type]);
            $item.prop('id', params.item_id);

            return $item;
        },
        "update" : function(params) {},
        "insert" : function(params) {
            if($('#'+params.$item.prop('id')).length == 0) {
                $(params.parent_item.append(params.$item));
            }

            return $('#'+params.$item.prop('id'));
        }
    },

    "init" : {
        "start" : function() {
            $flow_element = $('#flow-frame');
            $detail_element = $('#detail-frame');


            for(var pyramid_item_key in current_flow_status.pyramid_data) {
                var pyramid_item = current_flow_status.pyramid_data[pyramid_item_key];

                var pyramid_id = 'flow-' + pyramid_item_key;

                var $pyramid_element = pyramid_status.render.create({
                    "item_id"   : pyramid_id,
                    "section"   : "global",
                    "type"      : "pyramid"
                });

                for(var level_item_key in pyramid_item.levels) {
                    var level_item = pyramid_item.levels[level_item_key];

                    var level_id = pyramid_id + '-' + level_item_key;

                    var $level_element = pyramid_status.render.create({
                        "item_id"   : level_id,
                        "section"   : "global",
                        "type"      : "level"
                    });

                    for(var group_item_key in level_item.groups) {
                        var group_item = level_item.groups[group_item_key];

                        var group_id = level_id + '-' + group_item_key;

                        var $group_element = pyramid_status.render.create({
                            "item_id"   : group_id,
                            "section"   : "global",
                            "type"      : "group"
                        });

                        pyramid_status.render.insert({
                            "$item"          : $group_element,
                            "parent_item"   : $level_element
                        });
                    }

                    for(group_item_key in level_item.groups) {
                        group_item = level_item.groups[group_item_key];

                        group_id = level_id + '-detail-' + group_item_key;

                        $group_element = pyramid_status.render.create({
                            "item_id"   : group_id,
                            "section"   : "detail",
                            "type"      : "group"
                        });

                        pyramid_status.render.insert({
                            "$item"          : $group_element,
                            "parent_item"   : $detail_element
                        });
                    }

                    pyramid_status.render.insert({
                        "$item"          : $level_element,
                        "parent_item"   : $pyramid_element
                    });
                }

                pyramid_status.render.insert({
                    "$item"          : $pyramid_element,
                    "parent_item"   : $flow_element
                });
            }
        }
    }
};