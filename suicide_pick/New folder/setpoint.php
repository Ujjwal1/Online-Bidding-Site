<?php
require('includes/application_top.php');
require('includes/header.php');
require('suicide_pick/includes/config.php');
require('suicide_pick/includes/weekdetail.php');
session_start();
if(($_SESSION['logged']) && ($_SESSION['loggedInUser']=="admin"))
{
	if($dbConnected)
			{
				
?>
		<form action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
			<strong>Set Pool Points:</strong> <input type="text" name="point" placeholder="Enter whole number"> &nbsp; <input type="submit" value="Submit">
		</form>
<?php
		$sql_point = "select points from topherball_users where userID = 1";
		$query_point = mysql_query($sql_point);
		if($query_point)
			{
				if($result = mysql_fetch_array($query_point))
				{
					$curr_point = $result['points'];
				}
				else
				{
					echo "Not set";
				}
			}
			else
			{
				$curr_point = mysql_error();
			}
		echo "<strong>Current Pool point: ".$curr_point."</strong><br><br>";
		if(isset($_GET['point']))
		{	
			$point= $_GET['point'];
			if ( strpos( $point, '.' ) === false )
			{
				if(preg_match('/^[0-9]*$/', $point))
				{
					$sql_point = "update topherball_users set points = $point where userID = 1";
					$query_point = mysql_query($sql_point);
					if($query_point)
					{
						?> <script>
						alert("Point set!");
						window.location = "setpoint.php";
						</script> <?php
											}
					else
					{
						echo "Error: ".mysql_error();
					}
				}
			
				else
				{
					echo "Warning: Not a whole number.";
				}
			}
			else
			{
				echo "Warning: No Floating number or String Alowed!";
			}
		}
			}
		else
			{
				echo "Database not connected";
			}
	
}
else
{
?>	
			<script>
				window.location.href = "http://topherball.com//logout.php";
			</script>
<?php
}

?>

<html>
<head><title>Set Pool Points</title></head>
</html>