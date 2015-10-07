<?php
session_start();
require('includes/config.php');
require('includes/weekdetail.php');

if(($_SESSION['logged']) && ($_SESSION['loggedInUser']=="admin"))
{
	if($isActivationWeek)
	{
		if($dbConnected)
		{	echo "User Name of the Users who have to be Reactivated are: <br>";
			
			if(isset($_GET['id']))
			{		$id= $_GET['id'];
					$sql_activate = "update topherballusers set Activation=2 where userID=$id";
					
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
			
			$sql_chance = "select * from topherballusers where Activation=1";
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
				window.location.href = "/logout.php";
			</script>
<?php
	}
?>

<html>
<head>
<title>
Re-Activate User!
</title>
<br><a href="/index.php?login=success">Home</a>
</html>

<script>
	function reactivate(id)
	{
		window.location.href= "reactivateUser.php?id="+id;
	}
</script>