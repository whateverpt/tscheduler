<?PHP
if($_GET["Save"] == "Save"){
	$CookieExpire = time() + (365*24*60*60);
	setcookie("TeamNames",$_GET["TeamNames"],$CookieExpire);
	setcookie("AreaNames",$_GET["AreaNames"],$CookieExpire);
	setcookie("PerGame",$_GET["PerGame"],$CookieExpire);
	setcookie("NumberOfGames",$_GET["NumberOfGames"],$CookieExpire);
	setcookie("StartTime",$_GET["StartTime"],$CookieExpire);
	setcookie("EndTime",$_GET["EndTime"],$CookieExpire);
	setcookie("GameLength",$_GET["GameLength"],$CookieExpire);
	setcookie("CallBefore",$_GET["CallBefore"],$CookieExpire);
	setcookie("TimeBetween",$_GET["TimeBetween"],$CookieExpire);
	setcookie("StartDate",$_GET["StartDate"],$CookieExpire);
	setcookie("Saved","Saved",$CookieExpire);
	setcookie("GCS",$_GET["GCS"],$CookieExpire);
	setcookie("DCS",$_GET["DCS"],$CookieExpire);
	setcookie("CCS",$_GET["CCS"],$CookieExpire);
	setcookie("Best",$_GET["Best"],$CookieExpire);
	setcookie("STime",$_GET["STime"],$CookieExpire);
	setcookie("SGame",$_GET["SGame"],$CookieExpire);
	setcookie("FillT",$_GET["FillT"],$CookieExpire);
	setcookie("allSchedule",$_GET["allSchedule"],$CookieExpire);
	setcookie("outputStyle",$_GET["outputStyle"],$CookieExpire);
	setcookie("Header",$_GET["Header"],$CookieExpire);
}

?>
<html>
<head>
<style>
	@media screen {
		div.noprint {
			visiblity: inline;
		}
	}	

	@media print{
		div.noprint{
			display: none;
			visibility: collapse;
		}
		table.pageBreak{
			page-break-after:always;
		}
	}

	
</style>
<title>Tournament Scheduler</title>
<script language="JavaScript">
// 3 team fucked.
// test
function TimeChanged(){

	if(document.forms["main"].STime.checked == 1){
		return 1;
	}

	// calc time for tournament.....
	StartTime = document.forms["main"].StartTime.value;
	EndTime = document.forms["main"].EndTime.value;

	SArray = StartTime.split(":");
	EArray = EndTime.split(":");


	Time1 = (SArray[0] * 60) + parseInt(SArray[1]);	
	Time2 = (EArray[0] * 60) + parseInt(EArray[1]);
	Time3 = Time2 - Time1;

	BMinutes = document.forms["main"].TimeBetween.value;

	// divide by number of games....
	Minutes = Math.floor(Time3 / document.forms["main"].NumberOfGames.value) - BMinutes;


	Time4 = Minutes;
	document.forms["main"].GameLength.value = Time4;
}

function FillToRound(){

	if(document.forms["main"].SGame.checked == "1"){
		return 1;
	}

	// figuire out number of teams
	Teams = document.forms["main"].TeamNames.value;
	TeamNames = Teams.split(",");
	NumberOfTeams = TeamNames.length;

//	window.alert(NumberOfTeams);

	// figuire out teams per game
	PerGame = document.forms["main"].PerGame.value;
	PerGameNames = PerGame.split(",");
	NumberPerGame = PerGameNames.length;
	//Count1!/(Count2!*(Count1-Count2)!)
	document.forms["main"].NumberOfGames.value = Factorial(NumberOfTeams)/(Factorial(NumberPerGame)*Factorial(NumberOfTeams-NumberPerGame));
	
	TimeChanged();
}

function AgainstEachTeam(){

	// figuire out number of teams
	Teams = document.forms["main"].TeamNames.value;
	TeamNames = Teams.split(",");
	NumberOfTeams = TeamNames.length;


	// figuire out teams per game
	PerGame = document.forms["main"].PerGame.value;
	PerGameNames = PerGame.split(",");
	NumberPerGame = PerGameNames.length;
	document.forms["main"].NumberOfGames.value = (Factorial(NumberOfTeams)/(Factorial(NumberPerGame)*Factorial(NumberOfTeams-NumberPerGame))) * document.forms["main"].againstEach.value;
	
	TimeChanged();
}

function Factorial(din){
	$out = 1;
	for($i=2;$i<=din;$i++){
		$out *= $i;
	}

	return $out;
}

</script>
</head>

<?php
require "../../../horizontal/horizontal.php";
$myHits = 0;
$hits = fopen("hits.txt","r");
$myHits = chop(fgets($hits));
$myHits += 1;
fclose($hits);
$hits = fopen("hits.txt","w");
fwrite($hits,(string)$myHits);
fclose($hits);

//error_reporting(E_ALL);
define('ROOT_PATH',dirname(__FILE__).'/');
include_once( ROOT_PATH.'inc/common.inc.php' );
include_once( ROOT_PATH.'inc/leagues.class.php' );
include_once( ROOT_PATH.'inc/resource.class.php' );
include_once( ROOT_PATH.'inc/team.class.php' );
include_once( ROOT_PATH.'inc/date.class.php' );

$Error = "";
$Success = 1;
$Leagues= new League();
$Primes = array(3,7,11,13);

