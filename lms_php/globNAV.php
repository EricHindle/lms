<?php
$currentlevel = $_SESSION['retaccess'];
$adminlevelneeded = 999;
$devlevelneeded = 901;

echo '
		   <section>

			<nav role="navigation" class="navbar navbar-default navbar-fixed-top navbar-expand-lg">
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
								<li class="nav-item"><a class="nav-link" href="' . $myPath . 'menus/home.php">Home</a></li>
								<li class="nav-item"><a href="' . $myPath . 'struct/game/game-manage.php">Manage Games</a></li>';

if ($currentlevel == $adminlevelneeded || $currentlevel == $devlevelneeded) {
    echo '
								<li class="nav-item"><a href="' . $myPath . 'struct/main.php">Admin</a></li>';
}
if ($currentlevel == $devlevelneeded) {
    echo '
								<li class="nav-item"><a href="' . $myPath . 'menus/testmenu.php">Test</a></li>';
}
echo '                      </ul>

<ul class="nav navbar-nav navbar-right" >';
if ($currentlevel != $adminlevelneeded && $currentlevel != $devlevelneeded) {
    echo '                        <li class="nav-item">    <a class="nav-link" href="#">Match week: ' . $_SESSION['currentweek'] . '/' . $_SESSION['currentseason'] . '</a></li>';
}
echo '                            <li class="nav-item">    <form class="form-inline" role="form" name ="myaccount" method="post" action="' . $myPath . 'struct/player/myaccount.php">
                                <span class="glyphicon glyphicon-user"></span>' . $key . '<input id="submit" name="submit" type="submit" value="'. $_SESSION['nickname'] . '" class="navbar nav-button">
                            </form></li>
                        <li class="nav-item">    <a  class="nav-link" href="' . $myPath . 'logout.php"><span class="glyphicon glyphicon-log-in"></span> Sign out</a></li>
</ul>

						</div>
					</div>
				</nav>
</section>
	';
?>