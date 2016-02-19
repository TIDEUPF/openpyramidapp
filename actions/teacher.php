<?php
session_start(); 
include('dbvar.php');

//include('inc_pyramid_func.php');

if(isset($_SESSION['user'])) {
	$teacher_id = $_SESSION['user'];
	
	$student_count = mysqli_num_rows(mysqli_query($link, "select * from students"));
	
	if(isset($_POST['cflow'])) {
		$fname = mysqli_real_escape_string($link, stripslashes(trim(strip_tags($_POST['fname']))));
		$fdes =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['fdes']))));
		$fcname =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['fcname']))));
		$qs =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['qs']))));
		$tst = 60 * (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['tst']))));
		$rt = 60 * (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['rt']))));
		$htst = $tst + 120;
		$hrt = $rt + 120;
		$expe = mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['expe']))));
		$sync = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['sync']))));
		$multi_py = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['multi_py']))));
		$ch = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['ch']))));
		$n_selected_answers = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['n_selected_answers']))));
		$random_selection = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['random_selection']))));
		$fesname = '';//$fesname =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['fesname']))));
		$fl = (int) $_POST['fl'];
		$fsg = (int) $_POST['fsg'];

		//80 per cent of expected students
		if($multi_py) {
			$multi_pyramid_max_size = 20;
			$multi_pyramid_min_size = 4;
			$pyramid_size = max(min($multi_pyramid_max_size, floor($expe / 2)), $multi_pyramid_min_size);
			if($pyramid_size > 4)
				$min_pyramid = floor($pyramid_size * 0.8);
			else
				$min_pyramid = $pyramid_size;
		} else {
			$pyramid_size = $expe;

			//assume everyone participating for experiments <=8
			if($expe > 8)
				$min_pyramid = floor($expe * 0.8);
			else
				$min_pyramid = $expe;
		}

		//number of levels
		$max_levels = floor(log(floor($min_pyramid/$fsg), 2)) + 1;
		if($fl > $max_levels)
			$fl = $max_levels;

		$rps = 1;//TODO: legacy var

		if($fl < 1 || $rps < 1 || $fsg < 1)	{
			$error = 'Levels and Responses cannot be 0';
		} else {
			$datestamp = time();
			mysqli_query($link,"insert into flow values (null, '$teacher_id', '$fname', '$fdes', '$fcname', '$fesname', '$fsg', '$fl', '$pyramid_size', '$min_pyramid', '$expe', '$rps', '$datestamp', $tst, $rt, $htst, $hrt, '{$qs}', '{$ch}', '{$sync}', '{$multi_py}', '{$n_selected_answers}', '{$random_selection}')");
		}
	}
} else {
	header("location: login.php");
	exit(0);
}	

global $default_teacher_question;
$tq = $default_teacher_question;

