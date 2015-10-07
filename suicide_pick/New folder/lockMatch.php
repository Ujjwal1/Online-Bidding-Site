<?php
session_start();
require('includes/config.php');
require('includes/weekdetail.php');

if(($_SESSION['logged']) && ($_SESSION['loggedInUser']=="admin"))
{
	if($isSeasonWeek)
	{
		if($dbConnected)
		{

?>		<form action="<?php $_SERVER["PHP_SELF"];?>" method="get">		
		Search team by week: <input type="text" name="week" placeholder="Enter the Week Number here..."> <input type="submit" value="Submit">
		</form>
<?php	
			if(isset($_GET['id']))
			{
				$id=$_GET['id'];
				echo $id;
				$sql_lock = "update topherballschedule set `lock` = 1 where gameID = $id";
				if(mysql_query($sql_lock))
				{
					$sql_lock = "update topherballpicks set `lock` = \"true\" where gameID = $id";
					if(mysql_query($sql_lock))
					{
					?>
					<script>
						alert("Match Locked!");
						window.location= "lockMatch.php";
					</script>
					<?php
					}
				}
				else
				{
					echo "Error while setting the lock";
				}
			}
			
			if(isset($_GET['week']))
			{
				$week= $_GET['week'];
				if(($week > 0)&&($week<18))
				{
				$sql_team="select * from topherballschedule where weekNum=$week and `lock`=0";
				$query_team= mysql_query($sql_team);
				if(mysql_num_rows($query_team)>0)
				{	
					while($result_team = mysql_fetch_array($query_team))
					{
						$homeID=$result_team['homeID'];
						$visitorID=$result_team['visitorID'];
						$gameID=$result_team['gameID'];
						$time=$result_team['gameTimeEastern'];
						
						$query_home= "select team from topherballteams where teamID=\"$homeID\"";
						$sql_home= mysql_query($query_home)or die("Unable to connect database for team table");
						if(mysql_num_rows($sql_home)>0)
							{
								$result_home=mysql_fetch_array($sql_home);
								$home_team= $result_home['team'];
							}
				
						$query_visitor= "select team from topherballteams where teamID=\"$visitorID\"";
						$sql_visitor= mysql_query($query_visitor)or die("Unable to connect database");
							if(mysql_num_rows($sql_visitor)>0)
								{
										$result_visitor=mysql_fetch_array($sql_visitor);
										$visitor_team= $result_visitor['team'];
								}
						
						echo $gameID."  ".$home_team."&nbsp  vs  &nbsp".$visitor_team."   ".$time;
						?>
						<button onclick="lock('<?php echo $gameID; ?>')">Lock the Match</button>
						<?php echo "<br>";
					}
					
				}
			
				}
				else
				{
					echo "Please enter the week number in between 1 to 17 only.";
				
				}
			}
			
		}
		else
		{
			echo "Error in connection with the Database";
		}

	}
	else
	{
		echo $err_week;
	}
}
	else
	{
?>	<script>
		window.location.href = "http://topherball.com/logout.php";
	</script>
<?php
	}
?>
<html>
<head>
<title>
Lock The Match
</title>

<br><a href="/index.php?login=success">Home</a>
</html>
<script>
	function lock(id)
	{
		window.location.href = "lockMatch.php?id="+id;
	}
</script>