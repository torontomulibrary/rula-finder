<?php
/*This file belongs to the RULA BookFinder Application.
*
*Bookfinder is free software: you can redistribute it and/or modify
*it under the terms of the GNU General Public License as published by
*the Free Software Foundation, either version 3 of the License, or
*(at your option) any later version.
*
*Bookfinder is distributed in the hope that it will be useful,
*but WITHOUT ANY WARRANTY; without even the implied warranty of
*MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*GNU General Public License for more details.
*
*You should have received a copy of the GNU General Public License
*along with RULA Bookfinder.  If not, see <http://www.gnu.org/licenses/>.
*
*/
?>

<?php
	function convert_callnum($callnum_str){
		$callnum_str = strtoupper($callnum_str);
		
		//IGNORE 'Per.' in call numbers (eg. Per. TJ1180.A1 M329 1999)
		if(substr($callnum_str, 0, 5) === "PER. "){
			$callnum_str = substr($callnum_str,5);
		}
		
		$callnum_str .= " |";
		$array = str_split($callnum_str); //Convert into a char array
		$callnum_int  = '';
		$i = $index = 0;
		$count = 0;
		$length = count($array);
		
		
		/* Part 1 - 1 to 3 letters
		* Converts the first 1-3 letters to their ascii value
		* If there are less then 3 letters then it adds 00 to the end of the converted number
		* ex - AC becomes 656700
		* AAA - 656565
		* A - 650000		
		*/
		for ( $i=0; $i<3; $i++ ){
			if( preg_match("/[A-Z]/" ,$array[$index]) ){
				$callnum_int .= ord($array[$index]); //Get ASCII value
				$index++;
			}
			else
				$callnum_int .= "00";
		}
		
		
		//Handle a space
		$tempString = '';
		if(isset($array[$index+1])){
			$tempString = $array[$index].$array[$index+1];
		}
		
		if( preg_match("/(\s)[A-Z]/" ,$tempString) )
			$index++;
	
		
		/* Part 2  - A number between 1 and 9999.99999
		*
		*/
		//count how many numbers there are before the period
		while( is_numeric($array[$index]) ){
			$count++;
			$index++;
		}
		
		$index = $index - $count;

		//Up to 4 numbers, prepend 0's if less then that
		for ( $i=0; $i<(4-$count); $i++ ){
			$callnum_int .= "0";
		}
		
		for ( $i=0; $i<$count; $i++ ){
			$callnum_int .= $array[$index];
			$index++;
		}		
		
		if( preg_match("/\s/" ,$array[$index]) ){
			$index++;
		}
		
		$tempString = '';
		if(isset($array[$index+1])){
			$tempString = $array[$index].$array[$index+1];
		}
		
		
		
		//skip the period
		if(preg_match("/\.[0-9]/" ,$tempString))   
			$index++;
		
				
		//numbers after the period
		for ( $i=0; $i<5; $i++ ){
			if(preg_match("/[0-9]/" ,$array[$index])){
				$callnum_int .= $array[$index];
				$index++;
			}
			else
				$callnum_int .= "0";
		}
		
		/* Part 3 - A period followed by a letter and 0-4 digits
		*/		
		if( preg_match("/\s/" ,$array[$index]) ){
			$index++;
		}
		
		
		$tempString = '';
		if(isset($array[$index+1])){
			$tempString = $array[$index].$array[$index+1];
		}
		
		//skip the period
		if(preg_match("/\.[A-Z]/" ,$tempString))
			$index++;
		
		
		//the first letter
		if( preg_match("/[A-Z]/" ,$array[$index]) ){
			
			$callnum_int .= ord($array[$index]);
			$index++;
			
			//the 0-4 digits
			for ( $i=0; $i<4; $i++ ){
				if(preg_match("/[0-9]/" ,$array[$index])){
					$callnum_int .= $array[$index];
					$index++;
				}
				else
					$callnum_int .= "0";
			}
		}
		else
			$callnum_int .= "000000";

		
		/* Part 4 - Optional - a letter and 0-5 digits, no period
		*/
		$tempString = '';
		if(isset($array[$index+1])){
			$tempString = $array[$index].$array[$index+1];
		}
		
		
		if( preg_match("/(\s|\.)[A-Z]/" ,$tempString) )
			$index++;
		//the first letter
		if( preg_match("/[A-Z]/" ,$array[$index]) ){
			$callnum_int .= ord($array[$index]);
			$index++;
			
			//the 0-4 digits
			for ( $i=0; $i<5; $i++ ){
				if(preg_match("/[0-9]/" ,$array[$index])){
					$callnum_int .= $array[$index];
					$index++;
				}
				else
					$callnum_int .= "0";
			}
		}
		else
			$callnum_int .= "0000000";
		
		
		/* Part 6 - Optional - a year
		*/
		
		$tempString = '';
		if(isset($array[$index+1])){
			$tempString = $array[$index].$array[$index+1];
		}
		
		if( preg_match("/\s[0-9]/" ,$tempString) )
			$index++;
		for ( $i=0; $i<4; $i++ ){
			if(preg_match("/[0-9]/" ,$array[$index])){
				$callnum_int .= $array[$index];
				$index++;
			}
			else
				$callnum_int .= "0";
		}
		
		return $callnum_int;
	}
	
	