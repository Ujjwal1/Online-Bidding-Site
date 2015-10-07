<?php
	
	$start_date=4; 		// Write 1,2,3...9 not like 01,02,03,..09 other-wise it will give erroneous result
	$start_month=9;  		//Write 1,2,3...9 not like 01,02,03,..09
	$start_year=2014;
	$isSeasonWeek = false;
	$isActivationWeek= false;
	
	$season_start= date("W", mktime(0,0,0,$start_month,$start_date,$start_year));  // The week Changes from new Monday
	
	//$curr_week_by_calendar = date("W");
	$curr_week_by_calendar =39;
	
	$last_week_to_reactivate_user =($season_start+14);
	
	$season_end = ($season_start + 17);
	
	$_curr_week = $curr_week_by_calendar - $season_start;
	//Condition on week about the period of season.
	
	if($curr_week_by_calendar < $season_start)
	{
		$err_week = "Season yet to be started";
	}
	else
	if($curr_week_by_calendar > $season_end)
	{
		$err_week = "This Season ends. Now wait for the next season";
	}
	else 
	if(($curr_week_by_calendar >= $season_start)&&($curr_week_by_calendar <= $season_end))
	{
		$isSeasonWeek = true;
	}
	
	//Condition on week about reactivation.
	if($curr_week_by_calendar < $season_start)
	{
		$err_activation_week = "Season yet to be started";
	}
	else
	if($curr_week_by_calendar > $last_week_to_reactivate_user)
	{
		$err_activation_week = "Week extends beyond the 14th week.. Now you cannot reactivate the user.";
	}
	else 
	if(($curr_week_by_calendar >= $season_start)&&($curr_week_by_calendar <= $last_week_to_reactivate_user))
	{
		$isActivationWeek = true;
	}

?>