//////////// output form
if(($_GET["Posted"] != "True") && ($_COOKIE["Saved"] != "Saved")){
	$TeamNamesList = "Team 1, Team 2, Team 3";
	$AreaNamesList = "Area 1";
	$PerGameList = "Red, Green";
	$NumberOfGames = 3;
	
	$StartTime = "8:00";
	$EndTime = "9:00";
	$GameLength = "15";
	$CallBefore = "0";
	$TimeBetween = "02";
	$StartDate = "8/17/2011";

	$GCS = 2;
	$CCS = 2;
	$DCS = 2;

} else if(($_COOKIE["Saved"] == "Saved") && ($_GET["Posted"] != "True")){
		$CookieExpire = time() + (365*24*60);
		$TeamNamesList = $_COOKIE["TeamNames"];
		$AreaNamesList = $_COOKIE["AreaNames"];
		$PerGameList = $_COOKIE["PerGame"];
		$NumberOfGames = $_COOKIE["NumberOfGames"];
		$StartTime = $_COOKIE["StartTime"];
		$EndTime = $_COOKIE["EndTime"];
		$GameLength = $_COOKIE["GameLength"];
		$CallBefore = $_COOKIE["CallBefore"];
		$TimeBetween = $_COOKIE["TimeBetween"];
		$StartDate = $_COOKIE["StartDate"];
		$GCS = $_COOKIE["GCS"];
		$CCS = $_COOKIE["CCS"];
		$DCS = $_COOKIE["DCS"];

		$Header = $_COOKIE["Header"];

		$Best = ReturnIf("Best",1,"Checked");
		$STime = ReturnIf("STime",1,"Checked");
		$SGame = ReturnIf("SGame",1,"Checked");
		$FillT = ReturnIf("FillT",1,"Checked");
		$AllS = ReturnIf("allSchedule","yes","Checked");

		
		$CDLD = ReturnIf("outputStyle","CDL","Selected");
		$TableD	= ReturnIf("outputStyle","Table","Selected");
		$TDLD = ReturnIf("outputStyle","TDL","Selected");
		$PreFormatD = ReturnIf("outputStyle","PreFormat","Selected");

} else {

	//////////// test

	/// Name Block
	// TeamNames
	$TeamNames = NamesListTest($_GET["TeamNames"]);
	if(count($TeamNames) == 0){
		$Success = 0;
		$Error .= "Names list is invalid, check for trailing comma or unnamed team<BR>";
	}
	$TeamNames = StripLeadingTrailingSpaces($TeamNames);
	$NumberOfTeams = count($TeamNames);
	$TeamNamesList = $_GET["TeamNames"];

	// AreaNames
	$AreaNames = NamesListTest($_GET["AreaNames"]);
	if(count($AreaNames) == 0){
		$Success = 0;
		$Error .= "Area list is invalid, check for trailing comma or unnamed team<BR>";
	}
	$AreaNames = StripLeadingTrailingSpaces($AreaNames);
	$NumberOfAreas = count($AreaNames);
	$AreaNamesList = $_GET["AreaNames"];

	// NumberOfTeams
	$PerGame = NamesListTest($_GET["PerGame"]);
	if((count($PerGame) == 0) || (count($PerGame) > 4)){
		$Success = 0;
		$Error .= "Colors list is invalid, check for trailing comma or unnamed team<BR>";
	}
	$PerGame = StripLeadingTrailingSpaces($PerGame);
	$NumberPerGame = count($PerGame);
	$PerGameList = $_GET["PerGame"];

	/// Number Block
	// NumberOfGames
	if(!NumberTest($_GET["NumberOfGames"])){
		$Success = 0;
		$Error .= "Number Of Games must be a number > 0 <BR>";
	}

	$NumberOfGames = $_GET["NumberOfGames"];

	/// Time Block
	// StartTime
	if(!(TimeTest($_GET["StartTime"]))){
		$Success = 0;
		$Error .= "Start time needs to be a valid 24 hour time, such as 08:00:00 being 8 am<BR>";
	}
	
	$StartTime = $_GET["StartTime"];

	// end time
	$EndTime = $_GET["EndTime"];

	if(!(TimeTest($_GET["EndTime"]))){
		$Success = 0;
		$Error .= "End time needs to be a valid 24 hour time, such as 08:00:00 being 8 am<BR>";
	}

	if(!(StartBeforeEnd($StartTime,$EndTime))){
		$Success = 0;
		$Error .= "Start time must be before end time<BR>";
	}

	
	// GameLength
	if(!(NumberTest($_GET["GameLength"]))){
		$Success = 0;
		$Error .= "Game Length needs to be positive<BR>";
	}	

	$GameLength = $_GET["GameLength"];

	//CallBefore
	if((!(NumberTest($_GET["CallBefore"])) && ($_GET["CallBefore"] != 0))){
		$Success = 0;
		$Error .= "Call before needs to be a length of time or zero<BR>";
	}

	// WHY IS THIS COMMENTED OUT???
	//$CallBefore = $_GET["CallBefore"];

	// TimeBetween
	if(!(NumberTest($_GET["TimeBetween"]))){
		$Success = 0;
		$Error .= "Time Between needs to be a length of time, such as 00:02:00 being 2 minutes between<BR>";
	}

	$TimeBetween = $_GET["TimeBetween"];

	// Date
	if(!(DateTest($_GET["StartDate"]))){
		$Success = 0;
		$Error .= "Date must be MM/DD/YYYY, does not check if DD is within the months range<BR>";
	}

	// CCS,GCS,DCS
	if(!(NumberTest($_GET["CCS"]))){
		$Sucess = 0;	
		$Error .= "Color Count Score must a number and non-zero<BR>";
	}
	$CCS = $_GET["CCS"];

	if(!(NumberTest($_GET["GCS"]))){
		$Sucess = 0;	
		$Error .= "Game Count Score must a number and non-zero<BR>";
	}
	$GCS = $_GET["GCS"];

	if(!(NumberTest($_GET["DCS"]))){
		$Sucess = 0;	
		$Error .= "Distance Count Score must a number and non-zero<BR>";
	}
	$DCS = $_GET["DCS"];


	$StartDate = $_GET['StartDate'];
}

if(($Success != 1) || ($_GET["Posted"] != "True")){
	$FillToRound = "FillToRound()";
	$TimeChanged = "TimeChanged()";

	print <<<END
	<h1>Tag Da Planet's Tournament Scheduler</h1>
	<a href="" onclick="window.open('halp.html','herp')">Halp</a><BR>
	<Font color="red">$Error</font>
	<form method="GET" name="main">
	<table>

	<tr>
	<td width="10%"><B>Tournament Setup</B></TD>
	<td width="90%" align="left"></TD>
	</tr>


	<tr>
	<td width="10%">Team Names</TD>
	<td width="90%" align="left"><textarea name="TeamNames" rows="3" cols="40" onchange="$FillToRound">$TeamNamesList</textarea></TD>
	</tr>

	<tr>
	<td width="10%">Names of teams<BR>in each game</TD>
	<td width="90%" align="left"><textarea name="PerGame" rows="2" cols="40" onchange="$FillToRound">$PerGameList</textarea></TD>
	</tr>

	<tr>
	<td width="10%">Number of Games</TD>		
	<td width="90%" align="left"><input name="NumberOfGames" type="text" size="50" value="$NumberOfGames"></TD>
	</tr>

	<tr>
	<td width="10%">Areas</TD>
	<td width="90%" align="left"><textarea name="AreaNames" rows="2" cols="40" onchange="$FillToRound">$AreaNamesList</textarea></TD>
	</tr>

	<tr>
	<td width="10%"><B>Time</B></TD>
	<td width="90%" align="left"></TD>
	</tr>

	<tr>
	<td width="10%">Tournament Start Time<BR>HH:MM</TD>
	<td width="90%" align="left"><input type="text" name="StartTime" size="50" value="$StartTime" onchange="$TimeChanged"></TD>
	</tr>

	<tr>
	<td width="10%">Tournament End Time<BR>HH:MM</TD>
	<td width="90%" align="left"><input type="text" name="EndTime" size="50" value="$EndTime" onchange="$TimeChanged"></TD>
	</tr>

	<tr>
	<td width="10%">Game Length</TD>
	<td width="90%" align="left"><input type="text" name="GameLength" size="50" value="$GameLength"></TD>
	</tr>

	<tr>
	<td width="10%">Time Between</TD>
	<td width="90%" align="left"><input type="text" name="TimeBetween" size="50" value="$TimeBetween" onchange="$TimeChanged"></TD>
	</tr>

	<tr>
	<td width="10%">Tournament Date<BR>DD:MM:YYYY</TD>
	<td width="90%" align="left"><input type="text" name="StartDate" size="50" value="$StartDate"></TD>
	</tr>	

	<tr>
	<td width="10%">Call X<BR>Minutes Before</TD>
	<td width="90%" align="left"><input type="text" name="CallBefore" size="50" value="$CallBefore"></TD>
	</tr>

	<tr>
	<td width="10%"><B>Options</B></TD>
	<td width="90%" align="left"></TD>
	</tr>
	<tr>
	<td width="10%">Header</TD>
	<td width="90%" align="left"><textarea name="Header" rows="2" cols="40">$Header</textarea></TD>
	</tr>
	</table>

	<table width="100%">
	<tr>
	<td width="20%">
	<input type="checkbox" value="1" name="Best" $Best>Place as best as possible<BR>
	<input type="checkbox" value="1" name="STime" $STime>Stop screwing with the time<BR>
	<input type="checkbox" value="1" name="SGame" $SGame>Stop screwing with the number of games<BR>
	<input type="checkbox" value="1" name="FillT" $FillT>Fill Time Slots before moving on<BR>
	<input type="text" value="1" name="againstEach" size="50" onchange="AgainstEachTeam()">Play every team x times<BR>
	<select name="outputStyle"><option value="CDL" $CDLD>Comma Delimted List<option value="Table" $TableD>Table<option value="TDL" $TDLD>Tab Delimited List<option value="PreFormat" $PreFormatD>Pre formated text</select><BR>
	<input type="Checkbox" name="allSchedule" value="yes" $AllS> Print individual schedules <BR>
 	</td>
	</tr>
	</table>
	<table>

	<tr>
	<td width="10%"><B>Advanced</B></td>
	<td width="90%">Nothing will explode if you mess with this so try away</td>
	</tr>


	<tr>
	<td width="10%">Game Count Score</td>
	<td width="90%"><input type="text" name="GCS" size="50" value="$GCS"></td>
	</tr>

	<tr>
	<td width="10%">Distance Count Score</td>
	<td width="90%"><input type="text" name="DCS" size="50" value="$DCS"></td>
	</tr>

	<tr>
	<td width="10%">Color Count Score</td>
	<td width="90%"><input type="text" name="CCS" size="50" value="$CCS"></td>
	</tr>
	</table>
	<input type="submit" value="Submit"><input type="reset" value="Reset"><input type="checkbox" name="Save" value="Save"> Save
	<input type="hidden" value="True" name="Posted">
	</form>
	
END;
}

