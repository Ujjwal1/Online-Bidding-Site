<?php
	session_start();
	require('includes/config.php');
	include('includes/header.php');
	function getTeamName($id)
	{
		$sql_home_team="select team from topherballteams where teamID=\"$id\"";
			$query_home_team = mysql_query($sql_home_team);
			if(mysql_num_rows($query_home_team)>0)
			{
				$result_home_team = mysql_fetch_array($query_home_team);
				return $result_home_team['team'];
			}
			else
			{
				echo "Error in Database entry for Home team.";
			}
	}
	
if($dbConnected)	
{
	if($_SESSION['logged'] && ($_SESSION['loggedInUser']==="admin"))
	{ $allData="";
	echo "<strong>Upload Result:</strong><br>";
	?>
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
	Select the week number( between 1 to 17): <input type="text" name="week" placeholder="Enter the week number"><input type="submit" value="Submit">
	</form>
	<?php
	
	if((isset($_GET['homeScore']))&&(isset($_GET['visitScore'])))
	{
			$homeScore = (int)$_GET['homeScore'];
			$visitScore= (int)$_GET['visitScore'];
			$overtime= $_GET['overtime'];
			//unset($_GET['home']);
			//unset($_GET['visit']);
			$allData= $_GET['all'];
			$result="";
			
			if(($homeScore >= 0) && ($visitScore >= 0))
			{
				//Exploding and getting data.
				$data= explode("_",$allData);
				$i_gameID = $data[0];
				$i_week = $data[1];
				$i_time = $data[2];
				$i_home = $data[3];
				$i_visit = $data[4];
				if($homeScore < $visitScore)
				{
					$result = $i_visit;
				}
				else if($homeScore > $visitScore)
				{
					$result = $i_home;
				}
				else if($homeScore === $visitScore)
				{
					$result = "Draw";
				}
				$lock = 1;
				$sql_result = "UPDATE topherballschedule SET  homeScore=$homeScore , visitorScore=$visitScore , overtime=$overtime ,result=\"$result\",`lock` = $lock where gameID=$i_gameID";
				$query_result = mysql_query($sql_result) or die("Error while Updation.");
				if($query_result)
				{
					echo "Result Updated Successfully<br>";
					 $sql_active = "select spotID, team from topherballpicks where gameID = $i_gameID";
					 $query_active=mysql_query($sql_active);
					 if(mysql_num_rows($query_active)>0)
					 {
						while($result_active=mysql_fetch_array($query_active))
						{
						$spotID_active = $result_active['spotID'];
						$team_active = $result_active['team'];
						
						$sql_team = "select teamID from topherballteams where team =\"$team_active\"";
						$query_team=mysql_query($sql_team);
						if(mysql_num_rows($query_team)>0)
						{
							$result_team = mysql_fetch_array($query_team);
							$teamID_active = $result_team['teamID'];
						}
						else
						{
							echo "Error: ".mysql_error();
						}
						
						$sql_active_state = "select Activation from topherballspot where spotID = \"$spotID_active\"";
						$query_active_state=mysql_query($sql_active_state);
						if(mysql_num_rows($query_active_state)>0)
						{
							$result_active_state = mysql_fetch_array($query_active_state);
							$spotID_active_state = $result_active_state['Activation'];
							//$points = $result_active_state['points'];
							
							if(($teamID_active === $result)||($result ==="Draw"))		// Increasing the points of the successful users.
							{		
									/*$points++;
									$sql_point ="update topherballusers set points=$points where spotID = \"$spotID_active\"";
									if(mysql_query($sql_point))
									{
										echo "<br>Point increased for userID ".$userID_active." and it turns to ".$points;
									}
									else
									{
										echo "Failed to update the point.";
									}
									?> <script> forward("<?php echo $i_week; ?>");  </script> <?php */
							}
							
							else
									//Setting the activation state of the unsuccessful users.
							if(($teamID_active != $result)||($result !="Draw"))
							{
									if($spotID_active_state == 1)		//If deactivated for the first time
									{
										$spotID_active_state = 2;
										mysql_query("update topherballspot set Activation=$spotID_active_state where spotID= \"$spotID_active\"");
										echo $i_userID." <br>Some Ids have been Deactivated temporarily.<br>";
									}
									else
									if($spotID_active_state == 3)		//Deactivated second time.
									{
										$spotID_active_state = 4;
										mysql_query("update topherballspot set Activation=$spotID_active_state where spotID=\"$spotID_active\"");
										echo $i_userID."Some Ids have been Deactivated permanently<br>";
									}
									
									$sql_active_result = "update topherballpicks set spotState = $spotID_active_state where spotID= \"$spotID_active\" and gameID = $i_gameID";
									if(mysql_query($sql_active_result))
										{
											echo "&nbsp; Result also added to the picks history.<br>";
										}
										
										else
										{
											echo "Mysql error: ".mysql_error();
										}
							}
						}
					 
					   }
					 }
					  else
					 {
						?> <script> alert("Result Uploded Successfully!"); forward("<?php echo $i_week; ?>"); </script> <?php
					 }
				}
			}
			else
			{
				echo "Either or both the fields of Scores are empty. Please fill them and try again.";
			}
	
	}
	
	if((isset($_GET['home']))&&(isset($_GET['visit'])))
		{
			$home_ID=$_GET['home'];
			$visit_ID=$_GET['visit'];
			$all=$_GET['all'];
			unset($_GET['week']);
			
			$sql_home_team="select team from topherballteams where teamID=\"$home_ID\"";
			$query_home_team = mysql_query($sql_home_team);
			if(mysql_num_rows($query_home_team)>0)
			{
				$result_home_team = mysql_fetch_array($query_home_team);
				$home= $result_home_team['team'];
			}
			else
			{
				echo "Error in Database entry.";
			}
			
			$sql_visit_team="select team from topherballteams where teamID=\"$visit_ID\"";
			$query_visit_team = mysql_query($sql_visit_team);
			if(mysql_num_rows($query_visit_team)>0)
			{
				$result_visit_team = mysql_fetch_array($query_visit_team);
				$visit= $result_visit_team['team'];
			}
			else
			{
				echo "Error in Database entry.";
			}
			
			if((mysql_num_rows($query_home_team)>0)&&(mysql_num_rows($query_visit_team)>0))
			{
				?>
				<table cellpadding="4" cellspacing="0" class="table1">
				<form method="get" action="<?php echo $_SERVER['PHP_SELF'];?>">
				<tr><td>Score of <?php echo $home;?></td><td><input type="text" name="homeScore" value="0"></tr>
				<tr><td>Score of <?php echo $visit;?></td><td><input type="text" name="visitScore" value="0"></tr>
				<tr><td>OverTime(in minutes)</td><td><input type="text" name="overtime" value="0"></tr>
				<input type="text" name="all" value="<?php echo $all; ?>" hidden>
				</table>
				<input type="Submit" value="Set Score">
				</form>
				<?php
			}
			
		}
		
		
if(isset($_GET['week']))
	{
	$week=$_GET['week'];
	//$weeklike=$week."_%";
	if($week > 0 && $week <18)
	{
	echo "<strong>Week Number: ".$week."</strong><br>";
	$sql_match ="select * from topherballschedule where (result is NULL || result = \"NULL\")and weekNum = \"$week\"";
	$query_match=mysql_query($sql_match);
	if(mysql_num_rows($query_match)>0)
	{ echo '<table cellpadding="4" cellspacing="0" class="table1"> <tr><th>Match ID</th><th>Host Team</th><th colspan="2">Visitor Team</th></tr>';
	while($result_match=mysql_fetch_array($query_match))
	{	echo '<tr>';
		$gameID= $result_match['gameID'];
		$homeID= $result_match['homeID'];
		$visitorID= $result_match['visitorID'];
		$date_time = $result_match['gameTimeEastern'];
		$allData = $gameID."_".$week."_".$date_time."_".$homeID."_".$visitorID;
		$home = getTeamName($homeID);
		$visitor = getTeamName($visitorID);	
			
		echo '<td>'.$gameID."</td><td>".$home."</td><td>".$visitor."</td>";?> <td> <button onclick="selectMatch('<?php echo $homeID;?>','<?php echo $visitorID;?>','<?php echo $allData; ?>')"> Select Match </button> </td> 
<?php
		echo '</tr>';
	}
	echo '<table>';
	}
	else
	{
		echo "<br> Results of all the teams of this week is Uploaded!";
	}

	}
	else
	{
		echo "<br> Sorry! Week Number Not found. Make sure you enter the value in between 1 to 17";
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
}
?>
<script>
function selectMatch(home,visit,all)
{
	window.location.href= "uploadResult.php?home="+home+"&visit="+visit+"&all="+all;
}

function forward(week)
{
	window.location.href="http://topherball.com/uploadResult.php?week="+week;
}
</script>