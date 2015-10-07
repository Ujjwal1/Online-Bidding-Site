<?php
session_start();
require('includes/application_top.php');
require('includes/header.php');
if(($_SESSION['logged']) && ($_SESSION['loggedInUser']=="admin"))
{
	
?> <!-- <br>If you want to <strong>verify a user</strong>, then Please <a href="http://topherball.com/verify_user.php">Click here!</a> <br><br> -->
	If you want to <strong>verify Spots</strong> of a user, then Please <a href="http://topherball.com/verify_spot.php">Click here! </a>
<?php
}
else
{
?>
<script>
	window.location = "http://topherball.com/login.php";
</script>
<?php
}