if(($Success == 1) && ($_GET["Posted"] == "True")){

	// ok... we need to built this......

	$Leagues->GameDuration = $GameLength;

	// straightish assignment
	$Leagues->PerGame = $NumberPerGame;
	$Leagues->Games = $NumberOfGames;
	$Leagues->MinutesBetweenGames = $TimeBetween;

	// build dt
	$UseableDT = array();
	$UseableDT[0] = new Date();
	
	$UseableDT[0]->StartTime = $StartTime; // HH:MM
	$UseableDT[0]->StartDate = $StartDate; // MM/DD/YYYY
	$UseableDT[0]->EndTime = $EndTime; // HH:MM
	$UseableDT[0]->EndDate = $StartDate; // MM/DD/YYYY

	// build resources....
	$ResourceArray = array();
	for($i=0;$i<$NumberOfAreas;$i++){
		$ResourceArray[$i] = new Resource();
		
		$ResourceArray[$i]->ResourceName = $AreaNames[$i];
		$ResourceArray[$i]->UsedDateTimes = $UseableDT;

	}

	$Leagues->Resources = $ResourceArray;

	// build teams
	$TeamsArray = array();
	for($i=0;$i<$NumberOfTeams;$i++){

		$TeamArray[$i] = new Team();
		$TeamArray[$i]->TeamName = $TeamNames[$i];
	}

	$Leagues->Teams = $TeamArray;
	$Leagues->CallBefore = $_GET["CallBefore"];
	$Leagues->Best = $_GET["Best"];
	if(1 == $_GET["FillT"]){
		$Leagues->Fill = $_GET["FillT"];
	}

	$Leagues->GameCountScore = $GCS;
	$Leagues->DistanceScore = $DCS;
	$Leagues->ColorScore = $CCS;

	print "<div class=\"noprint\">";
	$myResult = GenerateLeagueSchedule($Leagues,"","");
	print "</div>";

	//	Unimplemented <select name="outputStyle"><option value="CDL">Comma Delimted List<option value="Table">Table<option value="TDL">Tab Delimited List<option value="PreFormat">Pre formated text</select><BR>
	// Unimplemented <input type="Checkbox" name="allSchedule" value="yes"> Print individual schedules <BR>


	if($_GET["outputStyle"] == "Table"){
		Table($myResult,$Leagues,-1);

		if($_GET["allSchedule"] == "yes"){
			for($j=0;$j<count($TeamNames);$j++){
				Table($myResult,$Leagues,$TeamNames[$j]);
			}

		}
	
	} else if($_GET["outputStyle"] == "CDL"){
		CDL($myResult,$Leagues,-1);
		if($_GET["allSchedule"] == "yes"){
			for($j=0;$j<count($TeamNames);$j++){
				CDL($myResult,$Leagues,$TeamNames[$j]);
			}

		}
	} else if($_GET["outputStyle"] == "TDL"){
		TDL($myResult,$Leagues,-1);
		if($_GET["allSchedule"] == "yes"){
			for($j=0;$j<count($TeamNames);$j++){
				TDL($myResult,$Leagues,$TeamNames[$j]);
			}

		}
	} else if($_GET["outputStyle"] == "PreFormat"){
		Text($myResult,$Leagues,-1);
		if($_GET["allSchedule"] == "yes"){
			for($j=0;$j<count($TeamNames);$j++){
				Text($myResult,$Leagues,$TeamNames[$j],-1);
			}

		}
	}




}

