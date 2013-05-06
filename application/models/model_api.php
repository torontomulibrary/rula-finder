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
class Model_Api extends CI_Model {
	function catalogue_info($callNo){
		//Determine if "callNo" is LC Call Number, an ISBN, or a BIB Record
		$type = "";
	
		if(is_numeric(substr($callNo, 0, -1)) && (strlen($callNo) == 10 || strlen($callNo) == 13)) $type = 'ISBN';
		else if(substr($callNo, 0, 1) === 'b' && is_numeric(substr($callNo, 1)) && strlen($callNo) > 5) $type = 'BIB';
		else $type = 'CALLNO';
		
		switch($type){
			case "ISBN": 	$search_url = 'http://catalogue.library.ryerson.ca/search/i?SEARCH='; break;
			case "BIB": 	$search_url = 'http://catalogue.library.ryerson.ca/record='; break;
			default: 		$search_url = 'http://catalogue.library.ryerson.ca/search/c?SEARCH='; break;
		}
		
		
		$html = file_get_contents($search_url.$callNo);
		if($html === FALSE) return '0';
		
		//Remove all html special symbols such as &nbsp;, &gt;, etc...
		$html = preg_replace("/[&]\S*;/", "", $html);

		if(strrpos($html,"No matches found") !== FALSE) return '3';
		if(strrpos($html,"No Such Record") !== FALSE) return '3';
		
		
		//If multiple search results appear, click first link (closest match)
		if(strrpos($html,"anchor_2") !== FALSE){
			if(strrpos($html, "briefcitTitle") === FALSE){
				if(preg_match('/anchor_1.*href=\"(.*)\"/i', $html, $matches)){
					$html = preg_replace("/[&]\S*;/", "", file_get_contents('http://catalogue.library.ryerson.ca'.$matches[1]));
					
					//Another sub-results page, so click first result again
					if(strrpos($html, "briefcitTitle")){
						preg_match("/class=\"briefcitTitle\">\n<a href=\"(.+?)\"/s", $html, $matches);
						$html = preg_replace("/[&]\S*;/", "", file_get_contents('http://catalogue.library.ryerson.ca'.$matches[1]));
					}
				}
			}
			else{
				preg_match("/class=\"briefcitTitle\">\n<a href=\"(.+?)\"/s", $html, $matches);
				$html = preg_replace("/[&]\S*;/", "", file_get_contents('http://catalogue.library.ryerson.ca'.$matches[1]));
			}
		} 
		
		$offset = 0; //Used to choose an available book over online/missing/borrowed
		
		$data['availability'] = array('');
		
		//Get the status of item
		if(preg_match_all("/<!-- field % -->(.*?)<\/td>/", $html, $matches)){ 
			
			//Determine the offset (remove?)
			$count=0;
			foreach($matches[0] as $match){
				if(trim(strip_tags($match)) === "AVAILABLE"){
					$offset = $count;
				}
				$count++;
			}
			
			
			$data['availability'] = $matches[1];
		}
		
		
		//Get the floor(s) item is located on
		if(preg_match_all("/<!-- field 1 -->.*?(<a.+?>)?(.*?)<\/a>/", $html, $matches)){
			$data['location'] = $matches[2]; //Array
		}
		else if(preg_match_all("/.*?<a href=\".*?locations.*?\">(.*?)<\/a>/", $html, $matches)){
			
			$data['location'] = ($matches[1]);
			
		}
		else{
			$data['location'] = array(); //Added Dec 21, 2012 (fixed online reasoures not working)
		}
		
		
		//<div class="bibRecordLink"><a id="recordnum" href="/record=b2073290~S0">Permanent Link</a></div>		
		if(preg_match_all("/bibRecordLink.*?<a.+?record=(.+?)\">.*<\/a>/", $html, $matches)){ 		
			$data['bibno'] = $matches[1][0]; //Array
			
		}

		//Get the callnumber item is located on
		if(preg_match_all("/<!-- field C -->.*?(?:<!-- field v -->)?(?:<a.+?>)?(.*?)(?:<\/a>)? <!-- field (?:v|#)?/", $html, $matches)){
			
			if(isset($matches[1][0])){
				$data['callNumber'] = trim($matches[1][0]);
			}
		}
		else if(preg_match_all("/.*?<em>CALL #.*?class=\"bibHoldingsEntry\">(.*?)<\/td>/", $html, $matches)){
			if(isset($matches[1][0])){
				$data['callNumber'] = trim($matches[1][0]);
			}

		}
		else{		
			//Else set it to the user query
			$data['callNumber'] = $callNo; 
		}
		
		preg_match('/.*http:\/\/www.syndetics.com\/hw7.pl\?isbn=.*sc\.gif.*/i', $html, $matches);
		if(count($matches) === 1){
			$url = explode('"', strip_tags($matches[0], '<img>'));
			$data['cover_url']  = trim($url[1]);
			
			//Also parse ISBN out of cover
			preg_match('/isbn=.*\//i', $data['cover_url'], $match);
			$data['isbn'] = trim(substr($match[0],5,-1));
			
		}
		//else{
			//Try to scrape title / author
			//print 'here';
			if(preg_match("/<!-- next row for fieldtag=t -->.*<strong>(.*)<\/strong>.*<!-- next row for fieldtag=p -->/s", $html, $matches)){
				$data['title'] = trim($matches[1]);
			}			
		//}
		
		if(preg_match("/Author(.*)\<!-- next row for fieldtag=t --\>/s", $html, $matches)){
			$data['author'] =  trim(strip_tags($matches[1]));
		}
		
		//Check if it is online/has online version
		if(strrpos($html, "Connect to Internet Resources") > 0 || (isset($data['availability']) && $data['availability']) === "ONLINE"){ $data['isOnline'] = TRUE;	}
		else { $data['isOnline'] = FALSE; }
		
		return $data;
	}
	
