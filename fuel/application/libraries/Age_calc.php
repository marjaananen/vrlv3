<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Age_calc
{
	public function calculateAge ($date, $aging = 84) {
	
		if( empty($aging) ) { $agingdays = 84;} else { $agingdays = $aging; }
	
		$today = date("d.m.Y");
		
		# How many days from date of birth, $date?
		$difference = strtotime($today) - strtotime($date);
		
		$days = round($difference / 86400);
		
		$age = round($days / $agingdays, 1);
		
		return $age;
	}
	
	public function fourYears ($date, $aging = 84) {
	
		if( empty($aging) ) { $agingdays = 84;} else { $agingdays = $aging; }
	
		$days = $agingdays * 4; // 4v päivät
		
		$new = strtotime($date) + ($days*86400);
		$fouryears = date("d.m.Y", $new);
		
		print $fouryears;
	}	
	
	public function calculateYears ($date, $years, $aging = 84) {
	
		if( empty($aging) ) { $agingdays = 84;} else { $agingdays = $aging; }
	
		$days = $agingdays * $years; // 4v päivät
		
		$new = strtotime($date) + ($days*86400);
		$newdate = date("d.m.Y", $new);
		
		return $newdate;
	}
	
	public function agingList ($date, $howmanyears, $aging = 84) {
	
		if( empty($aging) ) { $agingdays = 84;} else { $agingdays = $aging; }
		if( empty($howmanyears) ) { $howmanyears = 5;}
		
		$i = 1;
		
		print '<p>';
		while($i <= $howmanyears) {
			$days = $agingdays * $i; // Xv päivät
		
			$new = strtotime($date) + ($days*86400);
			$when = date("d.m.Y", $new);
			
			print $i.'v '.$when.'<br />';
			
			$i++;
		}
		print '</p>';
		
	}
	
	public function calculateBirthDate ($date, $aging = 84) {
	
		if( empty($aging) ) { $agingdays = 84;} else { $agingdays = $aging; }
				
		$month = round($agingdays / 12); 
		$pregnancytime = $month * 11; 
		
		$birth = strtotime($date) + ($pregnancytime * 86400);
		
		print date("d.m.Y", $birth);
		
	}

}
