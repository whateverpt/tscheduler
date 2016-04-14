<?php
class Team {
   	function __construct() {
       //$this->TeamName=$name;
   	}	
	var $TeamName = 'Undefined';
	var $RestrictedDateTimes=array(); /*OPTIONAL: Date/Time that this team cannot play.. I highly recommend the ability to specify ranges here*/   
}
?>