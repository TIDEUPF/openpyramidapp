<?php
namespace Util;

function pyramid_time() {
    global $debug_time;

    if(!empty($debug_time))
        return $debug_time;

    return time();
}

// Generate a random character string
function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
    // Length of character list
    $chars_length = (strlen($chars) - 1);

    // Start our string
    $string = $chars{mt_rand(0, $chars_length)};

    // Generate random string
    for ($i = 1; $i < $length; $i = strlen($string)) {
        // Grab a random character from our list
        $r = $chars{mt_rand(0, $chars_length)};

        // Make sure the same two characters don't appear next to each other
        if ($r != $string{$i - 1}) $string .=  $r;
    }

    // Return the string
    return $string;
}

function exec_sql($sql) {
    global $link, $sid, $fid, $ps, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $result = mysqli_query($link, $sql);
    $result_rows = [];
    if(mysqli_num_rows($result) > 0){ //get current level pyramid group info
        while($row = mysqli_fetch_assoc($result))
        {
            if(isset($row['timestamp'])) $row['timestamp'] = (int)$row['timestamp'];
            if(isset($row['fid'])) $row['fid'] = (int)$row['fid'];
            if(isset($row['pid'])) $row['pid'] = (int)$row['pid'];
            if(isset($row['skip'])) $row['skip'] = (int)$row['skip'];

            $result_rows[] = $row;
        }
        return $result_rows;
    } else {
        return [];
    }
}

function exec_sql_bool($sql) {
    global $link, $sid, $fid, $ps, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $result = mysqli_query($link, $sql);
    if(mysqli_num_rows($result) > 0){ //get current level pyramid group info
        return true;
    } else {
        return false;
    }
}

function get_sql_pyramid($params= null) {
    global $fid, $pid;

    $table_prefix = '';
    $fid_prefix = '';
    $pid_prefix = '';

    if(isset($params['table'])) {
        $table_prefix = $params['table'];
    }

    if(isset($params['prefix'])) {
        $fid_prefix = $params['prefix'] . '_';
        $pid_prefix = $params['prefix'] . '_';
    }

    $sql = " {$table_prefix}{$fid_prefix}fid='{$fid}' and {$table_prefix}{$pid_prefix}pid='{$pid}' ";

    return $sql;
}

function sql_gen() {
    global $ps;
    $prefixes = ['sa', 'pg', 'fsr'];

    $ps['e'] = get_sql_pyramid();
    foreach($prefixes as $p) {
        $ps[$p] = get_sql_pyramid(['prefix'=>$p]);
    }
}

function get_room_string($fid, $pid, $activity_level, $peer_group_id) {
    //$location_id = get_localtion_id();

    //if(!$location)
        $location_id = "";

    //$room = 'room_' . $fid . '_' . $peer_group_id . '_' . $activity_level . '_' . $pid . '_' . $location_id;
    $room = 'room_' . $fid . '_' . $peer_group_id . '_' . $activity_level . '_' . $pid . '_';

    return $room;
}

function get_localtion_id() {
    return hash('crc32b', $_SERVER['SCRIPT_NAME']);
}

function sanitize_array($data_array) {
    global $link;

    $sanitized_array = array();
    if(!empty($data_array)) {
        foreach($data_array as $entry) {
            $sanitized_array[] = mysqli_real_escape_string($link, $entry);
        }
    }

    return $data_array;
    return $sanitized_array;
}

function log_submit() {
    if(empty($_REQUEST['log']))
        return false;

    if(($data = json_decode($_REQUEST['log'])) === NULL)
        return false;

    foreach($data as $entry) {
        log(['activity' => $entry->type, 'timestamp' => floor($entry->timestamp/1000), 'origin' => 'browser', 'entry' => $entry]);
    }
}

