log_sent_index = 0;
event_log_array = new Array();

$(function(){

    $("*").on('click', function(e) {
        if(e.toElement == e.currentTarget) {
            var element_data = getElementData(e);

            element_data.x = e.pageX;
            element_data.y = e.pageY;

//        console.log(e);
            add_event_log(element_data);
        }
    });

    $('input, select').on('change', function(e) {
        var element_data = getElementData(e);
        element_data.input_text = e.currentTarget.value;
        element_data.timestamp = e.timeStamp;

        add_event_log(element_data);
//        console.log(element_data);
    });

    $('form').on('submit', function(e) {
        var element_data = {};

        element_data.submitted_form = $(this).serializeArray();
        element_data.type = 'form_submit';
        element_data.timestamp = Date.now();

        add_event_log(element_data);
//        console.log(element_data);
    });

    page_loaded_log();
});

function page_loaded_log() {
    var even_log = {};

    even_log.type = 'page_loaded';
    even_log.timestamp = Date.now();

    add_event_log(even_log);
}


$(window).unload(function(){
    send_log();
});


/*
window.onbeforeunload = function(){
    send_log();
};
*/

function send_log() {
    if(typeof socket === "undefined")
        return false;

    var end_index = event_log_array.length;
    var log_slice = event_log_array.slice(log_sent_index, end_index);
    log_sent_index = end_index;
    //send

    socket.emit('activity log', log_slice);
}

setInterval(send_log, 10000);

function add_event_log(event) {
    var info = ['user_id', 'username', 'level', 'group_id', 'page'];

    for(var i=0; i< info.length; i++) {
        if ($('input[name="' + info[i] + '"]').length > 0)
            event[info[i]] = $('input[name="' + info[i] + '"]').val();
        else
            event[info[i]] = null;
    }

    var info = ['fid', 'numofqustions'];

    for(var i=0; i< info.length; i++) {
        if ($('input[name="' + info[i] + '"]').length > 0)
            event[info[i]] = $('input[name="' + info[i] + '"]').val();
    }

    event.form_data = $('form').serializeArray();
    event.origin = 'browser';
    if(typeof event.timestamp === "undefined")
        event.timestamp = Date.now();

    console.log(event);
    event_log_array.push(event);
}

function logTimerActivation() {
    var even_log = {};

    even_log.type = 'timer_active';
    even_log.timestamp = Date.now();

    add_event_log(even_log);
}

function logRecMsg(msg, room, sender, recipient) {
    var even_log = {};

    even_log.type = 'message_received';
    even_log.timestamp = Date.now();
    even_log.message = msg;
    even_log.sender = sender;
    even_log.recipient = recipient;
    even_log.room = room;

    add_event_log(even_log);
}

function logSentMsg(msg, room, sender) {
    var even_log = {};

    even_log.type = 'message_sent';
    even_log.timestamp = Date.now();
    even_log.message = msg;
    even_log.sender = sender;
    even_log.room = room;

    add_event_log(even_log);
}

function getElementData(e) {
    var even_log = {};
    var element = e.currentTarget;
    even_log.type = e.type;
    even_log.tag = element.localName;
    even_log.attributes = {};
    for(var i=0; i< element.attributes.length; i++) {
        even_log.attributes[element.attributes[i].nodeName] = element.attributes[i].value;
    }
    even_log.URL = element.baseURI;

    return even_log;
}
