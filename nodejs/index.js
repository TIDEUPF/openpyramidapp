/**
 * Created by saden on 11/05/2017.
 */
// Setup basic express server
/*
 var app = express();
 var server = require('http').createServer(app);
 var io = require('socket.io')(server);
 */
var port = process.env.PORT || 8000;

var mysql = require('mysql');
var pool  = mysql.createPool({
    connectionLimit     : 25,
    host                : '',
    user                : '',
    password            : '',
    database            : '',
    charset             : 'utf8mb4',
    /*socketPath          : '/var/run/mysqld/mysqld.sock',*/
    stringifyObjects    : true,
    multipleStatements  : true
});

var io = require('socket.io').listen(port);

/*server.listen(port, function () {
 console.log('Server listening at port %d', port);
 });
 */
// Routing
//app.use(express.static(__dirname + '/public'));

// Chatroom

// usernames which are currently connected to the chat
var usernames = {};
var numUsers = 0;

io.on('connection', function (socket) {
    var addedUser = false;

    // when the client emits 'typing', we broadcast it to others
    socket.on('join room', function (data) {
        socket.join(data.room);
    });

    // when the client emits 'new message', this listens and executes
    socket.on('new message', function (data) {
        // we tell the client to execute 'new message'
        //socket.broadcast.emit('new message', {
        socket.to(data.room).emit('new message', {
            username: socket.username,
            message: data.message,
            room: data.room
        });

        console.log([data.fid, data.pid, socket.username, data.room, data.message]);
        pool.query('insert into `chat` values (null, ? , ? , ? , ? , ? , NOW())', [data.fid, data.pid, socket.username, data.room, data.message]);
    });

    socket.on('activity log', function (data) {
        // we tell the client to execute 'new message'
        log_data = data;
        for(var i=0;i<log_data.length;i++) {
            pool.query('insert into activity_log values (null, ? , ? , ? , ? , ? , ? , ? , FROM_UNIXTIME(?), ? )', [log_data[i].user_id, log_data[i].page, log_data[i].username, log_data[i].type, log_data[i].level, log_data[i].group_id, JSON.stringify(log_data[i]), Math.floor(log_data[i].timestamp/1000), log_data[i].origin]);
        }

        console.log(data);
    });

    // when the client emits 'add user', this listens and executes
    socket.on('add user', function (username) {
        // we store the username in the socket session for this client
        socket.username = username;
        // add the client's username to the global list
        usernames[username] = username;
        ++numUsers;
        addedUser = true;
        socket.emit('login', {
            numUsers: numUsers
        });
        // echo globally (all clients) that a person has connected
        socket.broadcast.emit('user joined', {
            username: socket.username,
            numUsers: numUsers
        });
    });

    // when the client emits 'typing', we broadcast it to others
    socket.on('typing', function () {
        socket.broadcast.emit('typing', {
            username: socket.username
        });
    });

    // when the client emits 'stop typing', we broadcast it to others
    socket.on('stop typing', function () {
        socket.broadcast.emit('stop typing', {
            username: socket.username
        });
    });

    // when the user disconnects.. perform this
    socket.on('disconnect', function () {
        // remove the username from global usernames list
        if (addedUser) {
            delete usernames[socket.username];
            --numUsers;

            // echo globally that this client has left
            socket.broadcast.emit('user left', {
                username: socket.username,
                numUsers: numUsers
            });
        }
    });
});
