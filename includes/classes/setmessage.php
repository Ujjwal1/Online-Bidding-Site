<?php
session_start();
require('includes/applicatoin_top.php');
if(($_SESSION['logged']) && ($_SESSION['loggedInUser']=="admin"))
{
	if(isset($_GET['msg']))
	{	global $message;
		$message = "Ujjwal";
		$message= $_GET['msg'];
		if($message == "")
		{
			echo "Not set";
		}
		else
		{
		echo $message;
		}
	}
else{
?>
		<form action = "<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		Subject: <input type= "text" name="sub" value="by_admin_and_its_hidden" hidden><br>
		Set Message: <input type= "text" name="msg"><br>
		<input type="Submit" value="Submit">
		</form>
<?php	}

}
?>