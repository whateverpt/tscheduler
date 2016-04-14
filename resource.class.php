<?php
class Resource {
   	function __construct() {
      // $this->ResouceName=$name;
   	}	
	var $ResourceName = 'SportsFieldA';
	var $UsedDateTimes=null; 
	// implemented as times this field can be used.
	/*Date/Time that this team cannot play.. I highly recommend the ability to specify ranges here*/   
	var $UsedDT = 0;
	var $EndDT = 0;

}
?>
