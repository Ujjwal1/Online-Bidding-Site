<?php
require('includes/application_top.php');
require('includes/header.php');
require('suicide_pick/includes/config.php');
session_start();

if($_SESSION['logged'] && ($_SESSION['loggedInUser']==="admin"))
	{
		if($dbConnected)	
			{
							
				function checkTeamForUser($ID_user,$check_team)
				{
					$sql_check_for_team = "select spotID, team from topherballpicks where spotID = \"$ID_user\" and team = \"$check_team\"";
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
		<br>
<?php
				if(isset($_GET['noPickUsers']))
				{	$allSpot[] = null;
					$noSpot[] = null;
					$week_is = (int)$_GET['noPickUsers'];
					$sql_all = "select spotID from topherballspot";
					$query_all = mysql_query($sql_all) or die(mysql_error());
					if($query_all)
					{
						while($result_all = mysql_fetch_array($query_all))
						{
							$allSpot[] = $result_all['spotID'];
						}
					}
					
					for($i=0 ; $i<count($allSpot) ; $i++)
					{
						$pickLike =$week_is."\_%";
						$sql_no = "select * from topherballpicks where spotID = \"$allSpot[$i]\" and pickID like \"$pickLike\"";
						$query_no = (mysql_query($sql_no));
						if(mysql_num_rows($query_no) <= 0)
						{
							$noSpot[] = $allSpot[$i];
						}
					}
					$count = 0;
					if(count($noSpot) > 0)
					{
						while($count != count($noSpot))
						{
							echo $noSpot[$count++]."<br>";
						}
					}
					else
					{
						echo "<strong> No such User</strong>";
					}
				}
				
				if(isset($_GET['def']))
				{
					$def = explode("_",$_GET['def']);
					$gameID= $def[0];
					$homeID = $def[1];
					$visitorID = $def[2];
					$week = $def[3];
					
					$home= getTeamName($homeID);
					$visitor = getTeamName($visitorID);
					
					echo "<strong>Select among two:<strong> <br>";
?>					<button onclick="defaultTeam('<?php echo $gameID."_".$home."_".$week;?>')"> <?php echo $home;?> </button> &nbsp; <button onclick="defaultTeam('<?php echo $gameID."_".$visitor."_".$week;?>')"> <?php echo $visitor;?> </button>
<?php
							
				
				}
				
				if(isset($_GET['team']))
				{
					$exp_team = explode("_",$_GET['team']);
					$gameID= $exp_team[0];
					$team = $exp_team[1];
					$week = $exp_team[2];
					$spotID="";				
					$pick_like = $week."\_%";
					
					
					$sql_user= "select distinct spotID from topherballpicks where gameID = $gameID or pickID like \"$pick_like\"";
					$query_user = mysql_query($sql_user);
					
					while($result_user = mysql_fetch_array($query_user))
						{
							$spotID .= "\"".$result_user['spotID']."\" ,";
							
						}
						$spotID = trim($spotID, " ,");
						$spotID= "(".$spotID.")";
						
						$sql_remaining= "select spotID , Activation from topherballspot where spotID not in $spotID";
						$query_remaining = mysql_query($sql_remaining);
						$userID_not_updated[]= null;
						$counter = 0;
						if(mysql_num_rows($query_remaining)>0)
							{
								$count=0;
								while($result_remaining = mysql_fetch_array($query_remaining))
									{	$remaining_userID= $result_remaining['spotID'];
										$activation = $result_remaining['Activation'];
										if(!checkTeamForUser($remaining_userID,$team))
											{
												$remaining_userID= $result_remaining['spotID'];
												$pick= $week."_".$gameID."_".$remaining_userID;
												$sql_user_insert ="insert into topherballpicks values ( $gameID, \"$pick\",1,\"$team\",\"$remaining_userID\",$activation)";
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
										echo "<strong>Default team set successfully for ".$count." user.</strong>";
									}
									else
									{
										echo "<strong>Default team set successfully for ".$count." users.</strong>";
									}
								}
								if($counter != 0)
								{
									 echo "<br><strong>".$counter." user already picked this team. Choose again </strong>" ; ?> <button onclick="retryPick('<?php echo $week;?>')">Continue</button> <?php echo "<br>";
								}
							}		
						else
						{
							echo "<strong>No user Found!</strong>".mysql_error();
						}
					
				}
				
				if(isset($_GET['week']))
				{	$week= (int)$_GET['week'];
					if($week > 0 || $week < 18)
					{
					?>	Look for users with No Picks: <button onclick="noPickUsers('<?php echo $week; ?>')">Look for them</button> <br> <?php
					echo "<br> Select the match: <br>";
					$sql_week= "select * from topherballschedule where weekNum = $week";
					$query_week = mysql_query($sql_week);
					if(mysql_num_rows($query_week)>0)
					{ 	echo '<table cellpadding="4" cellspacing="0" class="table1"> <tr><th align="left">Match ID</th><th align="left">Home Team</th><th colspan="2">Visitor Team</th></tr>';
						
						while($result_week = mysql_fetch_array($query_week))
						{	echo '<tr>';
							$gameID = $result_week['gameID'];
							$homeID = $result_week['homeID'];
							$visitorID = $result_week['visitorID'];
								
							$home= getTeamName($homeID);
							$visitor = getTeamName($visitorID);
					
							echo '<td>'.$gameID."</td> <td>".$home."</td><td> ".$visitor." </td><td> ";?> <button onclick="defaultPick('<?php echo $gameID."_".$homeID."_".$visitorID."_".$week; ?>')"> Set Default</button> </td> <?php
							echo '</tr>';
						}
						echo '</table>';
					}
					else
					{
						echo "No team found!";
					}
					}
					else
					{
						echo "<br>Please enter the value in between 1 to 17";
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
		window.location="http://topherball.com//login.php";
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
	function noPickUsers(week)
	{
		window.location = "defaultPick.php?noPickUsers="+week;
	}
</script>
