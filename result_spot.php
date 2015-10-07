<?php
	session_start();
	include('includes/application_top.php');
	include('includes/header.php');
	if(isset($_SESSION['loggedInUser']))
	{
		$userID = $_SESSION['loginID'];
		$counter = 0;
			$sql_all = "select spotID from topherballspot where Activation <> 0 order by spotID";
			$query_all = mysql_query($sql_all) or die(mysql_error());
			if(mysql_num_rows($query_all) > 0)
			{
				while($result_all = mysql_fetch_array($query_all))
				{
					$allspot[$counter++] = $result_all['spotID'];
				}
			}
			//$team[] = null;
			//$spotState[] = null;
		echo '<table cellpadding="4" cellspacing="0" class="table1"><thead>Meaning of various colours:</thead><tr><td bgcolor="#C0C0C0">Not Verified</td><td bgcolor="#00FF00">Active</td><td bgcolor="#FF6600">De-Active</td><td bgcolor="#00FFFF">Re-Active</td><td bgcolor="#FF0000">Knocked Out</td></tr></table>';
		echo "<br><hr><br>";
		echo '<table cellpadding="4" cellspacing="0" class="table1"> <tr><th>User</th><th>Week 1</th><th>Week 2</th><th>Week 3</th><th>Week 4</th><th>Week 5</th><th>Week 6</th><th>Week 7</th><th>Week 8</th><th>Week 9</th><th>Week 10</th><th>Week 11</th><th>Week 12</th><th>Week 13</th><th>Week 14</th><th>Week 15</th><th>Week 16</th><th>Week 17</th></tr>';
		for($i =0; $i< count($allspot); $i++)
		{	$spot = $allspot[$i];
			$sql_active = "select Activation from topherballspot where spotID =\"$spot\"";
			$query_active = mysql_query($sql_active) or die(mysql_error());
			if($query_active)
			{	$result_active = mysql_fetch_array($query_active);
				$actives = $result_active['Activation'];
				echo "<tr>";
				switch($actives)
							{
								case "0": ?><td bgcolor="#C0C0C0"> <?php echo "<strong>$spot </strong></td>";
								break;
								case "1":	?><td bgcolor="#00FF00"> <?php echo "<strong>$spot </strong></td>";
								break;
								case "2":	?><td bgcolor="#FF6600"> <?php echo "<strong>$spot </strong></td>";
								break;
								case "3":	?><td bgcolor="#00FFFF"> <?php echo "<strong>$spot </strong></td>";
								break;
								case "4":	
								default:	?><td bgcolor="#FF0000"> <?php echo "<strong>$spot </strong></td>";
								break;		
							}				
			}
			$count = 0;
			for($j=1; $j<18;$j++)
			{
			$picklike = $j."\_%";
			$sql_state = "select * from topherballpicks where spotID = \"$spot\" and pickID like \"$picklike\" and `lock` = 1";
			$query_state= mysql_query($sql_state);
			if(mysql_num_rows($query_state) > 0)
			{	
				if($result_state = mysql_fetch_array($query_state))
					{
						$week[$j] = 1;
						$team[$count] = $result_state['team'];
						$spotState[$count] = $result_state['spotState'];
						$count++;
					}
			}
			else 
			{
				$week[$j]= 0;
			}
			}
			$counter = 0;
			
			for($z = 1 ; $z< 18 ;$z++)
			{
				if($week[$z]== 1)
				{	
					switch($spotState[$counter])
							{
								case "0": ?><td bgcolor="#C0C0C0"> <?php echo "$team[$counter] </td>";
								break;
								case "1":	?><td bgcolor="#00FF00"> <?php echo "$team[$counter] </td>";
								break;
								case "2":	?><td bgcolor="#FF6600"> <?php echo "$team[$counter] </td>";
								break;
								case "3":	?><td bgcolor="#00FFFF"> <?php echo "$team[$counter] </td>";
								break;
								case "4":	
								default:	?><td bgcolor="#FF0000"> <?php echo "$team[$counter] </td>";
								break;		
							}
					$counter++;
				}
				else
				{
					echo "<td></td>";
				}
			
			}
			echo "</tr>";
		}
		
	}
	else
	{
?>	<script>
	window.location = "http://topherball.com/login.php";
	</script>
<?php
	}
?>