<?php
require_once('includes/application_top.php');
require('includes/classes/team.php');
if(!(isset($_SESSION['loginID'])) || !(isset($_SESSION['spotID'])))
{
	header("Location: choose_spot.php");
}
	$userID = $_SESSION['loginID'];
	$spotID = $_SESSION['spotID'];
	if ($_POST['action'] == 'Submit') {
	$week = $_POST['week'];
	$game = $_POST['game'];
	$spotID = $_SESSION['spotID'];
	$get = explode("_",$game);
	$gameID = $get[0];
	$teamID = $get[1];
	$teamName = getTeamName($teamID);
	$cutoffDateTime = getCutoffDateTime($week);
	
	//update summary table
	$sql = "delete from " . $db_prefix . "picksummary where weekNum = " . $_POST['week'] . " and userID = " . $user->userID . ";";
	mysql_query($sql) or die('Error updating picks summary: ' . mysql_error());
	$sql = "insert into " . $db_prefix . "picksummary (weekNum, userID) values (" . $_POST['week'] . ", " . $user->userID . ");";
	mysql_query($sql) or die('Error updating picks summary: ' . mysql_error());
	
	//loop through non-expire weeks and update picks
	
			//$existing_team=null;
			//$counter=0;
			//$flag_team=false;
			//$flag_week=false;
			
			$sql_check="select * from topherballpicks where spotID = \"$spotID\" and pickID like \"".$week."\_%\_".$spotID."\"";
			$query_check=mysql_query($sql_check);
			
			$sql_team = "select * from topherballpicks where spotID = \"$spotID\" and team in (\"$teamName\") ";
			$query_team = mysql_query($sql_team);
			
			$sql_lock="select * from topherballschedule where gameID = $gameID and `lock` = 1";
			$query_lock = mysql_query($sql_lock);
			if(mysql_num_rows($query_lock)>0)
			{				
					?>	<script> alert("You can not pick this team because this Match is Locked.");	window.location = "entry_form.php?week="+<?php echo $week; ?>;	</script> <?php
			}
			else if(mysql_num_rows($query_check) > 0)
			{
			?> <script> alert("You have already made a pick for this week. To change it click on the Pick Team link");	window.location = "entry_form.php?week="+<?php echo $week; ?>;	</script>	<?php
			}
			
			else if(mysql_num_rows($query_team) > 0)
			{
			?> <script> alert("You had alreday choosen this Team. To change it click on the Pick Team link");	window.location = "entry_form.php?week="+<?php echo $week; ?>;	</script>	<?php
			}
			
			else
			{
			$spotState = '';
			$query_state =  mysql_query("select Activation from topherballspot where spotID = \"$spotID\"");
			if($query_state)
			{
				if($result_state = mysql_fetch_array($query_state))
				{
					$spotState = $result_state['Activation'];
				}
			}
			$pickID=$week."_".$gameID."_".$spotID;
			//$totalTeams = $I_selected.",\'".$choosed."\'";
			$teamQuery="INSERT INTO topherballpicks(gameID,  pickID,`lock`,team,spotID,spotState) VALUES ($gameID,\"$pickID\",0,\"$teamName\",\"$spotID\",$spotState)";
			$res=mysql_query($teamQuery);
			if($res)
			{ 
				?>
				<script>
					alert("Your pick has been added successfully. To change it click on the 'Pick Team' link");
					window.location="http://topherball.com/pickteam.php";
				</script>
				<?php
			}
			else 
			{
				?>
				<script>
					alert("You had already Choosen its Opponent");
					window.location="http://topherball.com/pickteam.php";
				</script>
				<?php
			}
			
			}
			
	header('Location: results.php?week=' . $_POST['week']);
} else {
	$week = (int)$_GET['week'];
	if (empty($week)) {
		//get current week
		$week = (int)getCurrentWeek();
	}
	$cutoffDateTime = getCutoffDateTime($week);
	$firstGameTime = getFirstGameTime($week);
}

