<?php
require('includes/application_top.php');
require('includes/header.php');
require('suicide_pick/includes/config.php');
require('suicide_pick/includes/weekdetail.php');
session_start();
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
				$id= (int)$_GET['id'];
				echo $id;
				$sql_lock = "update topherballschedule set `lock` = 1 where gameID = $id";
				if(mysql_query($sql_lock))
				{
					$sql_lock = "update topherballpicks set `lock` = 1 where gameID = $id";
					if(mysql_query($sql_lock))
					{
					?>
					<script>
						alert("Match Locked!");
						window.location= "http://topherball.com/lockMatch.php";
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
				{	echo '<strong>Week Number: '.$week."</strong><br>";
				$sql_team="select * from topherballschedule where weekNum=$week and `lock` = 0";
				$query_team= mysql_query($sql_team);
				if(mysql_num_rows($query_team)>0)
				{	
					echo '<table cellpadding="4" cellspacing="0" class="table1"> <tr><th align="left">Match ID</th><th align="left">Home Team</th><th>Visitor Team</th><th colspan="2">Time</th></tr>';
					while($result_team = mysql_fetch_array($query_team))
					{	echo '<tr>';
						$homeID=$result_team['homeID'];
						$visitorID=$result_team['visitorID'];
						$gameID=$result_team['gameID'];
						$time=$result_team['gameTimeEastern'];
						$visitor_team = getTeamName($visitorID);
						$home_team = getTeamName($homeID);
						echo '<td>'.$gameID."</td><td>".$home_team."</td> <td>".$visitor_team."</td><td>".$time; ?></td><td> <button onclick="lock('<?php echo $gameID; ?>')">Lock the Match</button></td><?php echo "</tr>";
					}
						echo '</table>';
				}
				else
				{
					echo "<strong> All Matches for the week is locked!</strong>";
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
		window.location.href = "http://topherball.com//logout.php";
	</script>
<?php
	}
?>
<html>
<head>
<title>
Lock The Match
</title>

</html>
<script>
	function lock(id)
	{
		window.location.href = "lockMatch.php?id="+id;
	}
</script>