var pyramid_status = {
    "utility" : {
        "clear_classes" : function($elem) {
            var type = $elem.prop('pyramid_data').type;

            if(typeof pyramid_status.render.resources.status[type] !== "undefined") {
                for (var class_item in pyramid_status.render.resources.status[type]) {
                    $elem.removeClass(pyramid_status.render.resources.status[type][class_item]);
                }
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
            "global-group" : function($elem) {
                pyramid_status.utility.clear_classes($elem);

                var pyramid_data = $elem.prop("pyramid_data");
                $elem.find('a').text("a pop up");
                $elem.find('a').attr("href", '#' + pyramid_data.parent_id + '-' + pyramid_data.metadata.sibling_id + '-' + pyramid_data.key);
            },
            "detail-group" : function($elem) {
                pyramid_status.utility.clear_classes($elem);
                $elem.text("a pop up");
            },
            "chat" : function() {},
            "student" : function() {},
            "rating" : function() {}
        },
        "create" : function(params) {
            var $item = $('#'+params.item_id).length ? $('#'+params.item_id) : $(pyramid_template[params.section][params.type]);
            $item.prop('id', params.item_id);

            var pyramid_data = {};
            Object.keys(params).forEach(function(key) {
                pyramid_data[key] = params[key];
            });

            $item.prop('pyramid_data', pyramid_data);

            if(typeof pyramid_status.render.elements[params.section+'-'+params.type] !== "undefined")
                pyramid_status.render.elements[params.section+'-'+params.type]($item);

            return $item;
        },
        "update" : function(params) {

            params.data.forEach(function(data_item, data_item_key) {
                var item_id = (typeof params.parent_id === "undefined") ? 'nid':params.parent_id;

                item_id +=
                    '-' /*+ pyramid_element_properties.section +
                    '.' + pyramid_element_properties.type +
                    '.'*/ + params.id +
                    '-' + data_item_key;

                var $element = pyramid_status.render.create({
                    "item_id"   : item_id,
                    "parent_id"   : params.parent_id,
                    "section"   : params.section,
                    "type"      : params.type,
                    "parent_chain" : params.parent_chain,
                    "data"      : data_item,
                    "key"       : data_item_key,
                    "metadata"  : (typeof params.metadata !== "undefined") ? params.metadata : null
                });

                if(typeof params.subtree !== "undefined") {
                    var child_array = Array.isArray(params.subtree) ? params.subtree : [params.subtree];
                    var parent_chain = {};

                    parent_chain[params.type] = {
                        "data"  : data_item,
                        "key"   : data_item_key,
                        "item_id" : item_id
                    };

                    Object.keys(params.parent_chain).forEach(function(key) {
                        parent_chain[key] = params.parent_chain[key];
                    });

                    child_array.forEach(function(child_item) {
                        child_item.data = (typeof child_item.data === "string") ? data_item[child_item.data] : child_item.data;
                        child_item.parent_id = item_id;
                        child_item.$parent_element = $element;
                        child_item.parent_chain = parent_chain;

                        pyramid_status.render.update(child_item);
                    });
                }

                var $parent_element = (typeof params.$alternate_parent === "undefined") ? params.$parent_element : params.$alternate_parent;

                pyramid_status.render.insert({
                    "$item"         : $element,
                    "$parent_element"   : $parent_element
                });

            });
        },
        "insert" : function(params) {
            if($('#'+params.$item.prop('id')).length == 0) {
                params.$parent_element.append(params.$item);
            }

            return $('#'+params.$item.prop('id'));
        }
    },

    "init" : {
        "start" : function() {
            $flow_element = $('#flow-frame');
            $detail_element = $('#detail-frame');

            var render_tree = {
                "id" :  "pyramid",
                "data" : current_flow_status.pyramid_data,
                "parent_id" : "flow",
                "parent_chain" : {
                    "flow" : {
                        "data": current_flow_status
                    }
                },
                "$parent_element" : $flow_element,
                "section"   : "global",
                "type"      : "pyramid",
                "subtree"   : {
                    "id" :  "level",
                    "data" : "levels",
                    "section"   : "global",
                    "type"      : "level",
                    "subtree"   : [
                        {
                            "id" :  "global-group",
                            "data" : "groups",
                            "section"   : "global",
                            "type"      : "group",
                            "metadata"  : {
                                "sibling_id" : "detail-group"
                            }
                        },
                        {
                            "id" :  "detail-group",
                            "data" : "groups",
                            "$alternate_parent" : $detail_element,
                            "section"   : "detail",
                            "type"      : "group"
                        }
                    ]
                }
            };

            pyramid_status.render.update(render_tree);
/*
            for(var pyramid_item_key in current_flow_status.pyramid_data) {
                var pyramid_item = current_flow_status.pyramid_data[pyramid_item_key];

                var pyramid_id = 'flow-' + pyramid_item_key;

                var pyramid_element_properties = {
                    "item_id"   : pyramid_id,
                    "section"   : "global",
                    "type"      : "pyramid"
                };

                var $pyramid_element = pyramid_status.render.create(element_properties);

                for(var level_item_key in pyramid_item.levels) {
                    var level_item = pyramid_item.levels[level_item_key];

                    var level_id = pyramid_id +
                            '-' + pyramid_element_properties.section + '-' + pyramid_element_properties.type + '-' +
                            level_item_key;

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
            */
        }
    }
};