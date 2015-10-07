<?php
session_start();
require('includes/application_top.php');
require('suicide_pick/includes/config.php');
require('suicide_pick/includes/weekdetail.php');
require('includes/header.php');
if(($_SESSION['logged']) && ($_SESSION['loggedInUser']=="admin"))
{
	if($isActivationWeek)
	{
		if($dbConnected)
		{
			if(isset($_GET['id']))
			{
				$id = $_GET['id'];
				$sql_user = "select userID from topherballspot where spotID = \"$id\"";
				$query_user = mysql_query($sql_user);
				if(mysql_num_rows($query_user)>0)
				{
					$result_user = mysql_fetch_array($query_user);
					$userID = $result_user['userID'];
				}
				$sql_verify = "update topherballspot set Activation = 1 where spotID = \"$id\" and Activation = 0";
				if(mysql_query($sql_verify))
				{	$sql_mail = "select firstname,email from topherballusers where userID= \"$userID\"";
						$query_mail = mysql_query($sql_mail);
						if($query_mail)
						{
						if($result_mail = mysql_fetch_array($query_mail))
						{
							$to = $result_mail['email'];
							//$to = "ujwalpande@gmail.com";
							$fname = $result_mail['firstname'];
							$header = "From: NFL TopherBall.com";
							$subject = "TOPHERBALL: Verification";
							$message = "Hi $fname,\nCongratulations!, Your spot $id is verified. \nPlease start making picks on http://topherball.com";
							
							$message= wordwrap($message,70);
							if(mail($to,$subject,$message,$header))
							{
								echo "<strong>Message Sent Succesfully. </strong>";
						?>
						<script>
							alert("User Verified Successfully!");
							window.location="verify_spot.php";
						</script>
						<?php
							}
							else
							{
								echo "Failed to message the user.";
							}
						}
						else
						{
							echo "Mysql fetch array error: ".mysql_error();
						}
						}
						else
						{
							echo "Error : ".mysql_error();
						}
						
					}
				else
				{
					echo "Sql Error: ".mysql_error();
				}
			}
			
			$sql_spotted = "select * from topherballspot where Activation = 0 order by spotID";
			$query_spotted = mysql_query($sql_spotted);
			if(mysql_num_rows($query_spotted) > 0)
			{	echo "<table cellpadding=\"4\" cellspacing=\"0\" class=\"table1\"><tr><th>User Name</th><th colspan= '2'>Spot ID</th></tr>";
				while($result_spotted = mysql_fetch_array($query_spotted))
				{	echo "<tr>";
					$userName = $result_spotted['userName'];
					$spotID = $result_spotted['spotID'];
					$activation = $result_spotted['Activation'];
					echo "<td>$userName</td><td>$spotID</td>";?> <td> <button onClick = "spot('<?php echo $spotID; ?>')">Verify Spot</button></td><?php
				
					echo "</tr>";
				}
				echo "</table>";
			}
			
		}
		else
		{
			echo "DataBase Not connected!";
		}
	}
	
}
else
{
?>
<script>
	window.location = "http://topherball.com/login.php";
</script>
<?php
}
?>
<script>
	function spot(val)
	{
		window.location = "http://topherball.com/verify_spot.php?id="+val;
	}
</script>