<html>
<head>
<title>
Select a Team
</title>
</head>
<body>
<a href="home.php">Back</a> 		 <a href="/phppickem/phppickem-master/login.php">Logout</a><br>
</body>
</html>


<?php
session_start();
require('includes/config.php');
if(isset($_SESSION['loggedInUser']))		
{	
	if($dbConnected)
	{
		$user=$_SESSION['loggedInUser'];
		$userID=$_SESSION['loginID'];
		
		$sql_activation = "select Activation from topherball_users where userID = $userID";
		$query_activation = mysql_query($sql_activation);
		$result_activation = mysql_fetch_array($query_activation);
		$activation = $result_activation['Activation'];
		
	if(($activation == 1)||($activation == 3))
	{	
		function team_name($teamID)
				{
				
							$sql_team="select team from topherball_teams where teamID=\"$teamID\"";
							$query_team = mysql_query($sql_team);
							if(mysql_num_rows($query_team)>0)
								{
									$result_team = mysql_fetch_array($query_team);
									$team= $result_team['team'];
									return $team;
								}
							else
								{
									echo "Error in Database entry for Home team.";
								}
				
				}
?>		
			<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="get">
			Select a week in between 1 to 17 not locked by you : <input type="text" name="week" placeholder="Enter week here..."> <input type="submit" value="Submit"> <br>
			</form>
			
<?php		
		/*		This commented out code works for giving the user, access to change the visibility of his pick to "public"
			if(isset($_GET['lock']))			// True if user ask for locking the team
		{	$lock = explode("_",$_GET['lock']);
			$user= $lock[0];
			$game = $lock[1];
			$sql_lock = "update topherball_picks set `lock`=\"true\" where userID=\"$user\" and gameID=\"$game\"";
			if(mysql_query($sql_lock))
			{
?>
			<script>
				window.location = "pickteam.php";			
			</script>
<?php		}
			else
			{
				echo "Error while locking.";
			}
		}
		
		*/
		if(isset($_GET['delete']))			//True if user is asking to delete a temporarily pick.
		{
			$del = explode("_",$_GET['delete']);
			$userID = $del[0];
			$gameID = $del[1];
			
			$sql_del = "delete from topherball_picks where userID= $userID and gameID=$gameID";

			if(mysql_query($sql_del))
			{
?>			
				<script>
					alert("Deleted Successfully!");
				</script>
<?php		}
			else
			{
				echo "<br> Error in deletion. <br>";
			}
		}
		
		if(isset($_GET['week']))
			{
				$week= $_GET['week'];
				if(($week > 0)&&($week<18))
				{
					echo "<strong>Week: ".$week."</strong><br>";
				$sql_team="select * from topherball_schedule where weekNum=$week";
				$query_team=mysql_query($sql_team);
				if($query_team)
				{	
					while($result_team = mysql_fetch_array($query_team))
					{
						$homeID=$result_team['homeID'];
						$visitorID=$result_team['visitorID'];
						$gameID=$result_team['gameID'];
						$time=$result_team['gameTimeEastern'];
						
						$home_team= team_name($homeID);
						
						$visitor_team= team_name($visitorID);
							
						echo $home_team;?> <button onclick="confirmTeam('<?php echo $home_team." game ID: ".$gameID." for the week ".$week;?>')"> Select </button> &nbsp; <?php echo " 	vs		&nbsp;".$visitor_team;?> <button onclick="confirmTeam('<?php echo $visitor_team." game ID: ".$gameID." for the week ".$week;?>')"> Select </button><?php echo "<br>";
					}
				}
				
				
				}
				else
				{
					echo "Please enter the week number in between 1 to 17 only.";
				
				}
			}
			
		
		if(isset($_GET['teamNweek'])) 	// True, if a team is selected by a user.
		{
			$teamNweek=$_GET['teamNweek'];
			$get = explode(" ",$teamNweek);
			$choosed=$get[0];
			$week=$get[7];
			$game=$get[3];
			
			$existing_team=null;
			$counter=0;
			$flag_team=false;
			$flag_week=false;
			$query_check="select pickID,team from topherball_picks where userID=\"$userID\"";
			$sql_check=mysql_query($query_check) or die("Unable to execute the query");
			while($result_check=mysql_fetch_array($sql_check))
			{
				$existing_team[$counter]=$result_check['team'];
				$users_past_pickID=$result_check['pickID'];
				$get_week=explode("_",$users_past_pickID);
				$existing_week[$counter]=$get_week[0];
				$counter++;
			}
			
			for($i=0;$i<$counter;$i++)
			{
				if($existing_team[$i]===$choosed)
				{
					$flag_team=true;
				}
				if($existing_week[$i]===$week)
				{
					$flag_week=true;
				}
			}
		if((!$flag_team) && (!$flag_week))
		{
			$pickID=$week."_".$game."_".$userID;
			//$totalTeams = $I_selected.",\'".$choosed."\'";
			$teamQuery="INSERT INTO topherball_picks VALUES ($userID,$game,\"$pickID\",\"false\",\"$choosed\")";
			$res=mysql_query($teamQuery);
			if($res)
			{ 
				?>
				<script>
					alert("Added Successfully! Now you are locked to choose for this week/ this team");
					window.location="/phppickem/phppickem-master/suicide_pick/pickteam.php";
				</script>
				<?php
			}
			else 
			{
				?>
				<script>
					alert("You had already choosen its opponent!");
					window.location="/phppickem/phppickem-master/suicide_pick/pickteam.php";
				</script>
				<?php
			}
			
		}
			else
			if($flag_week)
			{
				?>
				<script>
					alert("You had already choosen a team from this week. No more selection from this week.");
					window.location="/phppickem/phppickem-master/suicide_pick/pickteam.php";
				</script>
				<?php
			}
			else
			if($flag_team)
			{
				?>
				<script>
					alert("You had already choosen this team. One User cannot choose a team more than once in a season.");
					window.location="/phppickem/phppickem-master/suicide_pick/pickteam.php";
				</script>
				<?php
			}
			
			
		} 
		
	
		
		echo "<br><strong>Teams Already selected by you are: </strong><br>";
		
		$userQuery="select gameID,team,`lock`,pickID from topherball_picks where userID = $userID order by team"; 
		$userResult=mysql_query($userQuery) or die("Unable to Query from $userTable");
				
		while($userRow=mysql_fetch_array($userResult))
		{	$selectedTeam=$userRow['team'];
			$lockTeam = $userRow['lock'];
			$gameID = $userRow['gameID'];
			$pickID=$userRow['pickID'];
			$get_week=explode("_",$pickID);
			$is_week = $get_week[0];

			
			echo "(Week: ".$is_week.") &nbsp;".$selectedTeam;
			if($lockTeam==="false")
			{
			?> <!-- <button onclick="confirm('<?php // echo $userID."_".$gameID;?>')"> Lock the team </button> --> <button onclick = "deletePick('<?php echo $userID."_".$gameID;?>')"> Delete the Pick </button> <?php 
			} echo "<br>";
		}
		
		echo "<br>";
		
		
		mysql_free_result($userResult); 
		
	}
		else
		if($activation == 0)
		{
?>
	<script>
			alert("Sorry! registration is not verified yet.\n Contact administrator");
			window.location = "/phppickem/phppickem-master/suicide_pick/home.php";
	</script>
<?php	
		
		
		}
		
		else
		if($activation == 2)
		{
?>
	<script>
			alert("You can not pick any team as you are \"Deactivated\".\n Consult the Admin!");
			window.location = "/phppickem/phppickem-master/suicide_pick/home.php";
	</script>
<?php	
		
		
		}
		
		else
		{
		
?>
	<script>
			alert("You are DEACTIVATED PERMANENTLY! \n No picks for this season.");
			window.location = "/phppickem/phppickem-master/suicide_pick/home.php";
	</script>
<?php	
		}
	}	
}
	
else
{
	?>
		<script>
			window.location="/phppickem/phppickem-master/login.php";
		</script>
	<?php
}

?>

<script type="text/javascript">
	
/*	function confirm(lockIt)
	{
		window.location.href = "pickteam.php?lock="+lockIt;
	}
*/	
	function deletePick(deleteIt)
	{
		window.location.href = "pickteam.php?delete="+deleteIt;
	}
	
	function confirmTeam(teamNweek)
{	
	if (confirm("Are you sure you want to select "+teamNweek))
	{			
				window.location.href="pickteam.php?teamNweek="+teamNweek;
	}
}
	function back()
	{
		window.location.href("home.php");
	}
	function logout()
	{
		window.location.href("/phppickem/phppickem-master/logout.php");
	}
	
	
</script>