	function callno_search($callno){
		$this->load->helper('call_number');
		
		
		$catalogue = $this->catalogue_info($callno); 
		
		
		//Convert to long integer first
		$call_int = convert_callnum($catalogue['callNumber']);
		

		
		if($catalogue === '3') return array('3');
		if(is_array($catalogue)){
			
			foreach($catalogue as $property => $value) {
				$data[$property] = $value;
			}
			
			
			//Get XISBN info
			if(isset($data['isbn']) && $data['isbn'] != ""){ 
				foreach($this->xisbn_info($data['isbn']) as $property => $value){
					$data[$property] = $value;
				}
			}
		}

		
		$online_only = true;
		foreach ($data['availability'] as $item_avail){
			if(isset($data['location']) && count($data['location']) > 1) $online_only = false;
			else if(isset($data['location']) && count($data['location']) == 1 && $data['isOnline'] == false) $online_only = false;
			
			//No known location (either doesn't exist, or catalogue does not list a location)
			else if(!isset($data['location']) || count($data['location'] === 0)){
				if($online_only === FALSE) return array("3");
			}
			else break;
			
			
		}
		
		if($online_only){
			$data['availability'] = "ONLINE";
			return array("2", array($data));
		}
		
		$loc_data = array();
		$count = 0;

		foreach($data['location'] as $location){
			//Get database information
			$stack_location = $this->db->query	(	"SELECT CONCAT('".MAPS_DIR."/',c.img_url) as img_url, b.*, s_type.priority 
													FROM tbl_stack_range a, tbl_location b, tbl_floors c, tbl_stack_type s_type 
													WHERE a.stack_type_id = s_type.stack_type_id AND
													'".$this->db->escape_str(trim($location))."' LIKE s_type.catalogue_pattern AND
													a.loc_id = b.loc_id AND
													c.floor_id = b.floor_id AND
													a.call_int_start <= ".$call_int." AND
													a.call_int_end > ".$call_int
												);
	
		
			if($stack_location->num_rows() === 0){
				$temp['hasLocation'] = false;
			}
			else{
				$temp['hasLocation'] = true;
			}
			
			//Has a known location, store data into array
			foreach($stack_location->row() as $property => $value) {
				$temp[$property] = $value;
			}
			

			if(isset($data['availability'][$count]))	$temp['availability']	= 	trim($data['availability'][$count]);
														$temp['location']		= 	trim($location);
			if(isset($data['cover_url']))				$temp['cover_url']		= 	trim($data['cover_url']);
			if(isset($data['isbn']))					$temp['isbn']			= 	trim($data['isbn']);
			if(isset($data['isOnline']))				$temp['isOnline']		= 	trim($data['isOnline']);
			if(isset($data['author']))					$temp['author']			= 	trim($data['author']);
			if(isset($data['title']))					$temp['title']			= 	trim($data['title']);
			if(isset($data['callNumber']))				$temp['callNumber']		= 	trim($data['callNumber']);
			if(isset($data['bibno']))					$temp['bibno']			= 	trim($data['bibno']);
			
			//If location exists, check its availability. Keep it if it says "AVAILABLE"
			if(isset($loc_data[$location]) && isset($loc_data[$location]['availability']) && $loc_data[$location]['availability'] === "AVAILABLE") {
				$temp['availability'] = "AVAILABLE";
			}
			
			$loc_data[$location] = $temp; //This will keep the last entry of a location's availability
			$count++;
			
			
		}
	
		
		$json_array = array_values($loc_data);
		//if(count($json_array) === 1 && $json_array[0]['hasLocation'] === false) return array("2", $json_array); //Exists in catalogue but no known location
		
		$item_located = false;
		foreach ($json_array as $loc){
			if($loc['hasLocation']) $item_located = true;
		}
		if (!$item_located){
			//SEND EMAIL
			$this->load->library('email');
			$this->email->from('noreply@ryerson.ca', 'Bookfinder Message');
			$this->email->to(UNMAPPED_EMAIL);
			
			
			$this->email->subject('An item was unable to be located in Bookfinder');
			$this->email->message	(
										"A user has searched for: <a href=\"http://catalogue.library.ryerson.ca/record=".urldecode($callno)."\">".urldecode($callno)."</a>, but Bookfinder was unable to find a known location. ". 
										"Please verfify the record has a shelf assigned to it.<br /><br />".
										"If you believe this is an error, please contact a member of the LITS Team"
									);

			$this->email->send();

			
			return array("2", $json_array); //Exists in catalogue but no known location
		}
		
		
		//print_r($json_array);
		
		
		//Sort by priority first
		usort($json_array, "_priority_sort");
		return array("1", $json_array); //array_values to return an indexed array rather then assioative
	}
	