function GenerateLeagueSchedule($Leagues, $Resources, $StartDateTime ) {


	$GameOrders = TeamOrders($Leagues->PerGame);

	$EndDT = 0;
	$Teamz[0] = "team_home";
	$Teamz[1] = "team_away";
	$Teamz[2] = "team_3";
	$Teamz[3] = "team_4";

	/// init block
	for($i=0;$i<sizeof($Leagues->Teams);$i++){
		$GameCount[$i] = 0;
		$Distance[$i] = 0;
		for($j=0;$j<$Leagues->PerGame;$j++){
			$Position[$i][$j] = 0;
		}
		$CPositionMaster[$i] = 0;
	}
	
	for($i=0;$i<$Leagues->PerGame;$i++){
		for($j=0;$j<$Leagues->PerGame;$j++){
			$LowestColorsMaster[$i][$j] = 0;
		}
		$MarksMaster[$i] = 0;
		$ColorGamesList[$i] = $i+1;
	}

	$Error = 0;
	$Placed = 0;
	$ReallyHighInt = 100000000;
	$EndTime = 0;

	$GamesMaster = FillGames(sizeof($Leagues->Teams),$Leagues->PerGame);
	$GamesList = $GamesMaster;

	for($i=0;$i<sizeof($GamesMaster);$i++){
		$GameScoreMaster[$i] = 0;
	}


	if($Leagues->Games == NULL){
		$Leagues->Games = sizeof($GamesMaster);
	}

	$Loop = 0;
	$AverageColor = 0;
 	
	// ok so we loop... loop
	//for($i=0;$i<=$Leagues->Games;$i++){
	//for($i=0;$i<$Leagues->Games;$i++){
	$i=0;
	while($i<$Leagues->Games){	
		if(sizeof($GamesList) == 0){
			$GamesList = $GamesMaster;
		}

		// this is probably in the wrong place
		$GameScore = $GameScoreMaster;

		// Copy teams and setup scores
		$TeamsTemp = $Leagues->Teams;
		
		
		// Remove Junk and score...
		for($j=0;$j<sizeof($GamesList);$j++){
			// running out of things to code for here.... shit.
			// for position x.......
			// Color Distance,GameCount,DistanceCount
			for($k=0;$k<$Leagues->PerGame;$k++){
				$GameScore[$j] -= pow($Leagues->GameCountScore,$GameCount[$GamesList[$j][$k]]); // number of games played
				$GameScore[$j] += pow($Leagues->DistanceScore,$Distance[$GamesList[$j][$k]]); // distance from last game
				for($l=0;$l<$Leagues->PerGame;$l++){
					$GameScore[$j] -= pow($Leagues->ColorScore,($AverageColor - floor($Position[$GamesList[$j][$k]][$l]))+10); // average color
//					print($AverageColor - floor($Position[$GamesList[$j][$k]][$l]));
				}
				//print $Leagues->GameCountScore;
				//print $Leagues->DistanceScore;
				//print $Leagues->ColorScore;
				
			}
		


			for($k=0;$k<$Leagues->PerGame;$k++){
				// requested off??
				for($l=0;$l<sizeof($Leagues->Teams->RestrictedDateTimes);$l++){
					if($Leagues->Resources[$Placed]->UsedDateTimes[$Leagues->Resources[$Placed]->UsedDT]->Between($Leagues->Teams->RestrictedDateTimes[$k]->Time,$Leagues->Teams->RestrictedDateTimes[$k]->Date)){
						$GameScores[$j] = $ReallyHighInt;
					}
				}
			}
			if(($Loop != 0) && ($Leagues->Best != 1)){
				// same time???
				// unfuck this still
				for($k=0;$k<sizeof($return);$k++){
					//$GameStartTime = $return[$k]["GameDateTime"]->StartTime;
					//$GameStartDate = $return[$k]["GameDateTime"]->StartDate;
					$GameStartTime = $Leagues->Resources[$Placed]->EndDT->StartTime;
					$GameStartDate = $Leagues->Resources[$Placed]->EndDT->StartDate;

					$GameEndStuff = explode(":",$GameStartTime);
					$GameEndTimeMM = $GameEndStuff[1] + $Leagues->GameDuration;// + $Leagues->GameDuration;
					$GameEndTime = $GameEndStuff[0] . ":" . $GameEndTimeMM;
					$GameEndDate =  $GameStartDate;

					// am i fucking retarded???????
					
					if((($return[$k]["GameDateTime"]->Between($GameStartTime,$GameStartDate)) || ($return[$k]["GameDateTime"]->Between($GameEndTime,$GameEndDate))) && (TeamMatches($Leagues->PerGame,$return[$k],$GamesList[$j],$Leagues->Teams))){
						$GameScore[$j] = $ReallyHighInt;
					}
				}

				

			}
		}

		/// Sort Games
		// place lowest member first..... or first lowest....
		$LowestScore = $GameScore[0];
		$LowestLocation = 0;
		for($j=1;$j<sizeof($GamesList);$j++){
			if($GameScore[$j] <= $LowestScore){
				$LowestScore = $GameScore[$j];
				$LowestLocation = $j; 
			} 
		}
		
		// Time Fadoodle
		$SelectedResource = $Leagues->Resources[$Placed]->ResourceName; // this line crashes the whole works.....????
		$DateTimesUsed = $Leagues->Resources[$Placed]->UsedDT;	
		$SelectedDate = $Leagues->Resources[$Placed]->UsedDateTimes[$DateTimesUsed]; // why is this so fucked		

		if($Leagues->Resources[$Placed]->EndDT == 0){
			$EndTime = $Leagues->Resources[$Placed]->UsedDateTimes[$Leagues->Resources[$Placed]->UsedDT]->StartTime;
			$EndDate = $Leagues->Resources[$Placed]->UsedDateTimes[$Leagues->Resources[$Placed]->UsedDT]->EndDate;

			$Leagues->Resources[$Placed]->EndDT = IncrimentTime($EndDate,$EndTime,($Leagues->GameDuration));
			
			$DateTime = new Date();
			$DateTime->StartTime = $SelectedDate->StartTime;
			$DateTime->StartDate = $SelectedDate->StartDate;
			$DateTime->EndDate = $Leagues->Resources[$Placed]->EndDT->StartDate;
			$DateTime->EndTime = $Leagues->Resources[$Placed]->EndDT->StartTime;

			//$OutputDate = $SelectedDate->DDate($Leagues->Resources[$Placed]->EndDT);
			$OutputDate = FullTime($SelectedDate->StartTime) . " - " . FullTime($Leagues->Resources[$Placed]->EndDT->StartTime) . " " . $SelectedDate->StartDate;
			$CallBefore = DecrementTime($EndDate,$EndTime,($Leagues->CallBefore));
			$CallBeforeString = FullTime($CallBefore->StartTime) . " - " . FullTime($CallBefore->StartTime) . " " . $CallBefore->StartDate; 
				
		} else {
			$NextDT = IncrimentTime($Leagues->Resources[$Placed]->EndDT->StartDate,$Leagues->Resources[$Placed]->EndDT->StartTime,($Leagues->MinutesBetweenGames));
//			$CallBefore = DecrementTime($Leagues->Resources[$Placed]->EndDT->StartDate,$Leagues->Resources[$Placed]->StartTime,($Leagues->CallBefore));
			$CallBefore = DecrementTime($NextDT->StartDate,$NextDT->StartTime,($Leagues->CallBefore));
		
			$Leagues->Resources[$Placed]->EndDT = IncrimentTime($NextDT->StartDate,$NextDT->StartTime,($Leagues->GameDuration));		
				
			$DateTime = new Date();
			$DateTime->StartTime = $NextDT->StartTime;
			$DateTime->StartDate = $NextDT->StartDate;
			$DateTime->EndDate = $NextDT->StartDate;
			$DateTime->EndTime = $Leagues->Resources[$Placed]->EndDT->StartTime;
 
			$OutputDate = FullTime($NextDT->StartTime) . " - " . FullTime($Leagues->Resources[$Placed]->EndDT->StartTime) . " " . $NextDT->StartDate;
			$CallBeforeString = FullTime($CallBefore->StartTime) . " - " . FullTime($CallBefore->StartTime) . " " . $CallBefore->StartDate; 
		}
	

		if($LowestScore == $ReallyHighInt){
			print "ERROR: Could not place game moving to next <BR>";	
			$Error++;
		} else {
			$Error = 0;

			//print count($GameOrders);

			// score and mark lowest.....
			// colorize...
			for($j=0;$j<count($GameOrders);$j++){
				$CGameScore[$j] = 0;
				for($k=0;$k<$Leagues->PerGame;$k++){
					// ok so we need...	$GamesList[$LowestLocation][$j]
					// 					$Position[$GamesList[$LowestLocation][$j]][$GameOrders[$j][$k]]
					$CGameScore[$j] += pow($Leagues->ColorScore,($Position[$GamesList[$LowestLocation][$k]][$GameOrders[$j][$k]])); //screwed
					
				}	
				if($j==0){
					$CGScoreLowest = $CGameScore[$j];
					$CGScoreLowPos = 0;
				} else if($CGScoreLowest >= $CGameScore[$j]){
					$CGScoreLowest = $CGameScore[$j];
					$CGScoreLowPos = $j;
				} 
				//print "$CGameScore[$j]<BR>";
			}

			//print "<BR>";
			//print "low $CGScoreLowest low <BR>";
			//print "low pos $CGScoreLowPos low pos";
			//print "<BR><BR>";	


			// time fadoodle cut from here		
			// Place
	
			if($Leagues->CallBefore > 0){
				$return[]=array('game_id'=>"Call Before " . ($Game_id + 1),'team_home'=>$Leagues->Teams[$GamesList[$LowestLocation][$GameOrders[$CGScoreLowPos][0]]]->TeamName,'team_away'=>$Leagues->Teams[$GamesList[$LowestLocation][$GameOrders[$CGScoreLowPos][1]]]->TeamName,'game_resource'=>$SelectedResource,'game_Date'=>$CallBeforeString,'GameDateTime'=> $DateTime); // fucked			
		
				for($j=2;$j<$Leagues->PerGame;$j++){
					$TeamNum = $j+1;
					$return[sizeof($return)-1]["team_" . $TeamNum] = $Leagues->Teams[$GamesList[$LowestLocation][$GameOrders[$CGScoreLowPos][$j]]]->TeamName;
				}
			}


			$Game_id = $i+1;
			$return[]=array('game_id'=>$Game_id,'team_home'=>$Leagues->Teams[$GamesList[$LowestLocation][$GameOrders[$CGScoreLowPos][0]]]->TeamName,'team_away'=>$Leagues->Teams[$GamesList[$LowestLocation][$GameOrders[$CGScoreLowPos][1]]]->TeamName,'game_resource'=>$SelectedResource,'game_Date'=>$OutputDate,'GameDateTime'=>$DateTime); // fucked			
			for($j=2;$j<$Leagues->PerGame;$j++){
				$TeamNum = $j+1;
				$return[(sizeof($return)-1)]["team_" . $TeamNum] = $Leagues->Teams[$GamesList[$LowestLocation][$GameOrders[$CGScoreLowPos][$j]]]->TeamName;
			}

		
			/// Distance and Games
			//	-1 selected distances.....
			// and +1 colors
			for($j=0;$j<$Leagues->PerGame;$j++){
				$Distance[$GamesList[$LowestLocation][$j]] = -1;
				$GameCount[$GamesList[$LowestLocation][$j]]++;
				$Position[$GamesList[$LowestLocation][$j]][$GameOrders[$CGScoreLowPos][$j]]++; // aha this is ugly
			}
			

			array_splice($GamesList,$LowestLocation,1);
			$i++;
			;
		}

		// Incriment required
		$AverageColor = sizeof($return) / $Leagues->Games;
		for($j=0;$j<count($Distance);$j++){
				$Distance[$j]++;
		}
		
		// Cleanup
		unset($Scores);

		$Loop++;

		// is this fucked... ????
		if($Leagues->Fill == 0){
			$Placed = ($Placed+1) % sizeof($Leagues->Resources);
		}

		if(!($Leagues->Resources[$Placed]->UsedDateTimes[$Leagues->Resources[$Placed]->UsedDT]->Between($Leagues->Resources[$Placed]->EndDT->StartTime,$Leagues->Resources[$Placed]->EndDT->StartDate))){

			$Leagues->Resources[$Placed]->UsedDT++;

			if($Leagues->Resources[$Placed]->UsedDT >= count($Leagues->Resources[$Placed]->UsedDateTimes)){
				$Leagues->Resources[$Placed]->UsedDT = 0;
			}
		
			if($Leagues->Fill == 1){
				$Placed = ($Placed+1) % sizeof($Leagues->Resources);
			}
	
			$EndDT = 0;
		} 
	
		if($Error == 10){
			print "Error: Could not place with theese options, please try fill best as possible<BR>";
			return $return;
		}
	}			
	return $return;	
}

