<?php
session_start();
require('includes/application_top.php');
require('includes/header.php');

if(isset($_SESSION['loggedInUser']))
{
	$user= $_SESSION['loggedInUser'];
	$userID = $_SESSION['loginID'];

	if(isset($_GET['del']))
	{
		$id = $_GET['del'];
		$sql_del = "delete from topherballspot where spotID = \"$id\" and Activation = 0";
		$query_del = mysql_query($sql_del);
		if($query_del)
		{
		?> <script> alert("Deleted Successfully"); window.location = "http://topherball.com/manage_spot.php"; </script> <?php
		}
		else
		{
			echo "Error: ".mysql_error();
		}
	}
	
	$sql_list = "select spotID from topherballspot where userID = $userID and Activation = 0 order by spotID";
	$query_list = mysql_query($sql_list);
	if(mysql_num_rows($query_list) > 0)
	{	echo "<strong>Your spots: <br></strong>";
		while($result_list = mysql_fetch_array($query_list))
		{
			$spot = $result_list['spotID'];
			echo "$spot &nbsp "; ?> <button onclick = delet("<?php echo $spot ?>");> Delete </button><?php echo "<br>";
		}
		echo "(<strong>Note:</strong> You can use these spots for picks only after you verify them by Administrator.)";
	}
	else
	{
		echo "<strong> Your all spots are verified. No more spot to Delete.</strong>";
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

<script>
function delet(val)
{
	window.location = "http://topherball.com/manage_spot.php?del="+val;
}
</script>