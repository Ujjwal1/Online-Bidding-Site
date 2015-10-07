<?php
session_start();
require('suicide_pick/includes/config.php');
if(isset($_SESSION['loggedInUser']))		
{	
	if($dbConnected)
	{
		$user=$_SESSION['loggedInUser'];
		$userID=$_SESSION['loginID'];
		$spotID = $_SESSION['spotID'];
		include('includes/header.php');
		
		$sql_activation = "select Activation from topherballspot where spotID = \"$spotID\"";
		$query_activation = mysql_query($sql_activation);
		$result_activation = mysql_fetch_array($query_activation);
		$activation = $result_activation['Activation'];
		
	if(($activation == 1)||($activation == 3))
	{	
		function team_name($teamID)
				{
				
							$sql_team="select team from topherballteams where teamID=\"$teamID\"";
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
				
//display week nav
$sql = "select distinct weekNum from " . $db_prefix . "schedule order by weekNum;";
$query = mysql_query($sql);
$weekNav = '<div class="navbar3"><b>Go to week:</b> ';
$i = 0;
while ($result = mysql_fetch_array($query)) {
	if ($i > 0) $weekNav .= ' | ';
	if ($week !== (int)$result['weekNum']) {
		$weekNav .= '<a href="entry_form.php?week=' . $result['weekNum'] . '">' . $result['weekNum'] . '</a>';
	} else {
		$weekNav .= $result['weekNum'];
	}
	$i++;
}
$weekNav .= '</div>' . "\n";
echo $weekNav;
		
		
		$userID = $_SESSION['loginID'];
		$counter = 0;
			$sql_all = "select spotID from topherballspot where Activation <> 0 and userID = \"$userID\" order by spotID";
			$query_all = mysql_query($sql_all) or die(mysql_error());
			if(mysql_num_rows($query_all) > 0)
			{
				while($result_all = mysql_fetch_array($query_all))
				{
					$allspot[$counter++] = $result_all['spotID'];
				}
			}
			//$team[] = null;
			//$spotState[] = null;
		
		echo "<hr>";
		echo '<table cellpadding="4" cellspacing="0" class="table1"><thead>Meaning of various colours:</thead><tr><td bgcolor="#C0C0C0">Not Verified</td><td bgcolor="#00FF00">Active</td><td bgcolor="#FF6600">De-Active</td><td bgcolor="#00FFFF">Re-Active</td><td bgcolor="#FF0000">Knocked Out</td></tr></table>';
		echo "<br><hr><br>";
			
		echo '<table cellpadding="4" cellspacing="0" class="table1"> <tr><th>User</th><th>Week 1</th><th>Week 2</th><th>Week 3</th><th>Week 4</th><th>Week 5</th><th>Week 6</th><th>Week 7</th><th>Week 8</th><th>Week 9</th><th>Week 10</th><th>Week 11</th><th>Week 12</th><th>Week 13</th><th>Week 14</th><th>Week 15</th><th>Week 16</th><th>Week 17</th></tr>';
		for($i =0; $i< count($allspot); $i++)
		{	$spot = $allspot[$i];
			$sql_active = "select Activation from topherballspot where spotID =\"$spot\"";
			$query_active = mysql_query($sql_active) or die(mysql_error());
			if($query_active)
			{	$result_active = mysql_fetch_array($query_active);
				$actives = $result_active['Activation'];
				echo "<tr>";
				switch($actives)
							{
								case "0": ?><td bgcolor="#C0C0C0"> <?php echo "<strong>$spot </strong></td>";
								break;
								case "1":	?><td bgcolor="#00FF00"> <?php echo "<strong>$spot </strong></td>";
								break;
								case "2":	?><td bgcolor="#FF6600"> <?php echo "<strong>$spot </strong></td>";
								break;
								case "3":	?><td bgcolor="#00FFFF"> <?php echo "<strong>$spot </strong></td>";
								break;
								case "4":	
								default:	?><td bgcolor="#FF0000"> <?php echo "<strong>$spot </strong></td>";
								break;		
							}				
			}
			$count = 0;
			for($j=1; $j<18;$j++)
			{
			$picklike = $j."\_%";
			$sql_state = "select * from topherballpicks where spotID = \"$spot\" and pickID like \"$picklike\"";
			$query_state= mysql_query($sql_state);
			if(mysql_num_rows($query_state) > 0)
			{	
				if($result_state = mysql_fetch_array($query_state))
					{
						$week[$j] = 1;
						$team[$count] = $result_state['team'];
						$spotState[$count] = $result_state['spotState'];
						$count++;
					}
			}
			else 
			{
				$week[$j]= 0;
			}
			}
			$counter = 0;
			
			for($z = 1 ; $z< 18 ;$z++)
			{
				if($week[$z]== 1)
				{	
					switch($spotState[$counter])
							{
								case "0": ?><td bgcolor="#C0C0C0"> <?php echo "$team[$counter] </td>";
								break;
								case "1":	?><td bgcolor="#00FF00"> <?php echo "$team[$counter] </td>";
								break;
								case "2":	?><td bgcolor="#FF6600"> <?php echo "$team[$counter] </td>";
								break;
								case "3":	?><td bgcolor="#00FFFF"> <?php echo "$team[$counter] </td>";
								break;
								case "4":	
								default:	?><td bgcolor="#FF0000"> <?php echo "$team[$counter] </td>";
								break;		
							}
					$counter++;
				}
				else
				{
					echo "<td></td>";
				}
			
			}
			echo "</tr>";
		}
		echo "<table>";
		echo "<br><hr><br>";
		
		if(isset($_GET['delete']))			//True if user is asking to delete a temporarily pick.
		{
			$del = explode("_",$_GET['delete']);
			$spotID = $del[0];
			$gameID = $del[1];
			
			$sql_del = "delete from topherballpicks where spotID= \"$spotID\" and gameID=$gameID";

			if(mysql_query($sql_del))
			{
?>			
				<script>
					alert("Deleted Successfully!");
					window.location = "pickteam.php";
				</script>
<?php		}
			else
			{
				echo "<br> Error in deletion.".mysql_error();
			}
		}
		
		if(isset($_GET['week']))
			{	$week= $_GET['week'];
				echo "<strong>Matches for the Week: ".$week."</strong>";
				echo '<table cellpadding="4" cellspacing="0" class="table1"> <tr><th colspan= "2">Host Team</th><th colspan= "2">Visitor Team</th></tr>';
				if(($week > 0)&&($week<18))
				{
				$sql_team="select * from topherballschedule where weekNum=$week";
				$query_team=mysql_query($sql_team);
				
				if($query_team)
				{	
					while($result_team = mysql_fetch_array($query_team))
					{	echo '<tr>';
						$homeID=$result_team['homeID'];
						$visitorID=$result_team['visitorID'];
						$gameID=$result_team['gameID'];
						$time=$result_team['gameTimeEastern'];
						
						$home_team= team_name($homeID);
						
						$visitor_team= team_name($visitorID);
							
						echo '<td class="lighter">'.$home_team."</td>";?> <td class="lighter"> <button onclick="confirmTeam('<?php echo $home_team." game ID: ".$gameID." for the week ".$week;?>')"> Select </button> </td> <td class="lighter"><?php echo $visitor_team;?> </td> <td class="lighter"><button onclick="confirmTeam('<?php echo $visitor_team." game ID: ".$gameID." for the week ".$week;?>')"> Select </button> </td>
<?php 					echo "</tr>";				
					}				
					
				}			
				echo '</table>';	
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
			$query_check="select pickID,team from topherballpicks where spotID=\"$spotID\"";
			$sql_check=mysql_query($query_check) or die("Unable to execute the query");
			while($result_check=mysql_fetch_array($sql_check))
			{
				$existing_team[$counter]=$result_check['team'];
				$spot_past_pickID=$result_check['pickID'];
				$get_week=explode("_",$spot_past_pickID);
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
			$pickID=$week."_".$game."_".$spotID;
			//$totalTeams = $I_selected.",\'".$choosed."\'";
			$teamQuery="INSERT INTO topherballpicks VALUES ($userID,$game,\"$pickID\",\"false\",\"$choosed\",$\"$spotID\",$activation)";
			$res=mysql_query($teamQuery);
			if($res)
			{ 
				?>
				<script>
					alert("Your pick has been added successfully. To change it click on the 'Pick Team' link");
					window.location="http://topherball.com/pickteam.php";
				</script>
				<?php
			}
			else 
			{
				?>
				<script>
					alert("Error in SQL statement.");
					window.location="http://topherball.com/pickteam.php";
				</script>
				<?php
			}
			
		}
			else
			if($flag_week)
			{
				?>
				<script>
					alert("You have already made a pick for this week. To change it click on the Pick Team link.");
					window.location="http://topherball.com/pickteam.php";
				</script>
				<?php
			}
			else
			if($flag_team)
			{
				?>
				<script>
					alert("You had already choosen this team. One User cannot choose a team more than once in a season.");
					window.location="http://topherball.com/pickteam.php";
				</script>
				<?php
			}
			
			
		} 
		
	
		
		echo "<br><strong>Teams Already selected by you are: </strong><br>";
		
		$userQuery="select gameID,team,`lock`,pickID from topherballpicks where spotID = \"$spotID\" order by gameID"; 
		$userResult=mysql_query($userQuery);
		if(mysql_num_rows($userResult)>0)
		{
		echo '<table cellpadding="4" cellspacing="0" class="table1"> <tr><th>Week</th><th colspan="2">Team</th></tr>';
		while($userRow=mysql_fetch_array($userResult))
		{	$selectedTeam=$userRow['team'];
			$lockTeam = $userRow['lock'];
			$gameID = $userRow['gameID'];
			$pickID=$userRow['pickID'];
			$get_week=explode("_",$pickID);
			$is_week = $get_week[0];
			echo '<tr>';
			echo "<td>".$is_week."</td> <td>".$selectedTeam."</td>";
			if($lockTeam == 0)
			{
			?><td><button onclick = "deletePick('<?php echo $spotID."_".$gameID;?>')"> Delete the Pick </button></td> <?php 
			}
			else { echo '<td>LOCKED</td>';}
			echo '</tr>';
		}
		echo '</table>';
		}
		else
		{
			echo "No team selected yet.";
		}
		mysql_free_result($userResult); 
		
	}
		else
		if($activation == 0)
		{
?>
	<script>
			alert("Sorry! registration is not verified yet.\n Contact administrator");
			window.location = "http://topherball.com/index.php?login=success";
	</script>
<?php	
		
		
		}
		
		else
		if($activation == 2)
		{
?>
	<script>
			alert("You can not pick any team as you are \"Deactivated\".\n Consult the Admin!");
			window.location = "http://topherball.com/index.php?login=success";
	</script>
<?php	
		
		
		}
		
		else
		{
		
?>
	<script>
			alert("You are DEACTIVATED PERMANENTLY! \n No picks for this season.");
			window.location = "http://topherball.com/index.php?login=success";
	</script>
<?php	
		}
	}	
}
	
else
{
	?>
		<script>
			window.location="http://topherball.com/login.php";
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
		window.location.href("http://topherball.com/logout.php");
	}
	
	
</script>
<html>
<head>
<title>
Select a Team
</title>
</head>
</html>
