<?php
session_start();
require('includes/application_top.php');
require('includes/header.php');
if(($_SESSION['logged']) && ($_SESSION['loggedInUser']=="admin"))
{
	
?> <!--<br>If you want to <strong>reactivate a user</strong>, then Please <a href="http://topherball.com/reactivateUser.php">Click here!</a> <br><br> -->
	If you want to <strong>reactivate Spots</strong> of a user, then Please <a href="http://topherball.com/reactivate_spot.php">Click here! </a>
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
