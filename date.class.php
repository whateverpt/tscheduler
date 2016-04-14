<?php
// i only accept military time..... :)
class Date {
   	function __construct() {

   	}	
	var $StartTime; // HH:MM
	var $StartDate; // MM/DD/YYYY
	var $EndTime; // HH:MM
	var $EndDate = NULL; // MM/DD/YYYY

	function Between($Time,$Date){
		
			$StartDateArray = explode('/',$this->StartDate);		
			$EndDateArray = explode('/',$this->EndDate);
			$TestDateArray = explode('/',$Date);

			$StartTimeArray = explode(':',$this->StartTime);
			$EndTimeArray = explode(':',$this->EndTime);		
			$TestTimeArray = explode(':',$Time);

			$StartTrans = 0;
			$StartTrans += ($StartDateArray[2] % 1000) * 365 * 24 * 60;
			$StartTrans += $StartDateArray[1] * 24 * 60;
			$StartTrans += $this->Months($StartDateArray[0],$StartDateArray[2]) * 24; 		
			$StartTrans += $StartTimeArray[0] * 60;
			$StartTrans += $StartTimeArray[1];


			$EndTrans = 0;
			$EndTrans += ($EndDateArray[2] % 1000) * 365 * 24 * 60;
			$EndTrans += $EndDateArray[1] * 24 * 60;
			$EndTrans += $this->Months($EndDateArray[0],$EndDateArray[2]) * 24; 		
			$EndTrans += $EndTimeArray[0] * 60;
			$EndTrans += $EndTimeArray[1];
					
			$TestTrans = 0;
			$TestTrans += ($TestDateArray[2] % 1000) * 365 * 24 * 60;
			$TestTrans += $TestDateArray[1] * 24 * 60;
			$TestTrans += $this->Months($TestDateArray[0],$TestDateArray[2]) * 24; 		
			$TestTrans += $TestTimeArray[0] * 60;
			$TestTrans += $TestTimeArray[1];

			//print "$StartTrans < $TestTrans < $EndTrans<BR>";
			
			if(($StartTrans <= $TestTrans) && ($TestTrans <= $EndTrans)){
				//print "This is firing";
				return 1;
			} 

			return 0;
	}

	function OverLap($Datein){
	
			// this date
			$StartDateArray = explode('/',$this->StartDate);		
			$EndDateArray = explode('/',$this->EndDate);
			
			$StartTimeArray = explode(':',$this->StartTime);
			$EndTimeArray = explode(':',$this->EndTime);		

			// date in
			$TestTimeArray = explode(':',$Datein->StartTime);
			$TestDateArray = explode('/',$Datein->StartDate);

			$TestTimeArray2 = explode(':',$Datein->EndTime);
			$TestDateArray2 = explode('/',$Datein->EndDate);

			// start date array
			$StartTrans = 0;
			$StartTrans += ($StartDateArray[2] % 1000) * 365 * 24 * 60;
			$StartTrans += $StartDateArray[1] * 24 * 60;
			$StartTrans += $this->Months($StartDateArray[0],$StartDateArray[2]) * 24; 		
			$StartTrans += $StartTimeArray[0] * 60;
			$StartTrans += $StartTimeArray[1];

			// end date array
			$EndTrans = 0;
			$EndTrans += ($EndDateArray[2] % 1000) * 365 * 24 * 60;
			$EndTrans += $EndDateArray[1] * 24 * 60;
			$EndTrans += $this->Months($EndDateArray[0],$EndDateArray[2]) * 24; 		
			$EndTrans += $EndTimeArray[0] * 60;
			$EndTrans += $EndTimeArray[1];
					
			// start time array
			$TestTrans = 0;
			$TestTrans += ($TestDateArray[2] % 1000) * 365 * 24 * 60;
			$TestTrans += $TestDateArray[1] * 24 * 60;
			$TestTrans += $this->Months($TestDateArray[0],$TestDateArray[2]) * 24; 		
			$TestTrans += $TestTimeArray[0] * 60;
			$TestTrans += $TestTimeArray[1];

			// end time array
			$TestTrans2 = 0;
			$TestTrans2 += ($TestDateArray2[2] % 1000) * 365 * 24 * 60;
			$TestTrans2 += $TestDateArray2[1] * 24 * 60;
			$TestTrans2 += $this->Months($TestDateArray2[0],$TestDateArray2[2]) * 24; 		
			$TestTrans2 += $TestTimeArray2[0] * 60;
			$TestTrans2 += $TestTimeArray2[1];

			/*
			if(($StartTrans < $TestTrans) && ($TestTrans < $EndTrans)){
				print "Overlaps";
				return 1;
			} 
			*/

			$RTime[0] = $StartTrans;
			$RTime[1] = $EndTrans;

			$ETime[0] = $TestTrans;
			$ETime[1] = $TestTrans2;

			for($i=0;$i<2;$i++){
	
				if(($RTime[0] < $ETime[0]) && ($ETime[0] < $RTime[1])){
					return 1;
				}
				if(($RTime[0] < $ETime[1]) && ($ETime[1] < $RTime[1])){
					return 1;
				} 
				$this->Swap($RTime[0],$ETime[0]);
				$this->Swap($RTime[1],$ETime[1]);
			}
			return 0;
		

	}
	
	function Swap(&$in,&$in2){
		$temp = $in;
		$in = $in2;
		$in2 = $temp;
	}

	function Months($Month,$Year){
		$Count = 0;

		$Months = array(0,31,28,31,30,31,30,31,31,30,31,30,31);

		for($i=0;$i<$Month;$i++){


			if(($i == 2) && ($Year % 4 == 0) && !($Year % 1000 != 0)){
				$Count += 29;
			} else {
				$Count += $Months[$i];
			}
		}

		return $Count;
	}

	function DDate($EndTime){
		$ReturnString = ($this->StartTime . " - " . $EndTime->StartTime . " " . $this->StartDate);
		return $ReturnString;
	}

	function AddMinutes($Minutes){
		
	}
}
?>