<?php

Student\enforce_student_login();
$sname = Student\get_student_username();
$sid = $_SESSION['student'];

//get information the latest flow
$res3 = mysqli_query($link, "select * from flow order by fid desc limit 1");
if(mysqli_num_rows($res3) > 0){
	$data3 = mysqli_fetch_assoc($res3);
	$levels = $data3["levels"];
	$fname = $data3["fname"];
	$fdes = $data3["fdes"];
	$fid = $data3["fid"];
}
else{
	throw new Exception("There are no flows");
}

	//submit answer
	if(isset($_POST['answer'])) {
		$ans_input =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['qa']))));
		if($ans_input != ''){
			//why can't edit the answer once submitted? because should not rate while editing. wrong answer will be rated
			if(mysqli_num_rows(mysqli_query($link, "select * from flow_student where sid = '$sid' and fid = '$fid'")) > 0){
				//edit if already answered
				//mysqli_query($link,"update flow_student set fs_answer = '$ans_input' where sid = '$sid' and fid_ = '$fid'");
				//if(mysqli_affected_rows($link) > 0){ $success = 'Submitted.'; }
			}
			else{
				//insert new
				mysqli_query($link,"insert into flow_student values ('', '$fid', '$sid', '$ans_input')");
				if(mysqli_affected_rows($link) > 0) {
					$success = 'Submitted.';
				} else {
					$error = 'Database error!';
				}
			}	
		}
		else{
			$error = 'Field cannot be empty!';
		}
		if(isset($error)) {
			$body = View\element("question_form", array(
					'username' 					=> $sname,
					'level' 					=> 'Level ' . '0/' . $levels,
					'question_text' 			=> 'Write a question',
					'question_submit_button' 	=> 'Submit your question',
					'error' 					=> $error,
					'hidden_input_array' 		=> array(
						'a_lvl' 			=> $_POST['a_lvl'],
						'a_peer_group_id'	=> $_POST['a_peer_group_id'],
					),
			));
			View\page(array(
					'title' => 'Question',
					'body' => $body,
			));
			exit;
		}
	}//submit answer
	
	//submit rating
	if(isset($_POST['rate'])) { //why 3set, cz there can be 3ratings sometimes
		$rate_input =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['optradio1']))));
		$rate_lvl =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['lvl1']))));
		$to_whom_rated_id =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['to_whom_rated_id1']))));
		$rgroup_id =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['group_id1']))));
		
		$rate_input2 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['optradio2']))));
		$rate_lvl2 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['lvl2']))));
		$to_whom_rated_id2 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['to_whom_rated_id2']))));
		$rgroup_id2 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['group_id2']))));
		
		$rate_input3 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['optradio3']))));
		$rate_lvl3 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['lvl3']))));
		$to_whom_rated_id3 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['to_whom_rated_id3']))));
		$rgroup_id3 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['group_id3']))));

		if($rate_input != ''){
			
			
			if(mysqli_num_rows(mysqli_query($link, "select * from flow_student_rating where fsr_sid = '$sid' and fsr_fid = '$fid' and fsr_level = '$rate_lvl' and fsr_group_id = '$rgroup_id' and fsr_to_whom_rated_id = '$to_whom_rated_id' ")) > 0){
				//if editing is required of submitted rating
			}
			else{
				//insert new
				if($rate_lvl == 0)
				{
					mysqli_query($link,"insert into flow_student_rating values ('', '$fid', '$sid', '$rate_lvl', '$rgroup_id', '$rate_input', '$to_whom_rated_id', NOW() )");
				}
				else{
					
					$numofqustions =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['numofqustions']))));
					
					if($numofqustions == 1)
					{
						mysqli_query($link,"insert into flow_student_rating values ('', '$fid', '$sid', '$rate_lvl', '$rgroup_id', '$rate_input', '$to_whom_rated_id', NOW() )");
					}
					elseif($numofqustions == 2)
					{						
						if(empty($rate_input2) || empty($rate_input))
						{
							$error = 'Please Rate All!';
						}
						else
						{
							mysqli_query($link,"insert into flow_student_rating values ('', '$fid', '$sid', '$rate_lvl', '$rgroup_id', '$rate_input', '$to_whom_rated_id', NOW() )");	
							mysqli_query($link,"insert into flow_student_rating values ('', '$fid', '$sid', '$rate_lvl2', '$rgroup_id2', '$rate_input2', '$to_whom_rated_id2', NOW() )");
						}
					}
					elseif($numofqustions == 3)
					{						
						if(empty($rate_input2) || empty($rate_input) || empty($rate_input3))
						{
							$error = 'Please Rate All!';
						}
						else
						{
							mysqli_query($link,"insert into flow_student_rating values ('', '$fid', '$sid', '$rate_lvl', '$rgroup_id', '$rate_input', '$to_whom_rated_id', NOW() )");	
							mysqli_query($link,"insert into flow_student_rating values ('', '$fid', '$sid', '$rate_lvl2', '$rgroup_id2', '$rate_input2', '$to_whom_rated_id2', NOW() )");
							mysqli_query($link,"insert into flow_student_rating values ('', '$fid', '$sid', '$rate_lvl3', '$rgroup_id3', '$rate_input3', '$to_whom_rated_id3', NOW() )");
						}
					}
				}
				
				if(mysqli_affected_rows($link) > 0){ $success = 'Rating Submitted.'; }else{	/*$error = 'Database error!';*/	}
			}
		}
		else{
			$error = 'Rating cannot be empty!';
		}
	}//submit rating

include('activity_rating.php');
/*
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootstrap.min.css">
	<script type="text/javascript">
	var timeoutPeriod = 10000;
	function del_vali(){ if(confirm('Submit Answer?')) { return true; }else{ return false; } }
	function del_vali2(){ if(confirm('Rate Answer?')) { return true; }else{ return false; } }
	</script>
  </head>
  <body>
  
	<?php include('topnav2.php'); ?>
    <div class="container">
			<span>User: <?php echo $_SESSION['student']; ?></span>
		<h3><span class="label label-primary"><?php echo $fname; ?></span> - <a class="small" href=""><kbd>Refresh</kbd></a></h3>
		<span>In each level, rate your peers! When <b>ALL</b> of you finish one level, the next level will appear.</span><br />
		<br /><span>The task is : <br /><b><?php echo $fdes; ?></b></span><br />
				
		<?php if(!empty($error)) {echo '<br /><span class="label label-danger">'.$error.'</span><br /><br />';} ?>
		<?php if(!empty($success)) {echo '<br /><span class="label label-success">'.$success.'</span><br /><br />';} ?>
		
		<?php include('activity_rating.php'); ?>
		
	</div>	
  </body>
  
<script type="text/JavaScript">
if(timeoutPeriod != ''){setTimeout("refreshp();",timeoutPeriod);}
function refreshp(){
	window.location.href = window.location.href;
}
</script>  
</html>
*/