?><!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="vendors/bootstrap/bootstrap.min.css">
	<script src="vendors/jquery/jquery-2.1.4.min.js"></script>
	  <script src="https://cdn.socket.io/socket.io-1.3.7.js"></script>
	  <script src="lib/actions.js"></script>
	<script type="text/javascript">
		var socket = io();
	$(document).ready(function(){
		$("#fsg").change(function() {
			var student_c = Math.floor(parseInt($("#expe").val(), 10) * 0.8);
			var lval = $("#fsg :selected").val();
			if(lval != 0)
			{
				var	newgroup = 0;
				var first_groups = Math.floor(student_c/lval);
				if(first_groups > 3)
				{					
					$("#fl").find('option').remove();
					$("#fl").append($("<option>").attr("value", "1").text("1"));
					
					var i = 2;	
					do{
						var x = Math.pow(2,i);
						var result = first_groups/x;
						var $fl = $("<option>").attr("value", i).text(i)
						if(i == 3)
							$fl.attr("selected", "selected");
						$("#fl").append($fl);

						i++;
					}while(result >= 2);

					var $fl = $("<option>").attr("value", i).text(i)
					if(i == 3)
						$fl.attr("selected", "selected");
					$("#fl").append($fl);
				}
				else
				{	
					$("#fl").find('option').remove();
					if(first_groups == 2 || first_groups == 3)
					{
						$("#fl").append($("<option>").attr("value", "1").text("1"));
						$("#fl").append($("<option>").attr("value", "2").text("2"));
					}
					else
					{
						
						$("#fl").append($("<option>").attr("value", "0").text("Select"));
						alert("Only 1 level can be created.");
					}	
				}	
				//alert(first_groups);
				
			}
			else
			{
				$("#fl").find('option').remove();
				$("#fl").append($("<option>").attr("value", "0").text("Select"));
			}			
		});
	});	
	</script>
  </head>
  <body>
  <script>

  </script>
  <input type="hidden" name="page" value="teacher_create_flow" />
  <?php include('topnav.php'); ?>
    <div class="container">
		<h3><b>Create Flow</b></h3>
		<?php if(!empty($error)) {echo '<br /><span class="label label-danger">'.$error.'</span><br /><br />';} ?>
		<?php if(!empty($success)) {echo '<br /><span class="label label-success">'.$success.'</span><br /><br />';} ?>
		<form role="form" action="" method="post">
        <div class="form-group">
          <label for="fname">Flow Name:</label>
          <input type="text" class="form-control" id="fname" name="fname">
        </div>
		<div class="form-group">
          <label for="fdes">Description:</label>
          <textarea class="form-control" id="fdes" name="fdes"></textarea>
        </div>
		<div class="form-group">
          <label for="fcname">Course Name:</label>
          <input type="text" class="form-control" id="fcname" name="fcname">
        </div>
		<!--
		<div class="form-group">
          <label for="fesname">Enrolled Students:</label>
          <input type="text" class="form-control" id="fesname" name="fesname">
        </div>
		-->
        <h4>Pyramid Details</h4>

			<div class="form-group">
				<label for="qs">Petition for your students:</label>
				<input type="text" class="form-control" id="qs" name="qs" value="<?php echo htmlspecialchars($default_teacher_question) ?>" />
			</div>

			<div class="form-group">
				<label for="fma">Time available in minutes:</label>
				<select class="form-control" id="fma" name="fma">
					<option value="5">5</option>
					<option value="10" selected="selected">10</option>
					<option value="15">15</option>
					<option value="20">20</option>
					<option value="25">25</option>
				</select>
			</div>

			<div class="form-group">
				<label for="expe">Number of expected students:</label>
				<input type="text" class="form-control" id="expe" name="expe" value="20" />
			</div>

			<div class="form-group">
				<label for="tst">Text submission timer in minutes:</label>
				<select class="form-control" id="tst" name="tst">
					<option value="2">2</option>
					<option value="3" selected="selected">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
					<option value="10">10</option>
				</select>
			</div>

			<div class="form-group">
				<label for="rt">Rating timer in minutes:</label>
				<select class="form-control" id="rt" name="rt">
					<option value="2" selected="selected">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
					<option value="10">10</option>
				</select>
			</div>


		<div class="form-group">
          <label for="fsg">No. Students. per Group:</label>
		  <select class="form-control" id="fsg" name="fsg">
			<option value="2" selected="selected">2</option>
			<option value="3">3</option>
          </select>
        </div>
		
		<div class="form-group">
          <label for="fl">No. Levels:</label>
		  <select class="form-control" id="fl" name="fl">
			  <option value="2">2</option>
			  <option value="3" selected="selected">3</option>
			  <option value="4">4</option>
          </select>
        </div>

		<div class="form-group">
          <label for="sync">Mode:</label>
		  <select class="form-control" id="sync" name="sync">
			  <option value="0">Asynchronous</option>
			  <option value="1" selected="selected">Syncronous</option>
          </select>
        </div>

		<!--
		<div class="form-group">
          <label for="rps">No. Responses per student:</label>
          <input type="text" class="form-control" id="rps" name="rps">
        </div>
		-->
		
		<div class="form-group">
			<label for="ch">Chat:</label>
			<select class="form-control" id="ch" name="ch">
				<option value="1" selected="selected">Enabled</option>
				<option value="0">Disabled</option>
			</select>
		</div>

		<div class="form-group">
			<label for="multi_py">Multiple pyramids:</label>
			<select class="form-control" id="multi_py" name="multi_py">
				<option value="1" selected="selected">Enabled</option>
				<option value="0">Disabled</option>
			</select>
		</div>

		<div class="form-group">
			<label for="n_selected_answers">Number of selected answers per group:</label>
			<select class="form-control" id="n_selected_answers" name="n_selected_answers">
				<option value="1" selected="selected">1</option>
				<option value="2">2</option>
			</select>
		</div>

		<div class="form-group">
			<label for="random_selection">Random answer select in case of no ratings submitted:</label>
			<select class="form-control" id="random_selection" name="random_selection">
				<option value="1" selected="selected">Enabled</option>
				<option value="0">Disabled</option>
			</select>
		</div>

		<div class="form-group">
          <input type="submit" class="btn btn-info" value="Create Flow" name="cflow">
        </div>
		
		</form>
		
		<br />
		<h3><b>Flows</b></h3>
		<?php
		$res2 = mysqli_query($link, "select * from flow where teacher_id = '$teacher_id'");
		if(mysqli_num_rows($res2) > 0){
		
			echo '<table class="table"><tr><th>Flow Name</th><th>View Groups</th></tr>';
		
			while($data2 = mysqli_fetch_assoc($res2)){
				$flow_id = $data2["fid"];
				$flow_name = $data2["fname"];
				//$flow_pyramid = $data2["pyramid"];
		?>		
		<tr><td><?php echo $flow_name; ?></td><td><?php echo '<a target="_blank" href="view_group.php?fid='.$flow_id.'">View</a>'; ?></td></tr>
		<?php
			}
			echo '</table>';
		}
		else{
			echo '<span>No flows found.</span>';
		}
		?>
		
    </div>
	<br />
	
  </body>
</html>