<?php
namespace Util;

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
        $date = time();

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

function get_users_email() {
    global $link;

    $students_result = mysqli_query($link, "select sid from students");

    $emails = [];

    while($students_row = mysqli_fetch_assoc($students_result)) {
        if(filter_var($students_row['sid'], FILTER_VALIDATE_EMAIL))
            $emails[] = $students_row['sid'];
    }

    return $emails;
}

function get_html($step) {
    if($step == 1) {
        $html = <<<HTML
<html>
<body>
<p>Hi,</p>

<p>You have successfully promoted to the next level in the PyramidApp. You can login to the app using the same email address or the username that you used previously. This is the link to the app: <a href="http://tiny.cc/pyramidapp">tiny.cc/pyramidapp</a>.</p>

<p>In this new level you can see the submitted questions and you can use chat feature to discuss with other group members regarding their questions.</p>

<p>You have time till 6 pm, Saturday, 27th (from Central European Time) to discuss and rate. You can modify previously submitted rating till this deadline. &nbsp;</p>

<p>Hope you will have a nice time in the app!</p>

<p>&nbsp;</p>

<p>Best Regards<br />
GTI - UPF<br />
Barcelona</p>
</body>
</html>
HTML;
    } elseif($step == 2) {
        $html = <<<HTML
<html>
<body>
<p>Hi,</p>

<p>You have successfully promoted to the next level in the PyramidApp. You can login to the app using the same email address or the username that you used previously. This is the link to the app: <a href="http://tiny.cc/pyramidapp">tiny.cc/pyramidapp</a>.</p>

<p>In this new level you can see the submitted questions and you can use chat feature to discuss with other group members regarding their questions.</p>

<p>You have time till 12 midnight, Saturday, 28th (from Central European Time) to discuss and rate. You can modify previously submitted rating till this deadline. &nbsp;</p>

<p>Hope you will have a nice time in the app!</p>

<p>&nbsp;</p>

<p>Best Regards<br />
GTI - UPF<br />
Barcelona</p>
</body>
</html>
HTML;
} elseif($step == 3) {
        $html = <<<HTML
<html>
<body>
<p>Hi,</p>

<p>You have successfully promoted to the next level in the PyramidApp. You can login to the app using the same email address or the username that you used previously. This is the link to the app: <a href="http://tiny.cc/pyramidapp">tiny.cc/pyramidapp</a>.</p>

<p>In this new level you can see the submitted questions and you can use chat feature to discuss with other group members regarding their questions.</p>

<p>You have time till 12 midnight, Thursday, 25th (from Central European Time) to discuss and rate. You can modify previously submitted rating till this deadline. &nbsp;</p>

<p>Hope you will have a nice time in the app!</p>

<p>&nbsp;</p>

<p>Best Regards<br />
GTI - UPF<br />
Barcelona</p>
</body>
</html>
HTML;
    } elseif($step == 4) {
        $html = <<<HTML
<html>
<body>
<p>Hi,</p>

<p>It's the final stage of the PyramidApp. You can login to the app using the same email address or the username that you used previously. This is the link to the app: <a href="http://tiny.cc/pyramidapp">tiny.cc/pyramidapp</a>.</p>

<p>In this new level you can see the submitted questions and you can use chat feature to discuss with other group members regarding their questions.</p>

<p>You have time till 12 midnight, Thursday, 25th (from Central European Time) to discuss and rate. You can modify previously submitted rating till this deadline. &nbsp;</p>

<p>Answers will be provided in the "Step 2.1 comments".</p>
<p>Thank you for being in the app!</p>

<p>&nbsp;</p>

<p>Best Regards<br />
GTI - UPF<br />
Barcelona</p>
</body>
</html>
HTML;
    }

    return $html;
}

function notification_mail($recipients, $html) {
    require_once 'Mail.php';
    include_once 'Mail/mime.php';

    $from = "Pyramid Interaction App <ssp.clfp@upf.edu>";

    $subject = "PyramidApp notification";
    $crlf = "\n";

    $host = "ssl://smtp.gmail.com";
    $port = "465";
    $username = "ssp.clfp@upf.edu";
    $password = "sos14Gti!";

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
            echo("error delevering to {$recipient}: " . $mail->getMessage() . "\n");
        } else {
            echo("Message successfully sent message to {$recipient}\n");
        }
        sleep(4);
    }

    unset($smtp);
}