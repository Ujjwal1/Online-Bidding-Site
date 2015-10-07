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
		{	echo "User Name of the Users who have to be Reactivated are: <br>";
			
			if(isset($_GET['id']))
			{		$id= $_GET['id'];
					$sql_activate = "update topherballusers set Activation=3 where userID=$id";
					if(mysql_query($sql_activate))
					{
						$sql_mail = "select firstname,email from topherballusers where userID=$id";
						$query_mail = mysql_query($sql_mail);
						if($query_mail)
						{
						
						if($result_mail = mysql_fetch_array($query_mail))
						{
							$to = $result_mail['email'];
							//$to = "ujwalpande@gmail.com";
							$fname = $result_mail['firstname'];
							$from = "admin@topherball.com";
							$header = "From: NFL TopherBall.com";
							$subject = "TOPHERBALL: Verification";
							$message = "Hi $fname, \nYou are successfully Re-registered. \nPlease start making picks on http://topherball.com";
							
							$message= wordwrap($message,70);
							if(mail($to,$subject,$message,$header))
							{
								echo "<strong>Message Sent Succesfully. </strong>";
						?>
						<script>
							alert("User Re-registered Successfully!");
							window.location="reactivateUser.php";
						</script>
						<?php
							}
							else
							{
								echo "Failed to message the user.";
							}
						}
						
						}
						else
						{
							echo "Error: ".mysql_error();
						}
					}
					else
					{
						echo "Error while Updating the Activation Number.";
					}
			
			}
			
			$sql_chance = "select * from topherballusers where Activation=2";
			$query_chance= mysql_query($sql_chance);
			if(mysql_num_rows($query_chance)>0)
				{
					while($result_chance = mysql_fetch_array($query_chance))
					{
						$userID = $result_chance['userID'];
						$userName=$result_chance['userName'];
						echo $userName."&nbsp"; ?> <button onclick="reactivate('<?php echo $userID; ?>')"> Activate again! </button> <?php echo "<br>";
					}
				}
			else
			{
				echo "No user Found!";
			}
		}
		else
		{
			echo "Failed to connect to the database";
		}
		
	}
	else
	{
		echo $err_activation_week;
	
	}
	
}
	else
	{ ?>
			<script>
				window.location.href = "http://topherball.com/logout.php";
			</script>
<?php
	}
?>

<html>
<head>
<title>
Re-Activate User!
</title>
</html>

<script>
	function reactivate(id)
	{
		window.location.href= "reactivateUser.php?id="+id;
	}
</script>