function FillGames($NumberOfTeams,$NumberPerGame){

	$BuildFinished = 0;

	for($j=0;$j<$NumberPerGame;$j++){
		$Games[0][$j] = $j;
	}

	for($i=0;$i<$NumberPerGame;$i++){
		$CurrentGame[$i] = $i+1;
	}

	for($i=0;$i<$NumberPerGame;$i++){
		$LastGame[] = ($NumberOfTeams-($NumberPerGame-1)) + $i;
	}

	while($BuildFinished==0){

		$IncrimentFinished = 0;
		$Position = count($CurrentGame)-1;
		$Count = 0;

	
		while($IncrimentFinished == 0){
			$CurrentGame[$Position]++;

			if(($CurrentGame[$Position] == (($NumberOfTeams+1)-$Count)) && ($NumberPerGame-1 > $Count)){

				$Position--;
				$Count++;
			} else {
				$IncrimentFinished = 1;
			}

		}

		// out of bounds??
		$Base = $CurrentGame[(count($CurrentGame)-1) - ($Count)];
		for($i=0;$i<=$Count;$i++){
			$CurrentGame[((count($CurrentGame)-1) - $Count) + $i] = $Base+$i;

		}

		// Place
		$Games[][0] = $CurrentGame[0] -1;
		for($j=1;$j<$NumberPerGame;$j++){
			$Games[count($Games)-1][$j] =  $CurrentGame[$j]-1;
		}


		// this is retarded complex... why'd i write it like this???
		$BuildCompletionCount = 0;
		for($i=0;$i<$NumberPerGame;$i++){
			if($CurrentGame[$i] == $LastGame[$i]){
				$BuildCompletionCount++;
			}
		}	

		if($BuildCompletionCount == $NumberPerGame){
			$BuildFinished = 1;
		}

	}	

	return $Games;
}

