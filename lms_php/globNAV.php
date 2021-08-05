<?php
$currentlevel = $_SESSION['retaccess'];
$adminlevelneeded = 999;
$devlevelneeded = 901;
?>

<header>
	<link rel="stylesheet" href="<?php echo $myPath; ?>css/nav.css">
	<a href="<?php echo $myPath; ?>menus/home.php">
		<img src="<?php echo $myPath; ?>img/Lastman-Logo.png" alt="logo" class="logo-image" />
	</a>
	
	
	<div class="account" style="z-index: 99;" onClick="menuToggle();">
			<span class="account-icon" style="background-image: url(<?php echo $myPath; ?>img/icons/home-grey.svg)"></span>
		<div class="account-name"> 
		   	<?php echo $_SESSION['nickname'];?>
		</div>
		<div class="account-menu">
			<ul>
				<li>
					<form class="txt-btn" role="form" name ="myaccount" method="post" action="<?php echo $myPath; ?>struct/player/myaccount.php">
					<?php echo $key; ?>
					<input id="submit" type="submit" value="Manage Your Account">
					</form>
				</li>
				<?php	if ($currentlevel == $adminlevelneeded || $currentlevel == $devlevelneeded) {echo '<li><a class="admin-btn" href="' . $myPath . 'struct/main.php">Admin</a></li>';} 
				    	if ($currentlevel == $devlevelneeded) {echo '<li><a class="admin-btn" href="' . $myPath . 'menus\testmenu.php">Testing</a></li>';} ?>
				
				<li><a class="btn" href="<?php echo $myPath; ?>logout.php">Sign out</a></li>
				
			</ul>
		</div>
	</div>
</header>



<nav>
	<a class="nav-item <?php echo $currentPage == 'home' ? 'active' : ''; ?>" href="<?php echo $myPath; ?>menus/home.php"><span class="nav-icon home-icon" style="background-image: url(<?php echo $myPath; ?>img/icons/home-grey.svg)"></span>Home</a>
	<a class="nav-item <?php echo $currentPage == 'games' ? 'active' : ''; ?>" href="<?php echo $myPath; ?>menus/game-list.php"><span class="nav-icon games-icon" style="background-image: url(<?php echo $myPath; ?>img/icons/games-grey.svg)"></span>Games</a>
	<a class="nav-item <?php echo $currentPage == 'create' ? 'active' : ''; ?>" href="<?php echo $myPath; ?>struct/game/game-create.php"><span class="nav-icon create-icon" style="background-image: url(<?php echo $myPath; ?>img/icons/create-grey.svg)"></span>Create</a>
	<a class="nav-item <?php echo $currentPage == 'join' ? 'active' : ''; ?>" href="<?php echo $myPath; ?>menus/join.php"><span class="nav-icon join-icon" style="background-image: url(<?php echo $myPath; ?>img/icons/join-grey.svg)"></span>Join</a>
	<a class="nav-item <?php echo $currentPage == 'manage' ? 'active' : ''; ?>" href="<?php echo $myPath; ?>struct/game/game-manage.php"><span class="nav-icon manage-icon" style="background-image: url(<?php echo $myPath; ?>img/icons/manage-grey.svg)"></span>Manage</a>			
	
</nav>



<script>
	function menuToggle(){
		const toggleMenu = document.querySelector('.account');
		toggleMenu.classList.toggle('active')
	}
</script>

