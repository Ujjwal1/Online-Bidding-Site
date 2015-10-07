<?php
	session_start();
	require('includes/application_top.php');
	require('suicide_pick/includes/config.php');
	require('suicide_pick/includes/weekdetail.php');
	if($_SESSION['logged'])
	{	$userID[]=null;
		$teamID[]=null;
		$userName[]=null;
		if($dbConnected)
		{		$loggedInUser=$_SESSION['loggedInUser'];
				$loginID=$_SESSION['loginID'];
				include ('includes/header.php');
						
				//select userID from the pick table
				$other_user_query="select distinct userID from topherballpicks where userID <> 1";
				$sql_other_user=mysql_query($other_user_query);
				while ($result_other_user = mysql_fetch_array($sql_other_user)) 
				{
				$userID[] =$result_other_user['userID'];
				}
				
				
			function userpick($week)
			{	//select userID from the pick table
				$other_user_query="select distinct userID from topherballpicks where userID not in (\"1\")";
				$sql_other_user=mysql_query($other_user_query);
				while ($result_other_user = mysql_fetch_array($sql_other_user)) 
				{
				$userID[] =$result_other_user['userID'];
				}
				
				
				//Get the user-Name from users table
				for($j=0;$j<count($userID);$j++)
				{
					$query_users ="select userName from topherballusers where userID=\"$userID[$j]\" and userName not in (\"admin\")";
					$sql_users=mysql_query($query_users) or die("Unable to connect database for user-table");
					while ($user_result = mysql_fetch_array($sql_users)) 
						{
							$userName[] = $user_result['userName'];
							//echo $userName."<br>";
						}
				}
				
				echo "<br><strong>All Users Picks' for the week : (".$week.")</strong><br>";
				
				$other_users[]=null;
				for($k=0;$k<count($userID);$k++)
				{	$team="";
					$pick_like = $week."\_%";
					$query_teamID = "select team from topherballpicks where userID=\"$userID[$k]\" and `lock` in (\"true\") and pickID like (\"$pick_like\") order by gameID desc";
					$sql_other_teamID = mysql_query($query_teamID);
					while($teamID_result = mysql_fetch_array($sql_other_teamID))
					{	
						$team.=$teamID_result['team']." (Week No: ".$week.") | ";
					}
					//$teams[]=$team;
					if($team ==="")
					{
					}
					else
					{
						$other_users[$k]= "<strong>".$userName[$k]."</strong>"." -> ".$team;
					}
				}
				
				for($i=0;$i<count($userID);$i++)
				{
					if($other_users[$i] != null)
					{
						echo $other_users[$i]."<br>";
					}
				}
			
			
			}
			
			userpick($_curr_week);
				
				echo "<br><br><strong>My Complete Picks:</strong><br>";
					
				$query_logged="select * from topherballpicks where userID=$loginID order by gameID desc";
				$sql_logged=mysql_query($query_logged) or die("Unable to connect Database for Pick table");
				while($logged_result= mysql_fetch_array($sql_logged))
					{	$pickIDs = $logged_result['pickID'];
						$pickWeek= explode("_",$pickIDs);	
						$week=$pickWeek[0];
						$myteams= $logged_result['team'];
						echo $myteams." (Week No:".$week.")<br>";
					}
				
		}
	
	
	}
	else
	{
?>
<script>
	window.loaction="http://topherball.com/login.php";
</script>
<?php
	}
?>
<html>
<a href="http://topherball.com/pickteam.php">Pick a team</a>&nbsp; &nbsp; <a href="http://topherball.com/index.php?login=success"> Back </a><br> 
</html>