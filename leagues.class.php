<?php
class League {
   	function __construct() {
       //$this->LeagueName=$name;
   	}	
	var $LeagueName = 'EasyLeague';
	var $DatesTimes=null; /*Monday (0) - Sunday (7) with times... $####  where $ is day of the week and #### are hours  */
	var $GameDuration=45; /*in minutes*/
	var $MinutesBetweenGame=10;/*in minutes*/
	var $Games=NULL;
	var $PerGame = 2; /* IGNORE ME or use it to eventually output for more then 2 teamsf*/
	var $Teams=null; /* The teams in this league */
	var $Resources=array(); /* The resources this league are allowed to use */
	var $CallBefore=0;
	var $Best = 0;
	var $Fill = 0;

	var $GameCountScore = 2;
	var $DistanceScore = 2;
	var $ColorScore = 2;
}
?>
