<?php
require('includes/application_top.php');

$week = (int)$_GET['week'];
if (empty($week)) {
	//get current week
	$week = (int)getCurrentWeek();
}

$cutoffDateTime = getCutoffDateTime($week);
$weekExpired = ((date("U", time()+(SERVER_TIMEZONE_OFFSET * 3600)) > strtotime($cutoffDateTime)) ? 1 : 0);

include('includes/header.php');
//include('includes/column_right.php');


//get array of games
$allScoresIn = true;
$games = array();
$sql = "select * from " . $db_prefix . "schedule where weekNum = " . $week . " order by gameTimeEastern, gameID";
$query = mysql_query($sql);
while ($result = mysql_fetch_array($query)) {
	$games[$result['gameID']]['gameID'] = $result['gameID'];
	$games[$result['gameID']]['homeID'] = $result['homeID'];
	$games[$result['gameID']]['visitorID'] = $result['visitorID'];
	if (strlen($result['homeScore']) > 0 && strlen($result['visitorScore']) > 0) {
		if ((int)$result['homeScore'] > (int)$result['visitorScore']) {
			$games[$result['gameID']]['winnerID'] = $result['homeID'];
		}
		if ((int)$result['visitorScore'] > (int)$result['homeScore']) {
			$games[$result['gameID']]['winnerID'] = $result['visitorID'];
		}
	} else {
		$games[$result['gameID']]['winnerID'] = '';
		$allScoresIn = false;
	}
}

//get array of player picks
$playerPicks = array();
$playerTotals = array();
$sql = "select p.userID, p.gameID, p.pickID ";
$sql .= "from " . $db_prefix . "picks p ";
$sql .= "inner join " . $db_prefix . "users u on p.userID = u.userID ";
$sql .= "inner join " . $db_prefix . "schedule s on p.gameID = s.gameID ";
$sql .= "where s.weekNum = " . $week . " and u.userName <> 'admin' ";
$sql .= "order by p.userID, s.gameTimeEastern, s.gameID";
$query = mysql_query($sql);
$i = 0;
if(mysql_num_rows($query)>0)
{
while ($result = mysql_fetch_array($query)) {
	$playerPicks[$result['userID']][$result['gameID']] = $result['pickID'];
	if (!empty($games[$result['gameID']]['winnerID']) && $result['pickID'] == $games[$result['gameID']]['winnerID']) {
		//player has picked the winning team
		$playerTotals[$result['userID']] += 1;
	} else {
		$playerTotals[$result['userID']] += 0;
	}
	$i++;
}
}
else
{
	echo "Error in query.";
}
?>
<script type="text/javascript">
$(document).ready(function(){
$(".table1 tr").mouseover(function() {
	$(this).addClass("over");}).mouseout(function() {$(this).removeClass("over");
});
$(".table1 tr").click(function() {
	if ($(this).attr('class').indexOf('overPerm') > -1) {
		$(this).removeClass("overPerm");
	} else {
		$(this).addClass("overPerm");
	}
});
});
</script>
<style type="text/css">
.pickTD { width: 24px; font-size: 9px; text-align: center; }
</style>
<h1>Results </h1>
<?php

