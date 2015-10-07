<?php
session_start();
require('includes/application_top.php');
require('includes/header.php');

if(isset($_SESSION['loggedInUser']))
{
	$user= $_SESSION['loggedInUser'];
	$userID = $_SESSION['loginID'];
	
	$sql_check = "select * from topherballspot where userID= $userID order by spotID desc";
	$query_check = mysql_query($sql_check);
	if(mysql_num_rows($query_check)>0)
	{
		if($result_check = mysql_fetch_array($query_check))
		{
			$recent_spot = $result_check['spotID'];
			$get_spot= explode("-",$recent_spot);
			$recent_spot_no = (int)$get_spot[1];
			$recent_spot_no++;

			$spotID = $user."-".$recent_spot_no;
			$sql_set = "insert into topherballspot values(\"$spotID\",$userID,\"$user\",0)";
			$query_set = mysql_query($sql_set);
			if($query_set)
			{
			?> <script> alert("Additional Spot Created Successfully!"); window.location = "http://topherball.com/manage_spot.php"; </script> <?php
			}
			else
			{
				echo "Erro while inserting: ".mysql_error();
			}

		}
	
	}
	else
	{
			$spotID = $user."-1";
			$sql_set = "insert into topherballspot values(\"$spotID\",$userID,\"$user\",0)";
			$query_set = mysql_query($sql_set);
			if($query_set)
			{
			?> <script> alert("Additional Spot Created Successfully!"); window.location = "http://topherball.com/manage_spot.php"; </script> <?php
			}
			else
			{
				echo "Erro while inserting: ".mysql_error();
			}
	}

}
else
{
?>
<script>
	window.location = 'http://topherball.com/login.php';
</script>
<?php
}

?>