	function xisbn_info($isbn){
		$raw_data = json_decode(file_get_contents('http://xisbn.worldcat.org/webservices/xid/isbn/'.$isbn.'.?format=json&fl=*'));
		if(isset($raw_data->list[0])){
			$raw_data = $raw_data->list[0];
			if(isset($raw_data->publisher)) $data['publisher'] 	= $raw_data->publisher;
			if(isset($raw_data->year)) 		$data['year'] 		= $raw_data->year;
			if(isset($raw_data->title)) 	$data['title']		= $raw_data->title;
			if(isset($raw_data->city)) 		$data['city'] 		= $raw_data->city;
		}
		else{
			return array();
		}
		return $data;
	}
	
	
	
}

//Private function used for sorting
function _priority_sort($a, $b){
	if(!isset($a['priority'])) $a['priority'] = 99;
	if(!isset($b['priority'])) $b['priority'] = 99;
	
	//Check for 'avail' status
	//print_r($a); die;
	if(strtoupper($a['availability'] !== "AVAILABLE") && strtoupper($b['availability'] === "AVAILABLE")){
		return 1;
	}
	else if(strtoupper($b['availability'] !== "AVAILABLE") && strtoupper($a['availability'] === "AVAILABLE")){
		return -1;
	}
	
	
	if($a['priority'] === $b['priority']) return 0;
	
	return ($a['priority'] < $b['priority']) ? -1 : 1;
}