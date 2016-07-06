<?php
namespace Util;

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
    $date_string = \Flow\end_date_string($step);

    if($step == 1) {

        $html = <<<HTML
<html>
<body>
<p>Hi,</p>

<p>You have successfully promoted to the next level in the PyramidApp. You can login to the app using the same email address or the username that you used previously. This is the link to the app: <a href="http://sos.gti.upf.edu/app/student.php">sos.gti.upf.edu/app/student.php</a>.</p>

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
        $html = <<<HTML
<html>
<body>
<p>Hi,</p>

<p>You have successfully promoted to the next level in the PyramidApp. You can login to the app using the same email address or the username that you used previously. This is the link to the app: <a href="http://sos.gti.upf.edu/app/student.php">sos.gti.upf.edu/app/student.php</a>.</p>

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

} elseif($step == 3) {
        $html = <<<HTML
<html>
<body>
<p>Hi,</p>

<p>It’s the final stage of the PyramidApp. You can login to the app using the same email address or the username that you used previously. This is the link to the app: <a href="http://sos.gti.upf.edu/app/student_login.php">pyramid app</a>.</p>

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

    } elseif($step == 4) {
        $html = <<<HTML
<html>
<body>
<p>Hi,</p>

<p>We have two winning links selected from the PyramidApp. You can login to the app using the same email address or the username that you used previously to view final results. This is the link to the app: <a href="http://sos.gti.upf.edu/app/student.php">sos.gti.upf.edu/app/student.php</a>.</p>

<p>Please make sure that you fill out the small feedback form also.</p>

<p>Thank you for helping us in our research. Hope you liked this activity!</p>

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
    global $email;

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