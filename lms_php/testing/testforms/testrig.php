<?php
$myPath = "../../";
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'scheduled/week-end-functions.php';

$_SESSION['currentweek'] = get_global_value('currweek');
$_SESSION['currentseason'] = get_global_value('currseason');
$_SESSION['selectweek'] = get_global_value('selectweek');
$_SESSION['matchweek'] = $_SESSION['currentseason'] . $_SESSION['currentweek'];
$_SESSION['selectweekkey'] = $_SESSION['currentseason'] . $_SESSION['selectweek'];
$_SESSION['selperiod'] = $_SESSION['selectweek'] . '/' . $_SESSION['currentseason'];
$_SESSION['deadline'] = get_current_deadline_date($_SESSION['selectweekkey']);


$logfile = fopen($myPath . "logs/test-log-" . $_SESSION['matchweek'] . ".log", "a");
fwrite($logfile, "Testing Weekend Processing --------------------------------------\n");

/*
 * Test function here
 */

fwrite($logfile, "Test Complete --------------------------------------\n");
echo "<script>
								alert('Test complete.');
								window.location.href='../../menus/testmenu.php';
							</script>";

?>