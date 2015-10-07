<?php
session_start();
require('includes/application_top.php');
require('includes/header.php');

if(isset($_SESSION['loggedInUser']))
{	
	$userID = $_SESSION['loginID'];
	$sql_spot = "select spotID from topherballspot where userID = $userID and Activation in (\"1\",\"3\") order by spotID";
	$query_spot = mysql_query($sql_spot);
	if(mysql_num_rows($query_spot) <= 0)
	{	
	?>
<script>
	window.location = 'http://topherball.com/result_spot.php';
</script>
<?php
	}
	else
	{
		echo "<br><br><strong>Choose the Spot: </strong><br>";
		while($result_spot = mysql_fetch_array($query_spot))
		{
			$spotID = $result_spot['spotID'];
			echo $spotID."&nbsp; &nbsp;";?><button onclick = "spot('<?php echo "$spotID"; ?>')";>Choose</button><br><?php
		}
	}
	
	if(isset($_GET['spot']))
	{
	if(isset($_SESSION['spotID']))
	{
		unset($_SESSION['spotID']);
	}
	$id = $_GET['spot'];
	$_SESSION['spotID']=$id;
?>
	<script>
		alert("<?php echo $id;?>"+" is set as your current Spot.");
		window.location = "http://topherball.com/index.php";
	</script>
<?php	}
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
<script>
	function spot(val)
	{
		window.location = "http://topherball.com/choose_spot.php?spot="+val;
	}
</script>