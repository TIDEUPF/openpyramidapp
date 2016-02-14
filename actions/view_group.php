<?php
session_start(); 
include('dbvar.php');

if(isset($_SESSION['user'])) {
	$teacher_id = $_SESSION['user'];
	$fid = (int) $_GET['fid'];

	//find the last pid
	$npid = null;
	$result = mysqli_query($link,"select * from pyramid_students where fid='$fid' order by pid desc limit 1");

	if(!mysqli_num_rows($result))
		$npid = 0;
	else {
		$result_row = mysqli_fetch_assoc($result);
		$npid = (int)$result_row['pid'];
	}
}
else{
	header("location: login.php");
	exit(0);
}	
q
?><!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="vendors/bootstrap/bootstrap.min.css">
	  <script src="vendors/jquery/jquery-2.1.4.min.js"></script>
	  <script src="https://cdn.socket.io/socket.io-1.3.7.js"></script>
	  <script src="lib/actions.js"></script>
	  <script>var socket = io();</script>
  </head>
  <body>
	<script>
		timeoutPeriod = 5000;
		setTimeout("refreshp();",timeoutPeriod);
		function refreshp(){
			window.location.href = window.location.href;
		}
	</script>
  <input type="hidden" name="page" value="teacher_flow_info" />
  <?php include('topnav.php'); ?>
    <div class="container">
		
		<a href="teacher.php">&lt;&lt;Back</a>
		<br />

		<?php if($npid == 0): ?>
		<?php echo '<br /><span class="label btn-success">No pyramid created.</span><br />'; ?>
		<?php else:?>
			<?php for($i=0;$i<=$npid;$i++):?>
			<?php
			$res2 = mysqli_query($link, "select fname, pg_level, pg_group, pg_group_id, pid from pyramid_groups, flow where pg_fid = '$fid' and pg_fid = fid  and pid = '{$i}' order by pg_level ASC");
			if(mysqli_num_rows($res2) > 0) {
				$py_created = true;
			} else {
				$py_created = false;
			}

			if(!$py_created) {
				echo '<br /><span class="label btn-success">The pyramid number <?=$i?> still is not created.</span><br />';
			} else {

				if (mysqli_num_rows(mysqli_query($link, "select * from pyramid_groups where pg_fid = '$fid'")) == mysqli_num_rows(mysqli_query($link, "select * from selected_answers where sa_fid = '$fid'"))) {

					$res3 = mysqli_query($link, "select * from flow where fid = '$fid'");
					$data3 = mysqli_fetch_assoc($res3);
					$activity_level = $data3['levels'];
					$activity_level = $activity_level - 1;
					$result_11 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_level = '$activity_level'");
					echo '<br /><span class="label btn-success">Selected answers are:</span><br />';
					while ($data_t_11 = mysqli_fetch_assoc($result_11)) {
						$qa_last_selected_id = $data_t_11['sa_selected_id'];
						$result_12 = mysqli_query($link, "select * from flow_student where fid = '$fid' and sid = '$qa_last_selected_id'");
						$data_t_12 = mysqli_fetch_assoc($result_12);
						echo '<br /><span class="">' . $data_t_12['fs_answer'] . '</span><br />';
					}
				} else {
					echo '<br /><span class="label btn-warning">Activity not finished.</span><br /><br />';
				}

				?>

				<?php
				$count = 0;
				$count2 = 0;
				$count3 = 0;
				$grp_cnt = 0;
				while ($data2 = mysqli_fetch_assoc($res2)) {
					$flow_name = $data2["fname"];
					$level = $data2["pg_level"];
					$group = $data2["pg_group"];
					$pg_group_id = $data2["pg_group_id"];
					if ($count == 0) {
						echo '<h3><b>' . $flow_name . ' Groups</b></h3> <table><th> Groups </th>';
					}
					if ($count2 == $level) {
						if ($count3 == 0) {
							echo '<tr> <td style="padding:10px;"> <b> <font color="#4e9a06" size="4">' . 'Level - ' . ($level + 1) . '</b> </font></td> </tr> ';
							echo ' <tr> <td style="padding:10px;"><font color="#cc0000"><b>' . $group . '</b></font>';
						} else {
							echo ' <tr> <td style="padding:10px;"><font color="#cc0000"><b>' . $group . '</b></font>';
						}
						$count3++;
						$grp_cnt++;
					} else //UP a level
					{
						$count2++;
						//$grp_cnt++;
						echo '<tr> <td style="padding:10px;"> <b> <font color="#de9a00" size="3"> No of Groups - ' . $grp_cnt . ' </b> </font></td> </tr>';
						echo '<tr> <td style="padding:10px;"> <b> <font color="#4e9a06" size="4">' . 'Level -' . ($level + 1) . '</b> </font></td> </tr> ';
						echo ' <tr> <td style="padding:10px;"><font color="#cc0000"><b>' . $group . '</b></font>';

						$grp_cnt = 0;
					}

					$res3 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_level='$level' and sa_group_id='$pg_group_id'");
					if (mysqli_num_rows($res3) > 0) { //the group has selected an answer

						$data3 = mysqli_fetch_assoc($res3);
						$selected_id = $data3['sa_selected_id'];

						$res4 = mysqli_query($link, "select * from flow_student where fid = '$fid' and sid = '$selected_id'");
						if (mysqli_num_rows($res4) > 0) {
							$data4 = mysqli_fetch_assoc($res4);
							$answer = $data4['fs_answer'];
							echo '  <span class="label btn-success">Selected group answer:</span>  '. $answer;
						}
					} else {
						//echo '<tr> <td><span class="label btn-success">The group did not select an answer.</span></td></tr>';
					}

					if((int)$count) {
						echo '</td> </tr>';
					}

					$count++;
				}
				if (mysqli_num_rows($res2) == $count) {
					echo '<tr> <td style="padding:10px;"> <b> <font color="#de9a00" size="3"> No of Groups - ' . ($grp_cnt + 1) . ' </b> </font></td> </tr>';
				}

				echo '</table>';
			}

				?>
			<?php endfor;?>
		<?php endif;?>
    </div>
	<br />
	
  </body>
</html>