function log($data) {
    global $link, $fid, $pid, $sname, $activity_level, $peer_group_id;

    if(!isset($data['origin']))
        $data['origin'] = 'php_backend';

    $data['fid'] = $fid;
    $data['pid'] = $pid;
    if(!empty($sname))
        $data['sname'] = $sname;

    $user_id = !empty($_SESSION['user_id']) ? mysqli_real_escape_string($link, $_SESSION['user_id']) : '';
    $sname = !empty($data['sname']) ? mysqli_real_escape_string($link, $data['sname']) : '';

    if(isset($data['timestamp']))
        $date = $data['timestamp'];
    else
        $date = \Util\pyramid_time();

    if(isset($data['entry']))
        $data_json = mysqli_real_escape_string($link, json_encode($data['entry']));
    else
        $data_json = mysqli_real_escape_string($link, json_encode((object)$data));

    mysqli_query($link, "insert into activity_log values (null, '$user_id', null, '$sname', '{$data['activity']}', '$activity_level', '$peer_group_id', '{$data_json}', FROM_UNIXTIME('$date'), '{$data['origin']}')");
}

function filter_email($email_array) {
    if(empty($email_array) or !is_array($email_array))
        return $email_array;

    $filtered = [];
    foreach($email_array as $email) {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_split = explode('@', $email);
            $filtered[] = $email_split[0];
        } else {
            $filtered[] = $email;
        }
    }
    return $filtered;
}

function get_users_email($fid, $pid) {
    global $link;

    if(is_array($pid))
        $pid_list = implode(',', $pid);
    else
        $pid_list = $pid;

    $students_result = mysqli_query($link, "select sid from pyramid_students where fid = {$fid} and pid in ($pid_list)");

    $emails = [];

    while($students_row = mysqli_fetch_assoc($students_result)) {
        if(filter_var($students_row['sid'], FILTER_VALIDATE_EMAIL) and strpos(strtolower($students_row['sid']), "@test.tt") === FALSE)
            $emails[] = $students_row['sid'];
    }

    return $emails;
}

