<nav class="navbar navbar-default navbar-static-top">
		
	<div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Pyramid Flow Pattern</a>
	</div>
	<div class="container">        
	<div id="navbar">
	  <ul class="nav navbar-nav">
		<li><a href="index.php">Home</a></li>
		<?php
		if(!isset($_SESSION['user'])){		
			echo '<li><a href="login.php">Login</a></li>';
		}
		else{
			echo '<li><a href="teacher.php">Flows</a></li>';
			echo '<li><a href="logout.php">logout</a></li>';
		}
		?>
	  </ul>
	</div><!--/.nav-collapse -->
	</div>
</nav>