function TeamOrders($PerGame){

	$Count = 0;

	if($PerGame == 2){
		$Return[0][0] = 0;
		$Return[0][1] = 1;
		$Return[1][0] = 1;
		$Return[1][1] = 0;
		return $Return;
	}

	// well shit if this doesn't work...
	// just need to change this number to....
	$Combos = pow($PerGame,$PerGame);

	for($i=0;$i<$Combos;$i++){
		$Count+= $PerGame;
		$CountTemp = $Count;
		$Result = 0;
		$CurrentPower = $PerGame;
		$Loopy = 0;
		for($j=0;$j<$PerGame;$j++){
			$TeamOrders[$i][$j] = 0;
		}

		while($CountTemp > 0){
			$Divsor = pow($PerGame,$CurrentPower);		
			$Result = $CountTemp / $Divsor;
			$CountTemp = $CountTemp % $Divsor;	
			$CurrentPower--;

			$TeamOrders[$i][$Loopy] = floor($Result);	
			$Loopy++;
		}
	}

	// Remove
	for($i=0;$i<sizeof($TeamOrders);$i++){

		$InternalMatches = 0;

		for($j=0;$j<$PerGame;$j++){	
			for($l=0;$l<$PerGame;$l++){
	
				if(($TeamOrders[$i][$j] == $TeamOrders[$i][$l]) && ($j != $l)){
					$InternalMatches++;
				}
				
			}
		}

		if ($InternalMatches > 0){
				//print "TeamOrders $j removed";
				array_splice($TeamOrders,$i,1);
				$i--;
		} else {

			for($j=$i+1;$j<sizeof($TeamOrders);$j++){
				$MatchCount = 0;
				//print "i $i j $j k $k<BR>";
		
				for($k=0;$k<$PerGame;$k++){
					if($TeamOrders[$i][$k] == $TeamOrders[$j][$k]){
						$MatchCount++;
					}
				}	
		
				if($MatchCount == $PerGame){
					array_splice($TeamOrders,$j,1);
					$j--;
				}  
			}
		}
	}

	//for($i=0;$i<count($TeamOrders);$i++){
	//	for($j=0;$j<count($TeamOrders[$i]);$j++){	
	//		print $TeamOrders[$i][$j];
	//	}	
	//	print "<BR>";
	//}

	return $TeamOrders;
}

function TeamMatches($NumberOfTeams,$YourReturn,$CurrentTeams,$Team){

	$TeamNames[0] = "team_home";
	$TeamNames[1] = "team_away";
	for($j=2;$j<$NumberOfTeams;$j++){
		$TeamNames[$j] = "team_" . ($j+1);
	}

	for($i=0;$i<$NumberOfTeams;$i++){
		for($j=0;$j<$NumberOfTeams;$j++){
			//print $YourReturn[$TeamNames[$i]] . " == " .  $Team[$CurrentTeams[$j]]->TeamName;
	
			if($YourReturn[$TeamNames[$i]] == $Team[$CurrentTeams[$j]]->TeamName){
			//	print "<BR>";
			//	print "I'm firing on...";
			//	print $YourReturn[$TeamNames[$i]] . " == " .  $Team[$CurrentTeams[$j]]->TeamName;
			//	print "<BR>";
				return 1;
			}
		}
	}
	
	return 0;
}


function IncrimentTime($Date,$Time,$Minutes,$Hours=0,$Days=0,$Months=0,$Years=0){

		$Months = array(0,31,28,31,30,31,30,31,31,30,31,30,31);

		$TArray = explode(':',$Time);

		$TArray[1] += $Minutes;
//		print $TArray[1];
		$TArray[0] += floor($TArray[1] / 60);
		$TArray[1] %= 60;
//		print " $TArray[1] <BR>";	
		$TArray[0] += $Hours;
		
		// MM/DD/YYYY
		$DArray = explode('/',$Date);
		$DArray[1] += floor($TArray[0] / 24); 
		$TArray[0] %= 24;

		// shit.
		$CurrentMonth = $DArray[0];
		$DArray[0] += floor($DArray[1] / $Months[$DArray[0]]);
		$DArray[0] %= $Months[$CurrentMonth];
		$DArray[2] += floor($DArray[0] / 12);
		$DArray[0] %= 12;

		$DateTime = new Date();
		if($TArray[1] < 10){
			$TArray[1] = "0" .  $TArray[1];
		}
		$DateTime->StartTime = $TArray[0] . ":" . $TArray[1];
		$DateTime->StartDate = $DArray[0] . "/" . $DArray[1] . "/" . $DArray[2];

		return $DateTime;
}

function DecrementTime($Date,$Time,$Minutes,$Hours=0,$Days=0,$Months=0,$Years=0){

		$Months = array(0,31,28,31,30,31,30,31,31,30,31,30,31);

		$TArray = explode(':',$Time);

		$TArray[1] -= $Minutes;
	
		if($TArray[1] < 0){
			$TArray[0]--;
			$TArray[0]-= floor($TArray[0]/60);
			$TArray[1]+= 60;
		}

		$DArray = explode('/',$Date);
		if($TArray[0] < 0){
			$TArray[0] = 0;
			$DArray[1]--;
		}
		if($DArray[1] < 0){
			$DArray[1] = $Months[--$DArray[0]]; 
		}

		$DateTime->StartTime = $TArray[0] . ":" . $TArray[1];
		$DateTime->StartDate = $DArray[0] . "/" . $DArray[1] . "/" . $DArray[2];

		return $DateTime;
}
/*
function Codethatdidntwork(){
		// 1
			// setup color holy nested forloops batman
			for($j=0;$j<$Leagues->PerGame;$j++){
				$LowestColors = $LowestColorMaster;
				$Marks = $MarksMaster;
				for($k=0;$k<$Leagues->PerGame;$k++){
					$LowestColor[$i][0] = $Position[$GamesList[$LowestLocation][$j]][$k];
					$LCPicked = 0;
					for($l=0;$l<$Leagues->PerGame;$l++){
						// find the lowest.......
						if(($LowestColor[$i][0] < $Position[$GamesList[$LowestLocation][$j]][$k]) && ($Mark[$l] == 0)){
							$LowestColor[$i][0] = $Position[$GamesList[$LowestLocation][$j]][$k];			
							$LCPicked = $l; 
						}
						
					}
					// mark it dead
					$Mark[$LCPicked] = 1;
				}
			}
			
			// check the first three....... if they're all different numbers.....
			// can we just brute force this it seems simpler.
			// grumble grumble
			$Correct = 0;
			$CPosition;
			
			while($Correct == 0){
				$CPosition = $CPositionMaster;
				for($j=0;$j<$Leagues->PerTeam;$j++){
//					$LowestColor[$i]
//					if(
//						$CPosition[$j]++;
				}
			}
	// end 1
}
/* Debuging stuff
$GameOrders = TeamOrders(3);


$Games = FillGames(4,2);
for($i=0;$i<count($Games);$i++){
		for($j=0;$j<$Leagues->PerGame;$j++){
			print $Games[$i][$j];
		}
		print "\n";
	}
}
for($i=0;$i<sizeof($GameOrders);$i++){
	print "$i ";
	for($j=0;$j<3;$j++){
		print $GameOrders[$i][$j];
	}
	print "<BR>";
}


$junk[0][1] = "Blah";
$junk[0][2] = "Blah2";
$junk[0][3] = "Blah3";

printJunk($junk[0]);

function printJunk($blah){
	print $blah[1];
	print $blah[2];
	print $blah[3];
}

/* Create Test Data */
/*for ($iLeague = 0; $iLeague < 10; $iLeague++) {
	$LeagueName='League'.$LETTER[$iLeague];
	$League=new League($LeagueName);
	for ($iTeam = 0; $iTeam < 6; $iTeam++) {
		$TeamName=$LeagueName.'Team'.$LETTER[$iTeam];
		$Team=new Team($TeamName);
		$Leatgue->Teams[]=$Team;
	}
	if ($iLeague<7) {
		$League->Resources=array($Resources[0], $Resources[1]);	
	} else {
		$League->Resources=array($Resources[2]);
	}
	
	$Leagues[]=$League;
}
*/

