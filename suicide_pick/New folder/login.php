<?php
error_reporting(E_ALL ^ E_NOTICE);
//session_start();

require_once('includes/application_top.php');
require('includes/classes/crypto.php');
require('suicide_pick/includes/config.php');
require('suicide_pick/includes/weekdetail.php');
$crypto = new phpFreaksCrypto;

$_SESSION = array();

if(!empty($_POST['submitPass'])){
	$login->validate_password();
}

//require_once('includes/header.php');
if(empty($_SESSION['logged']) || $_SESSION['logged'] !== 'yes') {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>NFL Pick 'Em Login</title>
	<link href="css/main.css" rel="stylesheet" type="text/css" media="screen" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
</head>

<body>
	<div id="bgextend">
		<div id="login">
		<table>
			<tr valign="top">
				<td><img src="images/logos/nfl-logo.png" /></td>
				<td>&nbsp;</td>
				<td>
					<h1>NFL Pick 'Em Login</h1>
					<?php
					if ($_GET['login'] == 'failed') {
						echo '<div class="responseError">Oops!  Login failed, please try again.</div><br />';
					} else if ($_GET['signup'] == 'no') {
						echo '<div class="responseError">Sorry, signup is disabled.  Please contact your administrator.</div><br />';
					}
					?>
					<div style="margin: 25px; width: 500px; margin: auto;">
						<form action="login.php" method="POST" name="login">
								<table>
									<tr>
										<td><label for="name">Username:</label></td>
										<td><input name="username" id="username" type="text" size="40" /></td>
									</tr>
									<tr>
										<td><label for="password">Password:</label></td>
										<td><input name="password" type="password" size="40" /></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td><input type="submit" name="submitPass" value="Submit" class="button"></td>
									</tr>
								</table>
						</form>
						<?php
						if ($allow_signup && $show_signup_link) {
							echo '<p><a href="signup.php">Click here to sign up for an account</a></p>';
						}
						?>
						<p>Having trouble logging in?  Click here to <a href="password_reset.php">reset your password</a>.</p>
						
					</div>
					<script type="text/javascript">
					document.login.username.focus();
					</script>
				</td>
			</tr>
		</table>
	</div>
<?php
}		
	if($dbConnected)
		{
			function userpick($week)
			{	//select spotID from the pick table
				$pickLike = $week."\_%";
				$other_user_query="select spotID from topherballpicks where pickID like \"$pickLike\" and `lock` <> 0";
				$sql_other_user = mysql_query($other_user_query) or die("Error: ".mysql_error());
				if(mysql_num_rows($sql_other_user)>0)
				{
				while ($result_other_user = mysql_fetch_array($sql_other_user)) 
				{
					$spotID[] =$result_other_user['spotID'];
				}
				}
				else
				{
					echo "No Picks Locked!";
				}
				
				//Get the user-Name from users table
				for($j=0;$j<count($spotID);$j++)
				{
					$query_users ="select userName from topherballspot where spotID=\"$spotID[$j]\" and userName not in (\"admin\")";
					$sql_users=mysql_query($query_users) or die("Unable to connect database for user-table");
					while ($user_result = mysql_fetch_array($sql_users)) 
						{
							$userName[] = $user_result['userName'];
						}
				}
				
				echo "<br><strong>All Users Picks' for the week : (".$week.")</strong><br>";
				
				$other_users[]=null;
				$check_spot[] = null;
				for($k=0;$k<count($spotID);$k++)
				{	$team="";
					$pick_like = $week."\_%";
					/*
					$exps =  explode("-",$spotID[$k]);
					$exp_spot = $exps[0];
					$flag =0;
					for($m=0 ; $m <= count($check_spot); $m++)
					{
						if($check_spot[$m] === $exp_spot)
						{
							$flag = 1;
						}
					}
					if($flag != 1)
					{
					$check_spot[] = $exp_spot;
					$spotlike = $exps[0]."-%"; */
					$query_teamID = "select team from topherballpicks where spotID = \"$spotID[$k]\" and `lock` = 1 and pickID like (\"$pick_like\") order by gameID desc";
					$sql_other_teamID = mysql_query($query_teamID);
					while($teamID_result = mysql_fetch_array($sql_other_teamID))
					{	
						$team.=$teamID_result['team']." | ";
					}
					//$teams[]=$team;
					if($team != "")
					{
						$other_users[$k]= "<td class='lighter'><strong>".$spotID[$k]."</strong></td><td class='lighter'>".$team."</td>";
					}
				//	}
				}
				echo '<table cellpadding="4" cellspacing="0" class="table1"> <tr><th align="left">User Name</th><th>Teams</th></tr>';
				for($i=0;$i<count($spotID);$i++)
				{	echo '<tr>';
					if($other_users[$i] != null)
					{
						echo $other_users[$i];
					}
					echo '</tr>';
				}
				echo '</table>';
			
			}
			$week = $_curr_week;
			
			userpick($week);
				
?>
			<br><button onclick="redirect('<?php echo ($week -1); ?>')"> Previous Week </button> 
<?php			

		}
			else
			{
				echo "Database Not connected.";
			}
			
			echo "<hr>";
			if(isset($_GET['week']))
			{
				$get_week= $_GET['week'];
				if(($get_week <= 0 )||($get_week>$week))
				{
					echo "<br<br>Data Not Found!!";
				}
				else
				{
					
					if($get_week != $week)
				{
						userpick($get_week);
?>
			<br> <button onclick="redirect('<?php echo $get_week -1; ?>')"> Previous Week </button> &nbsp &nbsp <button onclick="redirect('<?php echo ($get_week +1); ?>')"> Next Week </button><br>
<?php			
				}
					
					
				}
			
			}
			

require('includes/footer.php');
?>


<script>
	function redirect(val)
	{	
		window.location.href = "login.php?week="+val;
	}
</script>