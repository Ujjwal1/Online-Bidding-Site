<?php
require('includes/config.php');
require('includes/weekdetail.php');
session_start();
if(($_SESSION['logged']) && ($_SESSION['loggedInUser']=="admin"))
{
	if($isActivationWeek)
	{
		if($dbConnected)
		{	echo "User Name of the Users who have to be Reactivated are: <br>";
			
			if(isset($_GET['id']))
			{		$id= $_GET['id'];
					$sql_activate = "update topherball_users set Activation=3 where userID=$id";
					
					if(mysql_query($sql_activate))
					{
						?>
						<script>
							alert("User Re-Activated Successfully!");
							window.location="reactivateUser.php";
						</script>
						<?php
					}
					else
					{
						echo "Error while Updating the Activation Number.";
					}
			
			}
			
			$sql_chance = "select * from topherball_users where Activation=2";
			$query_chance= mysql_query($sql_chance);
			if(mysql_num_rows($query_chance)>0)
				{
					while($result_chance = mysql_fetch_array($query_chance))
					{
						$userID = $result_chance['userID'];
						$userName=$result_chance['userName'];
						echo $userName."&nbsp"; ?> <button onclick="reactivate('<?php echo $userID; ?>')"> Activate again! </button> <?php echo "<br>";
					}
				}
			else
			{
				echo "No user Found!";
			}
		}
		else
		{
			echo "Failed to connect to the database";
		}
		
	}
	else
	{
		echo $err_activation_week;
	
	}
	
}
	else
	{ ?>
			<script>
				window.location.href = "/phppickem/phppickem-master/logout.php";
			</script>
<?php
	}
?>

<html>
<head>
<title>
Re-Activate User!
</title>
<br><a href="/phppickem/phppickem-master/index.php?login=success">Home</a>
</html>

<script>
	function reactivate(id)
	{
		window.location.href= "reactivateUser.php?id="+id;
	}
</script>