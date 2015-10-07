<?php
require('includes/config.php');
session_start();

if($_SESSION['logged'] && ($_SESSION['loggedInUser']==="admin"))
	{
		if($dbConnected)	
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
				
				function checkTeamForUser($ID_user,$check_team)
				{
					$sql_check_for_team = "select userID, team from topherball_picks where userID = $ID_user and team = \"$check_team\"";
					$query_check_for_team = mysql_query($sql_check_for_team);
					if(mysql_num_rows($query_check_for_team)>0)
					{
						return true;
					}
					else
					{
						return false;
					}
				}
			
				echo "Set Default pick for users.<br>";
?>
		<form method="get" action="<?php $_SERVER["PHP_SELF"]?>">
		Enter the week:<input type="text" name="week" placeholder="Enter here..."> &nbsp; <input type="submit" value="Submit">
		</form>
<?php
				if(isset($_GET['def']))
				{
					$def = explode("_",$_GET['def']);
					$gameID= $def[0];
					$homeID = $def[1];
					$visitorID = $def[2];
					$week = $def[3];
					
					$home= team_name($homeID);
					$visitor = team_name($visitorID);
					
					echo "Select among two: <br>";
?>					<button onclick="defaultTeam('<?php echo $gameID."_".$home."_".$week;?>')"> <?php echo $home;?> </button> &nbsp; <button onclick="defaultTeam('<?php echo $gameID."_".$visitor."_".$week;?>')"> <?php echo $visitor;?> </button>  
<?php
							
				
				}
				
				if(isset($_GET['team']))
				{
					$exp_team = explode("_",$_GET['team']);
					$gameID= $exp_team[0];
					$team = $exp_team[1];
					$week = $exp_team[2];
					$userID="'1',";				// Set default value of userID as it is also the userID of admin.
					$pick_like = $week."\_%";
					
					
					$sql_user= "select distinct userID from topherball_picks where gameID = $gameID or pickID like \"$pick_like\"";
					$query_user = mysql_query($sql_user);
					
					while($result_user = mysql_fetch_array($query_user))
						{
							$userID .= "'".$result_user['userID']."' ,";
							
						}
						$userID = trim($userID, " ,");
						$userID= "(".$userID.")";
						
						$sql_remaining= "select userID from topherball_users where userID not in $userID";
						$query_remaining = mysql_query($sql_remaining);
						$userID_not_updated[]= null;
						$counter = 0;
						if(mysql_num_rows($query_remaining)>0)
							{
								$count=0;
								while($result_remaining = mysql_fetch_array($query_remaining))
									{	$remaining_userID= $result_remaining['userID'];
										if(!checkTeamForUser($remaining_userID,$team))
											{
												$remaining_userID= $result_remaining['userID'];
												$pick= $week."_".$gameID."_".$remaining_userID;
												$sql_user_insert ="insert into topherball_picks values ($remaining_userID , $gameID, \"$pick\",\"true\",\"$team\")";
												if(mysql_query($sql_user_insert))
													{
														$count++;
													}
												else
													{
														echo "error while insertion";
													}
											}
											else
												{	
													$userID_not_updated[$counter++] = $remaining_userID;
												}
									}
								if($count !=0)
								{
									if($count == 1)
									{
										echo "Default team set successfully for ".$count." user.";
									}
									else
									{
										echo "Default team set successfully for ".$count." users.";
									}
								}
								if($counter != 0)
								{
									 echo "<br>".$counter." teams already picked this team. Choose again" ; ?> <button onclick="retryPick('<?php echo $week;?>')">Continue</button> <?php echo "<br>";
								}
							}		
						else
						{
							echo "No user Found!";
						}
					
				}
				
				if(isset($_GET['week']))
				{	echo "<br> Select the match: <br>";
					$week= $_GET['week'];
					$sql_week= "select * from topherball_schedule where weekNum = $week";
					$query_week = mysql_query($sql_week);
					if(mysql_num_rows($query_week)>0)
					{
						while($result_week = mysql_fetch_array($query_week))
						{
							$gameID = $result_week['gameID'];
							$homeID = $result_week['homeID'];
							$visitorID = $result_week['visitorID'];
								
							$home= team_name($homeID);
							$visitor = team_name($visitorID);
					
							echo $gameID." &nbsp ".$home." &nbsp vs &nbsp ".$visitor." &nbsp ";?> <button onclick="defaultPick('<?php echo $gameID."_".$homeID."_".$visitorID."_".$week; ?>')"> Set Default</button> <?php echo "<br>";
						
						}
					}
					else
					{
						echo "No team found!";
					}
				}
			}	
		else
			{
				echo "Failed to connect DataBase.";

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

<script>
	function defaultPick(val)
	{
		window.location = "defaultPick.php?def="+val;
	}
	
	function defaultTeam(val)
	{
		window.location = "defaultPick.php?team="+val;
	}
	
	function retryPick(val)
	{
		window.location = "defaultPick.php?week="+val;
	}
</script>
<br><a href="/phppickem/phppickem-master/index.php?login=success">Back to Home</a>