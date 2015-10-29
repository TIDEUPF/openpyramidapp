<?php

function noSc_pyramid($levels, $student_arr, $student_amount){
	
	/*LEVEL 1*/
	for($i=0; $i < $levels; $i++)
	{
		unset($spare);
		if($i == 0)
		{			
			$lev =  $student_amount; //pow(2, $i);
			$st_list = $student_arr;
			$newChunk = array_chunk($st_list, $lev);

			$chunk_index = 0;
			foreach($newChunk as $temp){
				
				if(count($temp) < $lev){
					
					foreach($temp as $t){
						$spare[] = $t;
					}	
				}
				else{
					
					$group[$i][$chunk_index] = $temp;
					//foreach($temp as $t){ $group[$chunk_index][] = $temp; }
				}		
				$chunk_index++;
			}

			//var_dump($spare);

			//assign spare to a group
			if(count($spare) > 0){
				
				$spare_index = 1;
				foreach($spare as $spare_temp){			
					$group[$i][ count($group[$i]) - $spare_index ][] = $spare_temp;
					$spare_index++;
				}
			}
			//var_dump($group[0]);
			/*END LEVEL 1 */
		}
		else
		{			
			$lev =  2; //2 bcos pyramid chunk 2 groups together
			//get group1 indexes
			$previous_level = $i-1;
			$st_list = array_keys($group[$previous_level]);
			//var_dump($st_list);
			$newChunk = array_chunk($st_list, $lev);
			//var_dump($newChunk);
			$chunk_index = 0;
			foreach($newChunk as $temp)
			{				
				if(count($temp) < $lev)
				{
					
					//var_dump($temp);					
					foreach($temp as $t)
					{						
						$spare[] = $t;				
					}	
				}
				else{
					
					$group[$i][$chunk_index] = $temp;
					//foreach($temp as $t){ $group[$chunk_index][] = $temp; }
				}		
				$chunk_index++;
			}
			//assign spare to a group
			if(count($spare) > 0){
				
				$spare_index = 1;
				foreach($spare as $spare_temp){			
					$group[$i][ count($group[$i]) - $spare_index ][] = $spare_temp;
					$spare_index++;
				}
			}
			
			//assign student ids to created group in level
			$gindex = 0;			
			foreach($group[$i] as $gtemp){
				
				$prem_group[$i][$gindex] = array();	
				foreach($gtemp as $gtemp2){
					//$prem_group[$i][$gindex] = $group[0][$gtemp2];
					if($i == 1)
					{
						$prem_group[$i][$gindex] = array_merge($prem_group[$i][$gindex], $group[$previous_level][$gtemp2]);	
					}
					else
					{
						$prem_group[$i][$gindex] = array_merge($prem_group[$i][$gindex], $prem_group[$previous_level][$gtemp2]);	
					}
				}
				$gindex++;
			}
			//var_dump($spare);
			//var_dump($group[$i]);
			//var_dump($prem_group[$i]);
			
			/*END LEVEL 2 */
		}		
		
	}
	
	
	for($l=0; $l<$levels; $l++)
	{
		if($l == 0){
			$final_result[$l][0] = $group[$l];
		}
		else{
			$final_result[$l][0] = $prem_group[$l];
			$final_result[$l][1] = $group[$l];
		}
	}
	//var_dump($final_result[2]);
	return $final_result;
	
}	

?>