function NamesListTest($List){
	$Listz = explode(",",$List);

	for($i=0;$i<count($Listz);$i++){
		str_replace("<","%3C",$Listz[$i]);
		str_replace(">","%3E",$Listz[$i]);

		if(strlen($Listz[$i]) == 0){
			$Listz = array();
		}
	}

	return $Listz;
}

function NumberTest($Number){
	if($Number <= 0){
		return 0;
	}

	return 1;
}

function TimeTest($Number){
	$Time = explode(":",$Number);

	if(count($Time) != 2){
		return 0;
	}

	if(($Time[0] > 24) || ($Time[0] < 0)){

		return 0;
	}

	if(($Time[1] > 60) || ($Time[1] < 0)){

		return 0;
	}

	return 1;
}

function DateTest($Number){
	$List = explode("/",$Number);

	if(($List[0] < 1) || ($List[0] > 12)){
		return 0;
	}

	if(($List[1] < 1) || ($List[0] > 31)){
		return 0;
	}

	if(($List[2] < 0) || (strlen($List[2]) != 4)){
		return 0;
	} 

	return 1;
}

function StartBeforeEnd($Start,$End){
	$SArray = explode(":",$Start);
	$EArray = explode(":",$End);

	if($SArray[0] > $EArray[0]){
		return 0;
	}

	return 1;
}

function StripLeadingTrailingSpaces($Strings){

	for($i=0;$i<count($Strings);$i++){

		while(substr($Strings[$i],0,1) == " "){
			$Strings[$i] = substr($Strings[$i],1);
		}

		while(substr($Strings[$i],strlen($Strings[$i]),1) == " "){
			$Strings[$i] = substr($Strings[$i],strlen($Strings[$i]) - 1,1);
		}
	}


	return $Strings;
}

function Table($myResult,$Leagues,$Team = -1){

	print "<CENTER><H1>" . $_GET["Header"] . "</h1></center>";

	if($Team != -1){
		print "<CENTER><H2>" . $Team . "</h2></center>";
	}


	print "<TABLE class=\"pageBreak\" width=\"100%\">";
	print "<TR><TD width=\"10%\">Game Number</TD><td width=\"15%\">Field</td>\n";
	$Colors = explode(",",$_GET["PerGame"]);
	StripLeadingTrailingSpaces($Colors);

	$Teamz[0] = "team_home";
	$Teamz[1] = "team_away";
	$Teamz[2] = "team_3";
	$Teamz[3] = "team_4";

	for($i=0;$i<$Leagues->PerGame;$i++){
		$Width = 30 / $Leagues->PerGame;
		$Width = $Width . "%";
		print "<TD width=\"$Width\">$Colors[$i]</TD>\n";

	}
	
	print "<TD width=\"10%\">Date</TD>";
	
	print "<TD width=\"25%\"> Results</TD>";
	
	print "</TR>\n";

	for($i=0;$i<sizeof($myResult);$i++){
	
		if($Team != -1){
			for($j=0;$j<count($Teamz);$j++){
				if($myResult[$i]["$Teamz[$j]"] == $Team){
			
					print "<TR>\n";
					print "<TD>\n";
					print $myResult[$i]["game_id"] . " ";
					print "</TD>\n";
					print "<TD>\n";
					print $myResult[$i]["game_resource"] . " ";
					print "</TD>\n";
	
					for($k=0;$k<$Leagues->PerGame;$k++){
						print "<TD>\n";
						print $myResult[$i][$Teamz[$k]] . " ";
						print "</TD>\n";
					}
				print "<TD>\n";
				print $myResult[$i]["game_Date"];
				print "</TD>\n";
				print "<TD>";
				for($k=0;$k<sizeof($Colors);$k++){
					$Spaces = 25 / (sizeof($Colors));	
						for($l=0;$l<$Spaces;$l++){
							print "_";
						}
						print "&nbsp;&nbsp;&nbsp;&nbsp";
					}
				print "</TD>";
				print "</TR>\n";
	
				}
			}	
		} else {

			print "<TR>\n";
			print "<TD>\n";
			print $myResult[$i]["game_id"] . " ";
			print "</TD>\n";
			print "<TD>\n";
			print $myResult[$i]["game_resource"] . " ";
			print "</TD>\n";
	
			for($j=0;$j<$Leagues->PerGame;$j++){
				print "<TD>\n";
				print $myResult[$i][$Teamz[$j]] . " ";
				print "</TD>\n";
			}
			print "<TD>\n";
			print $myResult[$i]["game_Date"];
			print "</TD>\n";
			print "<TD>";
			for($j=0;$j<sizeof($Colors);$j++){
				$Spaces = 25 / (sizeof($Colors));	
				for($k=0;$k< $Spaces;$k++){
					print "_";
				}
				print "&nbsp;&nbsp;&nbsp;&nbsp";
			}
			print "</TD>";
			print "</TR>\n";
			
		}
	}

	print "</table>\n";
}

function CDL($myResult,$Leagues,$Team = -1){

	print "<TABLE class=\"pageBreak\" width=\"100%\">";
	if($Team != -1){
		print "<B>$Team</B><B>R";
	}
	print "<TR><TD width=\"100%\"><TextArea rows=\"20\" cols=\"50\" onclick=\"this.select()\">";

	print $_GET["Header"];

	if($Team != -1){
		print ",$Team";
	}

	print "\n";

	print "Game Number,Field";
	$Colors = explode(",",$_GET["PerGame"]);
	StripLeadingTrailingSpaces($Colors);

	$Teamz[0] = "team_home";
	$Teamz[1] = "team_away";
	$Teamz[2] = "team_3";
	$Teamz[3] = "team_4";

	for($i=0;$i<$Leagues->PerGame;$i++){
		$Width = 30 / $Leagues->PerGame;
		$Width = $Width . "%";
		print ",$Colors[$i]";

	}
	
	print ",Date";
	
	print ",Results";
	
	print "\n";

	for($i=0;$i<sizeof($myResult);$i++){
	
		if($Team != -1){
			//for($j=0;$j<count($Teamz);$j++){
				if($myResult[$i]["$Teamz[$j]"] == $Team){
			
					print $myResult[$i]["game_id"];
					print "," . $myResult[$i]["game_resource"];
	
					for($k=0;$k<$Leagues->PerGame;$k++){
						print "," . $myResult[$i][$Teamz[$k]];
					}
					print ",";
					print $myResult[$i]["game_Date"];
					for($k=0;$k<sizeof($Colors);$k++){
						print ",";
						$Spaces = 25 / (sizeof($Colors));	
							for($l=0;$l<$Spaces;$l++){
									print "_";
							}
					}
					print "\n";
	
			//	}
			}	
		} else {

			//for($j=0;$j<count($Teamz);$j++){
				
				print "" . $myResult[$i]["game_id"];
				print "," . $myResult[$i]["game_resource"];
				for($k=0;$k<$Leagues->PerGame;$k++){
					print "," . $myResult[$i][$Teamz[$k]];
				}
				print ",";
				print $myResult[$i]["game_Date"];
				for($k=0;$k<sizeof($Colors);$k++){
					print ",";
					$Spaces = 25 / (sizeof($Colors));	
						for($l=0;$l<$Spaces;$l++){
						print "_";
					}
				}
				print "\n";
			//}	
		}	
	}

	print "</textarea></td></tr></table>\n";
}

