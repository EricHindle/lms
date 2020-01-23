<?php
$currentlevel = $_SESSION['retaccess'];
$levelneeded = 999;

echo '
		   <section>
				<nav role="navigation" class="navbar navbar-default navbar-fixed-top">
					<div class="container-fluid">
						<!-- Brand and toggle get grouped for better mobile display -->
						<div class="navbar-header">
							<button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a class="navbar-brand"><img style=" margin-top: -7px;" src="' . $myPath . '/img/logo_resp.png"></a>
						</div>
						<!-- Collection of nav links and other content for toggling -->
						<div id="navbarCollapse" class="collapse navbar-collapse">
							<ul class="nav navbar-nav">
								<li><a href="' . $myPath . 'menus/home.php">Home</a></li>
								<li><a href="' . $myPath . 'struct/picks/pick-main.php">Selections</a></li>
								<li><a href="' . $myPath . 'struct/game/game-main.php">Games</a></li>
								

					';

if ($currentlevel == $levelneeded) {
	echo '
								<li><a href="' . $myPath . 'struct/main.php">Admin</a></li>

					';
}

echo '
							</ul>
							<ul class="nav navbar-nav navbar-right">
                                <li><a href="#">Period: ' . $_SESSION['currentweek'] . '/' . $_SESSION['currentseason'] . '</a></li>
								<li><a href="#"><span class="glyphicon glyphicon-user"></span> ' . $_SESSION['nickname'] . '</a></li>
								<li><a href="' . $myPath . 'logout.php"><span class="glyphicon glyphicon-log-in"></span> Sign out</a></li>
							</ul>
						</div>
					</div>
				</nav>
			</section>
	';
?>