function get_html($step) {
    global $url, $fid, $flow_data;

    $date_string = \Pyramid\end_date_string($step);

    if($step == 1) {
/*
        $html = <<<HTML
<html>
<body>
<p>Hi,</p>

<p>You have successfully promoted to the next level in the PyramidApp. You can login to the app using the same email address or the username that you used previously. This is the link to the app: <a href="{$url}student.php">{$url}student.php</a>.</p>

<p>In this new level you can see the submitted questions and you can use chat feature to discuss with other group members regarding their questions.</p>

<p>You have time till {$date_string} (from Central European Time) to discuss and rate. You can modify previously submitted rating till this deadline. &nbsp;</p>

<p>Hope you will have a nice time in the app!</p>

<p>&nbsp;</p>

<p>Best Regards<br />
GTI - UPF<br />
Barcelona</p>
</body>
</html>
HTML;
        */
        $html = <<<HTML
<html>
<body>
<div dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;">&nbsp;</div>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54d2-597e-6134-c9fcbfa6a018"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">Hi,</span></span></span></span></p>

<div dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;">&nbsp;</div>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54d2-597e-6134-c9fcbfa6a018"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">Your submission was successful and you have been promoted to the next level where you can discuss and rate in small groups in the PyramidApp.</span></span></span></span></p>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54d2-597e-6134-c9fcbfa6a018"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">Login to the app using the same username as previously. This is the link to the app: </span></span></span>&nbsp;<a href="{$url}activity/{$fid}" style="text-decoration:none;"><span style="text-decoration:underline;vertical-align:baseline;white-space:pre-wrap;"><span style="background-color:#ffffff;"><span style="color:#1155cc;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">{$url}activity/{$fid}</span></span></span></span></span></a> </span></p>

<div dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;">&nbsp;</div>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54d2-597e-6134-c9fcbfa6a018"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">In this new level you can see options, about which you can discuss in the chat window and select the most relevant option with other group members. &nbsp;</span></span></span></span></p>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54d2-597e-6134-c9fcbfa6a018"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">You have time till {$date_string}</span></span></span><span style="vertical-align:super;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:8.8px;"></span></span></span><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;"> to discuss and rate. You can modify once submitted rating till this deadline. &nbsp;</span></span></span></span></p>
<br>
<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54d2-597e-6134-c9fcbfa6a018"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">A reminder of the task: &nbsp;</span></span></span></span></p>

<div>{$flow_data['question']}</div>

<p></p>
<div dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;">&nbsp;</div>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54d2-597e-6134-c9fcbfa6a018"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">Hope you will have a nice time in the app!</span></span></span></span></p>

<div dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;">&nbsp;</div>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54d2-597e-6134-c9fcbfa6a018"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">Best Regards</span></span></span></span></p>

<p><span id="docs-internal-guid-52bbb687-54d2-597e-6134-c9fcbfa6a018"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">PyramidApp team</span></span></span></span></p>
</body>
</html>
HTML;
        /*
        $html = <<<HTML
<html>
<body>
<p>Hola,</p>

<p>Has sido promocionado al siguiente nivel de la PyramidApp. Puedes volver a la app introduciendo el mismo email. Este es el enlace a la app: <a href="http://tiny.cc/pyramidapp">tiny.cc/pyramidapp</a>.</p>

<p>En este nivel puedes ver las preguntas entregadas por otros miembros y tienes un chat para discutir las diferentes opciones.</p>

<p>Tienes de tiempo hasta el miércoles a las 23:59h para discutir y puntuar las preguntas. &nbsp;</p>

<p>¡Que pases un buén rato en la app!</p>

<p>&nbsp;</p>

<p>Saludos<br />
GTI - UPF<br />
Barcelona</p>
</body>
</html>
HTML;
*/
    } elseif($step == 2) {
        /*
        $html = <<<HTML
<html>
<body>
<p>Hi,</p>

<p>You have successfully promoted to the next level in the PyramidApp. You can login to the app using the same email address or the username that you used previously. This is the link to the app: <a href="{$url}student.php">{$url}student.php</a>.</p>

<p>In this new level you can see the submitted questions and you can use chat feature to discuss with other group members regarding their questions.</p>

<p>You have time till {$date_string} (from Central European Time) to discuss and rate. You can modify previously submitted rating till this deadline. &nbsp;</p>

<p>Hope you will have a nice time in the app!</p>

<p>&nbsp;</p>

<p>Best Regards<br />
GTI - UPF<br />
Barcelona</p>
</body>
</html>
HTML;
*/
        $html = <<<HTML
<html>
<body>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54da-8bb3-752b-1e0381e27f35"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">Hi,</span></span></span></span></p>

<div dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;">&nbsp;</div>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54da-8bb3-752b-1e0381e27f35"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">It&rsquo;s the final stage of the PyramidApp. You can login to the app using the same username as previously. This is the link to the app: </span></span></span><a href="{$url}activity/{$fid}" style="text-decoration:none;"><span style="text-decoration:underline;vertical-align:baseline;white-space:pre-wrap;"><span style="background-color:#ffffff;"><span style="color:#1155cc;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">{$url}activity/{$fid}</span></span></span></span></span></a> &nbsp;</span></p>

<div dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;">&nbsp;</div>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54da-8bb3-752b-1e0381e27f35"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">In the last level, you are joined to much larger group with highest rated options from previous level. You can once again discuss and rate the most relevant option till {$date_string}</span></span></span><span style="vertical-align:super;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:8.8px;"></span></span></span><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">. Also you can modify once submitted rating till this deadline. &nbsp;</span></span></span></span></p>

<br>
<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54d2-597e-6134-c9fcbfa6a018"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">A reminder of the task: &nbsp;</span></span></span></span></p>

<div>{$flow_data['question']}</div>

<div dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;">&nbsp;</div>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54da-8bb3-752b-1e0381e27f35"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">Hope you will have a nice time in the app!</span></span></span></span></p>

<div dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;">&nbsp;</div>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54da-8bb3-752b-1e0381e27f35"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">Best Regards</span></span></span></span></p>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54da-8bb3-752b-1e0381e27f35"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">PyramidApp team</span></span></span></span></p>

<div>&nbsp;</div>

</body>
</html>
HTML;

} elseif($step == 3) {
        $html = <<<HTML
<html>
<body>
<p>Hi,</p>

<p>It’s the final stage of the PyramidApp. You can login to the app using the same email address or the username that you used previously. This is the link to the app: <a href="{$url}student.php">pyramid app</a>.</p>

<p>In this level you will see final highly rated questions promoted from the previous level. You can discuss about these questions and rate the best.</p>

<p>You have time till {$date_string} (from Central European Time) to discuss and rate. You can modify previously submitted rating till this deadline.</p>

<p>Thank you for being in the app!</p>

<p>&nbsp;</p>

<p>Best Regards<br />
GTI - UPF<br />
Barcelona</p>
</body>
</html>
HTML;

    } elseif($step == 99) {
        $html = <<<HTML
<html>
<body>
<p>Hi,</p>

<p>We have two winning links selected from the PyramidApp. You can login to the app using the same email address or the username that you used previously to view final results. This is the link to the app: <a href="{$url}student.php">{$url}student.php</a>.</p>

<p>Please make sure that you fill out the small feedback form also.</p>

<p>Thank you for helping us in our research. Hope you liked this activity!</p>

<p>&nbsp;</p>

<p>Best Regards<br />
GTI - UPF<br />
Barcelona</p>
</body>
</html>
HTML;

        $html = <<<HTML
<html>
<body>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54db-9501-12a9-fc6011a3567e"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">Hi, </span></span></span></span></p>

<div dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;">&nbsp;</div>

<p dir="ltr" style="line-height:1.38;margin-top:0pt;margin-bottom:0pt;"><span id="docs-internal-guid-52bbb687-54db-9501-12a9-fc6011a3567e"><span style="vertical-align:baseline;white-space:pre-wrap;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">Now you can see the top rated option from the complete activity and also what happened with other large groups in the class too if you login to PyramidApp : </span></span></span><a href="{$url}activity/{$fid}" style="text-decoration:none;"><span style="text-decoration:underline;vertical-align:baseline;white-space:pre-wrap;"><span style="background-color:#ffffff;"><span style="color:#1155cc;"><span style="font-family:Arial;"><span style="font-size:14.6667px;">{$url}activity/{$fid}</span></span></span></span></span></a> &nbsp;</span></p>

<div>&nbsp;</div>


</body>
</html>
HTML;
    }

    return $html;
}

