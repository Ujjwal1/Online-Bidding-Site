<?php
require('includes/config.php');
require('includes/weekdetail.php');
session_start();
if(($_SESSION['logged']) && ($_SESSION['loggedInUser']=="admin"))
{
	if($isActivationWeek)
	{
		if($dbConnected)
		{	echo "Verify the currently registered user: <br>";
			
			if(isset($_GET['id']))
			{		$id= $_GET['id'];
					$sql_activate = "update topherball_users set Activation=1 where userID=$id";
					
					if(mysql_query($sql_activate))
					{	$sql_mail = "select firstname,email from topherball_users where userID=$id";
						if(mysql_query($sql_mail))
						{
							$result_mail = mysql_fetch_array($sql_mail);
							$to = $result_mail['email'];
							//$to = "ujwalpande@gmail.com";
							$fname = $result_mail['firstname'];
							$from = "admin@topherball.com";
							$subject = "TOPHERBALL: Verification";
							$message = "Hi $fname, \n Congratulations, Your registration is verified. Please start making picks on <a href='http:topherball.com'></a>";
							
							$message= wordwrap($message,70);
							mail($to,$subject,$message,$from);
							echo "Message Sent Succesfully.";
						
						?>
						<script>
							alert("User Verified Successfully!");
							window.location="verify_user.php";
						</script>
						<?php
						
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
			
			$sql_chance = "select * from topherball_users where Activation=0";
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
				window.location.href = "/phppickem/phppickem-master/logout.php";
			</script>
<?php
	}
?>

<html>
<head>
<title>
Verify User!
</title>
<br><a href="/phppickem/phppickem-master/index.php?login=success">Home</a>
</html>

<script>
	function reactivate(id)
	{
		window.location.href= "verify_user.php?id="+id;
	}
</script>