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
		{	echo "Verify the currently registered user: <br>";
			
			if(isset($_GET['id']))
			{		$id= $_GET['id'];
					$sql_activate = "update topherballusers set Activation=1 where userID=$id";
					
					if(mysql_query($sql_activate))
					{	$sql_mail = "select userName,firstname,email from topherballusers where userID=$id";
						$query_mail = mysql_query($sql_mail);
						if($query_mail)
						{
						if($result_mail = mysql_fetch_array($query_mail))
						{
							//$to = "ujwalpande@gmail.com";
							$user = $result_mail['userName'];
							$spot_id = $user."-1";
							$sql_activate_spot = "insert into topherballspot values(\"$spot_id\",$id,\"$user\",1)";
							if(mysql_query($sql_activate_spot))
							{	
							$to = $result_mail['email'];
							$fname = $result_mail['firstname'];
							$header = "From: NFL TopherBall.com";
							$subject = "TOPHERBALL: Verification";
							$message = "Hi $fname, \nCongratulations!, Your registration is verified. \nPlease start making picks on http://topherball.com";
							
							$message= wordwrap($message,70);
							if(mail($to,$subject,$message,$header))
							{
								echo "<strong>Message Sent Succesfully. </strong>";
						?>
						<script>
							alert("User Verified Successfully!");
							window.location="verify_user.php";
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
						echo "Error while Updating the Activation Number.";
					}
			
			}
			
			$sql_chance = "select * from topherballusers where Activation=0";
			$query_chance= mysql_query($sql_chance);
			if(mysql_num_rows($query_chance)>0)
				{
					while($result_chance = mysql_fetch_array($query_chance))
					{
						$userID = $result_chance['userID'];
						$userName=$result_chance['userName'];
						echo $userName."&nbsp"; ?> <button onclick="reactivate('<?php echo $userID; ?>')"> Verify and Send Mail! </button> <?php echo "<br>";
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
				window.location.href = "http://topherball.com//logout.php";
			</script>
<?php
	}
?>

<html>
<head>
<title>
Verify User!
</title>
</html>

<script>
	function reactivate(id)
	{
		window.location.href= "verify_user.php?id="+id;
	}
</script>