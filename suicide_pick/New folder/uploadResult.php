<?php
	session_start();
	require('includes/config.php');

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
			$homeScore = $_GET['homeScore'];
			$visitScore= $_GET['visitScore'];
			$overtime= $_GET['overtime'];
			//unset($_GET['home']);
			//unset($_GET['visit']);
			$allData= $_GET['all'];
			$result="";
			
			if(!empty($homeScore) && !empty($visitScore))
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
					 $sql_active = "select userID, team from topherballpicks where gameID = $i_gameID";
					 $query_active=mysql_query($sql_active);
					 if(mysql_num_rows($query_active)>0)
					 {
						while($result_active=mysql_fetch_array($query_active))
						{
						$userID_active = $result_active['userID'];
						$team_active = $result_active['team'];
						
						$sql_team = "select teamID from topherballteams where team =\"$team_active\"";
						$query_team=mysql_query($sql_team);
						if(mysql_num_rows($query_team)>0)
						{
							$result_team = mysql_fetch_array($query_team);
							$teamID_active = $result_team['teamID'];
						}
						
						
						$sql_active_state = "select Activation,points from topherballusers where userID = $userID_active";
						$query_active_state=mysql_query($sql_active_state);
						if(mysql_num_rows($query_active_state)>0)
						{
							$result_active_state = mysql_fetch_array($query_active_state);
							$userID_active_state = $result_active_state['Activation'];
							$points = $result_active_state['points'];
							
							if(($teamID_active === $result)||($result ==="Draw"))		// Increasing the points of the successful users.
							{		$points++;
									$sql_point ="update topherballusers set points=$points where userID = \"$userID_active\"";
									
									if(mysql_query($sql_point))
									{
										echo "Point increased for userID ".$userID_active." and it turns to ".$points."<br>";
									}
									else
									{
										echo "Failed to update the point.";
									}
							}
							
							else
									//Setting the activation state of the unsuccessful users.
							if(($teamID_active != $result)||($result !="Draw"))
							{
									if($userID_active_state == 0)		//If deactivated for the first time
									{
										$userID_active_state = 1;
										mysql_query("update topherballusers set Activation=$userID_active_state where userID=$userID_active");
										echo $i_userID." Id is Deactivated temporarily.<br>";
									}
									else
									if($userID_active_state == 2)		//Deactivated second time.
									{
										$userID_active_state = 3;
										mysql_query("update topherballusers set Activation=$userID_active_state where userID=$userID_active");
										echo $i_userID." Id is Deactivated permanently<br>";
									}
									
							}
						}
					 
					 else
					 {
						echo "<br>Unsuccessful in changing effect for users. Maybe none of them had selected the winning team. <br>";
					 }
					 }
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
				<form method="get" action="<?php echo $_SERVER['PHP_SELF'];?>">
				Score of <?php echo $home;?><input type="text" name="homeScore"><br>
				Score of <?php echo $visit;?><input type="text" name="visitScore"><br>
				OverTime(in minutes):<input type="text" name="overtime" value="0"><input type="text" name="all" value="<?php echo $all; ?>" hidden><br>
				<input type="Submit" value="Set Score">
				</form>
				<?php
			}
			
		}
		
		
if(isset($_GET['week']))
	{
	$week=$_GET['week'];
	//$weeklike=$week."_%";
	$sql_match ="select * from topherballschedule where result is NULL and weekNum = \"$week\"";
	$query_match=mysql_query($sql_match);
	if(mysql_num_rows($query_match)>0)
	{
	while($result_match=mysql_fetch_array($query_match))
	{
		$gameID= $result_match['gameID'];
		$homeID= $result_match['homeID'];
		$visitorID= $result_match['visitorID'];
		$date_time = $result_match['gameTimeEastern'];
		$allData = $gameID."_".$week."_".$date_time."_".$homeID."_".$visitorID;
		$sql_home_team="select team from topherballteams where teamID=\"$homeID\"";
			$query_home_team = mysql_query($sql_home_team);
			if(mysql_num_rows($query_home_team)>0)
			{
				$result_home_team = mysql_fetch_array($query_home_team);
				$home= $result_home_team['team'];
			}
			else
			{
				echo "Error in Database entry for Home team.";
			}
			
			$sql_visit_team="select team from topherballteams where teamID=\"$visitorID\"";
			$query_visit_team = mysql_query($sql_visit_team);
			if(mysql_num_rows($query_visit_team)>0)
			{
				$result_visit_team = mysql_fetch_array($query_visit_team);
				$visitor= $result_visit_team['team'];
			}
			else
			{
				echo "Error in Database entry for visitor team.";
			}
		
		echo $gameID." &nbsp ".$home." &nbsp vs &nbsp ".$visitor." &nbsp ";?> <button onclick="selectMatch('<?php echo $homeID;?>','<?php echo $visitorID;?>','<?php echo $allData; ?>')"> Select Match </button> <?php echo "<br>";
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
		window.location="/login.php";
	</script>
	<?php
	}
}
?>
<br><a href="/index.php?login=success">Back to Home</a>
<script>
function selectMatch(home,visit,all)
{
	window.location.href= "uploadResult.php?home="+home+"&visit="+visit+"&all="+all;
}
</script>