if (sizeof($playerTotals) > 0)
{
?>
<strong>Select category:</strong> <select name="state" onchange="javascript:location.href='results.php?state=' + this.value;">
	<option value="">--Option--</option>
	<option value= "verify">Have to verify</option>
	<option value= "active">Activated</option>
	<option value= "deactive">Deactivated</option>
	<option value= "reactive">Re-Activated</option>
	<option value= "knock">Knocked Out</option>
	<option value="all">All</option>
</select>

<?php
	 if(!isset($_GET['state'])){
		echo '<table cellpadding="4" cellspacing="0" class="table1"> <tr><th align="left">Player</th><th align="left">State</th></tr>';
		$sql_active = "select userName , Activation from topherballusers where userName <> \"admin\"";
		$query_active = mysql_query($sql_active) or die("Error: ".mysql_error());
		if((mysql_num_rows($query_active))>0)
		{	
			while($result_active = mysql_fetch_array($query_active))
			{
				$name = $result_active['userName'];
				$active = $result_active['Activation'];
				
				switch($active)
				{case "0": ?><tr bgcolor="#C0C0C0"> <?php echo "<td>$name</td><td>Yet to be verified</td></tr>";
				break;
				case "1":	?><tr bgcolor="#0F0"> <?php echo "<td>$name</td><td>Acivated</td></tr>";
				break;
				case "2":	?><tr bgcolor="#F60"> <?php echo "<td>$name</td><td>Deactivated</td></tr>";
				break;
				case "3":	?><tr bgcolor="#00FFFF"> <?php echo "<td>$name</td><td>Reactivated</td></tr>";
				break;
				case "4":	
				default:	?><tr bgcolor="#F00"> <?php echo "<td>$name</td><td>Knocked Out</td></tr>";
				break;
				}
				}
		}
		echo '</table>';
	}
	else
	if(isset($_GET['state']))
	{	echo '<table cellpadding="4" cellspacing="0"> <tr><th align="left">Player</th><th align="left">State</th></tr>';
		$i = $_GET['state'];
		$j =0;
		switch($i) {case "verify": $j = 0; break; case "active": $j = 1; break; case "deactive": $j  = 2; break; case "reactive": $j = 3; break; case "knock": $j = 4; break; default: $j = 4; break;}
		if(!empty($i) && ($i != "all"))
	{	$sql_active = "select userName from topherballusers where Activation = $j and userName <> \"admin\"";
		$query_active = mysql_query($sql_active) or die("Error: ".mysql_error());
		if((mysql_num_rows($query_active))>0)
		{ while($result_active = mysql_fetch_array($query_active))
			{
				$name = $result_active['userName'];
				switch($i)
				{case "verify": ?><tr bgcolor="#C0C0C0"> <?php echo "<td>$name</td><td>Yet to verify</td></tr>";
				break;
				case "active":	?><tr bgcolor="#0F0"> <?php echo "<td>$name</td><td>Acivated</td></tr>";
				break;
				case "deactive":	?><tr bgcolor="#F60"> <?php echo "<td>$name</td><td>Deactivated</td></tr>";
				break;
				case "reactive":	?><tr bgcolor="#00FFFF"> <?php echo "<td>$name</td><td>Reactivated</td></tr>";
				break;
				case "knock":	
				default: ?><tr bgcolor="#F00"> <?php echo "<td>$name</td><td>Knocked Out</td></tr>";
				break;
				}
				}
		}
	}
		else if(!empty($i) && ($i === "all")){
		$sql_active = "select userName , Activation from topherballusers where userName <> \"admin\"";
		$query_active = mysql_query($sql_active) or die("Error: ".mysql_error());
		if((mysql_num_rows($query_active))>0)
		{
			while($result_active = mysql_fetch_array($query_active))
			{
				$name = $result_active['userName'];
				$active = $result_active['Activation'];
				switch($active)
				{	case "0": ?><tr bgcolor="#C0C0C0"> <?php echo " <td> $name </td> <td> Have to verify! </td> </tr>";
					break;
					
					case "1": ?><tr bgcolor="#0F0"> <?php echo " <td> $name </td> <td> Activated! </td> </tr>";
					break;
		
					case "2": ?><tr bgcolor="#F60"> <?php echo " <td> $name </td> <td> Deactivated! </td> </tr>";
					break;
		
					case "3": ?><tr bgcolor="#00FFFF"> <?php echo " <td> $name </td> <td> Re-Activated! </td> </tr>";
					break;
		
					case "4": ?><tr bgcolor="#F00"> <?php echo " <td> $name </td> <td> Knocked Out! </td> </tr>";
					break;
		
					default: ?><tr bgcolor="#F00"> <?php echo " <td> $name </td> <td> Knocked Out! </td> </tr>";
					break;
	
				}
				
			}
		
		}
		}
		
		echo '</table>';
	} 
?>

<?php
	
}

include('includes/comments.php');

include('includes/footer.php');
?>