function TDL($myResult,$Leagues,$Team = -1){

	print "<TABLE class=\"pageBreak\" width=\"100%\">";
	if($Team != -1){
		print "<B>$Team</B><B>R";
	}
	print "<TR><TD width=\"100%\"><TextArea rows=\"20\" cols=\"50\" onclick=\"this.select()\">";

	print $_GET["Header"];

	if($Team != -1){
		print "\t$Team";
	}

	print "\n";

	print "Game Number\tField";
	$Colors = explode(",",$_GET["PerGame"]);
	$Colors = StripLeadingTrailingSpaces($Colors);

	$Teamz[0] = "team_home";
	$Teamz[1] = "team_away";
	$Teamz[2] = "team_3";
	$Teamz[3] = "team_4";

	for($i=0;$i<$Leagues->PerGame;$i++){
		$Width = 30 / $Leagues->PerGame;
		$Width = $Width . "%";
		print "\t$Colors[$i]";

	}
	
	print "\tDate";
	
	print "\tResults";
	
	print "\n";

	for($i=0;$i<sizeof($myResult);$i++){
	
		if($Team != -1){
			for($j=0;$j<count($Teamz);$j++){
				if($myResult[$i]["$Teamz[$j]"] == $Team){
			
					print $myResult[$i]["game_id"];
					print "\t" . $myResult[$i]["game_resource"] . " ";
	
					for($k=0;$k<$Leagues->PerGame;$k++){
						print "\t" . $myResult[$i][$Teamz[$k]] . " ";
					}
					print "\t";
					print $myResult[$i]["game_Date"];
					for($k=0;$k<sizeof($Colors);$k++){
						print "\t";
						$Spaces = 25 / (sizeof($Colors));	
							for($l=0;$l<$Spaces;$l++){
									print "_";
							}
					}
					print "\n";
	
				}
			}	
		} else {

			for($j=0;$j<count($Teamz);$j++){
				
				print "" . $myResult[$i]["game_id"];
				print "\t" . $myResult[$i]["game_resource"] . " ";
				for($k=0;$k<$Leagues->PerGame;$k++){
					print "\t" . $myResult[$i][$Teamz[$k]] . " ";
				}
				print "\t";
				print $myResult[$i]["game_Date"];
				for($k=0;$k<sizeof($Colors);$k++){
					print "\t";
					$Spaces = 25 / (sizeof($Colors));	
						for($l=0;$l<$Spaces;$l++){
						print "_";
					}
				}
				print "\n";
			}	
		}	
	}

	print "</textarea></td></tr></table>\n";
}

function Text($myResult,$Leagues,$Team = -1){

	print "<TABLE class=\"pageBreak\" width=\"100%\">";
	print "<TR><TD width=\"100%\"><PRE>";

	print $_GET["Header"];
	print "\n";

	if($Team != -1){
		print "$Team\n";
	}

	print "\n";


	if($Leagues->CallBefore != 0){
		$PadGameNumber = 15;
		$PadGameNumber += floor(log($Leagues->Games,10));
	} else {
		$PadGameNumber = 13;
	}
	Pad("Game Number ",$PadGameNumber);
	
	$LongestResource = strlen($Leagues->Resources[0]->ResourceName);
	for($i=0;$i<count($Leagues->Resources);$i++){
		if(strlen($Leagues->Resources[$i]->ResourceName) > $LongestResource){
			$LongestResource = strlen($Leagues->Resources[$i]->ResourceName);
		}
	}
	$LongestResource+=5;

	Pad("Field",$LongestResource);

	$Colors = explode(",",$_GET["PerGame"]);
	$Colors = StripLeadingTrailingSpaces($Colors);

	$HighestColor = strlen($Colors[0]);
	for($i=1;$i<count($Colors);$i++){
		if($HighestColor < strlen($Colors[$i])){
			$HighestColor = strlen($Colors[$i]);
		}
	}

	for($i=0;$i<count($Leagues->Teams);$i++){
		if($HighestColor < strlen($Leagues->Teams[$i]->TeamName)){
			$HighestColor = strlen($Leagues->Teams[$i]->TeamName);
		}
	}

	$HighestColor +=5;

	$Teamz[0] = "team_home";
	$Teamz[1] = "team_away";
	$Teamz[2] = "team_3";
	$Teamz[3] = "team_4";

	for($i=0;$i<$Leagues->PerGame;$i++){
		Pad($Colors[$i],$HighestColor);
	}
	
	Pad("Date",27,0);
	
	print "Results";
	
	print "\n";

	for($i=0;$i<sizeof($myResult);$i++){
	
		if($Team != -1){
			for($j=0;$j<count($Teamz);$j++){
				if($myResult[$i]["$Teamz[$j]"] == $Team){
			
					Pad($myResult[$i]["game_id"],$PadGameNumber);
	
					Pad($myResult[$i]["game_resource"],$LongestResource);
					for($k=0;$k<$Leagues->PerGame;$k++){
						Pad($myResult[$i][$Teamz[$k]],$HighestColor);
					}
					print $myResult[$i]["game_Date"];

					print "     ";

					for($k=0;$k<sizeof($Colors);$k++){

						$Spaces = 25 / (sizeof($Colors));	
							for($l=0;$l<$Spaces;$l++){
									print "_";
							}
					print " ";
					}
					print "\n";
	
				}
			}	
		} else {

			Pad($myResult[$i]["game_id"],$PadGameNumber);
			Pad($myResult[$i]["game_resource"],$LongestResource);


			for($j=0;$j<count($Colors);$j++){
				
				Pad($myResult[$i][$Teamz[$j]],$HighestColor);
				
			}	

			print $myResult[$i]["game_Date"];

			print "     ";

			for($k=0;$k<sizeof($Colors);$k++){

				$Spaces = 25 / (sizeof($Colors));	
					for($l=0;$l<$Spaces;$l++){
					print "_";
				}
				print " ";
			}
			print "\n";
		}	
	}

	print "</pre></td></tr></table>\n";
}

function Pad($String,$Length,$Center=0){

	$Spaces = $Length - strlen($String);

	if($Center == 0){
		print $String;
		for($i=0;$i<=$Spaces;$i++){
			print " ";
		}
	} else {

		$SpacesLeft = floor($Spaces/2);
		$SpacesRight = ceiling($Spaces/2);
	
		for($i=0;$i<$SpacesLeft;$i++){
			print " ";
		}

		print "$String";

		for($i=0;$i<$SpacesRight;$i++){
			print " ";
		}
	}

}

function FullTime($Time){

	$Parts = explode(":",$Time);

	if(($Parts[0] < 10) && (strlen($Parts[0]) < 2)){
		$Parts[0] = 0 . $Parts[0];
	}

	if(($Parts[1] < 10) && (strlen($Parts[1]) < 2)){
		$Parts[1] = 0 . $Parts[1];
	}

	return $Parts[0] . ":" . $Parts[1];
}

function ReturnIf($GetName,$Value,$ToPrint){

	if($_COOKIE["$GetName"] == $Value){
		return $ToPrint;
	} 

}
?>

</body>
</html>