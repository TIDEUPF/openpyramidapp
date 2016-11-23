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

            //TODO: multipyramid-> available/remaining students, time left, 4 minutes timeout status
            //TODO: Pyramid awaiting to be created
            "pyramid" : ["label", "n_users", "started", "completed"],

            "global-group" : function($elem) {
                pyramid_status.utility.clear_classes($elem);
                //TODO: signal the status of the group
                //TODO: show time left on this group
                //TODO: show time satisfaction status?


                var pyramid_data = $elem.prop("pyramid_data");
                $elem.find('a').text((pyramid_data.key + 1));
                $elem.find('a').attr("href", '#' + pyramid_data.parent_id + '-' + pyramid_data.metadata.sibling_id + '-' + pyramid_data.key);
            },
            "detail-message" : function($elem) {
                var pyramid_data = $elem.prop("pyramid_data");
                pyramid_status.utility.clear_classes($elem);

                $elem.find('.username').text(pyramid_data.data.sid);
                $elem.find('.message').text(pyramid_data.data.message);
            },
            "detail-user-rating" : function($elem) {
                var pyramid_data = $elem.prop("pyramid_data");
                pyramid_status.utility.clear_classes($elem);

                $elem.find('.answer').text(pyramid_data.parent_chain.pyramid.data.users_with_groups[pyramid_data.data.answer_id].details.answer);
                $elem.find('.rating').text(pyramid_data.data.rating);
            },
            "detail-group-rating" : function($elem) {
                var pyramid_data = $elem.prop("pyramid_data");
                pyramid_status.utility.clear_classes($elem);

                $elem.find('.answer').text(pyramid_data.parent_chain.pyramid.data.users_with_groups[pyramid_data.data.answer_id].details.answer);
                $elem.find('.rating').text(pyramid_data.data.rating);
            },
            "detail-group" : function($elem) {
                var pyramid_data = $elem.prop("pyramid_data");
                pyramid_status.utility.clear_classes($elem);
                //$elem.text("Group " + (pyramid_data.key + 1));
            },
            "detail-group-user" : function($elem) {
                var pyramid_data = $elem.prop("pyramid_data");
                pyramid_status.utility.clear_classes($elem);
                $elem.text(pyramid_data.data);
            },
            "detail-user" : function($elem) {
                var pyramid_data = $elem.prop("pyramid_data");
                pyramid_status.utility.clear_classes($elem);
                $elem.find('.' + pyramid_data.section + '-' + pyramid_data.type +'-' + 'username').text(pyramid_data.data.details.sid);

                if(pyramid_data.data.details.answer_timestamp) {
                    $elem.find('.' + pyramid_data.section + '-' + pyramid_data.type + '-' + 'answer').text(pyramid_data.data.details.answer);
                    if (pyramid_data.data.details.answer_skip)
                        $elem.find('.' + pyramid_data.section + '-' + pyramid_data.type + '-' + 'answer').text('The student skipped the answer.').
                        addClass("answer-skipped");
                } else {
                    $elem.find('.' + pyramid_data.section + '-' + pyramid_data.type + '-' + 'answer').
                    text("No question submitted").
                    addClass("no-question");
                }
            },
            "detail-user-level" : function($elem) {
                var pyramid_data = $elem.prop("pyramid_data");

                var ratings = [];
                var group_data = pyramid_data.parent_chain.pyramid.data.levels[pyramid_data.key].groups[pyramid_data.data.group_id];
                var user_details = pyramid_data.parent_chain.user.data.details;

                var student_present = group_data.group_finish_timestamp - 60 > user_details.pyramid_access_timestamp;

                $elem.find('.level-label').text('Level ' + (pyramid_data.key+1));

                if(!student_present) {
                    $elem.find('.level-absent').text('The student started the activity at a later stage.');
                    return true;
                }

                $elem.find('.level-absent').text('');

                group_data.group_ratings.forEach(function(rating) {
                    if(rating.sid == user_details.sid) {
                        ratings.push(rating);
                    }
                });

                $elem.find('.level-label').text('Level ' + (pyramid_data.key+1));

                //list user ratings
                var rating_tree = {
                    "id": "rating",
                    "data": ratings,
                    "parent_id": pyramid_data.item_id,
                    "parent_chain": {
                        "flow": {
                            "data": current_flow_status
                        },
                        "pyramid": {
                            "data": pyramid_data.parent_chain.pyramid.data
                        },
                    },
                    "$parent_element": $elem.find('.' + pyramid_data.section + '-' + pyramid_data.type +'-' + 'ratings'),
                    "section": "detail",
                    "type": "user-rating"
                };

               pyramid_status.render.update(rating_tree);


                //messages
                var messages = [];
                pyramid_data.parent_chain.pyramid.data.levels[pyramid_data.key].groups[pyramid_data.data.group_id].chat_messages.forEach(function(message) {
                    if(message.sid == pyramid_data.parent_chain.user.data.details.username) {
                        messages.push(message);
                    }
                });

                //list user messages
                var messages_tree = {
                    "id": "message",
                    "data": messages,
                    "parent_id": pyramid_data.item_id,
                    "parent_chain": {
                        "flow": {
                            "data": current_flow_status
                        }
                    },
                    "$parent_element": $elem.find('.messages'),
                    "section": "detail",
                    "type": "message"
                };

                pyramid_status.render.update(messages_tree);

/*
                //ratings
                var ratings = [];
                pyramid_data.parent_chain.pyramid.data.levels[pyramid_data.key].groups[pyramid_data.data.group_id].group_ratings.forEach(function(rating) {
                    if(rating.sid == pyramid_data.parent_chain.user.data.details.sid) {
                        ratings.push(rating);
                    }
                });

                //list user ratings
                var rating_tree = {
                    "id": "rating",
                    "data": ratings,
                    "parent_id": pyramid_data.item_id,
                    "parent_chain": {
                        "flow": {
                            "data": current_flow_status
                        },
                        "pyramid": {
                            "data": pyramid_data.parent_chain.pyramid.data
                        },
                    },
                    "$parent_element": $elem.find('.group-ratings'),
                    "section": "detail",
                    "type": "user-rating"
                };

                pyramid_status.render.update(rating_tree);
*/
            },

            "chat" : function() {},
            "student" : function() {},
            "rating" : function() {}
        },
        "create" : function(params) {
            var $item = $('#'+params.item_id).length ? $('#'+params.item_id) : $(pyramid_template[params.section][params.type]);
            $item.prop('id', params.item_id);
            $item.attr('element-type', params.section+'-'+params.type);
            $item.attr('key', params.key);

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

            var update_item = function(data_item, data_item_key) {
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
                        var child_item_copy = {};

                        Object.keys(child_item).forEach(function(key) {
                            child_item_copy[key] = child_item[key];
                        });

                        child_item_copy.data = (typeof child_item.data === "string") ? data_item[child_item.data] : child_item.data;
                        child_item_copy.parent_id = item_id;
                        child_item_copy.$parent_element = $element;
                        child_item_copy.parent_chain = parent_chain;

                        pyramid_status.render.update(child_item_copy);
                    });
                }

                var $parent_element = (typeof params.$alternate_parent === "undefined") ? params.$parent_element : params.$alternate_parent;
                $parent_element = (typeof params.insert === "undefined") ? $parent_element : $parent_element.find('.'+params.insert);

                pyramid_status.render.insert({
                    "$item"         : $element,
                    "$parent_element"   : $parent_element
                });

            }

            if(typeof params.data.forEach === "undefined") {
                Object.keys(params.data).forEach(function (key) {
                    var numkey = '';
                    for (var i = 0; i < key.length; i++) {
                        numkey += key.charCodeAt(i) + '-';
                    }
                    numkey += '0';

                    update_item(params.data[key], numkey);
                });
            } else {
                params.data.forEach(update_item);
            }

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
            $user_detail = $('#user-detail-frame');

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
                "subtree"   : [{
                    "id" :  "user-detail",
                    "data" : "users_with_groups",
                    "$alternate_parent" : $user_detail,
                    "section"   : "detail",
                    "type"      : "user",
                    "subtree"   : {
                        "id": "user-detail-level",
                        "data": "levels",
                        "section": "detail",
                        "type": "user-level"
                    }
                }, {
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
                            "type"      : "group",
                            "subtree"   : [{
                                "id": "detail-group-user",
                                "data": "group_users",
                                "insert": "users",
                                "section": "detail",
                                "type": "group-user"
                            }, {
                                "id": "detail-group-ratings",
                                "data": "group_rating_table",
                                "insert": "ratings",
                                "section": "detail",
                                "type": "group-rating"
                            }, {
                                "id": "detail-group-message",
                                "data": "chat_messages",
                                "insert": "messages",
                                "section": "detail",
                                "type": "message"
                            }

                            ]
                        }
                    ]
                }]
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