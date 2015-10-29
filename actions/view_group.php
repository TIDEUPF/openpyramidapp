<?php
session_start(); 
include('dbvar.php');

if(isset($_SESSION['user'])){
	$teacher_id = $_SESSION['user'];
	$fid = (int) $_GET['fid'];
	$res2 = mysqli_query($link, "select fname, pg_level, pg_group from pyramid_groups, flow where pg_fid = '$fid' and pg_fid = fid order by pg_level ASC");
	if(mysqli_num_rows($res2) > 0){
		//echo mysqli_num_rows($res2);
		
	}
	else{
		header("location: teacher.php"); exit(0);
	}
}
else{
	header("location: login.php");
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
		
		<a href="teacher.php">&lt;&lt;Back</a>
		<br />
		
		<?php		
		
		if( mysqli_num_rows(mysqli_query($link, "select * from pyramid_groups where pg_fid = '$fid'")) == mysqli_num_rows(mysqli_query($link, "select * from selected_answers where sa_fid = '$fid'")) ){
			
			$res3 = mysqli_query($link, "select * from flow where fid = '$fid'");				
			$data3 = mysqli_fetch_assoc($res3);
			$activity_level = $data3['levels'];
			$activity_level = $activity_level-1;
			$result_11= mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_level = '$activity_level'");
			echo '<br /><span class="label btn-success">Selected answers are:</span><br />';
			while($data_t_11 = mysqli_fetch_assoc($result_11)){
				$qa_last_selected_id = $data_t_11['sa_selected_id'];
				$result_12= mysqli_query($link, "select * from flow_student where fid = '$fid' and sid = '$qa_last_selected_id'");
				$data_t_12 = mysqli_fetch_assoc($result_12);
				echo '<br /><span class="">'.$data_t_12['fs_answer'].'</span><br />';						
			}						
		}
		else{
			echo '<br /><span class="label btn-warning">Activity not finished.</span><br /><br />';
		}
		
		?>
		
		<?php
		$count = 0; $count2 = 0; $count3 = 0;
		$grp_cnt = 0;
		while($data2 = mysqli_fetch_assoc($res2)){
			$flow_name = $data2["fname"];
			$level = $data2["pg_level"];
			$group = $data2["pg_group"];
			if($count == 0){
				echo '<h3><b>'.$flow_name.' Groups</b></h3> <table><th> Groups </th>';
			}
			if($count2 == $level)
			{
				if($count3 == 0) {
					echo '<tr> <td style="padding:10px;"> <b> <font color="#4e9a06" size="4">'.'Level - '.($level+1). '</b> </font></td> </tr> ';
					echo ' <tr> <td style="padding:10px;"><font color="#cc0000"><b>'.$group.'</b></font></td> </tr>';
				}
				else {
					echo ' <tr> <td style="padding:10px;"><font color="#cc0000"><b>'.$group.'</b></font></td> </tr>';
				}
				$count3++;
				$grp_cnt++;
			}
			else
			{
				$count2++;
				//$grp_cnt++;
				echo '<tr> <td style="padding:10px;"> <b> <font color="#de9a00" size="3"> No of Groups - '. $grp_cnt .' </b> </font></td> </tr>';
				echo '<tr> <td style="padding:10px;"> <b> <font color="#4e9a06" size="4">'.'Level -'.($level+1).'</b> </font></td> </tr> ';
				echo ' <tr> <td style="padding:10px;"><font color="#cc0000"><b>'.$group.'</b></font></td> </tr>';
				$grp_cnt =0;
			}
			$count++;				
		}
		if(mysqli_num_rows($res2) == $count) 
		{
			echo '<tr> <td style="padding:10px;"> <b> <font color="#de9a00" size="3"> No of Groups - '. ($grp_cnt+1) .' </b> </font></td> </tr>';
		}
		
		echo '</table>';

			?>				
				
    </div>
	<br />
	
  </body>
</html>