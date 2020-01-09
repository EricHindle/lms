<?php
	$myPath='../../';

	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
    require $myPath.'includes/formkey.class.php';

	sec_session_start();
	$formKey = new formKey();
	$access = sanitize_int($_SESSION['retaccess']);
	if(login_check($mypdo) == true && $access == 999) {
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
	        if(!isset($_POST['form_key']) || !$formKey->validate())
	        {
	            header('Location: '.$myPath.'index.php?error=1');
	        }
	        else
	        {
	            if (isset($_POST['focustxt'], $_POST['foc']))
	            {
	                $text = htmlspecialchars($_POST['focustxt']);
	                $id = sanitize_int($_POST['foc']);
	                if($text && $id)
	                {
	                	$html="";
	                	$sql = "UPDATE focus SET title = :title, descry = :descry WHERE id = :id";
						$query = $mypdo->prepare($sql);
						$query->execute(array(':title' => $text, ':descry' => $text, ':id' => $id));
						$count = $query->rowCount();

						if($count >0){
							$html.= "<script>
										alert('Focus Updated.');
										window.location.href='focus-admin.php';
									</script>";
	                	} else {
								$html.= "<script>
										alert('There was a problem. Please check and try again.');
										window.location.href='focus-admin.php';
									</script>";
						}
						echo $html;	
                	} else {
	            		echo "<script>
								alert('There was a problem. Please check details and try again.');
								window.location.href='focus-admin.php';
							</script>";
	        		}
	            } else {
	                echo "<script>
								alert('There was a problem. Please check details and try again.');
								window.location.href='focus-admin.php';
							</script>";
	            }
        	}
	} else { 
	        header('Location: '.$myPath.'index.php?error=1');
	}
} else { 
	        header('Location: '.$myPath.'index.php?error=1');
}
?>