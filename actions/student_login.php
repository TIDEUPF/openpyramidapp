<?php
$error;
if(!isset($_SESSION['student'])){
if(isset($_POST['loginBtn'])) {
if(empty($_POST['usr'])) {
$error = "UserId can not be empty!";
}
//the following is the correct student login that cross-refer with the student excel table
else{
$uname = mysqli_real_escape_string($link, stripslashes(strtoupper(trim(strip_tags($_POST['usr'])))));

$res1 = mysqli_query($link, "select * from studentexcel where se_sid = '$uname'");
if(mysqli_num_rows($res1) > 0){
$data1 = mysqli_fetch_assoc($res1);
$sname = $data1['se_sname'];

$res2 = mysqli_query($link, "select * from students where sid = '$uname'");
if(mysqli_num_rows($res2) > 0){
//already in table
$_SESSION['student'] = $uname;
//header("location: student.php"); exit(0);
header("location: student.php"); exit(0);
}
else{
mysqli_query($link,"insert into students values ('$uname', '$sname', NOW() )");
if(mysqli_affected_rows($link) > 0){
$_SESSION['student'] = $uname;
header("location: student.php"); exit(0);
}
else{
$error = 'Database error!';
}

}

}
else{
$error = 'UserId not in course';
}
}

//comment this part------- this for testing purposes
/*else{
$uname = mysqli_real_escape_string($link, stripslashes(trim(strip_tags($_POST['usr']))));

$count_login = mysqli_num_rows(mysqli_query($link, "select sid from students where sid = '$uname' limit 1 "));
if($count_login > 0){
//$_SESSION['student'] = $token;
$_SESSION['student'] = $uname;
header("location: student.php");
exit(0);
}
else{
$error = 'UserId incorrect';
}

}*/

}
}
else{
header("location: student.php");
exit(0);
}