function notification_mail($recipients, $html) {
    global $email;

    echo date('l jS \of F Y h:i:s A') . "\n";
    echo var_export($recipients) . "\n";
    echo "$html" . "\n";

    if(empty($email)) {
        return false;
    }

    $from = "Pyramid Interaction App <{$email['address']}>";
    $host = $email['host'];
    $port = $email['port'];
    $username = $email['username'];
    $password = $email['password'];

    $subject = "PyramidApp notification";
    $crlf = "\n";

    $smtp = \Mail::factory('smtp',
        [   'host' => $host,
            'port' => $port,
            'auth' => true,
            'persist' => true,
            'username' => $username,
            'password' => $password]);

    foreach($recipients as $recipient) {
        $headers = array (
            'From' => $from,
            'To' => $recipient,
            'Subject' => $subject,
        );

        // Creating the Mime message
        $mime = new \Mail_mime($crlf);

        // Setting the body of the email
    //      $mime->setTXTBody($text);
        $mime->setHTMLBody($html);

        $body = $mime->get();
        $headers = $mime->headers($headers);

        $mail = $smtp->send($recipient, $headers, $body);

        if (\PEAR::isError($mail)) {
            echo("Error delivering to {$recipient}: " . $mail->getMessage() . "\n");
        } else {
            echo("Message successfully sent message to {$recipient}\n");
        }
        sleep(4);
    }

    unset($smtp);
}

/** Extracted from: http://codeaid.net/php/convert-seconds-to-hours-minutes-and-seconds-%28php%29
 * Convert number of seconds into hours, minutes and seconds
 * and return an array containing those values
 *
 * @param integer $seconds Number of seconds to parse
 * @return array
 */
function secondsToTime($seconds)
{
    // extract hours
    $hours = floor($seconds / (60 * 60));

    // extract minutes
    $divisor_for_minutes = $seconds % (60 * 60);
    $minutes = floor($divisor_for_minutes / 60);

    // extract the remaining seconds
    $divisor_for_seconds = $divisor_for_minutes % 60;
    $seconds = ceil($divisor_for_seconds);

    // return the final array
    $obj = array(
        "h" => (int) $hours,
        "m" => (int) $minutes,
        "s" => (int) $seconds,
    );
    return $obj;
}
