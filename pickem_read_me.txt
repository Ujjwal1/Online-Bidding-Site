DOCUMENTATION AND IDEA BEHIND THE "SUICIDE PICK POOL" http://topherball.com

1. Default WEEK: For testing purpose I had set a current week to be a constant 44 (a number between 35 to 52 , as NFL falls in between these weeks of the year). 
	You can change it from from the file at, /suicide_pick/includes/weekdetail.php  and test for various conditions on week.
	
2. CHANGE IN DATABASE Schema:   In 'schedule table' adding lock(0 or 1) | Lock, set:1 not-set:0 , & result(team ID) columns. In result we put the teamID of the winning team. ;
								In 'user table' adding 'Activation' and 'points' columns. 		Activation state(Initial:0 ; Deactivated once: 1, Reactivated: 2, Permanently deactivated: 3 or more)
								Removing the points column from the 'pick table'
								
3. LOGIN SCREEN: 	It shows the login panel, as well as all the users' pick for the latest week and ,week by week, all users past picks' history.


***ADMIN PANEL***

2. Upload Result: In this section there is a text area in which you have to enter the week, for which you have to upload result.
				  On submitting the week, you get all the remaining matches of the week whose result hasn't been uploaded yet.
				  Select the match and enter the Scores and extra time of both the teams, Server will automatically decide for the win/ lose/clash.
				  And in this way you are done with the result upload.

3. Set Default Pick: Here you can set a default pick for all those users who hadn't entered their pick until the last of the week.
					 Admin simply choose any of the week from the schedule provided.
					 
4. Lock the Match: It locks the Match, as well as the user's pick corresponding to the match(if not done by user).
					Locked teams(by user), or teams of the locked matches(to be done by admin) have public visibility, else private.
					
5.Reactivate user:  	It gives a list of all "Deactivated users" and option to reactivate them. Note: It doesn't show the permanently deactivated users


***User Account***  (Home > SUICIDE PICK POOL)
6. 				Picks of other users for the latest week is displayed on the top. 
				At the bottom of it, is the User's picks' History.
				Now if you are in activated-mode, then through "Pick a team" link you can enter to a page to pick your choices
				
7. Pick Team:   Search the week through the text area, meanwhile Teams selected by you are displayed at the bottom
				Select a team from the list, But the condition for selection is:
					a) You can select a team once in a season, i.e. Once you select a team you cannot select it until the whole season.
					b) From a week, one user can choose only one team, Not more than one
					c) If user hadn't selected any team throughout the week then while locking the match, Admin will set a "Default team" for all such users.
					
8. Lock Team: 	User once selected a team is not satisfied with it then he can change the team until he hasn't set the lock to the team.
				Once a team is locked by a user then he can not further change the choice, and the Pick get public.
				
9. Delete Team: If a pick is not locked then you can remove it from your list and add another team. Removal is done by "Delete Team"
				