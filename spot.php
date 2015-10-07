<?php
session_start();
require('includes/application_top.php');
require('includes/header.php');

if(isset($_SESSION['loggedInUser']))
{
	$user= $_SESSION['loggedInUser'];
	$userID = $_SESSION['loginID'];
	echo "<p> <a href='choose_spot.php'> Change the Current Spot</a><br><br>
	<a href='http://topherball.com/create_spot.php'> Create</a> new Spot <br><br>
	<a href='http://topherball.com/manage_spot.php'>Delete</a> unverified Spots</p>";

}
else
{
?>
<script>
	window.location = 'http://topherball.com/login.php';
</script>
<?php
}

?>