include('includes/header.php');
//include('includes/column_right.php');
echo "<strong>Spot Activated: </strong>".$spotID."<br><br>";
$sql_nav="select Activation from topherballspot where spotID = \"$spotID\"";
$query_nav = mysql_query($sql_nav);
if(mysql_num_rows($query_nav)>0)
{	$result_nav = mysql_fetch_array($query_nav);
if(($result_nav['Activation'] === '1' )||($result_nav['Activation'] === '3' ))
{
//display week nav
$sql = "select distinct weekNum from " . $db_prefix . "schedule order by weekNum;";
$query = mysql_query($sql);
$weekNav = '<div class="navbar3"><b>Go to week:</b> ';
$i = 0;
while ($result = mysql_fetch_array($query)) {
	if ($i > 0) $weekNav .= ' | ';
	if ($week !== (int)$result['weekNum']) {
		$weekNav .= '<a href="entry_form.php?week=' . $result['weekNum'] . '">' . $result['weekNum'] . '</a>';
	} else {
		$weekNav .= $result['weekNum'];
	}
	$i++;
}
$weekNav .= '</div>' . "\n";
echo $weekNav;

}
}
?>
<!--
	<table cellpadding="0" cellspacing="0">
		<tr valign="top">
			<td width="60%">
//-->
				<h2>Week <?php echo $week; ?> - Make Your Picks:</h2>
				<p>Make your picks below by clicking on the team helmet or checking the radio buttons to the right. <br><strong>Note:</strong> Only one team per week and per season!</p>
				<script type="text/javascript">
				function checkform() {
					//make sure all picks have a checked value
					var f = document.entryForm;
					var count = 0;
					var allR = document.getElementsByTagName('input');
					for (var i=0; i < allR.length; i++) {
						if(allR[i].type == 'radio') {
							if (radioIsChecked(allR[i].name)) {
								count++;
							}
						}      
				    }
				    if (count > 1) {
    					alert('Only one pick is allowed!');
						window.location.href = "entry_form.php";
						return false;
					}
					return true;
				}
				function radioIsChecked(elmName) {
					var elemeants = document.getElementsByName(elmName);
					for (var i = 0; i < elements.length; i++) {
						if (elements[i].checked) {
							return true;
						}
					}
					return false;
				}
				</script>
	<div style="float: right; width: 270px; margin-right: 10px"><?php include('includes/comments.php'); ?></div>
	<?php
	//get existing picks
	$picks = getUserPicks($week, $user->userID);
	
	//get show picks status
	$sql = "select * from " . $db_prefix . "picksummary where weekNum = " . $week . " and userID = " . $user->userID . ";";
	$query = mysql_query($sql);
	if (mysql_num_rows($query) > 0) {
		$result = mysql_fetch_array($query);
		//$showPicks = (int)$result['showPicks'];
	} else {
		//$showPicks = 1;
	}
	
	//display schedule for week
	$sql = "select s.*, (DATE_ADD(NOW(), INTERVAL " . SERVER_TIMEZONE_OFFSET . " HOUR) > gameTimeEastern or DATE_ADD(NOW(), INTERVAL " . SERVER_TIMEZONE_OFFSET . " HOUR) > '" . $cutoffDateTime . "')  as expired ";
	$sql .= "from " . $db_prefix . "schedule s ";
	$sql .= "inner join " . $db_prefix . "teams ht on s.homeID = ht.teamID ";
	$sql .= "inner join " . $db_prefix . "teams vt on s.visitorID = vt.teamID ";
	$sql .= "where s.weekNum = " . $week . " ";
	$sql .= "order by s.gameTimeEastern, s.gameID";
	//echo $sql;
	$query = mysql_query($sql);
	if (mysql_num_rows($query) > 0) {
		echo '<form name="entryForm" action="entry_form.php" method="post" onsubmit="return checkform();">' . "\n";
		echo '<input type="hidden" name="week" value="' . $week . '" />' . "\n";
		echo '<table cellpadding="4" cellspacing="0" class="table1">' . "\n";
		//echo '	<tr><th>Home</th><th>Visitor</th><th align="left">Game</th><th>Time / Result</th><th>Your Pick</th></tr>' . "\n";
		$i = 0;
		while ($result = mysql_fetch_array($query)) {
			$homeTeam = new team($result['homeID']);
			$visitorTeam = new team($result['visitorID']);
			$rowclass = (($i % 2 == 0) ? ' class="altrow"' : '');
			//$pickExpired = ((date("U") > strtotime($result['gameTimeEastern'])) ? true : false);
			echo '		<tr' . $rowclass . '>' . "\n";
			echo '			<td align="center">' . "\n";
			echo '				<table width="100%" border="0" cellpadding="2" cellspacing="0" class="nostyle">' . "\n";
			echo '					<tr valign="middle">' . "\n";
			echo '						<td align="center"><label for="' . $result['gameID'] . $visitorTeam->teamID . '"><img src="images/helmets_big/' . strtolower($visitorTeam->teamID) . '1.gif" onclick="document.entryForm.game' . $result['gameID'] . '[0].checked=true;" /></label><br /><span style="font-size: 9px;"><b>' . $visitorTeam->city . ' ' . $visitorTeam->team . '</b><br />Record: ' . getTeamRecord($visitorTeam->teamID) . '<br />Streak: ' . getTeamStreak($visitorTeam->teamID) . '</span></td>' . "\n";
			echo '						<td align="center">at</td>' . "\n";
			echo '						<td align="center"><label for="' . $result['gameID'] . $homeTeam->teamID . '"><img src="images/helmets_big/' . strtolower($homeTeam->teamID) . '2.gif" onclick="document.entryForm.game' . $result['gameID'] . '[1].checked=true;" /></label><br /><span style="font-size: 9px;"><b>' . $homeTeam->city . ' ' . $homeTeam->team . '</b><br />Record: ' . getTeamRecord($homeTeam->teamID) . '<br />Streak: ' . getTeamStreak($homeTeam->teamID) . '</span></td>' . "\n";
			echo '					</tr>' . "\n";
			if (strlen($result['homeScore']) > 0 && strlen($result['visitorScore']) > 0) {
				//if score is entered, show score
				echo '					<tr><td colspan="3" align="center"><b>Final: ' . $result['visitorScore'] . ' - ' . $result['homeScore'] . '</b></td></tr>' . "\n";
			} else {
				//else show time of game
				echo '					<tr><td colspan="3" align="center">' . date('D n/j g:i a', strtotime($result['gameTimeEastern'])) . ' ET</td></tr>' . "\n";
			}
			echo '				</table>' . "\n";
			echo '			</td>' . "\n";
			echo '			<td align="left"><b>Your Pick:</b><br />' . "\n";
			if (!$result['expired']) {
				//if game is not expired, show pick
				echo '			<input type="radio" name="game" value=" '. $result['gameID'] .'_' . $visitorTeam->teamID . '" id="' . $result['gameID'] . $visitorTeam->teamID . '"' . (($picks[$result['gameID']]['pickID'] == $visitorTeam->teamID) ? ' checked="checked"' : '') . ' /> <label for="' . $result['gameID'] . $visitorTeam->teamID . '">' . $visitorTeam->teamName . '</label><br />' . "\n";
				echo '			<input type="radio" name="game" value="'. $result['gameID'] .'_'. $homeTeam->teamID . '" id="' . $result['gameID'] . $homeTeam->teamID . '"' . (($picks[$result['gameID']]['pickID'] == $homeTeam->teamID) ? ' checked="checked"' : '') . ' /> <label for="' . $result['gameID'] . $homeTeam->teamID . '">' . $homeTeam->teamName . '</label><br />' . "\n";
			} else {
				//else show locked pick
				$pickID = getPickID($result['gameID'], $user->userID);
				if (!empty($pickID)) {
					$statusImg = '';
					$pickTeam = new team($pickID);
					$pickLabel = $pickTeam->teamName;
				} else {
					$statusImg = '<img src="images/cross_16x16.png" width="16" height="16" alt="" />';
					$pickLabel = 'None Selected';
				}
				if ($scoreEntered) {
					//set status of pick (correct, incorrect)
					if ($pickID == $result['winnerID']) {
						$statusImg = '<img src="images/check_16x16.png" width="16" height="16" alt="" />';
					} else {
						$statusImg = '<img src="images/cross_16x16.png" width="16" height="16" alt="" />';
					}
				}
				echo '			' . $statusImg . ' ' . $pickLabel . "\n";
			}
			echo '			</td>' . "\n";
			echo '		</tr>' . "\n";
			$i++;
		}
		echo '</table>' . "\n";
		//echo '<p><input type="checkbox" name="showPicks" id="showPicks" value="1"' . (($showPicks) ? ' checked="checked"' : '') . ' /> <label for="showPicks">Allow others to see my picks</label></p>' . "\n";
		echo '<p><input type="submit" name="action" value="Submit" /></p>' . "\n";
		echo '</form>' . "\n";
	}
	?>
<!--
			</td>
			<td width="40%">
				<h2>Latest Comments:</h2>
				<p>comment</p>
				<div>
				
				</div>
			</td>
		</tr>
	</table>
//-->
<?php
include('includes/footer.php'); 
?>