<?php
session_start(); 
include('dbvar.php');

$error; 
if(!isset($_SESSION['user'])){
	if(isset($_POST['loginBtn'])) {
		if(empty($_POST['usr']) || empty($_POST['pwd'])) {
			$error = "Username and Password can not be empty!";
		}
		else{
			$uname = mysqli_real_escape_string($link, stripslashes(trim(strip_tags($_POST['usr']))));
			$pass =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['pwd']))));			
			
			$count_login = mysqli_num_rows(mysqli_query($link, "select uname from teacher where uname = '$uname' and pass = '$pass' limit 1 "));
			if($count_login > 0){
				//$_SESSION['token'] = $token;
				$_SESSION['user'] = $uname;
				header("location: teacher.php");
				exit(0);
			}
			else{
				$error = 'Username or password incorrect';
			}
		
		}
	}
}
else{
	header("location: teacher.php");
	exit(0);
}
  
?><!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="vendors/bootstrap/bootstrap.min.css">
  </head>

  <body>
  <?php include('topnav.php'); ?>
    <div class="container">
      <h2>Teacher Login</h2>
      <h3><b>Login</b></h3>
      <form role="form" action="" method="post">
        <div class="form-group">
          <label for="usr">Name:</label>
          <input type="text" class="form-control" id="usr" name="usr">
        </div>
        <div class="form-group">
          <label for="pwd">Password:</label>
          <input type="password" class="form-control" id="pwd" name="pwd">
        </div>
		 <div class="form-group">
          <input type="submit" class="btn btn-info" value="Login" name="loginBtn">
        </div>
		<span><?php if(!empty($error)) {echo $error;} ?></span>
      </form>
    </div>

    
  </body>

</html>