<?php
session_start();
require('includes/application_top.php');
require('includes/header.php');
if(($_SESSION['logged']) && ($_SESSION['loggedInUser']=="admin"))
{
	if(isset($_POST['msg']))
	{	global $message;
		//$message = "Ujjwal";
		$sub = $_POST['sub'];
		$message= $_POST['msg'];
		if(!empty($message))
		{
			$sql_set = "update topherballcomments set  comment = \"$message\" , postDateTime = date_add(now(), INTERVAL 0 MINUTE) where userID = 1 and commentID = 1 ";
			if(mysql_query($sql_set))
			{
				echo "<strong>".$message."</strong> <br><br>Added Successfully!";
			}
			else
			{
				echo "Error: ".mysql_error();
			}
		}
		else
		{
			if(empty($message))
			{ 
				
						$sql_confirm = "update topherballcomments set  comment = \"$message\" , postDateTime = date_add(now(), INTERVAL 0 MINUTE) where userID = 1 and commentID = 1 ";
						if(mysql_query($sql_confirm))
							{
								echo "Added Empty String!";
							}
						else
						{	
							echo "Error: ".mysql_error();
						}
					
			}
			else
			{
				echo "Message Not set ".mysql_error();
			}
		}
	}
else{
?>
		<form action = "<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		Set Message: <input type= "text" name="msg"><input type= "text" name="sub" value="by_admin_and_its_hidden" hidden><br>
		<input type="Submit" value="Submit">
		</form>
<?php	}

}
?>